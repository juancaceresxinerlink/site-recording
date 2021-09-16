<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;


class CreateOrganizationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        if (DB::table('organizations')->where("name", "USS")->get()->count() == 0) {
            DB::table('organizations')->insert([
                'name' => 'USS'
            ]);
        }

        
    }
}
