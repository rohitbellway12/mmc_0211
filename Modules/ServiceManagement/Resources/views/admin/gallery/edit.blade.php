@extends('adminmodule::layouts.master')

@section('title', translate('edit_gallery'))

@push('css_or_js')
    <link rel="stylesheet" href="{{ asset('public/assets/admin-module/plugins/select2/select2.min.css') }}"/>
    <style>
        .media-card {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            transition: all 0.2s ease;
            position: relative;
        }
        .media-card:hover {
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            border-color: #cbd5e1;
        }
        .media-card .card-thumb {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 6px;
            background-color: #f1f5f9;
        }
        .media-card .btn-remove {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 26px;
            height: 26px;
            background: #ef4444;
            color: #ffffff;
            border-radius: 50%;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            transition: transform 0.15s ease;
            z-index: 10;
        }
        .media-card .btn-remove:hover {
            transform: scale(1.1);
            background: #dc2626;
        }

        /* High-Contrast Black & White Select2 Dropdown */
        .select2-container .select2-selection--single {
            height: 44px !important;
            border: 1px solid #94a3b8 !important;
            border-radius: 8px !important;
            background-color: #ffffff !important;
            display: flex !important;
            align-items: center !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #0f172a !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            line-height: 44px !important;
            padding-left: 12px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 42px !important;
            right: 10px !important;
        }
        .select2-dropdown {
            border: 2px solid #0f172a !important;
            border-radius: 8px !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.18) !important;
            background-color: #ffffff !important;
            z-index: 99999 !important;
            overflow: hidden !important;
        }
        .select2-search--dropdown {
            padding: 8px !important;
            background-color: #f1f5f9 !important;
            border-bottom: 2px solid #0f172a !important;
        }
        .select2-search--dropdown .select2-search__field {
            border: 1px solid #475569 !important;
            border-radius: 6px !important;
            padding: 8px 12px !important;
            font-size: 14px !important;
            color: #000000 !important;
            background-color: #ffffff !important;
            outline: none !important;
        }
        .select2-results__option {
            padding: 10px 14px !important;
            font-size: 13.5px !important;
            color: #0f172a !important;
            background-color: #ffffff !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #0f172a !important;
            color: #ffffff !important;
            font-weight: 600 !important;
        }
        .select2-container--default .select2-results__option[aria-selected="true"] {
            background-color: #e2e8f0 !important;
            color: #000000 !important;
            font-weight: 700 !important;
        }
    </style>
