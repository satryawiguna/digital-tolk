<?php

namespace DTApi\Repository;

use DTApi\Repository\Interfaces\IRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class BaseRepository implements IRepository
{
    protected readonly Model $_model;

    /**
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->_model = $model;
    }

    public function all(string $orderBy = "id", string $sort = "asc"): Collection
    {
        return $this->_model
            ->orderBy($orderBy, $sort)
            ->get();
    }

    public function findById(int | string $id): Model|null
    {
        return $this->_model->find($id);
    }

    public function findOrNew(array $data): Model
    {
        $model = $this->_model->firstOrNew($data);

        $model->save();

        return $model->fresh();
    }

    public function create(FormRequest $request): Model
    {
        $model = $this->_model->fill($request->all());

        $this->setAuditableInformationFromRequest($model, $request);

        $model->save();

        return $model->fresh();
    }

    public function update(FormRequest $request): Model|null
    {
        $model = $this->_model->find($request->id);

        if (!$model) {
            return null;
        }

        $this->setAuditableInformationFromRequest($model, $request);

        $model->update($request->all());

        return $model->fresh();
    }

    public function delete(int | string $id): Model|null
    {
        $model = $this->_model->find($id);

        if (!$model) {
            return null;
        }

        $model->delete();

        return $model;
    }

    public function count(): int
    {
        return $this->_model::all()->count();
    }

    protected function setAuditableInformationFromRequest(Model | array $entity, $request)
    {
        if ($entity instanceof Model) {
            if (!$entity->getKey()) {
                $entity->setCreatedInfo($request->request_by);
            } else {
                $entity->setUpdatedInfo($request->request_by);
            }
        }

        if (is_array($entity)) {
            if (!array_key_exists('id', $entity) || $entity['id'] == 0) {
                $entity['created_by'] = $request->request_by;
                $entity['created_at'] = Carbon::now()->toDateTimeString();
            } else {
                $entity['updated_by'] = $request->request_by;
                $entity['updated_at'] = Carbon::now()->toDateTimeString();
            }

            return $entity;
        }
    }
}
