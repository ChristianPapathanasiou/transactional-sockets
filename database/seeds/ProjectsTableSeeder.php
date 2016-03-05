<?php

use Illuminate\Database\Seeder;

class ProjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('projects')->insert([
          'name' => 'Project 1',
          'amount_goal' => 25000,
          'amount_raised' => 0,
          'amount_reserved' => 0,
        ]);
    }
}