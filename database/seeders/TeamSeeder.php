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
            'image' => 'no_image_available.jpg',
            'wins' => 5,
            'losses' => 5,
            'creator_id' => 1,
        ]);
        $creator1 = User::find(1);
        $team1->users()->attach($creator1->id);
        $team2 = Team::create([
            'name' => 'Example Team 2',
            'image' => 'no_image_available.jpg',
            'wins' => 2,
            'losses' => 4,
            'creator_id' => 2,
        ]);
        $creator2 = User::find(2);
        $team2->users()->attach($creator2->id);
    }
}
