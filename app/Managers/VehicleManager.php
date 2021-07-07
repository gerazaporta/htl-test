<?php


namespace App\Managers;

use App\Models\Vehicle;

class VehicleManager extends BaseManager
{
    public function __construct()
    {
        parent::__construct(Vehicle::class);
    }
}