@endpush

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-wrap d-flex justify-content-between align-items-center mb-3">
                        <h2 class="page-title">{{ translate('edit_gallery') }}</h2>
                        <a href="{{ route('admin.service.gallery.index') }}" class="btn btn--secondary">
                            <span class="material-icons">arrow_back</span>
                            {{ translate('back_to_list') }}
                        </a>
                    </div>

                    <div class="card mb-30">
                        <div class="card-body p-30">
                            <form action="{{ route('admin.service.gallery.update', [$gallery->id]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <!-- Category Selection -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">{{ translate('select_category') }} <span class="text-danger">*</span></label>
                                        <select class="js-select theme-input-style w-100" name="category_id" required>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ $gallery->category_id == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Gallery Title -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-semibold">{{ translate('gallery_title') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="title" class="form-control" required
                                               placeholder="{{ translate('gallery_title') }}"
                                               value="{{ old('title', $gallery->title) }}">
                                    </div>

                                    <!-- Album Priority -->
                                    <div class="col-md-2 mb-3">
                                        <label class="form-label fw-semibold">{{ translate('album_priority') }}</label>
                                        <input type="number" name="priority" min="1" class="form-control"
                                               placeholder="{{ translate('priority') }}"
                                               value="{{ old('priority', $gallery->priority) }}">
                                    </div>

                                    <!-- Description -->
                                    <div class="col-12 mb-4">
                                        <label class="form-label fw-semibold">{{ translate('description') }}</label>
                                        <textarea name="description" class="form-control" rows="3"
                                                  placeholder="{{ translate('description') }}">{{ old('description', $gallery->description) }}</textarea>
                                    </div>

                                    <!-- Existing Media Management -->
                                    <div class="col-12 mb-4">
                                        <div class="border rounded p-3 bg-light">
                                            <h5 class="mb-3 d-flex align-items-center justify-content-between">
                                                <span>{{ translate('existing_media_items') }} ({{ $gallery->media->count() }})</span>
                                                <small class="text-muted">{{ translate('you_can_edit_priorities_or_delete_individual_media') }}</small>
                                            </h5>
                                            <div class="row g-3" id="existing-media-grid">
                                                @forelse($gallery->media as $med)
                                                    <div class="col-6 col-md-4 col-lg-3 media-item-box" id="media-box-{{ $med->id }}">
                                                        <div class="media-card">
                                                            <button type="button" class="btn-remove delete-existing-media-btn" data-id="{{ $med->id }}" title="{{ translate('delete_item') }}">
                                                                <span class="material-icons" style="font-size: 16px;">close</span>
                                                            </button>

                                                            @if($med->media_type == 'image')
                                                                <img src="{{ $med->media_file_full_path }}" class="card-thumb mb-2" alt="image">
                                                            @else
                                                                @if($med->media_file)
                                                                    <video class="card-thumb mb-2" controls>
                                                                        <source src="{{ $med->media_file_full_path }}">
                                                                    </video>
                                                                @elseif($med->video_url)
                                                                    <div class="card-thumb position-relative mb-2">
                                                                        <img src="{{ $med->thumbnail_full_path }}" class="card-thumb" alt="thumbnail">
                                                                        <span class="position-absolute top-50 start-50 translate-middle material-icons text-danger" style="font-size: 32px;">play_circle</span>
                                                                    </div>
                                                                @endif
                                                            @endif

                                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                                <span class="badge bg-light text-dark border text-capitalize">{{ $med->media_type }}</span>
                                                                @if($med->video_url)
                                                                    <a href="{{ $med->video_url }}" target="_blank" class="small text-truncate" style="max-width: 90px;">{{ translate('link') }}</a>
                                                                @endif
                                                            </div>

                                                            <div class="d-flex align-items-center justify-content-between mt-2 pt-1 border-top">
                                                                <span class="small fw-semibold text-secondary">{{ translate('priority') }}:</span>
                                                                <input type="number" name="media_priorities[{{ $med->id }}]"
                                                                       value="{{ $med->priority }}" min="1"
                                                                       class="form-control form-control-sm text-center item-priority-input" style="width: 65px;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="col-12 text-muted text-center py-3">{{ translate('no_media_items_uploaded') }}</div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Append New Media Section -->
                                    <div class="col-12 mb-4">
                                        <div class="border rounded p-3 bg-light">
                                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3 pb-3 border-bottom">
                                                <div>
                                                    <h5 class="mb-1 text-primary d-flex align-items-center gap-2">
                                                        <span class="material-icons">add_photo_alternate</span>
                                                        {{ translate('add_more_media') }}
                                                    </h5>
                                                    <p class="text-muted small mb-0">{{ translate('append_new_images_or_videos_without_affecting_existing_ones.') }}</p>
                                                </div>

                                                <div class="d-flex flex-wrap gap-2">
                                                    <button type="button" class="btn btn--primary d-flex align-items-center gap-1" id="btn-add-images">
                                                        <span class="material-icons" style="font-size: 18px;">add_photo_alternate</span>
                                                        {{ translate('add_images') }}
                                                    </button>
                                                    <button type="button" class="btn btn--secondary d-flex align-items-center gap-1" id="btn-add-video-file">
                                                        <span class="material-icons" style="font-size: 18px;">video_file</span>
                                                        {{ translate('add_video_file') }}
                                                    </button>
                                                    <button type="button" class="btn btn-outline-primary d-flex align-items-center gap-1" id="btn-add-video-url">
                                                        <span class="material-icons" style="font-size: 18px;">link</span>
                                                        {{ translate('add_video_link') }}
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="row g-3" id="new-media-cards-container">
                                                <!-- Dynamic new media will be appended here -->
                                            </div>

                                            <div id="hidden-file-inputs-container"></div>
                                        </div>
                                    </div>

                                    <!-- Submit & Cancel -->
                                    <div class="col-12">
                                        <div class="d-flex justify-content-end gap-3">
                                            <a href="{{ route('admin.service.gallery.index') }}" class="btn btn--secondary">{{ translate('cancel') }}</a>
                                            <button class="btn btn--primary" type="submit">{{ translate('update_gallery') }}</button>
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

