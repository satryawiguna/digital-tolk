<?php

namespace DTApi\Http\Responses\Common;

use Illuminate\Support\Collection;

class GenericListBySearchResponse extends BasicResponse
{
    public Collection $dtoListBySearch;

    public int $totalCount;

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    public function getDtoListBySearch(): Collection
    {
        return $this->dtoListBySearch ?? new Collection();
    }
}
