<?php

namespace App\Providers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Storage::extend('google', function ($app, $config) {
            if (! class_exists(\Google\Client::class)) {
                throw new \RuntimeException(
                    'Google Drive storage requires google/apiclient. Run: composer require google/apiclient masbug/flysystem-google-drive-ext'
                );
            }

            $client = new \Google\Client();
            $client->setApplicationName('CSMS');

            $refreshToken = $config['refreshToken'] ?? null;

            if ($refreshToken) {
                // OAuth2 — files owned by the authorizing Gmail user (no quota issue)
                $client->setAuthConfig(base_path($config['oauthCredentials']));
                $client->setScopes([\Google\Service\Drive::DRIVE]);
                $client->setAccessType('offline');
                $client->fetchAccessTokenWithRefreshToken($refreshToken);
            } else {
                // Service account fallback
                $client->setAuthConfig(base_path($config['serviceAccountJson']));
                $client->setScopes([\Google\Service\Drive::DRIVE]);
            }

            $folderId = $config['folder'] ?? null;
            if (! $folderId) {
                throw new \RuntimeException('GOOGLE_DRIVE_FOLDER is not set in .env');
            }

            $service = new \Google\Service\Drive($client);
            $adapter = new \Masbug\Flysystem\GoogleDriveAdapter(
                $service,
                $folderId,
                ['useDisplayPaths' => true]
            );
            $driver = new \League\Flysystem\Filesystem($adapter);

            return new \Illuminate\Filesystem\FilesystemAdapter($driver, $adapter);
        });
    }
}
