<?php

namespace App\Traits;

use Illuminate\Pagination\LengthAwarePaginator;

trait PaginationTrait
{
    /**
     * Format pagination data.
     *
     * @param LengthAwarePaginator $paginator
     * @return array
     */
    protected function formatPagination(LengthAwarePaginator $paginator): array
    {
        return [
            'currentPage' => $paginator->currentPage(),
            'totalElement' => $paginator->total(),
            'totalPage' => $paginator->lastPage(),
            'perPage' => $paginator->perPage(),
        ];
    }
}
