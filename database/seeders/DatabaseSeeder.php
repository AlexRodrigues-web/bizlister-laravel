<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()\n    {\n        \->call([\n            PagesSeeder::class,\n        ]);\n
        // \App\Models\User::factory(10)->create();
    }
}
