<?php

namespace App\Http\Controllers;

use App\Managers\BaseManager;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    protected $baseManager;
    public function __construct(BaseManager $baseManager)
    {
        $this->baseManager = $baseManager;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            return response()->json($this->baseManager->get_all($request->all()));
        } catch (\Exception $exception) {
            return response($exception->getMessage());
        }
    }

}
