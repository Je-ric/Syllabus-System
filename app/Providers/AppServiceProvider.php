<?php

namespace App\Providers;

use App\Helpers\Sanitizer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Register custom Blade directives for XSS protection
        Blade::directive('safe', function ($expression) {
            return "<?php echo e(App\Helpers\Sanitizer::clean($expression)); ?>";
        });

        Blade::directive('safeName', function ($expression) {
            return "<?php echo e(App\Helpers\Sanitizer::sanitizeName($expression)); ?>";
        });

        Storage::extend('google', function ($app, $config) {
            if (! class_exists(\Google\Client::class)) {
                throw new \RuntimeException(
                    'Google Drive storage requires google/apiclient. Run: composer require google/apiclient masbug/flysystem-google-drive-ext'
                );
            }

            $refreshToken = $config['refreshToken'] ?? null;
            $oauthCredentials = $config['oauthCredentials'] ?? null;

            if (! $refreshToken || ! $oauthCredentials) {
                throw new \RuntimeException('Google Drive OAuth not configured. Run: php artisan gdrive:authorize');
            }

            $client = new \Google\Client();
            $client->setApplicationName('CSMS');
            $client->setAuthConfig(base_path($oauthCredentials));
            $client->setScopes([\Google\Service\Drive::DRIVE]);
            $client->setAccessType('offline');
            $client->refreshToken($refreshToken);

            $folderId = $config['folder'] ?? null;
            if (! $folderId) {
                throw new \RuntimeException('GOOGLE_DRIVE_FOLDER is not set in .env');
            }

            $service = new \Google\Service\Drive($client);

            $adapter = new \Masbug\Flysystem\GoogleDriveAdapter(
                $service,
                null,
                [
                    'useDisplayPaths' => true,
                    'sharedFolderId'  => $folderId,
                ]
            );
            $driver = new \League\Flysystem\Filesystem($adapter);

            return new \Illuminate\Filesystem\FilesystemAdapter($driver, $adapter);
        });
    }
}
