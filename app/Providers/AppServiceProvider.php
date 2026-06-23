<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use PhpOffice\PhpSpreadsheet\Shared\File as PhpSpreadsheetFile;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $temporaryDirectory = storage_path('framework/cache/laravel-excel');

        if (! is_dir($temporaryDirectory)) {
            mkdir($temporaryDirectory, 0775, true);
        }

        config([
            'excel.temporary_files.local_path' => $temporaryDirectory,
        ]);

        putenv("TMPDIR={$temporaryDirectory}");
        putenv("TEMP={$temporaryDirectory}");
        putenv("TMP={$temporaryDirectory}");

        if (function_exists('sys_get_temp_dir')) {
            @ini_set('upload_tmp_dir', $temporaryDirectory);
            PhpSpreadsheetFile::setUseUploadTempDirectory(true);
        }
    }
}
