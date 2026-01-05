@extends('layouts.main')

@section('title', 'Parties')

@section('content')
<div class="page-header">
    <h1>Parties</h1>
    <a href="{{ route('parties.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Add Party
    </a>
</div>

<!-- Search & Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('parties.index') }}" class="row g-3">
            <div class="col-md-8">
                <input type="text" name="search" class="form-control" 
                       placeholder="Search by name, contact or GST number..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search me-1"></i>Search
                </button>
                <a href="{{ route('parties.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Parties List -->
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Party Name</th>
                    <th>Contact</th>
                    <th>GST Number</th>
                    <th>Address</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($parties as $party)
                <tr>
                    <td>
                        <strong>{{ $party->name }}</strong>
                    </td>
                    <td>{{ $party->contact_number }}</td>
                    <td>{{ $party->gst_number ?: '-' }}</td>
                    <td>{{ Str::limit($party->address, 50) }}</td>
                    <td>
                        <a href="{{ route('parties.edit', $party) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('parties.destroy', $party) }}" method="POST" class="d-inline" 
                              onsubmit="return confirm('Are you sure you want to delete this party?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        <i class="bi bi-people display-4 d-block mb-2"></i>
                        No parties found. <a href="{{ route('parties.create') }}">Add your first party</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($parties->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $parties->links() }}
</div>
@endif
@endsection
