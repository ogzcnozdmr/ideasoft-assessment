<?php

namespace App\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class SanctumMongoDBServiceProvider extends ServiceProvider
{
    /**
     * @var AliasLoader $loader
     */
    private AliasLoader $loader;

    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->loader = AliasLoader::getInstance();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loader->alias(\Laravel\Sanctum\PersonalAccessToken::class, \App\Models\PersonalAccessToken::class);
    }
}
