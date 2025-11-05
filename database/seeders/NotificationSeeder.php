<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Notifications\SimpleNotification;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::take(10)->get();
        foreach ($users as $u) {
            $u->notify(new SimpleNotification('Welcome', 'This is a sample notification', ['example' => true]));
        }
    }
}
