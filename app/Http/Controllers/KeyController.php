<?php

namespace App\Http\Controllers;

use App\Managers\KeyManager;

class KeyController extends BaseController
{
    public function __construct()
    {
        parent::__construct(new KeyManager());
    }
}
