<?php

namespace App\Providers;

use Carbon\Carbon;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Filament\Support\Facades\FilamentTimezone;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        // Set timezone cho Filament
        FilamentTimezone::set('Asia/Ho_Chi_Minh');

        // Set locale cho Carbon (để hiển thị tên tháng, ngày bằng tiếng Việt)
        Carbon::setLocale('vi');

        // Set locale cho ứng dụng Laravel
        app()->setLocale('vi');

        if (str_contains(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer')
                        ->setDescription('Sử dụng mã thông báo của người mang để xác thực.'),
                );
            });

    }
}
