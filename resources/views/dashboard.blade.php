@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <h1>Dashboard</h1>
    <div>
        <a href="{{ route('invoices.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>New Invoice
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <h3>{{ $stats['parties'] ?? 0 }}</h3>
            <p><i class="bi bi-people me-1"></i>Total Parties</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
            <h3>{{ $stats['challans'] ?? 0 }}</h3>
            <p><i class="bi bi-file-text me-1"></i>Total Challans</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <h3>{{ $stats['pending_challans'] ?? 0 }}</h3>
            <p><i class="bi bi-clock me-1"></i>Pending Challans</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <h3>{{ $stats['invoices'] ?? 0 }}</h3>
            <p><i class="bi bi-receipt me-1"></i>Total Invoices</p>
        </div>
    </div>
</div>

<!-- Total Invoiced Amount -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body text-center py-4">
                <p class="text-muted mb-2">Total Invoiced Amount</p>
                <div class="highlight-amount">
                    ₹ {{ number_format($stats['total_invoiced'] ?? 0, 2) }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">Quick Actions</h5>
                <div class="d-grid gap-2">
                    <a href="{{ route('parties.create') }}" class="btn btn-outline-primary">
                        <i class="bi bi-person-plus me-2"></i>Add New Party
                    </a>
                    <a href="{{ route('challans.create') }}" class="btn btn-outline-success">
                        <i class="bi bi-file-earmark-plus me-2"></i>Create Challan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clock-history me-2"></i>Recent Challans
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Number</th>
                            <th>Party</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(\App\Models\Challan::with('party')->where('company_id', $currentCompany->id ?? 0)->latest()->limit(5)->get() as $challan)
                        <tr>
                            <td><a href="{{ route('challans.show', $challan) }}">{{ $challan->challan_number }}</a></td>
                            <td>{{ $challan->party->name }}</td>
                            <td>₹{{ number_format($challan->subtotal, 2) }}</td>
                            <td>
                                @if($challan->is_invoiced)
                                    <span class="badge bg-success">Invoiced</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No challans yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-receipt me-2"></i>Recent Invoices
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Number</th>
                            <th>Party</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(\App\Models\Invoice::with('party')->where('company_id', $currentCompany->id ?? 0)->latest()->limit(5)->get() as $invoice)
                        <tr>
                            <td><a href="{{ route('invoices.show', $invoice) }}">{{ $invoice->invoice_number }}</a></td>
                            <td>{{ $invoice->party->name }}</td>
                            <td>₹{{ number_format($invoice->final_amount, 2) }}</td>
                            <td>{{ $invoice->invoice_date->format('d/m/Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No invoices yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
