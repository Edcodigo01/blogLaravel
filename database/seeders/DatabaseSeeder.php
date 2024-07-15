<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        DB::table('users')->insert([
            'name'=>'maria',
            'email'=>'maria@gmail.com',
            'password'=>bcrypt('1234')
        ]);
        DB::table('users')->insert([
            'name'=>'Edwar',
            'email'=>'Edwar@gmail.com',
            'password'=>bcrypt('1234')
        ]);

        DB::table('categories')->insert([
            'name'=>'Para parejas',
            'slug'=>'para-parejas'
        ]);

        DB::table('categories')->insert([
            'name'=>'Para el',
            'slug'=>'para-el'
        ]);

        DB::table('categories')->insert([
            'name'=>'Para ella',
            'slug'=>'para-ella'
        ]);

        DB::table('categories')->insert([
            'name'=>'Para padres',
            'slug'=>'para-padres'
        ]);

        DB::table('categories')->insert([
            'name'=>'Para aprender',
            'slug'=>'para-aprender'
        ]);


    }
}
