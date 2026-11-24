@extends('providermanagement::layouts.master')

@section('title', translate('Edit_Customer'))

@push('css_or_js')
<style>
    .customer-form-card {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #ffffff;
    }
    .customer-form-card .card-header {
        background-color: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        padding: 1rem 1.5rem;
    }
    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 6px;
        display: block;
    }
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        padding: 0.65rem 0.9rem;
        font-size: 14px;
        color: #0f172a;
        background-color: #ffffff;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .form-control::placeholder {
        color: #94a3b8;
        font-size: 13px;
    }
    .form-control:focus, .form-select:focus {
        border-color: #0461A5;
        box-shadow: 0 0 0 3px rgba(4, 97, 165, 0.15);
        background-color: #ffffff;
    }
    .avatar-upload-box {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        position: relative;
        overflow: hidden;
        border: 3px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        background: #f8fafc;
        margin: 0 auto 1rem auto;
    }
    .avatar-upload-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .avatar-upload-box .avatar-upload-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(15, 23, 42, 0.72);
        color: #ffffff;
        text-align: center;
        padding: 6px 0;
        font-size: 11px;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        cursor: pointer;
        transition: background 0.2s ease;
    }
    .avatar-upload-box:hover .avatar-upload-overlay {
        background: rgba(15, 23, 42, 0.88);
    }
    .btn-brand-primary {
        background-color: #0461A5;
        border-color: #0461A5;
        color: #ffffff;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    .btn-brand-primary:hover {
        background-color: #034f86;
        border-color: #034f86;
        color: #ffffff;
        box-shadow: 0 4px 8px rgba(4, 97, 165, 0.25);
    }
