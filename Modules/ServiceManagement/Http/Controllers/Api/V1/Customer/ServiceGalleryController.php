<?php

namespace Modules\ServiceManagement\Http\Controllers\Api\V1\Customer;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\ServiceManagement\Entities\ServiceGallery;

class ServiceGalleryController extends Controller
{
    private ServiceGallery $gallery;

    public function __construct(ServiceGallery $gallery)
    {
        $this->gallery = $gallery;
    }

    /**
     * Get list of galleries for customer / frontend.
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'nullable|uuid',
            'service_id' => 'nullable|uuid',
            'type' => 'nullable|in:all,image,video',
            'limit' => 'nullable|numeric|min:1|max:200',
            'offset' => 'nullable|numeric|min:1|max:100000',
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $limit = $request->input('limit', 10);
        $offset = $request->input('offset', 1);
        $type = $request->input('type', 'all');
        $categoryId = $request->input('category_id');
        $serviceId = $request->input('service_id');

        $galleries = $this->gallery->where('is_active', 1)
            ->when($categoryId, function ($query) use ($categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->when($serviceId, function ($query) use ($serviceId) {
                return $query->where('service_id', $serviceId);
            })
            ->with(['category:id,name', 'service:id,name', 'media' => function ($query) use ($type) {
                $query->where('is_active', 1)
                    ->when($type !== 'all', function ($q) use ($type) {
                        return $q->where('media_type', $type);
                    })
                    ->orderBy('priority', 'asc');
            }])
            ->orderBy('priority', 'asc')
            ->latest()
            ->paginate($limit, ['*'], 'offset', $offset)
            ->withPath('');

        $formatted = $galleries->getCollection()->map(function ($item) {
            return [
                'id' => $item->id,
                'category_id' => $item->category_id,
                'category_name' => $item->category?->name,
                'service_id' => $item->service_id,
                'service_name' => $item->service?->name,
                'title' => $item->title,
                'description' => $item->description,
                'priority' => $item->priority,
                'media' => $item->media->map(function ($m) {
                    return [
                        'id' => $m->id,
                        'media_type' => $m->media_type,
                        'media_file_full_path' => $m->media_file_full_path,
                        'video_url' => $m->video_url,
                        'thumbnail_full_path' => $m->thumbnail_full_path,
                        'priority' => $m->priority,
                    ];
                }),
                'images' => $item->media->where('media_type', 'image')->values()->map(function ($m) {
                    return [
                        'id' => $m->id,
                        'media_file_full_path' => $m->media_file_full_path,
                        'priority' => $m->priority,
                    ];
                }),
                'videos' => $item->media->where('media_type', 'video')->values()->map(function ($m) {
                    return [
                        'id' => $m->id,
                        'media_file_full_path' => $m->media_file_full_path,
                        'video_url' => $m->video_url,
                        'thumbnail_full_path' => $m->thumbnail_full_path,
                        'priority' => $m->priority,
                    ];
                }),
            ];
        });

        $galleries->setCollection($formatted);

        return response()->json(response_formatter(DEFAULT_200, $galleries), 200);
    }

    /**
     * Get single gallery details by ID.
     */
    public function show(string $id): JsonResponse
    {
        $gallery = $this->gallery->where('is_active', 1)
            ->with(['category:id,name', 'service:id,name', 'media' => function ($query) {
                $query->where('is_active', 1)->orderBy('priority', 'asc');
            }])
            ->find($id);

        if (!$gallery) {
            return response()->json(response_formatter(DEFAULT_404), 404);
        }

        $formatted = [
            'id' => $gallery->id,
            'category_id' => $gallery->category_id,
            'category_name' => $gallery->category?->name,
            'service_id' => $gallery->service_id,
            'service_name' => $gallery->service?->name,
            'title' => $gallery->title,
            'description' => $gallery->description,
            'priority' => $gallery->priority,
            'media' => $gallery->media->map(function ($m) {
                return [
                    'id' => $m->id,
                    'media_type' => $m->media_type,
                    'media_file_full_path' => $m->media_file_full_path,
                    'video_url' => $m->video_url,
                    'thumbnail_full_path' => $m->thumbnail_full_path,
                    'priority' => $m->priority,
                ];
            }),
            'images' => $gallery->media->where('media_type', 'image')->values()->map(function ($m) {
                return [
                    'id' => $m->id,
                    'media_file_full_path' => $m->media_file_full_path,
                    'priority' => $m->priority,
                ];
            }),
            'videos' => $gallery->media->where('media_type', 'video')->values()->map(function ($m) {
                return [
                    'id' => $m->id,
                    'media_file_full_path' => $m->media_file_full_path,
                    'video_url' => $m->video_url,
                    'thumbnail_full_path' => $m->thumbnail_full_path,
                    'priority' => $m->priority,
                ];
            }),
        ];

        return response()->json(response_formatter(DEFAULT_200, $formatted), 200);
    }

    /**
     * Get galleries filtered by Category ID.
     */
    public function byCategory(Request $request, string $categoryId): JsonResponse
    {
        $request->merge(['category_id' => $categoryId]);
        return $this->index($request);
    }
}
