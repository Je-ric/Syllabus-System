<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GoogleDriveAuthorize extends Command
{
    // command = php artisan gdrive:authorize
    protected $signature   = 'gdrive:authorize';
    protected $description = 'Authorize Google Drive via OAuth2 and store refresh token in .env';

    public function handle(): void
    {
        $credentialsPath = base_path(config('filesystems.disks.google.oauthCredentials'));

        if (! file_exists($credentialsPath)) {
            $this->error("OAuth2 credentials file not found at: {$credentialsPath}");
            $this->line('Download from Google Cloud Console → APIs & Services → Credentials → OAuth 2.0 Client IDs → Download JSON');
            return;
        }

        $client = new \Google\Client();
        $client->setAuthConfig($credentialsPath);
        $client->setScopes([\Google\Service\Drive::DRIVE]);
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        $this->info('Open this URL in your browser and authorize:');
        $this->line($client->createAuthUrl());

        $code  = $this->ask('Paste the authorization code here');
        $token = $client->fetchAccessTokenWithAuthCode($code);

        if (isset($token['error'])) {
            $this->error('Token error: ' . ($token['error_description'] ?? $token['error']));
            return;
        }

        $refreshToken = $token['refresh_token'] ?? null;

        if (! $refreshToken) {
            $this->error('No refresh token returned. Revoke app access at myaccount.google.com and try again.');
            return;
        }

        $envPath    = base_path('.env');
        $envContent = file_get_contents($envPath);

        if (str_contains($envContent, 'GOOGLE_DRIVE_REFRESH_TOKEN=')) {
            $envContent = preg_replace('/^GOOGLE_DRIVE_REFRESH_TOKEN=.*/m', "GOOGLE_DRIVE_REFRESH_TOKEN={$refreshToken}", $envContent);
        } else {
            $envContent .= "\nGOOGLE_DRIVE_REFRESH_TOKEN={$refreshToken}";
        }

        file_put_contents($envPath, $envContent);

        $this->info('Done! Refresh token saved. Run: php artisan config:clear');
    }
}
