<?php

namespace Modules\ServiceManagement\Entities;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceGalleryMedia extends Model
{
    use HasUuid;

    protected $table = 'service_gallery_media';

    protected $fillable = [
        'gallery_id',
        'media_type',
        'media_file',
        'video_url',
        'thumbnail',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'priority' => 'integer',
        'is_active' => 'integer',
    ];

    protected $appends = [
        'media_file_full_path',
        'thumbnail_full_path',
    ];

    public function gallery(): BelongsTo
    {
        return $this->belongsTo(ServiceGallery::class, 'gallery_id', 'id');
    }

    public function getMediaFileFullPathAttribute(): ?string
    {
        $file = $this->media_file;
        if (!$file) {
            return null;
        }

        $defaultPath = null;
        if (!request()->is('api/*')) {
            $defaultPath = asset('public/assets/admin-module/img/placeholder.png');
        }

        $imagePath = 'service/gallery/' . $file;
        return function_exists('getSingleImageFullPath')
            ? getSingleImageFullPath(imagePath: $imagePath, s3Storage: null, defaultPath: $defaultPath)
            : asset('storage/app/public/' . $imagePath);
    }

    public function getThumbnailFullPathAttribute(): ?string
    {
        $thumbnail = $this->thumbnail;

        if ($thumbnail) {
            $defaultPath = !request()->is('api/*') ? asset('public/assets/admin-module/img/placeholder.png') : null;
            $imagePath = 'service/gallery/' . $thumbnail;
            return function_exists('getSingleImageFullPath')
                ? getSingleImageFullPath(imagePath: $imagePath, s3Storage: null, defaultPath: $defaultPath)
                : asset('storage/app/public/' . $imagePath);
        }

        // If it's an external YouTube URL, generate YouTube thumbnail automatically
        if ($this->video_url) {
            if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/', $this->video_url, $matches)) {
                return 'https://img.youtube.com/vi/' . $matches[1] . '/hqdefault.jpg';
            }
        }

        if (!request()->is('api/*')) {
            return asset('public/assets/admin-module/img/placeholder.png');
        }

        return null;
    }

    public function scopeOfStatus($query, $status)
    {
        return $query->where('is_active', $status);
    }
}

