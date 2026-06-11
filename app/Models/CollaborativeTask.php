<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollaborativeTask extends Model
{
    use HasFactory;

    // Kolom yang diizinkan untuk diisi
    protected $fillable = [
        'workspace_id',
        'title',
        'description',
        'status',
        'assignee_id',
        'due_date'
    ];

    // RELASI: Satu tugas kelompok dimiliki oleh/didelegasikan ke satu User
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function comments()
{
    return $this->hasMany(TaskComment::class);
}
}
