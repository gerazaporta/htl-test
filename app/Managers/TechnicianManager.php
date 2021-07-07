<?php


namespace App\Managers;

use App\Models\Technician;

class TechnicianManager extends BaseManager
{
    public function __construct()
    {
        parent::__construct(Technician::class);
    }
}
