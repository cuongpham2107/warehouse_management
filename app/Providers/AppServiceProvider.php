<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentTimezone;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Carbon\Carbon;

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
        
        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer')
                        ->setDescription('Sử dụng mã thông báo của người mang để xác thực.'),
                );
            });

       

    }
}
