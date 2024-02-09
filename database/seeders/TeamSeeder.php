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
        $team = Team::create([
            'name' => 'Example Team',
            'image' => 'no_image_available.jpg',
            'wins' => 5,
            'losses' => 5,
            'creator_id' => 1,
        ]);
        $creator = User::find(1);
        $team->users()->attach($creator->id);
    }
}
