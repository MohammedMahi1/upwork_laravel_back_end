<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
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
            ResetPassword::createUrlUsing(function ($notifiable, string $token) {
        $email = urlencode($notifiable->getEmailForPasswordReset());
        return "http://localhost:3000/reset-password?token={$token}&email={$email}";
    });
    }
}
