<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'sandi',
                'email' => 'sandi.cardinoza@gmail.com',
                'password' => bcrypt('m1ddl300t'),                
            ],
        ]);
    }
}
