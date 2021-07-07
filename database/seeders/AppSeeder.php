<?php

namespace Database\Seeders;

use App\Models\Key;
use App\Models\Order;
use App\Models\Technician;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class AppSeeder extends Seeder
{
    const PATH_PREFIX = './../database/data/';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        $data = [
            'keys' => Key::class,
            'vehicles' => Vehicle::class,
            'technicians' => Technician::class,
        ];

        foreach ($data as $index => $model) {
            $model::truncate();
            $records = json_decode(File::get(public_path(self::PATH_PREFIX . $index . '.json')), true);
            foreach ($records as $record) {
                $model::create($record);
            }
        }

        Order::truncate();
        Order::create([
            'vehicle_id' => 1,
            'key_id' => 1,
            'technician_id' => 1,
        ]);

        User::truncate();
        User::create([
            'name' => 'Admin',
            'email' => 'admin@email.com',
            'password' => bcrypt('Admin1234')
        ]);

        Schema::enableForeignKeyConstraints();
    }


}
