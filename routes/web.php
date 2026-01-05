<?php

use App\Http\Controllers\ChallanController;
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
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    $stats = [
        'parties' => \App\Models\Party::count(),
        'challans' => \App\Models\Challan::count(),
        'pending_challans' => \App\Models\Challan::where('is_invoiced', false)->count(),
        'invoices' => \App\Models\Invoice::count(),
        'total_invoiced' => \App\Models\Invoice::sum('final_amount'),
    ];
    return view('dashboard', compact('stats'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

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
    Route::resource('invoices', InvoiceController::class)->except(['edit', 'update']);
    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])
        ->name('invoices.pdf');
    Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])
        ->name('invoices.print');
    Route::post('/api/invoices/calculate', [InvoiceController::class, 'calculate'])
        ->name('api.invoices.calculate');
});

require __DIR__.'/auth.php';
