@extends('adminmodule::layouts.master')

@section('title', translate('Edit Customer Required Document'))

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-title-wrap mb-4">
                <h2 class="page-title">{{translate('Edit Customer Document')}}</h2>
            </div>

            <div class="card">
                <div class="card-body p-30">
                    <form action="{{route('admin.customer.document-setup.update', [$documentType->id])}}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="title">{{translate('Document Title / Name')}} <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" class="form-control"
                                       value="{{old('title', $documentType->title)}}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="description">{{translate('Description / Instructions (Optional)')}}</label>
                                <input type="text" name="description" id="description" class="form-control"
                                       value="{{old('description', $documentType->description)}}">
                            </div>
                            <div class="col-12 pt-2">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="is_required" id="is_required" value="1"
                                        {{old('is_required', $documentType->is_required) ? 'checked' : ''}}>
                                    <label class="form-check-label fw-semibold" for="is_required">
                                        {{translate('Mark as Mandatory / Required')}}
                                    </label>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn--primary">
                                        <span class="material-icons">save</span>
                                        {{translate('Update Document')}}
                                    </button>
                                    <a href="{{route('admin.customer.document-setup.index')}}" class="btn btn--secondary">
                                        {{translate('Cancel')}}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

