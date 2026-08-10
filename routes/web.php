<?php

use App\Http\Controllers\AdministrationController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\BulkOrderImportController;
use App\Http\Controllers\BrandingAssetController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\ClientLogoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentsController;
use App\Http\Controllers\FilterOptionController;
use App\Http\Controllers\JobsController;
use App\Http\Controllers\InquiriesController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\MyWorkController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\NotificationOpenController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileImageController;
use App\Http\Controllers\RichTextImageController;
// Reports page is intentionally disabled for now.
// use App\Http\Controllers\ReportsController;
use App\Http\Controllers\TaskPackSetupController;
use App\Http\Controllers\UserEditController;
use App\Http\Controllers\WorkflowSetupController;
use App\Models\Document;
use App\Services\JobService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;


Route::get('/branding-assets/{type}/{filename}', BrandingAssetController::class)
    ->where('type', 'logo|favicon')
    ->where('filename', '[A-Za-z0-9_-]+\.(?:jpg|jpeg|png|webp|ico)')
    ->name('branding-assets.show');

Route::get('/session/recover', function (\Illuminate\Http\Request $request) {
    // Recovery is intentionally a GET: it is the safe landing point after a
    // CSRF/session mismatch, where the old server-side session is discarded
    // and the browser receives a fresh session/CSRF cookie pair.
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login', ['reason' => 'session-refresh'])
        ->withHeaders([
            'Cache-Control' => 'private, no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
})->name('session.recover');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::get('/session/status', function () {
    return response()->json(['ok' => true, 'user_id' => auth()->id()]);
})->middleware('auth')->name('session.status');

Route::middleware('auth')->group(function () {
    Route::post('/session/timezone', function (\Illuminate\Http\Request $request) {
        $data = $request->validate([
            'timezone' => ['required', 'string', 'max:120'],
        ]);

        abort_unless(in_array($data['timezone'], \DateTimeZone::listIdentifiers(), true), 422, 'Invalid time zone.');
        $request->session()->put('flowtrack_timezone', $data['timezone']);

        return response()->noContent();
    })->name('session.timezone');
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
    Route::get('/filter-options/{type}', FilterOptionController::class)->where('type', 'clients|jobs|users|product-categories|products|workflows|priorities|task-statuses|document-categories|countries|job-statuses|job-healths|phases')->name('filter-options.index');
    Route::get('/my-work', MyWorkController::class)->middleware('permission:tasks.view')->name('my-work');
    Route::get('/inquiries', InquiriesController::class)->middleware('permission:inquiries.view')->name('inquiries.index');
    Route::get('/orders/bulk-import', [BulkOrderImportController::class, 'index'])->middleware('permission:jobs.create')->name('orders.bulk-import');
    Route::post('/orders/bulk-import/validate', [BulkOrderImportController::class, 'validateUpload'])->middleware('permission:jobs.create')->name('orders.bulk-import.validate');
    Route::post('/orders/bulk-import/revalidate', [BulkOrderImportController::class, 'revalidate'])->middleware('permission:jobs.create')->name('orders.bulk-import.revalidate');
    Route::post('/orders/bulk-import/import', [BulkOrderImportController::class, 'import'])->middleware('permission:jobs.create')->name('orders.bulk-import.import');
    Route::get('/orders/bulk-import/template', [BulkOrderImportController::class, 'template'])->middleware('permission:jobs.create')->name('orders.bulk-import.template');
    Route::get('/orders', JobsController::class)->middleware('permission:jobs.view')->name('jobs.index');
    Route::get('/jobs', function (\Illuminate\Http\Request $request) {
        return redirect()->route('jobs.index', $request->query());
    })->middleware('permission:jobs.view')->name('jobs.legacy');
    Route::get('/clients', ClientsController::class)->middleware('permission:clients.view')->name('clients.index');
    Route::get('/all-tasks', BoardController::class)->middleware('permission:tasks.view')->name('all-tasks');
    Route::get('/documents', DocumentsController::class)->middleware('permission:documents.view')->name('documents.index');
    Route::get('/inquiries/documents/{document}/open', function (\App\Models\InquiryDocument $document) {
        $inquiry = app(\App\Services\InquiryService::class)->visibleQuery(auth()->user())->whereKey($document->inquiry_id)->firstOrFail();
        return Storage::disk((string) config('flowtrack.document_disk', 'public'))->response(
            $document->path,
            $document->name,
            ['Content-Disposition' => 'inline; filename=\"'.addslashes($document->name).'\"']
        );
    })->name('inquiries.documents.open');
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
    // Reports page is intentionally disabled for now.
    // Route::get('/reports', ReportsController::class)->middleware('permission:reports.view')->name('reports');
    Route::get('/notifications', NotificationsController::class)->name('notifications');
    Route::get('/notifications/{notification}/open', NotificationOpenController::class)->whereNumber('notification')->name('notifications.open');
    Route::get('/notifications/unread-count', function () {
        $user = auth()->user();
        $service = app(\App\Services\NotificationService::class);
        $latest = $service->latest($user);

        return response()->json([
            'count' => $service->unreadCount($user),
            'latest' => $latest ? [
                'id' => $latest->id,
                'type' => $latest->type,
                'title' => $latest->title,
                'message' => app(\App\Services\RichTextService::class)->plainText($latest->message),
                'url' => $service->urlFor($latest),
                'created_at' => $latest->created_at?->toIso8601String(),
            ] : null,
        ]);
    })->name('notifications.unread-count');
    Route::get('/profile-images/{user}/{filename}', ProfileImageController::class)
        ->whereNumber('user')
        ->where('filename', '[A-Za-z0-9_-]+\.(?:jpg|jpeg|png|webp)')
        ->name('profile-images.show');
    Route::get('/client-logos/{client}/{filename}', ClientLogoController::class)
        ->whereNumber('client')
        ->where('filename', '[A-Za-z0-9_-]+\.(?:jpg|jpeg|png|webp)')
        ->name('client-logos.show');
    Route::post('/rich-text-images', [RichTextImageController::class, 'store'])
        ->name('rich-text-images.store');
    Route::get('/rich-text-images/{filename}/download', [RichTextImageController::class, 'download'])
        ->where('filename', '[A-Za-z0-9-]+\.(?:jpg|jpeg|png|webp|gif)')
        ->name('rich-text-images.download');
    Route::get('/rich-text-images/{filename}', [RichTextImageController::class, 'show'])
        ->where('filename', '[A-Za-z0-9-]+\.(?:jpg|jpeg|png|webp|gif)')
        ->name('rich-text-images.show');
    Route::get('/profile', ProfileController::class)->name('profile');
    Route::get('/users/{user}/edit', UserEditController::class)->whereNumber('user')->name('users.edit');
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
