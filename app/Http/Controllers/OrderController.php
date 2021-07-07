<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Managers\OrderManager;

class OrderController extends BaseController
{
    public function __construct()
    {
        parent::__construct(new OrderManager());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrderStoreRequest $request)
    {
        try {
            return response()->json($this->baseManager->create($request->all()));
        } catch (\Exception $exception) {
            return response($exception->getMessage());
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            return response()->json($this->baseManager->get_one($id));
        } catch (\Exception $exception) {
            return response($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * @param OrderUpdateRequest $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @throws \Throwable
     */
    public function update(OrderUpdateRequest $request, $id)
    {
        try {
            return response()->json($this->baseManager->update($id, $request->all()));
        } catch (\Exception $exception) {
            return response($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            return response()->json($this->baseManager->delete($id));
        } catch (\Exception $exception) {
            return response($exception->getMessage(), $exception->getCode());
        }
    }
}
