@extends('layouts.main')

@section('title', 'Edit Party')

@section('content')
<div class="page-header">
    <h1>Edit Party</h1>
    <a href="{{ route('parties.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to List
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('parties.update', $party) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Party Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name', $party->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="contact_number" class="form-label">Contact Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('contact_number') is-invalid @enderror" 
                           id="contact_number" name="contact_number" value="{{ old('contact_number', $party->contact_number) }}" required>
                    @error('contact_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                <textarea class="form-control @error('address') is-invalid @enderror" 
                          id="address" name="address" rows="3" required>{{ old('address', $party->address) }}</textarea>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="gst_number" class="form-label">GST Number <span class="text-muted">(Optional)</span></label>
                <input type="text" class="form-control @error('gst_number') is-invalid @enderror" 
                       id="gst_number" name="gst_number" value="{{ old('gst_number', $party->gst_number) }}" 
                       placeholder="e.g., 27AABCU9603R1ZX" maxlength="15">
                @error('gst_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('parties.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>Update Party
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
