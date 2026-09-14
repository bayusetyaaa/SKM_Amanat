<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $user = Auth::user();
            $menuFile = ($user && $user->role === 'admin')
                ? 'resources/menu/verticalMenuAdmin.json'
                : 'resources/menu/verticalMenuMember.json';

            if (file_exists(base_path($menuFile))) {
                $menuJson = file_get_contents(base_path($menuFile));
                $menuData = json_decode($menuJson);
            } else {
                $menuData = (object)['menu' => []];
            }

            $view->with('menuData', [$menuData]);
        });
    }
}
