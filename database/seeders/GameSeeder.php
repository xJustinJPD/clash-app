<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Game;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        Game::create([
            'team_id_1' => 1,
            'team_id_2' => 2,
            'team_1_score' => null, 
            'team_2_score' => null,
            'queue_type' => '1v1', 
            'status' => 'in_progress',
        ]);
    }
}
