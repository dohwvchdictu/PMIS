<?php

use App\Livewire\BacApprovedPr\BacApprovedPrCreatePage;
use App\Livewire\BacApprovedPr\BacApprovedPrEditPage;
use App\Livewire\BacApprovedPr\BacApprovedPrIndexPage;
use App\Livewire\BacApprovedPr\BacApprovedPrViewPage;
use App\Livewire\ModeOfProcurement\ModeOfProcurementCreatePage;
use App\Livewire\ModeOfProcurement\ModeOfProcurementEditPage;
use App\Livewire\ModeOfProcurement\ModeOfProcurementIndexPage;
use App\Livewire\ModeOfProcurement\ModeOfProcurementUpdatePage;
use App\Livewire\ModeOfProcurement\ModeOfProcurementPerItemPage;
use App\Livewire\ModeOfProcurement\ModeOfProcurementPerLotPage;
use App\Livewire\ModeOfProcurement\ModeOfProcurementBulkEditPerLotPage;
use App\Livewire\PMU\PmuIndexPage;
use App\Livewire\PMU\PmuCreatePage;
use App\Livewire\PMU\PmuEditPage;
use App\Livewire\PMU\PmuViewPage;
use App\Livewire\Supply\SupplyIndexPage;
use App\Livewire\Supply\SupplyEditPage;
use App\Livewire\Procurements\ProcurementCreatePage;
use App\Livewire\Procurements\ProcurementEditPage;
use App\Livewire\Procurements\ProcurementIndexPage;
use App\Livewire\Procurements\ProcurementViewPage;
use App\Livewire\Procurements\PRUpdateStatus;
use App\Livewire\Reports\BacPrsReceivedBPage;
use App\Livewire\Reports\BacPrsReceivedPage;
use App\Livewire\Reports\ProcurementStatusPage;
use App\Livewire\Reports\PmrCatAPage;
use App\Livewire\Reports\PmrCatBPage;
use App\Livewire\ScheduleForPr\ScheduleForPrCreatePage;
use App\Livewire\ScheduleForPr\ScheduleForPrEditPage;
use App\Livewire\ScheduleForPr\ScheduleForPrIndexPage;
use Illuminate\Support\Facades\Route;
use App\Livewire\LoginPage;
use App\Livewire\HomePage;

// Public login route
Route::get('/login', LoginPage::class)
    ->middleware('guest')
    ->name('login');

