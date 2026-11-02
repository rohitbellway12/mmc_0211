@extends('adminmodule::layouts.master')

@section('title', translate('service_gallery_list'))

@push('css_or_js')
    <link rel="stylesheet" href="{{ asset('public/assets/admin-module/plugins/select2/select2.min.css') }}"/>
    <style>
        .table-thumb-stack {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .thumb-preview-img {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        .video-preview-thumb {
            width: 48px;
            height: 48px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            background: #1e293b;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .priority-pill {
            font-size: 11px;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 4px;
        }
    </style>
@endpush

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-wrap d-flex justify-content-between flex-wrap align-items-center gap-3 mb-3">
                        <h2 class="page-title">{{ translate('service_gallery') }}</h2>
                        <div>
                            <a href="{{ route('admin.service.gallery.create') }}" class="btn btn--primary">
                                <span class="material-icons">add</span>
                                {{ translate('add_new_gallery') }}
                            </a>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom mx-lg-4 mb-10 gap-3">
                        <ul class="nav nav--tabs">
                            <li class="nav-item">
                                <a class="nav-link {{ $status == 'all' ? 'active' : '' }}"
                                   href="{{ route('admin.service.gallery.index', ['status' => 'all', 'search' => $search, 'category_id' => $categoryId]) }}">
                                    {{ translate('all') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $status == 'active' ? 'active' : '' }}"
                                   href="{{ route('admin.service.gallery.index', ['status' => 'active', 'search' => $search, 'category_id' => $categoryId]) }}">
                                    {{ translate('active') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $status == 'inactive' ? 'active' : '' }}"
                                   href="{{ route('admin.service.gallery.index', ['status' => 'inactive', 'search' => $search, 'category_id' => $categoryId]) }}">
                                    {{ translate('inactive') }}
                                </a>
                            </li>
                        </ul>

                        <div class="d-flex gap-2 fw-medium">
                            <span class="opacity-75">{{ translate('total_galleries') }}:</span>
                            <span class="title-color">{{ $galleries->total() }}</span>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="data-table-top d-flex flex-wrap gap-10 justify-content-between mb-3">
                                <form action="{{ route('admin.service.gallery.index') }}" class="search-form search-form_style-two" method="GET">
                                    <input type="hidden" name="status" value="{{ $status }}">
                                    <input type="hidden" name="category_id" value="{{ $categoryId }}">
                                    <div class="input-group search-form__input_group">
                                        <span class="search-form__icon">
                                            <span class="material-icons">search</span>
                                        </span>
                                        <input type="search" class="theme-input-style search-form__input"
                                               value="{{ $search }}" name="search"
                                               placeholder="{{ translate('search_by_title_or_category') }}">
                                    </div>
                                    <button type="submit" class="btn btn--primary">{{ translate('search') }}</button>
                                </form>

                                <div class="d-flex flex-wrap align-items-center gap-3">
                                    <form action="{{ route('admin.service.gallery.index') }}" method="GET" class="d-flex align-items-center gap-2">
                                        <input type="hidden" name="status" value="{{ $status }}">
                                        <input type="hidden" name="search" value="{{ $search }}">
                                        <select class="js-select theme-input-style" name="category_id" onchange="this.form.submit()">
                                            <option value="all" {{ $categoryId == 'all' ? 'selected' : '' }}>{{ translate('all_categories') }}</option>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                                                    {{ $cat->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead class="text-nowrap">
                                        <tr>
                                            <th>{{ translate('sl') }}</th>
                                            <th>{{ translate('category') }}</th>
                                            <th>{{ translate('gallery_title') }}</th>
                                            <th>{{ translate('images_&_priority') }}</th>
                                            <th>{{ translate('videos_&_priority') }}</th>
                                            <th>{{ translate('status') }}</th>
                                            <th class="text-center">{{ translate('action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($galleries as $key => $item)
                                        @php($images = $item->media->where('media_type', 'image')->sortBy('priority'))
                                        @php($videos = $item->media->where('media_type', 'video')->sortBy('priority'))
                                        <tr>
                                            <td>{{ $galleries->firstItem() + $key }}</td>
                                            <td>
                                                <span class="fw-semibold text-primary">{{ $item->category?->name ?? $item->service?->name ?? translate('n/a') }}</span>
                                            </td>
                                            <td>
                                                <div style="max-width: 200px;">
                                                    <span class="fw-semibold text-truncate d-block" title="{{ $item->title }}">{{ $item->title }}</span>
                                                    @if($item->description)
                                                        <small class="text-muted text-truncate d-block" title="{{ $item->description }}">{{ Str::limit($item->description, 40) }}</small>
                                                    @endif
                                                </div>
                                            </td>

                                            <!-- Images & Their Priorities -->
                                            <td>
                                                @if($images->count() > 0)
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                        <span class="badge bg-primary text-white">{{ $images->count() }} {{ translate('images') }}</span>
                                                    </div>
                                                    <div class="table-thumb-stack">
                                                        @foreach($images->take(3) as $img)
                                                            <div class="position-relative" title="Priority: {{ $img->priority }}">
                                                                <img src="{{ $img->media_file_full_path }}" class="thumb-preview-img" alt="img">
                                                                <span class="badge bg-dark text-white position-absolute top-0 start-0 m-1 priority-pill" style="font-size: 9px; padding: 1px 4px;">P:{{ $img->priority }}</span>
                                                            </div>
                                                        @endforeach
                                                        @if($images->count() > 3)
                                                            <span class="badge bg-light text-dark border">+{{ $images->count() - 3 }}</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted small">{{ translate('no_images') }}</span>
                                                @endif
                                            </td>

                                            <!-- Videos & Their Priorities -->
                                            <td>
                                                @if($videos->count() > 0)
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                        <span class="badge bg-danger text-white">{{ $videos->count() }} {{ translate('videos') }}</span>
                                                    </div>
                                                    <div class="table-thumb-stack">
                                                        @foreach($videos->take(3) as $vid)
                                                            <div class="position-relative" title="Priority: {{ $vid->priority }}">
                                                                <div class="video-preview-thumb">
                                                                    @if($vid->thumbnail_full_path)
                                                                        <img src="{{ $vid->thumbnail_full_path }}" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.6;">
                                                                    @endif
                                                                    <span class="material-icons text-white position-absolute" style="font-size: 20px;">play_circle</span>
                                                                </div>
                                                                <span class="badge bg-danger text-white position-absolute top-0 start-0 m-1 priority-pill" style="font-size: 9px; padding: 1px 4px;">P:{{ $vid->priority }}</span>
                                                            </div>
                                                        @endforeach
                                                        @if($videos->count() > 3)
                                                            <span class="badge bg-light text-dark border">+{{ $videos->count() - 3 }}</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted small">{{ translate('no_videos') }}</span>
                                                @endif
                                            </td>

                                            <!-- Status Switcher -->
                                            <td>
                                                <label class="switcher">
                                                    <input class="switcher_input route-alert"
                                                           data-route="{{ route('admin.service.gallery.status-update', [$item->id]) }}"
                                                           data-message="{{ translate('want_to_update_status') }}"
                                                           type="checkbox"
                                                           {{ $item->is_active ? 'checked' : '' }}>
                                                    <span class="switcher_control"></span>
                                                </label>
                                            </td>

                                            <!-- Actions -->
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <!-- View Modal Trigger -->
                                                    <button type="button" class="action-btn btn--primary" style="--size: 30px"
                                                            data-bs-toggle="modal" data-bs-target="#galleryViewModal-{{ $item->id }}"
                                                            title="{{ translate('view_media') }}">
                                                        <span class="material-icons">visibility</span>
                                                    </button>

                                                    <!-- Edit Button -->
                                                    <a href="{{ route('admin.service.gallery.edit', [$item->id]) }}"
                                                       class="action-btn btn--light-primary" style="--size: 30px"
                                                       title="{{ translate('edit') }}">
                                                        <span class="material-icons">edit</span>
                                                    </a>

                                                    <!-- Delete Button -->
                                                    <button type="button" data-id="delete-{{ $item->id }}"
                                                            data-message="{{ translate('want_to_delete_this_gallery') }}?"
                                                            class="action-btn btn--danger form-alert" style="--size: 30px"
                                                            title="{{ translate('delete') }}">
                                                        <span class="material-icons">delete</span>
                                                    </button>
                                                    <form action="{{ route('admin.service.gallery.delete', [$item->id]) }}"
                                                          method="POST" id="delete-{{ $item->id }}" class="hidden d-none">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-5">
                                                <div class="d-flex flex-column align-items-center">
                                                    <span class="material-icons text-muted" style="font-size: 48px;">photo_library</span>
                                                    <p class="mt-2">{{ translate('no_galleries_found') }}</p>
                                                    <a href="{{ route('admin.service.gallery.create') }}" class="btn btn-sm btn--primary mt-1">
                                                        <span class="material-icons" style="font-size: 16px;">add</span> {{ translate('add_now') }}
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-end mt-3">
                                {!! $galleries->links() !!}
                            </div>
                        </div>
                    </div>

                    <!-- Modals placed outside table to preserve table layout -->
                    @foreach($galleries as $item)
                        @php($modalImages = $item->media->where('media_type', 'image')->sortBy('priority'))
                        @php($modalVideos = $item->media->where('media_type', 'video')->sortBy('priority'))
                        <div class="modal fade" id="galleryViewModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <div>
                                            <h5 class="modal-title mb-0">{{ $item->title }}</h5>
                                            <small class="text-muted">{{ translate('service') }}: <strong>{{ $item->service?->name }}</strong></small>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        @if($item->description)
                                            <div class="p-2 mb-3 bg-light rounded text-muted">
                                                {{ $item->description }}
                                            </div>
                                        @endif

                                        <!-- Images Section -->
                                        <div class="mb-4">
                                            <h6 class="fw-bold text-primary d-flex align-items-center gap-1 mb-2">
                                                <span class="material-icons" style="font-size: 18px;">photo_library</span>
                                                {{ translate('images') }} ({{ $modalImages->count() }})
                                            </h6>
                                            <div class="row g-2">
                                                @forelse($modalImages as $m)
                                                    <div class="col-6 col-sm-4 col-md-3">
                                                        <div class="border rounded p-1 text-center position-relative">
                                                            <span class="badge bg-primary position-absolute top-0 start-0 m-1">P: {{ $m->priority }}</span>
                                                            <a href="{{ $m->media_file_full_path }}" target="_blank">
                                                                <img src="{{ $m->media_file_full_path }}" class="rounded w-100" style="height: 100px; object-fit: cover;">
                                                            </a>
                                                            <small class="text-muted d-block mt-1">{{ translate('image_priority') }}: {{ $m->priority }}</small>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="col-12 text-muted small py-2">{{ translate('no_images_in_this_gallery') }}</div>
                                                @endforelse
                                            </div>
                                        </div>

                                        <!-- Videos Section -->
                                        <div>
                                            <h6 class="fw-bold text-danger d-flex align-items-center gap-1 mb-2">
                                                <span class="material-icons" style="font-size: 18px;">video_library</span>
                                                {{ translate('videos') }} ({{ $modalVideos->count() }})
                                            </h6>
                                            <div class="row g-2">
                                                @forelse($modalVideos as $m)
                                                    <div class="col-6 col-sm-4 col-md-3">
                                                        <div class="border rounded p-1 text-center position-relative">
                                                            <span class="badge bg-danger position-absolute top-0 start-0 m-1">P: {{ $m->priority }}</span>
                                                            @if($m->media_file)
                                                                <video controls class="rounded w-100" style="height: 100px; object-fit: cover;">
                                                                    <source src="{{ $m->media_file_full_path }}">
                                                                </video>
                                                            @elseif($m->video_url)
                                                                <a href="{{ $m->video_url }}" target="_blank" class="d-block position-relative">
                                                                    <img src="{{ $m->thumbnail_full_path }}" class="rounded w-100" style="height: 100px; object-fit: cover;">
                                                                    <span class="position-absolute top-50 start-50 translate-middle material-icons text-danger" style="font-size: 28px;">play_circle</span>
                                                                </a>
                                                            @endif
                                                            <small class="text-muted d-block mt-1">{{ translate('video_priority') }}: {{ $m->priority }}</small>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="col-12 text-muted small py-2">{{ translate('no_videos_in_this_gallery') }}</div>
                                                @endforelse
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('public/assets/admin-module/plugins/select2/select2.min.js') }}"></script>
    <script>
        "use strict";
        $(document).ready(function () {
            $('.js-select').select2();
        });
    </script>
@endpush
