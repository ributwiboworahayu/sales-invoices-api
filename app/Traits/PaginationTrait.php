<?php

namespace App\Traits;

trait PaginationTrait
{
    protected int $page = 1;
    protected int $limit = 5;
    protected string $order = 'ASC';

    /**
     * this function is used to set pagination manually
     *
     * @param int $page
     * @param int $limit
     * @param int $total
     * @param $data
     * @return array
     */
    protected function manualPaginateWrapper(int $page, int $limit, int $total, $data): array
    {
        return [
            "_metadata" => [
                "page" => $page,
                "per_page" => $limit,
                "total" => $total
            ],
            "records" => $data
        ];
    }

    /**
     * this function is used to set pagination automatically using laravel paginate eloquent
     *
     * @param mixed $data
     * @return array
     */
    protected static function autoPaginateWrapper(mixed $data): array
    {
        return [
            "_metadata" => [
                "page" => (int)$data->currentPage(),
                "per_page" => (int)$data->perPage(),
                "total" => (int)$data->total(),
            ],
            "records" => $data->items()
        ];
    }
}
