<?php


namespace App\Managers;

use App\Models\Key;

class KeyManager extends BaseManager
{
    public function __construct()
    {
        parent::__construct(Key::class);
    }
}