// Protected routes with JwtMiddleware and Filament Shield
Route::middleware(['jwt'])->group(function () {

    Route::get('/', HomePage::class)->name('dashboard');

    // Procurement routes with Shield permissions
    Route::prefix('procurements')->name('procurements.')->group(function () {
        Route::get('/', ProcurementIndexPage::class)
            ->name('index')
            ->middleware('can:view_any_procurement');

        Route::get('/create', ProcurementCreatePage::class)
            ->name('create')
            ->middleware('can:create_procurement');

        Route::get('/{procurement}/edit', ProcurementEditPage::class)
            ->name('edit')
            ->middleware('can:update_procurement');

        Route::get('/{procurement}/view', ProcurementViewPage::class)
            ->name('view')
            ->middleware('can:view_procurement');

        Route::get('/{procurement}/update_status', PRUpdateStatus::class)
            ->name('update_status')
            ->middleware('can:update_procurement');

    });

    Route::prefix('bac-approved-pr')->name('bac-approved-pr.')->group(function () {
        Route::get('/', BacApprovedPrIndexPage::class)
            ->name('index')
            ->middleware('can:view_any_b::a::c::approved::p::r');

        Route::get('/create', BacApprovedPrCreatePage::class)
            ->name('create')
            ->middleware('can:create_b::a::c::approved::p::r');

        Route::get('/{bacapprovedpr}/edit', BacApprovedPrEditPage::class)
            ->name('edit')
            ->middleware('can:edit_b::a::c::approved::p::r');

        Route::get('/{bacapprovedpr}', BacApprovedPrViewPage::class)
            ->name('view')
            ->middleware('can:view_b::a::c::approved::p::r');
    });

    Route::prefix('schedule-for-procurement')->name('schedule-for-procurement.')->group(function () {
        Route::get('/', ScheduleForPrIndexPage::class)
            ->name('index')
            ->middleware('can:view_any_schedule::for::procurement');

        Route::get('/create', ScheduleForPrCreatePage::class)
            ->name('create')
            ->middleware('can:create_schedule::for::procurement');

        Route::get('/{id}/edit', ScheduleForPrEditPage::class)
            ->name('edit')
            ->middleware('can:edit_schedule::for::procurement');
    });

    // Mode of procurement routes with Shield permissions
    Route::prefix('mode-of-procurement')->name('mode-of-procurement.')->group(function () {
        Route::get('/', ModeOfProcurementIndexPage::class)
            ->name('index')
            ->middleware('can:view_any_mode::of::procurement');

        Route::get('/bulk-edit', ModeOfProcurementBulkEditPerLotPage::class)
            ->name('bulk-edit')
            ->middleware('can:update_mode::of::procurement');

        Route::get('/create', ModeOfProcurementCreatePage::class)
            ->name('create')
            ->middleware('can:create_mode::of::procurement');

        Route::get('/{procurement}/update', ModeOfProcurementUpdatePage::class)
            ->name('update')
            ->middleware('can:update_mode::of::procurement');

        Route::get('/{procurement}/update-per-item', ModeOfProcurementPerItemPage::class)
            ->name('update-per-item')
            ->middleware('can:update_mode::of::procurement');

        Route::get('/{procurement}/update-per-lot', ModeOfProcurementPerLotPage::class)
            ->name('update-per-lot')
            ->middleware('can:update_mode::of::procurement');

        Route::get('/{procurement}/edit', ModeOfProcurementEditPage::class)
            ->name('edit')
            ->middleware('can:edit_mode::of::procurement');
    });

    // PMU routes with Shield permissions
    Route::prefix('pmu')->name('pmu.')->group(function () {
        Route::get('/', PmuIndexPage::class)
            ->name('index')
            ->middleware('can:view_any_pmu');

        Route::get('/create', PmuCreatePage::class)
            ->name('create')
            ->middleware('can:create_pmu');

        Route::get('/{id}/edit', PmuEditPage::class)
            ->name('edit')
            ->middleware('can:update_pmu');

        Route::get('/{id}/view', PmuViewPage::class)
            ->name('view')
            ->middleware('can:view_pmu');
    });

    // Supply routes
    Route::prefix('supply')->name('supply.')->group(function () {
        Route::get('/', SupplyIndexPage::class)
            ->name('index')
            ->middleware('can:view_any_supply');

        Route::get('/{id}/edit', SupplyEditPage::class)
            ->name('edit')
            ->middleware('can:update_supply');
    });

    // Reports routes
    Route::prefix('reports')->name('reports.')->middleware('can:view_reports')->group(function () {
        Route::get('/pmr-cat-a', PmrCatAPage::class)
            ->name('pmr-cat-a');
        Route::get('/pmr-cat-b', PmrCatBPage::class)
            ->name('pmr-cat-b');

        Route::prefix('bac')->name('bac.')->middleware('can:view_bac_reports')->group(function () {
            Route::get('/prs-received', BacPrsReceivedPage::class)
                ->name('prs-received');
            Route::get('/prs-received-b', BacPrsReceivedBPage::class)
                ->name('prs-received-b');
            Route::get('/procurement-status', ProcurementStatusPage::class)
                ->name('procurement-status');
        });
    });

    // Logout
    Route::post('/logout', function () {
        auth()->logout();
        session()->forget(['jwt_token', 'token_created_at', 'login_credentials', 'roleName', 'user', 'user_photo']);
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});