</style>
@endpush

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            {{-- Top Header --}}
            <div class="page-title-wrap d-flex justify-content-between flex-wrap align-items-center gap-3 mb-4">
                <div>
                    <h2 class="page-title mb-1">{{ translate('Edit_Customer') }}</h2>
                    <p class="text-muted fz-14 mb-0">{{ translate('Update client profile information, contact and address details.') }}</p>
                </div>
                <div>
                    <a href="{{ route('provider.customer.detail', [$customer->id]) }}" class="btn btn--secondary d-flex align-items-center gap-2">
                        <span class="material-icons">arrow_back</span>
                        {{ translate('Back_to_Details') }}
                    </a>
                </div>
            </div>

            <form action="{{ route('provider.customer.update', [$customer->id]) }}" method="POST" enctype="multipart/form-data" id="customer_edit_form">
                @csrf
                @method('PUT')

                @php($latestAddress = $customer->addresses->first())

                <div class="row g-4">
                    {{-- Left Column: Personal Information & Address --}}
                    <div class="col-lg-8">
                        {{-- Personal Information Card --}}
                        <div class="card customer-form-card shadow-sm mb-4">
                            <div class="card-header py-3">
                                <h4 class="card-title mb-0 d-flex align-items-center gap-2 fz-16 fw-bold text-dark">
                                    <span class="material-icons text-primary">person</span>
                                    {{ translate('Personal_Information') }}
                                </h4>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    {{-- First Name --}}
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label" for="first_name">
                                                {{ translate('First_Name') }} <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" name="first_name" id="first_name"
                                                   placeholder="{{ translate('e.g. John') }}" value="{{ old('first_name', $customer->first_name) }}" required>
                                        </div>
                                    </div>

                                    {{-- Last Name --}}
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label" for="last_name">
                                                {{ translate('Last_Name') }} <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" name="last_name" id="last_name"
                                                   placeholder="{{ translate('e.g. Doe') }}" value="{{ old('last_name', $customer->last_name) }}" required>
                                        </div>
                                    </div>

                                    {{-- Email --}}
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label" for="email">
                                                {{ translate('Email_Address') }} <span class="text-danger">*</span>
                                            </label>
                                            <input type="email" class="form-control" name="email" id="email"
                                                   placeholder="{{ translate('e.g. client@example.com') }}" value="{{ old('email', $customer->email) }}" required>
                                        </div>
                                    </div>

                                    {{-- Phone Number --}}
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label" for="phone">
                                                {{ translate('Phone_Number') }} <span class="text-danger">*</span>
                                            </label>
                                            <input type="tel" class="form-control" name="phone" id="phone"
                                                   placeholder="{{ translate('e.g. +44 7911 123456') }}" value="{{ old('phone', $customer->phone) }}" required>
                                        </div>
                                    </div>

                                    {{-- Gender --}}
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label" for="gender">
                                                {{ translate('Gender') }}
                                            </label>
                                            <select class="form-select" name="gender" id="gender">
                                                <option value="male" {{ old('gender', $customer->gender) == 'male' ? 'selected' : '' }}>{{ translate('Male') }}</option>
                                                <option value="female" {{ old('gender', $customer->gender) == 'female' ? 'selected' : '' }}>{{ translate('Female') }}</option>
                                                <option value="others" {{ old('gender', $customer->gender) == 'others' ? 'selected' : '' }}>{{ translate('Others') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Address Information Card --}}
                        <div class="card customer-form-card shadow-sm mb-4">
                            <div class="card-header py-3">
                                <h4 class="card-title mb-0 d-flex align-items-center gap-2 fz-16 fw-bold text-dark">
                                    <span class="material-icons text-primary">location_on</span>
                                    {{ translate('Customer_Address') }}
                                </h4>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    {{-- Full Address / Street --}}
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label" for="address">
                                                {{ translate('Street_Address') }}
                                            </label>
                                            <input type="text" class="form-control" name="address" id="address"
                                                   placeholder="{{ translate('e.g. 10 Downing Street') }}"
                                                   value="{{ old('address', $latestAddress?->address) }}">
                                        </div>
                                    </div>

                                    {{-- House / Flat / Building --}}
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label" for="house">
                                                {{ translate('House_/_Flat_/_Building') }}
                                            </label>
                                            <input type="text" class="form-control" name="house" id="house"
                                                   placeholder="{{ translate('e.g. Flat 4B, King Towers') }}"
                                                   value="{{ old('house', $latestAddress?->house) }}">
                                        </div>
                                    </div>

                                    {{-- Floor / Unit --}}
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label" for="floor">
                                                {{ translate('Floor_/_Unit') }}
                                            </label>
                                            <input type="text" class="form-control" name="floor" id="floor"
                                                   placeholder="{{ translate('e.g. 2nd Floor') }}"
                                                   value="{{ old('floor', $latestAddress?->floor) }}">
                                        </div>
                                    </div>

                                    {{-- City / Town --}}
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label" for="city">
                                                {{ translate('City_/_Town') }}
                                            </label>
                                            <input type="text" class="form-control" name="city" id="city"
                                                   placeholder="{{ translate('e.g. London') }}"
                                                   value="{{ old('city', $latestAddress?->city) }}">
                                        </div>
                                    </div>

                                    {{-- Postcode / Zip Code --}}
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label" for="zip_code">
                                                {{ translate('Postcode_/_Zip_Code') }}
                                            </label>
                                            <input type="text" class="form-control" name="zip_code" id="zip_code"
                                                   placeholder="{{ translate('e.g. SW1A 2AA') }}"
                                                   value="{{ old('zip_code', $latestAddress?->zip_code) }}">
                                        </div>
                                    </div>

                                    {{-- Country --}}
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label" for="country">
                                                {{ translate('Country') }}
                                            </label>
                                            <input type="text" class="form-control" name="country" id="country"
                                                   placeholder="{{ translate('Country') }}"
                                                   value="{{ old('country', $latestAddress?->country ?: 'United Kingdom') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Customer Uploaded Documents Card --}}
                        @if(isset($documentTypes) && $documentTypes->count() > 0)
                            <div class="card customer-form-card shadow-sm mb-4">
                                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                                    <h4 class="card-title mb-0 d-flex align-items-center gap-2 fz-16 fw-bold text-dark">
                                        <span class="material-icons text-primary">folder_shared</span>
                                        {{ translate('Customer_Documents') }}
                                    </h4>
                                    <span class="badge bg-light text-muted fz-12">
                                        {{ $documentTypes->count() }} {{ translate('Documents Setup') }}
                                    </span>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-3">
                                        @foreach($documentTypes as $docType)
                                            @php($existingDoc = $customer->documents ? $customer->documents->firstWhere('document_type_id', $docType->id) : null)
                                            <div class="col-md-6">
                                                <div class="border rounded p-3 h-100 bg-light">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <label class="form-label fw-bold mb-0 text-dark">
                                                            {{ $docType->title }}
                                                            @if($docType->is_required)
                                                                <span class="text-danger">* ({{ translate('Required') }})</span>
                                                            @endif
                                                        </label>
                                                        @if($existingDoc)
                                                            <span class="badge bg-success text-white px-2 py-1 fz-11">{{ translate('Uploaded') }}</span>
                                                        @else
                                                            <span class="badge bg-warning text-dark px-2 py-1 fz-11">{{ translate('Pending') }}</span>
                                                        @endif
                                                    </div>
                                                    @if(!empty($docType->description))
                                                        <small class="text-muted d-block mb-2">{{ $docType->description }}</small>
                                                    @endif

                                                    @if($existingDoc)
                                                        <div class="mb-2 p-2 bg-white rounded border d-flex align-items-center justify-content-between">
                                                            <div class="d-flex align-items-center gap-2 text-truncate" style="max-width: 70%;">
                                                                <span class="material-icons text-primary fz-18">description</span>
                                                                <span class="fz-12 text-truncate" title="{{ $existingDoc->file_name }}">{{ $existingDoc->file_name ?: translate('Uploaded File') }}</span>
                                                            </div>
                                                            <a href="{{ $existingDoc->file_path_full_path }}" target="_blank" class="btn btn-outline-primary btn-sm py-1 px-2 fz-12 d-flex align-items-center gap-1">
                                                                <span class="material-icons fz-14">visibility</span>
                                                                <span>{{ translate('View') }}</span>
                                                            </a>
                                                        </div>
                                                    @endif

                                                    <div class="mt-2">
                                                        <label class="form-label fz-12 text-muted mb-1">
                                                            {{ $existingDoc ? translate('Replace / Upload New File') : translate('Upload Document') }}
                                                        </label>
                                                        <input type="file" 
                                                               class="form-control" 
                                                               name="documents[{{ $docType->id }}]" 
                                                               accept=".jpeg,.jpg,.png,.webp,.pdf,.doc,.docx"
                                                               {{ ($docType->is_required && !$existingDoc) ? 'required' : '' }}>
                                                        <div class="form-text fs-12 text-muted mt-1">
                                                            {{ translate('Formats: JPG, PNG, WEBP, PDF, DOC (Max 15MB)') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Right Column: Profile Image & Actions --}}
                    <div class="col-lg-4">
                        {{-- Profile Photo Upload Card --}}
                        <div class="card customer-form-card shadow-sm mb-4">
                            <div class="card-header py-3">
                                <h4 class="card-title mb-0 d-flex align-items-center gap-2 fz-16 fw-bold text-dark">
                                    <span class="material-icons text-primary">account_circle</span>
                                    {{ translate('Profile_Photo') }}
                                </h4>
                            </div>
                            <div class="card-body p-4 text-center">
                                <div class="avatar-upload-box">
                                    <img id="profile_preview" src="{{ $customer->profile_image_full_path ?: asset('public/assets/admin-module/img/media/upload-file.png') }}" alt="Preview">
                                    <div class="avatar-upload-overlay">
                                        <span class="material-icons fz-16">photo_camera</span>
                                        <span>{{ translate('Change') }}</span>
                                    </div>
                                    <input type="file" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer"
                                           name="profile_image" id="profile_image" accept="image/*" style="cursor: pointer; z-index: 5;">
                                </div>
                                <p class="text-muted fz-12 mb-0">
                                    {{ translate('JPG, PNG, GIF format. Max size 2MB.') }}
                                </p>
                            </div>
                        </div>

                        {{-- Action Buttons Card --}}
                        <div class="card customer-form-card shadow-sm mb-4">
                            <div class="card-body p-4">
                                <div class="d-grid gap-3">
                                    <button type="submit" class="btn btn-brand-primary py-2 d-flex align-items-center justify-content-center gap-2">
                                        <span class="material-icons fz-18">check_circle</span>
                                        <span>{{ translate('Update_Customer') }}</span>
                                    </button>

                                    <a href="{{ route('provider.customer.detail', [$customer->id]) }}" class="btn btn-light py-2 text-muted fw-medium text-center">
                                        {{ translate('Cancel') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            // Profile image live preview
            $('#profile_image').on('change', function(e) {
                if (e.target.files && e.target.files[0]) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        $('#profile_preview').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(e.target.files[0]);
                }
            });
        });
    </script>
@endpush
