<?php


namespace App\Managers;

class BaseManager
{
    private $base_model;

    public function __construct($base_model)
    {
        $this->base_model = $base_model;
    }

    /**
     * @param array $query
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function get_all(array $query) {
        $db_query = $this->base_model::query();

        foreach ($query as $field => $value) {
            $db_query->where($field, $value);
        }

        return $db_query->get();
    }
}
