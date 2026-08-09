<?php

namespace App\Providers;

use App\Events\CaseConfirmed;
use App\Events\PointThresholdReached;
use App\Listeners\NotifyGuruBkOnThreshold;
use App\Listeners\SendParentNotification;
use App\Models\CaseRecord;
use App\Models\Student;
use App\Policies\CasePolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
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
        Paginator::useBootstrapFour();

        Gate::policy(CaseRecord::class, CasePolicy::class);
        Gate::policy(Student::class, \App\Policies\StudentPolicy::class);

        Event::listen(CaseConfirmed::class, SendParentNotification::class);
        Event::listen(PointThresholdReached::class, NotifyGuruBkOnThreshold::class);
    }
}
