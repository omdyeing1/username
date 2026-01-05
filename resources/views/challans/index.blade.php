@extends('layouts.main')

@section('title', 'Challans')

@section('content')
<div class="page-header">
    <h1>Challans</h1>
    <a href="{{ route('challans.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Create Challan
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('challans.index') }}" class="row g-3">
            <div class="col-md-3">
                <select name="party_id" class="form-select">
                    <option value="">All Parties</option>
                    @foreach($parties as $party)
                        <option value="{{ $party->id }}" {{ request('party_id') == $party->id ? 'selected' : '' }}>
                            {{ $party->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}" placeholder="From Date">
            </div>
            <div class="col-md-2">
                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}" placeholder="To Date">
            </div>
            <div class="col-md-2">
                <select name="invoiced" class="form-select">
                    <option value="">All Status</option>
                    <option value="no" {{ request('invoiced') == 'no' ? 'selected' : '' }}>Pending</option>
                    <option value="yes" {{ request('invoiced') == 'yes' ? 'selected' : '' }}>Invoiced</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary me-2"><i class="bi bi-filter me-1"></i>Filter</button>
                <a href="{{ route('challans.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Challans List -->
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Challan No.</th>
                    <th>Date</th>
                    <th>Party</th>
                    <th>Items</th>
                    <th class="text-end">Subtotal</th>
                    <th>Status</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($challans as $challan)
                <tr>
                    <td><a href="{{ route('challans.show', $challan) }}"><strong>{{ $challan->challan_number }}</strong></a></td>
                    <td>{{ $challan->challan_date->format('d/m/Y') }}</td>
                    <td>{{ $challan->party->name }}</td>
                    <td>{{ $challan->items_count ?? $challan->items->count() }} items</td>
                    <td class="text-end"><strong>₹{{ number_format($challan->subtotal, 2) }}</strong></td>
                    <td>
                        @if($challan->is_invoiced)
                            <span class="badge bg-success">Invoiced</span>
                        @else
                            <span class="badge bg-warning text-dark">Pending</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('challans.show', $challan) }}" class="btn btn-sm btn-outline-info" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('challans.edit', $challan) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @if(!$challan->is_invoiced)
                        <form action="{{ route('challans.destroy', $challan) }}" method="POST" class="d-inline" 
                              onsubmit="return confirm('Are you sure you want to delete this challan?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        <i class="bi bi-file-text display-4 d-block mb-2"></i>
                        No challans found. <a href="{{ route('challans.create') }}">Create your first challan</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($challans->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $challans->links() }}
</div>
@endif
@endsection
