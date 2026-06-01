<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Workspace;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat User Dummy (Alex Mercer)
        $user = User::create([
            'id' => 1,
            'name' => 'Alex Mercer',
            'email' => 'alex@syncflow.edu',
            'password' => Hash::make('12345678'),
        ]);

        // 2. Buat Workspace Dummy ("Kepanitiaan BEM" sesuai mockup)
        $workspace = Workspace::create([
            'name' => 'Kepanitiaan BEM',
            // Pakai gambar acak dari Unsplash untuk dummy
            'cover_image' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=500&q=80', 
        ]);

        // 3. Masukkan Alex sebagai Admin di workspace tersebut
        $workspace->members()->attach($user->id, ['role' => 'admin']);
    }
}