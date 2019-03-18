<?php

use Illuminate\Database\Seeder;
use App\Models\Painel\Senha;

class SenhasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Senha::create([
            'id_tipo' => 1,
            'numero' => 10,
            'id_tela_grupo' => 1

        ]);

        Senha::create([
            'id_tipo' => 2,
            'numero' => 11,
            'id_tela_grupo' => 2

        ]);

//        Senha::create([
//            'id_tipo' => 2,
//            'numero' => 3,
//            'sala_id_stg' => 1
//        ]);
//
//        Senha::create([
//            'id_tipo' => 1,
//            'numero' => 11,
//            'sala_id_stg' => 1
//        ]);
    }
}