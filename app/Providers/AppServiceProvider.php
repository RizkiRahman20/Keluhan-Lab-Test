<?php

namespace App\Providers;

use App\Models\Perbaikan;
use App\Policies\PerbaikanPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Gate::policy(Perbaikan::class, PerbaikanPolicy::class);
    }
}