<?php

namespace Modules\ServiceManagement\Http\Controllers\Web\Admin;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\CategoryManagement\Entities\Category;
use Modules\ServiceManagement\Entities\Service;
use Modules\ServiceManagement\Entities\ServiceGallery;
use Modules\ServiceManagement\Entities\ServiceGalleryMedia;

class ServiceGalleryController extends Controller
{
    private ServiceGallery $gallery;
    private ServiceGalleryMedia $galleryMedia;
    private Service $service;
    private Category $category;

    public function __construct(
        ServiceGallery $gallery,
        ServiceGalleryMedia $galleryMedia,
        Service $service,
        Category $category
    ) {
        $this->gallery = $gallery;
        $this->galleryMedia = $galleryMedia;
        $this->service = $service;
        $this->category = $category;
    }

    /**
     * Display listing of galleries.
     */
    public function index(Request $request): View|Factory|Application
    {
        $search = $request->input('search', '');
        $status = $request->input('status', 'all');
        $categoryId = $request->input('category_id', 'all');

        $queryParam = ['search' => $search, 'status' => $status, 'category_id' => $categoryId];

        $galleries = $this->gallery->with(['category', 'service', 'media'])
            ->when($status !== 'all', function ($query) use ($status) {
                return $query->where('is_active', $status === 'active' ? 1 : 0);
            })
            ->when($categoryId !== 'all', function ($query) use ($categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', '%' . $search . '%')
                      ->orWhere('description', 'LIKE', '%' . $search . '%')
                      ->orWhereHas('category', function ($sq) use ($search) {
                          $sq->where('name', 'LIKE', '%' . $search . '%');
                      });
                });
            })
            ->orderBy('priority', 'asc')
            ->latest()
            ->paginate(pagination_limit())
            ->appends($queryParam);

        $categories = $this->category->where('position', 1)->orderBy('name', 'asc')->get();

        return view('servicemanagement::admin.gallery.list', compact('galleries', 'categories', 'search', 'status', 'categoryId'));
    }

    /**
     * Show form for creating a new gallery.
     */
    public function create(): View|Factory|Application
    {
        $categories = $this->category->where('position', 1)->orderBy('name', 'asc')->get();
        return view('servicemanagement::admin.gallery.create', compact('categories'));
    }

    /**
     * Store newly created gallery and its media.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'category_id' => 'required|uuid|exists:categories,id',
            'service_id' => 'nullable|uuid',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|integer|min:1',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:10240',
            'images.*' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:10240',
            'gallery_videos.*' => 'nullable|mimes:mp4,mov,ogg,qt,webm|max:51200',
            'video_files.*' => 'nullable|mimes:mp4,mov,ogg,qt,webm|max:51200',
            'video_urls.*' => 'nullable|url',
        ]);

        DB::beginTransaction();
        try {
            $gallery = $this->gallery->create([
                'category_id' => $request->category_id,
                'service_id' => $request->service_id ?? null,
                'title' => $request->title,
                'description' => $request->description,
                'priority' => $request->input('priority', 1),
                'is_active' => 1,
            ]);

            // Upload & save images
            if ($request->hasFile('gallery_images')) {
                $imagePriorities = $request->input('gallery_image_priorities', []);
                foreach ($request->file('gallery_images') as $index => $image) {
                    $ext = $image->getClientOriginalExtension();
                    $fileName = file_uploader('service/gallery/', $ext, $image);
                    $priority = isset($imagePriorities[$index]) && is_numeric($imagePriorities[$index])
                        ? (int)$imagePriorities[$index]
                        : ($index + 1);

                    $this->galleryMedia->create([
                        'gallery_id' => $gallery->id,
                        'media_type' => 'image',
                        'media_file' => $fileName,
                        'priority' => $priority,
                        'is_active' => 1,
                    ]);
                }
            } elseif ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $ext = $image->getClientOriginalExtension();
                    $fileName = file_uploader('service/gallery/', $ext, $image);
                    $this->galleryMedia->create([
                        'gallery_id' => $gallery->id,
                        'media_type' => 'image',
                        'media_file' => $fileName,
                        'priority' => $index + 1,
                        'is_active' => 1,
                    ]);
                }
            }

            // Upload & save video files
            if ($request->hasFile('gallery_videos')) {
                $videoPriorities = $request->input('gallery_video_priorities', []);
                foreach ($request->file('gallery_videos') as $index => $videoFile) {
                    $ext = $videoFile->getClientOriginalExtension();
                    $fileName = file_uploader('service/gallery/', $ext, $videoFile);
                    $priority = isset($videoPriorities[$index]) && is_numeric($videoPriorities[$index])
                        ? (int)$videoPriorities[$index]
                        : ($index + 1);

                    $this->galleryMedia->create([
                        'gallery_id' => $gallery->id,
                        'media_type' => 'video',
                        'media_file' => $fileName,
                        'priority' => $priority,
                        'is_active' => 1,
                    ]);
                }
            } elseif ($request->hasFile('video_files')) {
                foreach ($request->file('video_files') as $index => $videoFile) {
                    $ext = $videoFile->getClientOriginalExtension();
                    $fileName = file_uploader('service/gallery/', $ext, $videoFile);
                    $this->galleryMedia->create([
                        'gallery_id' => $gallery->id,
                        'media_type' => 'video',
                        'media_file' => $fileName,
                        'priority' => $index + 1,
                        'is_active' => 1,
                    ]);
                }
            }

            // Save video URLs
            if ($request->filled('video_urls')) {
                $urlPriorities = $request->input('video_url_priorities', []);
                foreach ($request->video_urls as $index => $url) {
                    if (!empty($url)) {
                        $priority = isset($urlPriorities[$index]) && is_numeric($urlPriorities[$index])
                            ? (int)$urlPriorities[$index]
                            : ($index + 1);

                        $this->galleryMedia->create([
                            'gallery_id' => $gallery->id,
                            'media_type' => 'video',
                            'video_url' => trim($url),
                            'priority' => $priority,
                            'is_active' => 1,
                        ]);
                    }
                }
            }

            DB::commit();
            Toastr::success(translate('Gallery created successfully!'));
            return redirect()->route('admin.service.gallery.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error(translate('Failed to create gallery: ') . $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Show edit form.
     */
    public function edit(string $id): View|Factory|Application|RedirectResponse
    {
        $gallery = $this->gallery->with(['category', 'service', 'media'])->find($id);
        if (!$gallery) {
            Toastr::error(translate('Gallery not found!'));
            return redirect()->route('admin.service.gallery.index');
        }

        $categories = $this->category->where('position', 1)->orderBy('name', 'asc')->get();
        return view('servicemanagement::admin.gallery.edit', compact('gallery', 'categories'));
    }

    /**
     * Update gallery and append/modify media.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $gallery = $this->gallery->find($id);
        if (!$gallery) {
            Toastr::error(translate('Gallery not found!'));
            return redirect()->route('admin.service.gallery.index');
        }

        $request->validate([
            'category_id' => 'required|uuid|exists:categories,id',
            'service_id' => 'nullable|uuid',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|integer|min:1',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:10240',
            'images.*' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:10240',
            'gallery_videos.*' => 'nullable|mimes:mp4,mov,ogg,qt,webm|max:51200',
            'video_files.*' => 'nullable|mimes:mp4,mov,ogg,qt,webm|max:51200',
            'video_urls.*' => 'nullable|url',
        ]);

        DB::beginTransaction();
        try {
            $gallery->update([
                'category_id' => $request->category_id,
                'service_id' => $request->service_id ?? null,
                'title' => $request->title,
                'description' => $request->description,
                'priority' => $request->input('priority', 1),
            ]);

            // Update priorities of existing media items
            if ($request->has('media_priorities') && is_array($request->media_priorities)) {
                foreach ($request->media_priorities as $mediaId => $priority) {
                    $this->galleryMedia->where('id', $mediaId)->where('gallery_id', $gallery->id)->update([
                        'priority' => (int)$priority,
                    ]);
                }
            }

            $currentMaxPriority = (int)$gallery->media()->max('priority');
            $mediaPriority = $currentMaxPriority > 0 ? $currentMaxPriority + 1 : 1;

            // Upload & append new images
            if ($request->hasFile('gallery_images')) {
                $imagePriorities = $request->input('gallery_image_priorities', []);
                foreach ($request->file('gallery_images') as $index => $image) {
                    $ext = $image->getClientOriginalExtension();
                    $fileName = file_uploader('service/gallery/', $ext, $image);
                    $priority = isset($imagePriorities[$index]) && is_numeric($imagePriorities[$index])
                        ? (int)$imagePriorities[$index]
                        : $mediaPriority++;

                    $this->galleryMedia->create([
                        'gallery_id' => $gallery->id,
                        'media_type' => 'image',
                        'media_file' => $fileName,
                        'priority' => $priority,
                        'is_active' => 1,
                    ]);
                }
            } elseif ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $ext = $image->getClientOriginalExtension();
                    $fileName = file_uploader('service/gallery/', $ext, $image);
                    $this->galleryMedia->create([
                        'gallery_id' => $gallery->id,
                        'media_type' => 'image',
                        'media_file' => $fileName,
                        'priority' => $mediaPriority++,
                        'is_active' => 1,
                    ]);
                }
            }

            // Upload & append new video files
            if ($request->hasFile('gallery_videos')) {
                $videoPriorities = $request->input('gallery_video_priorities', []);
                foreach ($request->file('gallery_videos') as $index => $videoFile) {
                    $ext = $videoFile->getClientOriginalExtension();
                    $fileName = file_uploader('service/gallery/', $ext, $videoFile);
                    $priority = isset($videoPriorities[$index]) && is_numeric($videoPriorities[$index])
                        ? (int)$videoPriorities[$index]
                        : $mediaPriority++;

                    $this->galleryMedia->create([
                        'gallery_id' => $gallery->id,
                        'media_type' => 'video',
                        'media_file' => $fileName,
                        'priority' => $priority,
                        'is_active' => 1,
                    ]);
                }
            } elseif ($request->hasFile('video_files')) {
                foreach ($request->file('video_files') as $videoFile) {
                    $ext = $videoFile->getClientOriginalExtension();
                    $fileName = file_uploader('service/gallery/', $ext, $videoFile);
                    $this->galleryMedia->create([
                        'gallery_id' => $gallery->id,
                        'media_type' => 'video',
                        'media_file' => $fileName,
                        'priority' => $mediaPriority++,
                        'is_active' => 1,
                    ]);
                }
            }

            // Append new video URLs
            if ($request->filled('video_urls')) {
                $urlPriorities = $request->input('video_url_priorities', []);
                foreach ($request->video_urls as $index => $url) {
                    if (!empty($url)) {
                        $priority = isset($urlPriorities[$index]) && is_numeric($urlPriorities[$index])
                            ? (int)$urlPriorities[$index]
                            : $mediaPriority++;

                        $this->galleryMedia->create([
                            'gallery_id' => $gallery->id,
                            'media_type' => 'video',
                            'video_url' => trim($url),
                            'priority' => $priority,
                            'is_active' => 1,
                        ]);
                    }
                }
            }

            DB::commit();
            Toastr::success(translate('Gallery updated successfully!'));
            return redirect()->route('admin.service.gallery.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error(translate('Failed to update gallery: ') . $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Toggle status via AJAX switcher.
     */
    public function statusUpdate(Request $request, string $id): JsonResponse
    {
        $gallery = $this->gallery->find($id);
        if (!$gallery) {
            return response()->json(['success' => false, 'message' => translate('Gallery not found')], 404);
        }

        $gallery->is_active = !$gallery->is_active;
        $gallery->save();

        return response()->json([
            'success' => true,
            'status' => $gallery->is_active,
            'message' => translate('Status updated successfully!'),
        ]);
    }

    /**
     * Delete an entire gallery and clean up disk files.
     */
    public function destroy(string $id): RedirectResponse|JsonResponse
    {
        $gallery = $this->gallery->with('media')->find($id);
        if (!$gallery) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => translate('Gallery not found')], 404);
            }
            Toastr::error(translate('Gallery not found!'));
            return back();
        }

        // Clean up stored files
        foreach ($gallery->media as $mediaItem) {
            if ($mediaItem->media_file && function_exists('file_remover')) {
                file_remover('service/gallery/', $mediaItem->media_file);
            }
            if ($mediaItem->thumbnail && function_exists('file_remover')) {
                file_remover('service/gallery/', $mediaItem->thumbnail);
            }
        }

        $gallery->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => translate('Gallery deleted successfully!')]);
        }

        Toastr::success(translate('Gallery deleted successfully!'));
        return redirect()->route('admin.service.gallery.index');
    }

    /**
     * Delete an individual media item from a gallery (AJAX).
     */
    public function ajaxDeleteMedia(string $media_id): JsonResponse
    {
        $media = $this->galleryMedia->find($media_id);
        if (!$media) {
            return response()->json(['success' => false, 'message' => translate('Media item not found')], 404);
        }

        if ($media->media_file && function_exists('file_remover')) {
            file_remover('service/gallery/', $media->media_file);
        }
        if ($media->thumbnail && function_exists('file_remover')) {
            file_remover('service/gallery/', $media->thumbnail);
        }

        $media->delete();

        return response()->json([
            'success' => true,
            'message' => translate('Media deleted successfully!'),
        ]);
    }
}
