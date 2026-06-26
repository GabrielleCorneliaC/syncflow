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
        $user = User::create([
            'id' => 1,
            'name' => 'Alex Mercer',
            'email' => 'alex@syncflow.edu',
            'password' => Hash::make('12345678'),
        ]);

        $workspace = Workspace::create([
            'name' => 'Kepanitiaan BEM',
            'cover_image' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=500&q=80', 
        ]);

        $workspace->members()->attach($user->id, ['role' => 'admin']);
        

        User::firstOrCreate(
            ['email' => 'admin@syncflow.test'],
            [
                'name'     => 'Admin SyncFlow',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'elizabeth@syncflow.test'],
            [
                'name'     => 'Elizabeth',
                'password' => Hash::make('password'),
                'role'     => 'member',
            ]
        );
    }
}