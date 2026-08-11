<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\FlowJob;
use App\Models\FlowJobMember;
use App\Models\Inquiry;
use App\Models\InquiryTask;
use App\Models\MasterRecord;
use App\Models\Task;
use App\Models\User;
use App\Models\WorkflowPhase;
use App\Observers\WorkspaceDataObserver;
use App\Services\AccessControlService;
use App\Services\BrandingService;
use App\Services\MentionService;
use App\Services\MasterDataService;
use App\Services\ShellDataService;
use App\Services\SetupContext;
use App\Support\Performance\RequestPerformanceMonitor;
use Illuminate\Cache\Events\CacheFailedOver;
use Illuminate\Cache\Events\CacheHit;
use Illuminate\Cache\Events\CacheMissed;
use Illuminate\Cache\Events\KeyForgotten;
use Illuminate\Cache\Events\KeyWritten;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Http\Client\Events\ConnectionFailed;
use Illuminate\Http\Client\Events\RequestSending;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use RuntimeException;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AccessControlService::class);
        $this->app->scoped(MentionService::class);
        $this->app->scoped(MasterDataService::class);
        $this->app->scoped(BrandingService::class);
        $this->app->scoped(RequestPerformanceMonitor::class);
        $this->app->scoped(ShellDataService::class);
        $this->app->scoped(SetupContext::class);
        $this->app->scoped(\App\Services\WorkspaceRefreshService::class);
    }

    public function boot(): void
    {
        foreach ([
            Client::class,
            FlowJob::class,
            FlowJobMember::class,
            Inquiry::class,
            InquiryTask::class,
            MasterRecord::class,
            Task::class,
            User::class,
            WorkflowPhase::class,
        ] as $modelClass) {
            $modelClass::observe(WorkspaceDataObserver::class);
        }

        Event::listen(QueryExecuted::class, function (QueryExecuted $event): void {
            app(RequestPerformanceMonitor::class)->recordQuery($event);
        });

        Event::listen(CacheHit::class, fn () => app(RequestPerformanceMonitor::class)->recordCacheHit());
        Event::listen(CacheMissed::class, fn () => app(RequestPerformanceMonitor::class)->recordCacheMiss());
        Event::listen(KeyWritten::class, fn () => app(RequestPerformanceMonitor::class)->recordCacheWrite());
        Event::listen(KeyForgotten::class, fn () => app(RequestPerformanceMonitor::class)->recordCacheForget());
        Event::listen(CacheFailedOver::class, fn () => app(RequestPerformanceMonitor::class)->recordCacheFailover());

        Event::listen(RequestSending::class, function (RequestSending $event): void {
            app(RequestPerformanceMonitor::class)->startOutgoing($event->request);
        });

        Event::listen(ResponseReceived::class, function (ResponseReceived $event): void {
            app(RequestPerformanceMonitor::class)->finishOutgoing($event->request, $event->response);
        });

        Event::listen(ConnectionFailed::class, function (ConnectionFailed $event): void {
            $exception = property_exists($event, 'exception') && $event->exception instanceof Throwable
                ? $event->exception
                : new RuntimeException('HTTP connection failed.');
            app(RequestPerformanceMonitor::class)->finishOutgoing($event->request, null, $exception);
        });

        View::composer(['layouts.app', 'auth.login'], function ($view): void {
            $view->with('branding', app(BrandingService::class)->current());
        });

        View::composer('layouts.app', function ($view): void {
            $user = auth()->user();
            $view->with('shellData', $user ? app(ShellDataService::class)->for($user) : []);
        });
    }
}
