<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('layouts.navigation', function ($view): void {
            $view->with(
                'catalogCategories',
                Category::query()
                    ->orderBy('name')
                    ->get(['id', 'name', 'slug'])
            );
        });
    }
}
