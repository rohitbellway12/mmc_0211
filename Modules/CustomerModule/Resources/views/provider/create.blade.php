@extends('providermanagement::layouts.master')

@section('title', translate('Add_New_Customer'))

@section('content')
    <div class="main-content">
        <div class="container-fluid">
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
                    {{-- Left Column: Personal Information --}}
                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent border-bottom py-3">
                                <h4 class="card-title mb-0 d-flex align-items-center gap-2 fz-16">
                                    <span class="material-icons text-primary">person</span>
                                    {{ translate('Personal_Information') }}
                                </h4>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" name="first_name" id="first_name"
                                                   placeholder="{{ translate('First Name') }} *" required value="{{ old('first_name') }}">
                                            <label for="first_name">{{ translate('First_Name') }} <span class="text-danger">*</span></label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" name="last_name" id="last_name"
                                                   placeholder="{{ translate('Last Name') }} *" required value="{{ old('last_name') }}">
                                            <label for="last_name">{{ translate('Last_Name') }} <span class="text-danger">*</span></label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-floating">
                                            <input type="email" class="form-control" name="email" id="email"
                                                   placeholder="{{ translate('Email') }} *" required value="{{ old('email') }}">
                                            <label for="email">{{ translate('Email') }} <span class="text-danger">*</span></label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-floating">
                                            <input type="tel" class="form-control" name="phone" id="phone"
                                                   placeholder="{{ translate('Phone Number') }} *" required value="{{ old('phone') }}">
                                            <label for="phone">{{ translate('Phone') }} <span class="text-danger">*</span></label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-floating">
                                            <select class="form-select" name="gender" id="gender">
                                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>{{ translate('Male') }}</option>
                                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>{{ translate('Female') }}</option>
                                                <option value="others" {{ old('gender') == 'others' ? 'selected' : '' }}>{{ translate('Others') }}</option>
                                            </select>
                                            <label for="gender">{{ translate('Gender') }}</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-floating">
                                            <input type="date" class="form-control" name="date_of_birth" id="date_of_birth"
                                                   value="{{ old('date_of_birth') }}">
                                            <label for="date_of_birth">{{ translate('Date_of_Birth') }}</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-floating position-relative">
                                            <input type="password" class="form-control" name="password" id="password"
                                                   placeholder="{{ translate('Password') }} *" required minlength="6">
                                            <label for="password">{{ translate('Password') }} <span class="text-danger">*</span></label>
                                            <span class="material-icons position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer text-muted"
                                                  id="toggle_password" style="cursor: pointer;">visibility</span>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-floating position-relative">
                                            <input type="password" class="form-control" name="confirm_password" id="confirm_password"
                                                   placeholder="{{ translate('Confirm Password') }}" minlength="6">
                                            <label for="confirm_password">{{ translate('Confirm_Password') }}</label>
                                            <span class="material-icons position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer text-muted"
                                                  id="toggle_confirm_password" style="cursor: pointer;">visibility</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Address Information Card --}}
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent border-bottom py-3">
                                <h4 class="card-title mb-0 d-flex align-items-center gap-2 fz-16">
                                    <span class="material-icons text-primary">location_on</span>
                                    {{ translate('Customer_Address (Optional)') }}
                                </h4>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" name="house" id="house"
                                                   placeholder="{{ translate('House / Flat / Building') }}" value="{{ old('house') }}">
                                            <label for="house">{{ translate('House_/_Flat_/_Building') }}</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" name="floor" id="floor"
                                                   placeholder="{{ translate('Floor') }}" value="{{ old('floor') }}">
                                            <label for="floor">{{ translate('Floor') }}</label>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" name="street" id="street"
                                                   placeholder="{{ translate('Street / Road') }}" value="{{ old('street') }}">
                                            <label for="street">{{ translate('Street_/_Road') }}</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" name="city" id="city"
                                                   placeholder="{{ translate('City') }}" value="{{ old('city') }}">
                                            <label for="city">{{ translate('City') }}</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" name="zip_code" id="zip_code"
                                                   placeholder="{{ translate('Postcode / Zip Code') }}" value="{{ old('zip_code') }}">
                                            <label for="zip_code">{{ translate('Postcode_/_Zip_Code') }}</label>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" name="country" id="country"
                                                   placeholder="{{ translate('Country') }}" value="{{ old('country', 'United Kingdom') }}">
                                            <label for="country">{{ translate('Country') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Profile Image & Actions --}}
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent border-bottom py-3">
                                <h4 class="card-title mb-0 d-flex align-items-center gap-2 fz-16">
                                    <span class="material-icons text-primary">account_circle</span>
                                    {{ translate('Profile_Photo') }}
                                </h4>
                            </div>
                            <div class="card-body p-4 text-center">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="upload-file mb-3 position-relative" style="width: 140px; height: 140px; border-radius: 50%; overflow: hidden; border: 3px dashed #d1d5db; display: flex; align-items: center; justify-content: center; background: #f9fafb;">
                                        <input type="file" class="upload-file__input position-absolute w-100 h-100 opacity-0 cursor-pointer"
                                               name="profile_image" id="profile_image" accept="image/*" style="cursor: pointer; z-index: 2;">
                                        <img id="profile_preview" src="{{ asset('public/assets/admin-module/img/media/upload-file.png') }}"
                                             alt="Preview" style="max-width: 100%; max-height: 100%; object-fit: cover;">
                                    </div>
                                    <p class="text-muted fz-12 mb-0">
                                        {{ translate('JPG, PNG, GIF format. Maximum size 2MB.') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Submission Card --}}
                        <div class="card border-0 shadow-sm mb-4 bg-light">
                            <div class="card-body p-4">
                                <h5 class="fw-bold text-dark mb-2">{{ translate('Partner Customer Onboarding') }}</h5>
                                <p class="text-muted fz-13 mb-4">
                                    {{ translate('Once created, the customer will receive account credentials via email. You can directly create customized estimates or vehicle bookings for them.') }}
                                </p>

                                <div class="d-grid gap-3">
                                    <button type="submit" name="action" value="save" class="btn btn--primary py-2 d-flex align-items-center justify-content-center gap-2">
                                        <span class="material-icons">save</span>
                                        {{ translate('Save_Customer') }}
                                    </button>

                                    <button type="submit" name="action" value="create_estimate" class="btn btn-outline-primary py-2 d-flex align-items-center justify-content-center gap-2">
                                        <span class="material-icons">request_quote</span>
                                        {{ translate('Save_&_Create_Quotation') }}
                                    </button>

                                    <a href="{{ route('provider.customer.list') }}" class="btn btn-light py-2 text-muted">
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
            // Profile image preview
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
            $('#toggle_password').on('click', function() {
                let $pass = $('#password');
                let type = $pass.attr('type') === 'password' ? 'text' : 'password';
                $pass.attr('type', type);
                $(this).text(type === 'password' ? 'visibility' : 'visibility_off');
            });

            $('#toggle_confirm_password').on('click', function() {
                let $pass = $('#confirm_password');
                let type = $pass.attr('type') === 'password' ? 'text' : 'password';
                $pass.attr('type', type);
                $(this).text(type === 'password' ? 'visibility' : 'visibility_off');
            });
        });
    </script>
@endpush
