<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Friend;

class FriendSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // User 1 and User 2
        Friend::create([
            'user_id' => 1,
            'friend_id' => 2,
            'status' => 'accepted',
        ]);

        // User 3 and User 1
        Friend::create([
            'user_id' => 3,
            'friend_id' => 1,
            'status' => 'accepted',
        ]);

        // User 2 and User 3
        Friend::create([
            'user_id' => 2,
            'friend_id' => 3,
            'status' => 'accepted',
        ]);

        // User 4 and User 3
        Friend::create([
            'user_id' => 4,
            'friend_id' => 3,
            'status' => 'accepted',
        ]);
    }
}






