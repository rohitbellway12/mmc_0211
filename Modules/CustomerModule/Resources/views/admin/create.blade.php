@extends('adminmodule::layouts.master')

@section('title',translate('customer_add'))

@push('css_or_js')
    <link rel="stylesheet" href="{{asset('public/assets/admin-module/plugins/swiper/swiper-bundle.min.css')}}">
@endpush

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-wrap mb-3">
                        <h2 class="page-title">{{translate('add_new_customer')}}</h2>
                    </div>

                    <div class="card">
                        <div class="card-body p-30">
                            <form action="{{route('admin.customer.store')}}" method="post" enctype="multipart/form-data"
                                  id="customer-add-form">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-30">
                                            <div class="form-floating form-floating__icon">
                                                <input type="text" class="form-control" name="first_name"
                                                       placeholder="{{translate('first_name')}} *"
                                                       required="" value="{{old('first_name')}}">
                                                <label>{{translate('first_name')}} *</label>
                                                <span class="material-icons">account_circle</span>
                                            </div>
                                        </div>

                                        <div class="mb-30">
                                            <div class="form-floating form-floating__icon">
                                                <input type="text" class="form-control" name="last_name"
                                                       placeholder="{{translate('last_name')}} *"
                                                       required="" value="{{old('last_name')}}">
                                                <label>{{translate('last_name')}} *</label>
                                                <span class="material-icons">account_circle</span>
                                            </div>
                                        </div>

                                        <div class="mb-30">
                                            <div class="form-floating form-floating__icon">
                                                <input type="email" class="form-control" name="email"
                                                       placeholder="{{translate('ex: abc@email.com')}} *"
                                                       required="" value="{{old('email')}}">
                                                <label>{{translate('email')}} *</label>
                                                <span class="material-icons">mail</span>
                                            </div>
                                        </div>

                                        <div class="mb-30">
                                            <div class="form-floating form-floting-fix">
                                                <label for="phone">{{translate('Phone')}}</label>
                                                <input type="tel"
                                                       class="form-control"
                                                       name="phone"
                                                       placeholder="{{translate('phone')}} *"
                                                       id="phone"
                                                       required="" value="{{old('phone')}}">
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-lg-6">
                                        <div class="d-flex flex-column align-items-center gap-3">
                                            <p class="mb-0">{{translate('profile_image')}}</p>
                                            <div class="d-flex flex-column align-items-center">
                                                <div class="upload-file">
                                                    <input type="file" class="upload-file__input" name="profile_image" accept=".{{ implode(',.', array_column(IMAGEEXTENSION, 'key')) }}, |image/*">
                                                    <div class="upload-file__img">
                                                        <img
                                                            src="{{asset('public/assets/admin-module')}}/img/media/upload-file.png"
                                                            alt="">
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="opacity-75 max-w220 mx-auto text-center">
                                                {{translate('Image format - jpg, png,jpeg,gif Image Size -maximum size 2 MB Image Ratio - 1:1')}}
                                            </p>
                                        </div>
                                    </div>

                                    @if(isset($documentTypes) && $documentTypes->count() > 0)
                                        <div class="col-12 mt-4">
                                            <div class="d-flex align-items-center justify-content-between mb-3 border-top pt-4">
                                                <h4 class="mb-0 text-capitalize d-flex align-items-center gap-2">
                                                    <span class="material-icons text-primary">folder_shared</span>
                                                    {{translate('Customer Documents')}}
                                                </h4>
                                                <a href="{{route('admin.customer.document-setup.index')}}" class="btn btn-sm btn--secondary" target="_blank">
                                                    <span class="material-icons fs-14">settings</span>
                                                    {{translate('Document Setup')}}
                                                </a>
                                            </div>

                                            <div class="row g-3">
                                                @foreach($documentTypes as $docType)
                                                    <div class="col-md-6 col-lg-4">
                                                        <div class="border rounded p-3 h-100 bg-light">
                                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                                <label class="form-label fw-bold mb-0 text-dark">
                                                                    {{$docType->title}}
                                                                    @if($docType->is_required)
                                                                        <span class="text-danger">* ({{translate('Required')}})</span>
                                                                    @endif
                                                                </label>
                                                            </div>
                                                            @if(!empty($docType->description))
                                                                <small class="text-muted d-block mb-2">{{$docType->description}}</small>
                                                            @endif
                                                            <div class="mt-2">
                                                                <input type="file" 
                                                                       class="form-control" 
                                                                       name="documents[{{$docType->id}}]" 
                                                                       accept=".jpeg,.jpg,.png,.webp,.pdf,.doc,.docx"
                                                                       {{$docType->is_required ? 'required' : ''}}>
                                                                <div class="form-text fs-12 text-muted mt-1">
                                                                    {{translate('Formats: JPG, PNG, WEBP, PDF, DOC (Max 15MB)')}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-12">
                                        <div class="d-flex justify-content-end gap-20 mt-30">
                                            <button class="btn btn--secondary"
                                                    type="reset">{{translate('reset')}}</button>
                                            <button class="btn btn--primary" type="submit">
                                                {{translate('submit')}}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
