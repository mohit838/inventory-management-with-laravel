<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class EloquentBaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(array $columns = ['*'], array $relations = [])
    {
        return $this->model->with($relations)->get($columns);
    }

    public function find(int $id, array $relations = [])
    {
        return $this->model->with($relations)->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $model = $this->find($id);
        $model->update($data);

        return $model;
    }

    public function delete(int $id)
    {
        $model = $this->findWithInactive($id);

        return $model->delete();
    }

    public function findTrashed(int $id)
    {
        return $this->model->onlyTrashed()->findOrFail($id);
    }

    public function findWithInactive(int $id)
    {
        // Check if model has the scope before calling it to avoid errors if trait not used
        if (method_exists($this->model, 'scopeWithInactive')) {
            return $this->model->withInactive()->findOrFail($id);
        }

        return $this->find($id);
    }

    public function restore(int $id)
    {
        $model = $this->findTrashed($id);

        return $model->restore();
    }

    public function forceDelete(int $id)
    {
        $model = $this->findTrashed($id);

        return $model->forceDelete();
    }

    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = [])
    {
        return $this->model->with($relations)->paginate($perPage, $columns);
    }

    public function search(string $term, array $searchableFields, int $perPage = 15, array $relations = [])
    {
        return $this->model->with($relations)
            ->where(function ($query) use ($term, $searchableFields) {
                foreach ($searchableFields as $field) {
                    $query->orWhere($field, 'like', '%'.$term.'%');
                }
            })
            ->paginate($perPage);
    }

    public function toggleActive($id)
    {
        $item = $this->findWithInactive($id);
        $item->active = ! $item->active;
        $item->save();

        return $item;
    }
}
