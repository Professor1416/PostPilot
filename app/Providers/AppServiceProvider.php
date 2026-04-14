<?php

namespace App\Providers;

use App\Models\Post;
use App\Policies\PostPolicy;
use App\Services\AnthropicService;
use App\Services\MetaService;
use App\Services\RazorpayService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register services as singletons
        $this->app->singleton(AnthropicService::class);
        $this->app->singleton(MetaService::class);
        $this->app->singleton(RazorpayService::class);
    }

    public function boot(): void
    {
        // Register policies
        Gate::policy(Post::class, PostPolicy::class);

        // Use Bootstrap-style pagination (or Tailwind)
        Paginator::useBootstrapFive();
    }
}
