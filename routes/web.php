<?php

use App\Http\Controllers\ChallanController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanySelectionController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        if (session('selected_company_id')) {
            return redirect()->route('dashboard');
        }
        return redirect()->route('companies.select');
    }
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    // Company Selection Routes (before company middleware)
    Route::get('/companies/select', [CompanySelectionController::class, 'select'])->name('companies.select');
    Route::post('/companies/select', [CompanySelectionController::class, 'store'])->name('companies.select.store');
    Route::get('/companies/switch', [CompanySelectionController::class, 'switch'])->name('companies.switch');

    // Company Routes (can be accessed without company selection)
    Route::resource('companies', CompanyController::class);
});

Route::middleware(['auth', 'company'])->group(function () {
    Route::get('/dashboard', function () {
        $companyId = session('selected_company_id');
        $stats = [
            'parties' => \App\Models\Party::where('company_id', $companyId)->count(),
            'challans' => \App\Models\Challan::where('company_id', $companyId)->count(),
            'pending_challans' => \App\Models\Challan::where('company_id', $companyId)->where('is_invoiced', false)->count(),
            'invoices' => \App\Models\Invoice::where('company_id', $companyId)->count(),
            'total_invoiced' => \App\Models\Invoice::where('company_id', $companyId)->sum('final_amount'),
        ];
        return view('dashboard', compact('stats'));
    })->name('dashboard');

    // Party Routes
    Route::resource('parties', PartyController::class);
    Route::get('/api/parties/{party}/details', [PartyController::class, 'getDetails'])
        ->name('api.parties.details');

    // Challan Routes
    Route::resource('challans', ChallanController::class);
    Route::get('/api/parties/{party}/challans', [ChallanController::class, 'getByParty'])
        ->name('api.parties.challans');
    Route::post('/api/challans/check-duplicate', [ChallanController::class, 'checkDuplicate'])
        ->name('api.challans.check-duplicate');

    // Invoice Routes
    Route::resource('invoices', InvoiceController::class);
    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])
        ->name('invoices.pdf');
    Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])
        ->name('invoices.print');
    Route::post('/api/invoices/calculate', [InvoiceController::class, 'calculate'])
        ->name('api.invoices.calculate');
});

require __DIR__.'/auth.php';
