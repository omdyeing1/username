@extends('layouts.main')

@section('title', 'Companies')

@section('content')
<div class="page-header">
    <h1>Companies</h1>
    <a href="{{ route('companies.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Add Company
    </a>
</div>

<!-- Companies List -->
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Company Name</th>
                    <th>GST Number</th>
                    <th>State Code</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($companies as $company)
                <tr>
                    <td>
                        <strong>{{ $company->name }}</strong>
                    </td>
                    <td>{{ $company->gst_number ?: '-' }}</td>
                    <td>{{ $company->state_code ?: '-' }}</td>
                    <td>{{ Str::limit($company->address, 50) }}</td>
                    <td>
                        @if($company->is_default)
                            <span class="badge bg-success">Default</span>
                        @else
                            <span class="badge bg-secondary">-</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('companies.show', $company) }}" class="btn btn-sm btn-outline-info" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('companies.edit', $company) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('companies.destroy', $company) }}" method="POST" class="d-inline" 
                              onsubmit="return confirm('Are you sure you want to delete this company?');">
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
                    <td colspan="6" class="text-center py-4 text-muted">
                        <i class="bi bi-building display-4 d-block mb-2"></i>
                        No companies found. <a href="{{ route('companies.create') }}">Add your first company</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
