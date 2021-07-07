<?php

namespace App\Http\Controllers;

use App\Managers\TechnicianManager;

class TechnicianController extends BaseController
{
    public function __construct()
    {
        parent::__construct(new TechnicianManager());
    }
}
