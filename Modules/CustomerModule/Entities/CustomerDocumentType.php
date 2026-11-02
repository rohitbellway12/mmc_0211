<?php

namespace Modules\CustomerModule\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerDocumentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'is_required',
        'is_active',
    ];

    protected $casts = [
        'is_required' => 'integer',
        'is_active' => 'integer',
    ];

    public function documents()
    {
        return $this->hasMany(CustomerDocument::class, 'document_type_id');
    }
}

