<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class WorkspaceMember extends Model
{
    protected $fillable = [
        'workspace_id',
        'user_id',
        'role',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
