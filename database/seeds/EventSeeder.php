<?php

use App\Events;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        factory(Events::class, 10)->create();
    }
}