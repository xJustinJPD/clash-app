<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Team;
use App\Models\User;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $team1 = Team::create([
            'name' => 'Example Team 1',
            'size' => 1,
            'image' => 'no_image_available.jpg',
            'rank' => 10,
            'wins' => 0,
            'losses' => 0,
            'creator_id' => 1,
        ]);
        $creator1 = User::find(1);
        $team1->users()->attach($creator1->id);
        $team2 = Team::create([
            'name' => 'Example Team 2',
            'size' => 1,
            'image' => 'no_image_available.jpg',
            'rank' => 6,
            'wins' => 0,
            'losses' => 0,
            'creator_id' => 3,
        ]);
        $creator2 = User::find(3);
        $team2->users()->attach($creator2->id);
    }
}
