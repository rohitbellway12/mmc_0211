<?php

namespace Modules\ServiceManagement\Entities;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\CategoryManagement\Entities\Category;

class ServiceGallery extends Model
{
    use HasUuid;

    protected $table = 'service_galleries';

    protected $fillable = [
        'category_id',
        'service_id',
        'title',
        'description',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'priority' => 'integer',
        'is_active' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(ServiceGalleryMedia::class, 'gallery_id', 'id')->orderBy('priority', 'asc');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ServiceGalleryMedia::class, 'gallery_id', 'id')
            ->where('media_type', 'image')
            ->orderBy('priority', 'asc');
    }

    public function videos(): HasMany
    {
        return $this->hasMany(ServiceGalleryMedia::class, 'gallery_id', 'id')
            ->where('media_type', 'video')
            ->orderBy('priority', 'asc');
    }

    public function scopeOfStatus($query, $status)
    {
        return $query->where('is_active', $status);
    }
}
