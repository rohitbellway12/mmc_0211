@extends('adminmodule::layouts.master')

@section('title', translate('Customer Required Documents Setup'))

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-title-wrap mb-4">
                <h2 class="page-title">{{translate('Customer Required Documents')}}</h2>
                <p class="text-muted fs-14">{{translate('Set up the documents that customers should provide in their profile / booking.')}}</p>
            </div>

            {{-- Create Document Card --}}
            <div class="card mb-4">
                <div class="card-body p-30">
                    <form action="{{route('admin.customer.document-setup.store')}}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="title">{{translate('Document Title / Name')}} <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" class="form-control"
                                       placeholder="{{translate('e.g. Driving License, National ID, Address Proof')}}"
                                       value="{{old('title')}}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="description">{{translate('Description / Instructions (Optional)')}}</label>
                                <input type="text" name="description" id="description" class="form-control"
                                       placeholder="{{translate('e.g. Upload a clear photo of front and back')}}"
                                       value="{{old('description')}}">
                            </div>
                            <div class="col-12 d-flex align-items-center justify-content-between pt-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_required" id="is_required" value="1" {{old('is_required') ? 'checked' : ''}}>
                                    <label class="form-check-label fw-semibold" for="is_required">
                                        {{translate('Mark as Mandatory / Required')}}
                                    </label>
                                </div>
                                <button type="submit" class="btn btn--primary">
                                    <span class="material-icons">add</span>
                                    {{translate('Add Document')}}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- List Card --}}
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center">
                        <h4 class="mb-0 d-flex align-items-center gap-2">
                            <span>{{translate('Configured Documents')}}</span>
                            <span class="badge bg-soft-primary text-primary rounded-pill px-2">{{$documentTypes->total()}}</span>
                        </h4>
                        <form action="{{url()->current()}}" method="GET" class="search-form-wrap">
                            <div class="input-group search-form">
                                <input type="search" class="form-control" name="search"
                                       placeholder="{{translate('Search documents...')}}"
                                       value="{{$search}}">
                                <button type="submit" class="btn btn--primary">
                                    <span class="material-icons">search</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card-body p-0 pt-3">
                    <div class="table-responsive">
                        <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>{{translate('Document Title')}}</th>
                                <th>{{translate('Description')}}</th>
                                <th class="text-center">{{translate('Type')}}</th>
                                <th class="text-center">{{translate('Status')}}</th>
                                <th class="text-center">{{translate('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($documentTypes as $key => $doc)
                                <tr>
                                    <td>{{$documentTypes->firstItem() + $key}}</td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{$doc->title}}</div>
                                    </td>
                                    <td>
                                        <div class="text-muted text-wrap" style="max-width: 300px;">
                                            {{$doc->description ?: '—'}}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($doc->is_required)
                                            <span class="badge bg-danger text-white">{{translate('Required')}}</span>
                                        @else
                                            <span class="badge bg-light text-secondary border">{{translate('Optional')}}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{route('admin.customer.document-setup.status-update', [$doc->id])}}"
                                           class="btn btn-sm {{$doc->is_active ? 'btn-soft-success text-success' : 'btn-soft-danger text-danger'}} px-3 py-1">
                                            {{$doc->is_active ? translate('Active') : translate('Inactive')}}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{route('admin.customer.document-setup.edit', [$doc->id])}}"
                                               class="btn btn--secondary btn-sm"
                                               title="{{translate('Edit')}}">
                                                <span class="material-icons">edit</span>
                                            </a>
                                            <form action="{{route('admin.customer.document-setup.delete', [$doc->id])}}"
                                                  method="POST"
                                                  onsubmit="return confirm('{{translate('Are you sure you want to delete this document setup?')}}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="{{translate('Delete')}}">
                                                    <span class="material-icons">delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <span class="material-icons d-block mb-2 fs-36 opacity-50">description</span>
                                        {{translate('No document requirements configured yet.')}}
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($documentTypes->hasPages())
                        <div class="p-3 d-flex justify-content-end">
                            {!! $documentTypes->links() !!}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

