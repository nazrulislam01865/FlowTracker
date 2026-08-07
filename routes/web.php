<?php

use App\Http\Controllers\AdministrationController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentsController;
use App\Http\Controllers\JobsController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\MyWorkController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileImageController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\TaskPackSetupController;
use App\Http\Controllers\WorkflowSetupController;
use App\Models\Document;
use App\Services\JobService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::get('/session/status', function () {
    return response()->json(['ok' => true, 'user_id' => auth()->id()]);
})->middleware('auth')->name('session.status');

Route::middleware('auth')->group(function () {
    Route::post('/pusher/auth', function (\Illuminate\Http\Request $request) {
        $data = $request->validate([
            'socket_id' => ['required','string','max:80'],
            'channel_name' => ['required','string','max:160'],
        ]);
        return response()->json(app(\App\Services\PusherChannelService::class)->authenticate(
            $data['socket_id'], $data['channel_name'], (int) auth()->id()
        ));
    })->name('pusher.auth');
    Route::redirect('/', '/dashboard');
    Route::get('/dashboard', DashboardController::class)->middleware('permission:dashboard.view')->name('dashboard');
    Route::get('/my-work', MyWorkController::class)->middleware('permission:tasks.view')->name('my-work');
    Route::get('/jobs', JobsController::class)->middleware('permission:jobs.view')->name('jobs.index');
    Route::get('/clients', ClientsController::class)->middleware('permission:clients.view')->name('clients.index');
    Route::get('/board', BoardController::class)->middleware('permission:tasks.view')->name('board');
    Route::get('/documents', DocumentsController::class)->middleware('permission:documents.view')->name('documents.index');
    Route::get('/documents/{document}/open', function (Document $document) {
        app(\App\Services\AccessControlService::class)->applyDocumentScope(Document::query()->whereKey($document->id), auth()->user())->firstOrFail();
        return Storage::disk((string) config('flowtrack.document_disk', 'public'))->response(
            $document->path,
            $document->name,
            ['Content-Disposition' => 'inline; filename=\"'.addslashes($document->name).'\"']
        );
    })->name('documents.open');
    Route::get('/documents/{document}/download', function (Document $document) {
        abort_unless(auth()->user()->canModule('documents', 'export'), 403);
        app(\App\Services\AccessControlService::class)->applyDocumentScope(Document::query()->whereKey($document->id), auth()->user())->firstOrFail();
        return Storage::disk((string) config('flowtrack.document_disk', 'public'))->download($document->path, $document->name);
    })->name('documents.download');
    Route::get('/reports', ReportsController::class)->middleware('permission:reports.view')->name('reports');
    Route::get('/notifications', NotificationsController::class)->name('notifications');
    Route::get('/notifications/unread-count', function () {
        $user = auth()->user();
        $service = app(\App\Services\NotificationService::class);
        $latest = \App\Models\FlowNotification::query()
            ->where('user_id', $user->id)
            ->latest('id')
            ->first();

        return response()->json([
            'count' => $service->unreadCount($user),
            'latest' => $latest ? [
                'id' => $latest->id,
                'type' => $latest->type,
                'title' => $latest->title,
                'message' => $latest->message,
                'url' => $service->urlFor($latest),
                'created_at' => $latest->created_at?->toIso8601String(),
            ] : null,
        ]);
    })->name('notifications.unread-count');
    Route::get('/profile-images/{user}/{filename}', ProfileImageController::class)
        ->whereNumber('user')
        ->where('filename', '[A-Za-z0-9_-]+\.(?:jpg|jpeg|png|webp)')
        ->name('profile-images.show');
    Route::get('/profile', ProfileController::class)->name('profile');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::middleware('permission:workflow.manage')->group(function () {
        Route::get('/workflow-setup', [WorkflowSetupController::class, 'index'])->name('workflow.setup');
        Route::get('/workflow-setup/create', [WorkflowSetupController::class, 'create'])->name('workflow.create');
        Route::get('/workflow-setup/{workflow}/edit', [WorkflowSetupController::class, 'edit'])->whereNumber('workflow')->name('workflow.edit');
        Route::get('/task-pack-setup', [TaskPackSetupController::class, 'index'])->name('task-pack.setup');
        Route::get('/task-pack-setup/create', [TaskPackSetupController::class, 'create'])->name('task-pack.create');
        Route::get('/task-pack-setup/{taskPack}/edit', [TaskPackSetupController::class, 'edit'])->whereNumber('taskPack')->name('task-pack.edit');
    });
    Route::get('/master-data', MasterDataController::class)->middleware('permission:master.manage')->name('master-data');
    Route::get('/administration', AdministrationController::class)->middleware('super.admin')->name('administration');
});