@push('script')
    <script src="{{ asset('public/assets/admin-module/plugins/select2/select2.min.js') }}"></script>
    <script>
        "use strict";
        $(document).ready(function () {
            $('.js-select').select2();

            let mediaItemIndex = 0;

            function getNextPriority() {
                let max = 0;
                $('.item-priority-input').each(function () {
                    const val = parseInt($(this).val()) || 0;
                    if (val > max) max = val;
                });
                return max + 1;
            }

            // ADD IMAGES
            $('#btn-add-images').on('click', function () {
                const picker = $('<input type="file" accept="image/*" multiple class="d-none">');
                $('body').append(picker);

                picker.on('change', function () {
                    const files = this.files;
                    if (files && files.length > 0) {
                        Array.from(files).forEach((file) => {
                            mediaItemIndex++;
                            const itemId = 'new_image_' + mediaItemIndex;
                            const priorityVal = getNextPriority();

                            const dt = new DataTransfer();
                            dt.items.add(file);

                            const input = document.createElement('input');
                            input.type = 'file';
                            input.name = 'gallery_images[]';
                            input.className = 'd-none';
                            input.id = 'input_' + itemId;
                            input.files = dt.files;
                            document.getElementById('hidden-file-inputs-container').appendChild(input);

                            const reader = new FileReader();
                            reader.onload = function (e) {
                                const cardHtml = `
                                    <div class="col-6 col-md-4 col-lg-3 media-item-wrapper" id="card_${itemId}">
                                        <div class="media-card">
                                            <button type="button" class="btn-remove remove-media-btn" data-target="${itemId}" title="{{ translate('remove') }}">
                                                <span class="material-icons" style="font-size: 16px;">close</span>
                                            </button>
                                            <img src="${e.target.result}" class="card-thumb mb-2" alt="image">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="badge bg-primary text-white">{{ translate('new_image') }}</span>
                                                <small class="text-muted text-truncate" style="max-width: 100px;">${file.name}</small>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-2 pt-1 border-top">
                                                <span class="small fw-semibold text-secondary">{{ translate('priority') }}:</span>
                                                <input type="number" name="gallery_image_priorities[]" value="${priorityVal}" min="1"
                                                       class="form-control form-control-sm text-center item-priority-input" style="width: 65px;">
                                            </div>
                                        </div>
                                    </div>
                                `;
                                $('#new-media-cards-container').append(cardHtml);
                            };
                            reader.readAsDataURL(file);
                        });
                    }
                    picker.remove();
                });

                picker.trigger('click');
            });

            // ADD VIDEO FILE
            $('#btn-add-video-file').on('click', function () {
                const picker = $('<input type="file" accept="video/mp4,video/webm,video/ogg,video/quicktime" multiple class="d-none">');
                $('body').append(picker);

                picker.on('change', function () {
                    const files = this.files;
                    if (files && files.length > 0) {
                        Array.from(files).forEach((file) => {
                            mediaItemIndex++;
                            const itemId = 'new_video_' + mediaItemIndex;
                            const priorityVal = getNextPriority();

                            const dt = new DataTransfer();
                            dt.items.add(file);

                            const input = document.createElement('input');
                            input.type = 'file';
                            input.name = 'gallery_videos[]';
                            input.className = 'd-none';
                            input.id = 'input_' + itemId;
                            input.files = dt.files;
                            document.getElementById('hidden-file-inputs-container').appendChild(input);

                            const cardHtml = `
                                <div class="col-6 col-md-4 col-lg-3 media-item-wrapper" id="card_${itemId}">
                                    <div class="media-card">
                                        <button type="button" class="btn-remove remove-media-btn" data-target="${itemId}" title="{{ translate('remove') }}">
                                            <span class="material-icons" style="font-size: 16px;">close</span>
                                        </button>
                                        <div class="card-thumb d-flex flex-column align-items-center justify-content-center bg-dark text-white mb-2">
                                            <span class="material-icons text-danger" style="font-size: 38px;">play_circle</span>
                                            <small class="text-truncate px-2" style="max-width: 140px;">${file.name}</small>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="badge bg-danger text-white">{{ translate('new_video') }}</span>
                                            <small class="text-muted">${(file.size / (1024*1024)).toFixed(1)} MB</small>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mt-2 pt-1 border-top">
                                            <span class="small fw-semibold text-secondary">{{ translate('priority') }}:</span>
                                            <input type="number" name="gallery_video_priorities[]" value="${priorityVal}" min="1"
                                                   class="form-control form-control-sm text-center item-priority-input" style="width: 65px;">
                                        </div>
                                    </div>
                                </div>
                            `;
                            $('#new-media-cards-container').append(cardHtml);
                        });
                    }
                    picker.remove();
                });

                picker.trigger('click');
            });

            // ADD VIDEO URL
            $('#btn-add-video-url').on('click', function () {
                mediaItemIndex++;
                const itemId = 'new_url_' + mediaItemIndex;
                const priorityVal = getNextPriority();

                const cardHtml = `
                    <div class="col-6 col-md-4 col-lg-3 media-item-wrapper" id="card_${itemId}">
                        <div class="media-card">
                            <button type="button" class="btn-remove remove-media-btn" data-target="${itemId}" title="{{ translate('remove') }}">
                                <span class="material-icons" style="font-size: 16px;">close</span>
                            </button>
                            <div class="card-thumb d-flex flex-column align-items-center justify-content-center bg-light text-primary mb-2">
                                <span class="material-icons text-danger" style="font-size: 38px;">smart_display</span>
                                <small class="text-muted">{{ translate('video_link') }}</small>
                            </div>
                            <div class="mb-2">
                                <span class="badge bg-danger-subtle text-danger mb-1">{{ translate('video_url') }}</span>
                                <input type="url" name="video_urls[]" class="form-control form-control-sm"
                                       placeholder="https://www.youtube.com/watch?v=..." required>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-2 pt-1 border-top">
                                <span class="small fw-semibold text-secondary">{{ translate('priority') }}:</span>
                                <input type="number" name="video_url_priorities[]" value="${priorityVal}" min="1"
                                       class="form-control form-control-sm text-center item-priority-input" style="width: 65px;">
                            </div>
                        </div>
                    </div>
                `;
                $('#new-media-cards-container').append(cardHtml);
            });

            // REMOVE NEWLY APPENDED ITEM
            $(document).on('click', '.remove-media-btn', function () {
                const targetId = $(this).data('target');
                $('#card_' + targetId).remove();
                $('#input_' + targetId).remove();
            });

            // DELETE EXISTING MEDIA ITEM VIA AJAX
            $('.delete-existing-media-btn').on('click', function () {
                const mediaId = $(this).data('id');
                const box = $(`#media-box-${mediaId}`);

                if (confirm('{{ translate("Are you sure you want to delete this media item?") }}')) {
                    $.ajax({
                        url: `{{ url('admin/service/gallery/media-delete') }}/${mediaId}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (res) {
                            if (res.success) {
                                box.fadeOut(300, function () { $(this).remove(); });
                                toastr.success(res.message);
                            } else {
                                toastr.error(res.message);
                            }
                        },
                        error: function () {
                            toastr.error('{{ translate("Failed to delete media item.") }}');
                        }
                    });
                }
            });
        });
    </script>
@endpush
