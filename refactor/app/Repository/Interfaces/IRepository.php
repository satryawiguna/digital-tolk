<?php

namespace DTApi\Core\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;

interface IRepository
{
    public function all(string $order = "id", string $sort = "asc"): Collection;

    public function findById(int|string $id): Model|null;

    public function findOrNew(array $data): Model;

    public function create(FormRequest $request): Model;

    public function update(FormRequest $request): Model|null;

    public function delete(int|string $id): Model|null;
}
