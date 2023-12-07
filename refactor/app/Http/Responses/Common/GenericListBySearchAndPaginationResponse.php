<?php

namespace DTApi\Http\Responses\Common;

use Illuminate\Support\Collection;

class GenericListBySearchAndPaginationResponse extends BasicResponse
{
    public Collection $dtoListBySearchAndPagination;

    public int $totalCount;

    public array $meta;

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    public function getDtoListBySearchAndPagination(): Collection
    {
        return $this->dtoListBySearchAndPagination ?? new Collection();
    }
}
