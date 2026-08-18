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

            $client = new \Google\Client();
            $client->setApplicationName('CSMS');

            // Service account auth — never expires, correct for server deployments.
            // The service account email must be shared as Editor on the Drive folder.
            // To find the email: open storage/app/csms-*.json and read 'client_email'.
            $client->setAuthConfig(base_path($config['serviceAccountJson']));
            $client->setScopes([\Google\Service\Drive::DRIVE]);

            $folderId = $config['folder'] ?? null;
            if (! $folderId) {
                throw new \RuntimeException('GOOGLE_DRIVE_FOLDER is not set in .env');
            }

            $service = new \Google\Service\Drive($client);

            // Verify the folder exists and get its ID directly to avoid
            // the adapter resolving it by name and creating a duplicate folder.
            try {
                $folder = $service->files->get($folderId, ['fields' => 'id,name']);
                $resolvedId = $folder->getId();
            } catch (\Throwable $e) {
                throw new \RuntimeException("Google Drive folder '{$folderId}' not found or not accessible: " . $e->getMessage());
            }

            $adapter = new \Masbug\Flysystem\GoogleDriveAdapter(
                $service,
                null,
                [
                    'useDisplayPaths' => true,
                    'sharedFolderId'  => $resolvedId,
                ]
            );
            $driver = new \League\Flysystem\Filesystem($adapter);

            return new \Illuminate\Filesystem\FilesystemAdapter($driver, $adapter);
        });
    }
}
