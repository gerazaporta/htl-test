<?php


namespace App\Managers;

use App\Models\Order;

class OrderManager extends BaseManager
{
    public function __construct()
    {
        parent::__construct(Order::class);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return Order::create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return mixed
     * @throws \Throwable
     */
    public function update(int $id, array $data)
    {
        $order = Order::find($id);
        throw_if(!$order, new \Exception('Order was not found', 404));

        $order->update($data);
        return $order;
    }

    /**
     * @param int $id
     * @return mixed
     * @throws \Throwable
     */
    public function get_one(int $id)
    {
        $order = Order::find($id);
        throw_if(!$order, new \Exception('Order was not found', 404));

        return $order;
    }

    /**
     * @param int $id
     * @return mixed
     * @throws \Throwable
     */
    public function delete(int $id)
    {
        $order = Order::find($id);
        throw_if(!$order, new \Exception('Order was not found', 404));

        $order->delete();
        return $order;
    }
}
