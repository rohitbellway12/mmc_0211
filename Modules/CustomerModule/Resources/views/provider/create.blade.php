@extends('providermanagement::layouts.master')

@section('title', translate('Add_New_Customer'))

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
    .btn-brand-outline {
        color: #0461A5;
        border: 1.5px solid #0461A5;
        font-weight: 600;
        border-radius: 8px;
        background: transparent;
        transition: all 0.2s ease;
    }
    .btn-brand-outline:hover {
        background-color: #0461A5;
        color: #ffffff;
        box-shadow: 0 4px 8px rgba(4, 97, 165, 0.2);
    }
    /* Fix intl-tel-input full width */
    .iti {
        width: 100% !important;
    }
    .iti input.form-control {
        width: 100% !important;
    }
</style>
@endpush

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            {{-- Top Header --}}
            <div class="page-title-wrap d-flex justify-content-between flex-wrap align-items-center gap-3 mb-4">
                <div>
                    <h2 class="page-title mb-1">{{ translate('Add_New_Customer') }}</h2>
                    <p class="text-muted fz-14 mb-0">{{ translate('Create a verified customer profile to generate quotations, bookings and track client history.') }}</p>
                </div>
                <div>
                    <a href="{{ route('provider.customer.list') }}" class="btn btn--secondary d-flex align-items-center gap-2">
                        <span class="material-icons">arrow_back</span>
                        {{ translate('Back_to_Customer_List') }}
                    </a>
                </div>
            </div>

            <form action="{{ route('provider.customer.store') }}" method="POST" enctype="multipart/form-data" id="customer_add_form">
                @csrf

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
                                                   placeholder="{{ translate('e.g. John') }}" required value="{{ old('first_name') }}">
                                        </div>
                                    </div>

                                    {{-- Last Name --}}
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label" for="last_name">
                                                {{ translate('Last_Name') }} <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" name="last_name" id="last_name"
                                                   placeholder="{{ translate('e.g. Doe') }}" required value="{{ old('last_name') }}">
                                        </div>
                                    </div>

                                    {{-- Email --}}
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label" for="email">
                                                {{ translate('Email') }} <span class="text-danger">*</span>
                                            </label>
                                            <input type="email" class="form-control" name="email" id="email"
                                                   placeholder="{{ translate('e.g. john@example.com') }}" required value="{{ old('email') }}">
                                        </div>
                                    </div>

                                    {{-- Phone Number --}}
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label" for="phone">
                                                {{ translate('Phone_Number') }} <span class="text-danger">*</span>
                                            </label>
                                            <input type="tel" class="form-control" name="phone" id="phone"
                                                   placeholder="{{ translate('Enter mobile number') }}" required value="{{ old('phone') }}">
                                        </div>
                                    </div>

                                    {{-- Gender --}}
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label" for="gender">
                                                {{ translate('Gender') }}
                                            </label>
                                            <select class="form-select" name="gender" id="gender">
                                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>{{ translate('Male') }}</option>
                                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>{{ translate('Female') }}</option>
                                                <option value="others" {{ old('gender') == 'others' ? 'selected' : '' }}>{{ translate('Others') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Date of Birth --}}
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label" for="date_of_birth">
                                                {{ translate('Date_of_Birth') }}
                                            </label>
                                            <input type="date" class="form-control" name="date_of_birth" id="date_of_birth"
                                                   value="{{ old('date_of_birth') }}">
                                        </div>
                                    </div>

                                    {{-- Password --}}
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label" for="password">
                                                {{ translate('Password') }} <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="password" class="form-control border-end-0" name="password" id="password"
                                                       placeholder="{{ translate('Min 6 characters') }}" required minlength="6">
                                                <span class="input-group-text bg-white border-start-0 cursor-pointer text-muted toggle-password" data-target="#password" style="cursor: pointer;">
                                                    <span class="material-icons fz-18">visibility</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Confirm Password --}}
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label" for="confirm_password">
                                                {{ translate('Confirm_Password') }}
                                            </label>
                                            <div class="input-group">
                                                <input type="password" class="form-control border-end-0" name="confirm_password" id="confirm_password"
                                                       placeholder="{{ translate('Repeat password') }}" minlength="6">
                                                <span class="input-group-text bg-white border-start-0 cursor-pointer text-muted toggle-password" data-target="#confirm_password" style="cursor: pointer;">
                                                    <span class="material-icons fz-18">visibility</span>
                                                </span>
                                            </div>
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
                                    {{ translate('Customer_Address (Optional)') }}
                                </h4>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    {{-- Street / Road --}}
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label" for="street">
                                                {{ translate('Street_Address_/_Road') }}
                                            </label>
                                            <input type="text" class="form-control" name="street" id="street"
                                                   placeholder="{{ translate('e.g. 10 Downing Street or Oxford Road') }}" value="{{ old('street') }}">
                                        </div>
                                    </div>

                                    {{-- House / Flat / Building --}}
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label" for="house">
                                                {{ translate('House_/_Flat_/_Building') }}
                                            </label>
                                            <input type="text" class="form-control" name="house" id="house"
                                                   placeholder="{{ translate('e.g. Flat 4B, King Towers') }}" value="{{ old('house') }}">
                                        </div>
                                    </div>

                                    {{-- Floor / Unit --}}
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label" for="floor">
                                                {{ translate('Floor_/_Unit') }}
                                            </label>
                                            <input type="text" class="form-control" name="floor" id="floor"
                                                   placeholder="{{ translate('e.g. 2nd Floor') }}" value="{{ old('floor') }}">
                                        </div>
                                    </div>

                                    {{-- City / Town --}}
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label" for="city">
                                                {{ translate('City_/_Town') }}
                                            </label>
                                            <input type="text" class="form-control" name="city" id="city"
                                                   placeholder="{{ translate('e.g. London') }}" value="{{ old('city') }}">
                                        </div>
                                    </div>

                                    {{-- Postcode / Zip Code --}}
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label" for="zip_code">
                                                {{ translate('Postcode_/_Zip_Code') }}
                                            </label>
                                            <input type="text" class="form-control" name="zip_code" id="zip_code"
                                                   placeholder="{{ translate('e.g. SW1A 2AA') }}" value="{{ old('zip_code') }}">
                                        </div>
                                    </div>

                                    {{-- Country --}}
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label" for="country">
                                                {{ translate('Country') }}
                                            </label>
                                            <input type="text" class="form-control" name="country" id="country"
                                                   placeholder="{{ translate('Country') }}" value="{{ old('country', 'United Kingdom') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                                    <img id="profile_preview" src="{{ asset('public/assets/admin-module/img/media/upload-file.png') }}" alt="Preview">
                                    <div class="avatar-upload-overlay">
                                        <span class="material-icons fz-16">photo_camera</span>
                                        <span>{{ translate('Upload') }}</span>
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
                                <h5 class="fw-bold text-dark mb-2 fz-15">{{ translate('Partner Customer Onboarding') }}</h5>
                                <p class="text-muted fz-13 mb-4">
                                    {{ translate('Once created, the customer will receive account credentials via email. You can directly create customized estimates or vehicle bookings for them.') }}
                                </p>

                                <div class="d-grid gap-3">
                                    <button type="submit" name="action" value="save" class="btn btn-brand-primary py-2 d-flex align-items-center justify-content-center gap-2">
                                        <span class="material-icons fz-18">person_add</span>
                                        <span>{{ translate('Save_Customer') }}</span>
                                    </button>

                                    <button type="submit" name="action" value="create_estimate" class="btn btn-brand-outline py-2 d-flex align-items-center justify-content-center gap-2">
                                        <span class="material-icons fz-18">request_quote</span>
                                        <span>{{ translate('Save_&_Create_Quotation') }}</span>
                                    </button>

                                    <a href="{{ route('provider.customer.list') }}" class="btn btn-light py-2 text-muted fw-medium text-center">
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

            // Password visibility toggle
            $('.toggle-password').on('click', function() {
                let targetInput = $($(this).data('target'));
                let icon = $(this).find('.material-icons');
                if (targetInput.attr('type') === 'password') {
                    targetInput.attr('type', 'text');
                    icon.text('visibility_off');
                } else {
                    targetInput.attr('type', 'password');
                    icon.text('visibility');
                }
            });
        });
    </script>
@endpush
