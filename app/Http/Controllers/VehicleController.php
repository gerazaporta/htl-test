<?php

namespace App\Http\Controllers;

use App\Managers\VehicleManager;

class VehicleController extends BaseController
{
    public function __construct()
    {
        parent::__construct(new VehicleManager());
    }
}
