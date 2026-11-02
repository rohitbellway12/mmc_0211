<?php

namespace Modules\CustomerModule\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\UserManagement\Entities\User;

class CustomerDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_type_id',
        'file_path',
        'file_name',
    ];

    protected $appends = ['file_path_full_path'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function documentType()
    {
        return $this->belongsTo(CustomerDocumentType::class, 'document_type_id');
    }

    public function getFilePathFullPathAttribute()
    {
        $file = $this->file_path;
        if (!$file) {
            return null;
        }

        if (filter_var($file, FILTER_VALIDATE_URL)) {
            return $file;
        }

        return asset('storage/app/public/customer/document/' . $file);
    }
}

