<?php

namespace App\Providers;

use App\Models\Area;
use App\Models\Category;
use App\Models\Facility;
use App\Models\Owner;
use App\Models\Property;
use App\Models\User;
use App\Policies\AreaPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\FacilityPolicy;
use App\Policies\OwnerPolicy;
use App\Policies\PropertyPolicy;
use App\Policies\UserPolicy;
use Carbon\Carbon;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
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
        Carbon::setLocale(config('app.locale'));

        Gate::policy(Area::class, AreaPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Facility::class, FacilityPolicy::class);
        Gate::policy(Owner::class, OwnerPolicy::class);
        Gate::policy(Property::class, PropertyPolicy::class);
        Gate::policy(User::class, UserPolicy::class);

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
