<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoExpediente;

class TiposExpedienteSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            'Oficio Judicial',
            'Oficio Policial',
            'Nota',
            'Memorandum',
            'Texto',
        ];

        foreach ($tipos as $nombre) {
            TipoExpediente::firstOrCreate(['nombre' => $nombre]);
        }
    }
}
