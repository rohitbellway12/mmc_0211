@extends('adminmodule::layouts.master')

@section('title', translate('add_new_gallery'))

@push('css_or_js')
    <link rel="stylesheet" href="{{ asset('public/assets/admin-module/plugins/select2/select2.min.css') }}"/>
    <style>
        .gallery-card-container {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        }
        .media-toolbar-box {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 16px;
        }
        .media-card {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px;
            background: #ffffff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
            transition: all 0.2s ease-in-out;
            position: relative;
        }
        .media-card:hover {
            border-color: #3b82f6;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        }
        .media-card .card-thumb-wrap {
            width: 100%;
            height: 130px;
            background: #f1f5f9;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 8px;
        }
        .media-card .card-thumb-img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .media-card .btn-remove-circle {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 28px;
            height: 28px;
            background: #ef4444;
            color: #ffffff !important;
            border-radius: 50%;
            border: 2px solid #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(239, 68, 68, 0.4);
            transition: all 0.15s ease;
            z-index: 20;
        }
        .media-card .btn-remove-circle:hover {
            transform: scale(1.15);
            background: #dc2626;
        }
        .priority-highlight-box {
            border-radius: 6px;
            padding: 6px 10px;
            margin-top: 6px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .priority-image-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
        }
        .priority-video-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
        }
        .empty-placeholder-box {
            border: 2px dashed #94a3b8;
            border-radius: 10px;
            padding: 40px 20px;
            text-align: center;
            background: #f8fafc;
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
            color: #0f172a !important; /* Pure Black/Dark text */
            background-color: #ffffff !important; /* Pure White background */
            border-bottom: 1px solid #f1f5f9 !important;
        }
        /* Highlighted on hover */
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #0f172a !important; /* Pure dark navy/black */
            color: #ffffff !important; /* Pure white text */
            font-weight: 600 !important;
        }
        /* Selected item */
        .select2-container--default .select2-results__option[aria-selected="true"] {
            background-color: #e2e8f0 !important; /* Clean light gray */
            color: #000000 !important; /* Bold black text */
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
                        <h2 class="page-title">{{ translate('add_new_gallery') }}</h2>
                        <a href="{{ route('admin.service.gallery.index') }}" class="btn btn--secondary">
                            <span class="material-icons">arrow_back</span>
                            {{ translate('back_to_list') }}
                        </a>
                    </div>

                    <div class="card gallery-card-container mb-30">
                        <div class="card-body p-30">
                            <form action="{{ route('admin.service.gallery.store') }}" method="POST" enctype="multipart/form-data" id="gallery-form">
                                @csrf
                                <div class="row">
                                    <!-- Category Selection (Main Categories) -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold text-dark mb-1">
                                            {{ translate('select_category') }} <span class="text-danger">*</span>
                                            <span class="badge bg-light text-primary border ms-1">{{ $categories->count() }} {{ translate('categories_available') }}</span>
                                        </label>
                                        <select class="js-select theme-input-style w-100" name="category_id" required>
                                            <option value="" disabled selected>{{ translate('choose_a_category_from_the_list') }}...</option>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                                    {{ $cat->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Gallery Title -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold text-dark mb-1">{{ translate('gallery_title') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="title" class="form-control" required
                                               placeholder="{{ translate('e.g._Transformation_or_Showcase_Title') }}"
                                               value="{{ old('title') }}">
                                    </div>

                                    <!-- Album Priority -->
                                    <div class="col-md-2 mb-3">
                                        <label class="form-label fw-bold text-dark mb-1">{{ translate('album_priority') }}</label>
                                        <input type="number" name="priority" min="1" class="form-control text-center fw-bold"
                                               placeholder="{{ translate('priority') }}"
                                               value="{{ old('priority', 1) }}">
                                    </div>

                                    <!-- Description -->
                                    <div class="col-12 mb-4">
                                        <label class="form-label fw-bold text-dark mb-1">{{ translate('description') }}</label>
                                        <textarea name="description" class="form-control" rows="3"
                                                  placeholder="{{ translate('write_a_short_description_for_this_gallery_album...') }}">{{ old('description') }}</textarea>
                                    </div>

                                    <!-- Media Upload & Priority Manager -->
                                    <div class="col-12 mb-4">
                                        <div class="media-toolbar-box">
                                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3 pb-3 border-bottom border-secondary-subtle">
                                                <div>
                                                    <h5 class="mb-1 text-primary fw-bold d-flex align-items-center gap-2">
                                                        <span class="material-icons">perm_media</span>
                                                        {{ translate('media_upload_&_priority_manager') }}
                                                    </h5>
                                                    <p class="text-muted small mb-0">
                                                        {{ translate('upload_multiple_images_and_videos._each_item_has_its_own_independent_priority_and_wont_replace_others.') }}
                                                    </p>
                                                </div>

                                                <!-- 3 Action Buttons -->
                                                <div class="d-flex flex-wrap gap-2">
                                                    <button type="button" class="btn btn--primary d-flex align-items-center gap-1 shadow-sm" id="btn-add-images">
                                                        <span class="material-icons" style="font-size: 20px;">add_photo_alternate</span>
                                                        {{ translate('add_images') }}
                                                    </button>
                                                    <button type="button" class="btn btn--secondary d-flex align-items-center gap-1 shadow-sm" id="btn-add-video-file">
                                                        <span class="material-icons" style="font-size: 20px;">video_file</span>
                                                        {{ translate('add_video_file') }}
                                                    </button>
                                                    <button type="button" class="btn btn-outline-primary d-flex align-items-center gap-1 bg-white shadow-sm" id="btn-add-video-url">
                                                        <span class="material-icons" style="font-size: 20px;">link</span>
                                                        {{ translate('add_video_link') }}
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Live Count Summary -->
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="fw-bold text-dark">{{ translate('selected_media') }}:</span>
                                                    <span id="badge-total-items" class="badge bg-dark text-white px-2 py-1">0 {{ translate('items') }}</span>
                                                    <span id="badge-image-items" class="badge bg-primary text-white px-2 py-1">0 {{ translate('images') }}</span>
                                                    <span id="badge-video-items" class="badge bg-danger text-white px-2 py-1">0 {{ translate('videos') }}</span>
                                                </div>
                                                <small class="text-muted">{{ translate('you_can_re-order_priority_numbers_anytime') }}</small>
                                            </div>

                                            <!-- Media Cards Grid -->
                                            <div class="row g-3" id="media-cards-container">
                                                <div class="col-12" id="empty-media-placeholder">
                                                    <div class="empty-placeholder-box">
                                                        <span class="material-icons text-primary" style="font-size: 52px;">cloud_upload</span>
                                                        <h5 class="text-dark fw-bold mt-2 mb-1">{{ translate('no_media_added_yet') }}</h5>
                                                        <p class="text-muted small mb-0">{{ translate('click_the_buttons_above_to_attach_images,_video_files,_or_youtube/vimeo_links.') }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Container for appended file inputs -->
                                            <div id="hidden-file-inputs-container"></div>
                                        </div>
                                    </div>

                                    <!-- Submit Buttons -->
                                    <div class="col-12">
                                        <div class="d-flex justify-content-end gap-3 mt-2">
                                            <a href="{{ route('admin.service.gallery.index') }}" class="btn btn--secondary px-4">{{ translate('cancel') }}</a>
                                            <button class="btn btn--primary px-4 fw-bold" type="submit">{{ translate('save_gallery') }}</button>
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
            $('.js-select').select2({
                placeholder: "{{ translate('search_or_select_a_service') }}",
                allowClear: true
            });

            let mediaItemIndex = 0;

            function updateMediaCounters() {
                const totalImages = $('#media-cards-container .media-type-image').length;
                const totalVideos = $('#media-cards-container .media-type-video').length;
                const total = totalImages + totalVideos;

                $('#badge-total-items').text(total + ' {{ translate("items") }}');
                $('#badge-image-items').text(totalImages + ' {{ translate("images") }}');
                $('#badge-video-items').text(totalVideos + ' {{ translate("videos") }}');

                if (total === 0) {
                    $('#empty-media-placeholder').show();
                } else {
                    $('#empty-media-placeholder').hide();
                }
            }

            function getNextImagePriority() {
                let max = 0;
                $('.image-priority-input').each(function () {
                    const val = parseInt($(this).val()) || 0;
                    if (val > max) max = val;
                });
                return max + 1;
            }

            function getNextVideoPriority() {
                let max = 0;
                $('.video-priority-input').each(function () {
                    const val = parseInt($(this).val()) || 0;
                    if (val > max) max = val;
                });
                return max + 1;
            }

            // 1. ADD IMAGES (Multiple allowed, each appends cleanly without replacing)
            $('#btn-add-images').on('click', function () {
                const picker = $('<input type="file" accept="image/*" multiple class="d-none">');
                $('body').append(picker);

                picker.on('change', function () {
                    const files = this.files;
                    if (files && files.length > 0) {
                        Array.from(files).forEach((file) => {
                            mediaItemIndex++;
                            const itemId = 'image_' + mediaItemIndex;
                            const priorityVal = getNextImagePriority();

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
                                    <div class="col-6 col-md-4 col-lg-3 media-item-wrapper media-type-image" id="card_${itemId}">
                                        <div class="media-card">
                                            <button type="button" class="btn-remove-circle remove-media-btn" data-target="${itemId}" title="{{ translate('remove') }}">
                                                <span class="material-icons" style="font-size: 16px;">close</span>
                                            </button>
                                            <div class="card-thumb-wrap">
                                                <img src="${e.target.result}" class="card-thumb-img" alt="image">
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="badge bg-primary text-white font-12 fw-semibold px-2 py-1">📷 {{ translate('image') }}</span>
                                                <small class="text-dark fw-semibold text-truncate" style="max-width: 90px;" title="${file.name}">${file.name}</small>
                                            </div>
                                            <div class="priority-highlight-box priority-image-box">
                                                <span class="small fw-bold text-primary">{{ translate('image_priority') }}:</span>
                                                <input type="number" name="gallery_image_priorities[]" value="${priorityVal}" min="1"
                                                       class="form-control form-control-sm text-center fw-bold bg-white image-priority-input"
                                                       style="width: 65px; height: 30px; border: 1px solid #3b82f6;">
                                            </div>
                                        </div>
                                    </div>
                                `;
                                $('#media-cards-container').append(cardHtml);
                                updateMediaCounters();
                            };
                            reader.readAsDataURL(file);
                        });
                    }
                    picker.remove();
                });

                picker.trigger('click');
            });

            // 2. ADD VIDEO FILE (Direct file upload: .mp4, .webm, etc.)
            $('#btn-add-video-file').on('click', function () {
                const picker = $('<input type="file" accept="video/mp4,video/webm,video/ogg,video/quicktime" multiple class="d-none">');
                $('body').append(picker);

                picker.on('change', function () {
                    const files = this.files;
                    if (files && files.length > 0) {
                        Array.from(files).forEach((file) => {
                            mediaItemIndex++;
                            const itemId = 'video_' + mediaItemIndex;
                            const priorityVal = getNextVideoPriority();

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
                                <div class="col-6 col-md-4 col-lg-3 media-item-wrapper media-type-video" id="card_${itemId}">
                                    <div class="media-card">
                                        <button type="button" class="btn-remove-circle remove-media-btn" data-target="${itemId}" title="{{ translate('remove') }}">
                                            <span class="material-icons" style="font-size: 16px;">close</span>
                                        </button>
                                        <div class="card-thumb-wrap bg-dark text-white d-flex flex-column align-items-center justify-content-center">
                                            <span class="material-icons text-danger" style="font-size: 44px;">play_circle</span>
                                            <small class="text-truncate px-2 font-11 text-light" style="max-width: 140px;">${file.name}</small>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="badge bg-danger text-white font-12 fw-semibold px-2 py-1">🎥 {{ translate('video_file') }}</span>
                                            <small class="text-muted fw-semibold">${(file.size / (1024*1024)).toFixed(1)} MB</small>
                                        </div>
                                        <div class="priority-highlight-box priority-video-box">
                                            <span class="small fw-bold text-danger">{{ translate('video_priority') }}:</span>
                                            <input type="number" name="gallery_video_priorities[]" value="${priorityVal}" min="1"
                                                   class="form-control form-control-sm text-center fw-bold bg-white video-priority-input"
                                                   style="width: 65px; height: 30px; border: 1px solid #ef4444;">
                                        </div>
                                    </div>
                                </div>
                            `;
                            $('#media-cards-container').append(cardHtml);
                            updateMediaCounters();
                        });
                    }
                    picker.remove();
                });

                picker.trigger('click');
            });

            // 3. ADD VIDEO URL (YouTube, Vimeo, or Direct link)
            $('#btn-add-video-url').on('click', function () {
                mediaItemIndex++;
                const itemId = 'url_' + mediaItemIndex;
                const priorityVal = getNextVideoPriority();

                const cardHtml = `
                    <div class="col-6 col-md-4 col-lg-3 media-item-wrapper media-type-video" id="card_${itemId}">
                        <div class="media-card">
                            <button type="button" class="btn-remove-circle remove-media-btn" data-target="${itemId}" title="{{ translate('remove') }}">
                                <span class="material-icons" style="font-size: 16px;">close</span>
                            </button>
                            <div class="card-thumb-wrap bg-light d-flex flex-column align-items-center justify-content-center">
                                <span class="material-icons text-danger" style="font-size: 44px;">smart_display</span>
                                <small class="text-muted font-11 fw-semibold">{{ translate('online_video') }}</small>
                            </div>
                            <div class="mb-2">
                                <span class="badge bg-danger text-white font-12 fw-semibold px-2 py-1 mb-1">🔗 {{ translate('youtube/vimeo_url') }}</span>
                                <input type="url" name="video_urls[]" class="form-control form-control-sm"
                                       placeholder="https://www.youtube.com/watch?v=..." required>
                            </div>
                            <div class="priority-highlight-box priority-video-box">
                                <span class="small fw-bold text-danger">{{ translate('video_priority') }}:</span>
                                <input type="number" name="video_url_priorities[]" value="${priorityVal}" min="1"
                                       class="form-control form-control-sm text-center fw-bold bg-white video-priority-input"
                                       style="width: 65px; height: 30px; border: 1px solid #ef4444;">
                            </div>
                        </div>
                    </div>
                `;
                $('#media-cards-container').append(cardHtml);
                updateMediaCounters();
            });

            // REMOVE ITEM EVENT
            $(document).on('click', '.remove-media-btn', function () {
                const targetId = $(this).data('target');
                $('#card_' + targetId).remove();
                $('#input_' + targetId).remove();
                updateMediaCounters();
            });
        });
    </script>
@endpush
