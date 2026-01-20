<?php

namespace App\Repositories;

interface BaseRepositoryInterface
{
    public function all(array $columns = ['*'], array $relations = []);

    public function find(int $id, array $relations = []);

    public function create(array $data);

    public function update(int $id, array $data);

    public function delete(int $id);

    public function findTrashed(int $id);

    public function findWithInactive(int $id);

    public function restore(int $id);

    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = []);

    public function search(string $term, array $searchableFields, int $perPage = 15, array $relations = []);
}
