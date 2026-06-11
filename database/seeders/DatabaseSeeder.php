<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // 1. Tablas Maestras Independientes
            PerfilSeeder::class,
            PrivilegioSeeder::class,
            CarreraSeeder::class,
            GestionSeeder::class,
            ColegioSeeder::class,
            MateriaSeeder::class,

            // 2. Tablas Dependientes Nivel 1 (Relaciones Directas)
            PerfilPrivilegioSeeder::class,
            CarreraGestionSeeder::class,
            TurnoSeeder::class,
            GrupoSeeder::class,

            // 3. Tablas Dependientes Nivel 2 (Estructuras Complejas)
            SistemaSeeder::class,
            PostulanteSeeder::class,

            // 4. Tablas Operativas Finales (Transacciones y Eventos)
            ExamenSeeder::class,
            ReclamoSeeder::class,
        ]);
    }
}