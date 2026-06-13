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
    // Di App\Models\CollaborativeTask.php
public function assignee()
{
    return $this->belongsTo(\App\Models\User::class, 'assignee_id');
}

    public function comments()
{
    return $this->hasMany(TaskComment::class);
}

// // Tambahkan ini agar progress tidak error jika nilainya null
//     public function getProgressAttribute($value)
//     {
//         return $value ?? 0;
//     }

    // Tambahkan ini untuk mempermudah perhitungan file lampiran
    // Pastikan kamu punya relasi 'attachments' di model ini
    // public function getAttachmentsCountAttribute()
    // {
    //     return $this->attachments()->count(); 
    // }

    // public function attachments()
    // {
    //     return $this->hasMany(TaskAttachment::class); // Sesuaikan dengan nama model lampiranmu
    // }
}
