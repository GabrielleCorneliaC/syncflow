<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceLink extends Model
{
    protected $fillable =
    [
        'workspace_id',
        'title',
        'url',
        'description'
    ];
}
