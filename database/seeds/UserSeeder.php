<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class UserSeeder extends Seeder {
    /**
     * Run the user database seeds.
     *
     * @return void
     */
    public function run() {
        factory(User::class, 1)->create();
    }
}
