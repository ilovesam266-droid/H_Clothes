<?php

namespace App\Repositories\Constracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

interface BaseRepositoryInterface
{
    /**
     * Retrieve a list of records.
     *
     * @param  mixed|null  $criteria
     *      Supported values:
     *      - null: retrieve all records
     *      - int|string: filter by primary key
     *      - array: key-value where conditions
     *      - callable(Builder $query): customize the query builder
     *
     * @param  int     $perPage
     *      Number of records per page.
     *      Set to 0 to disable pagination.
     *
     * @param  array   $columns
     *      Columns to select.
     *
     * @param  string  $pageName
     *      Query parameter name for pagination.
     *
     * @return Collection|LengthAwarePaginator
     */
    public function all(
        $criteria = null,
        int $perPage = 20,
        array $columns = ['*'],
        string $pageName = 'page'
    );

    /**
     * Retrieve the first record matching the given criteria.
     *
     * @param  mixed|null  $criteria
     *      - int|string: primary key
     *      - array: where conditions
     *      - callable(Builder $query): custom query logic
     *
     * @param  array  $columns
     * @param  bool   $throwNotFound
     *      Whether to throw an exception if no record is found.
     *
     * @return Model|null
     *
     * @throws ModelNotFoundException
     */
    public function first(
        $criteria = null,
        array $columns = ['*'],
        bool $throwNotFound = false
    );

    /**
     * Find a record by ID or criteria.
     *
     * @param  mixed|null  $criteria
     *      - int|string: primary key
     *      - array: where conditions
     *      - callable(Builder $query): custom query logic
     *
     * @param  array  $columns
     * @param  bool   $throwNotFound
     *
     * @return Model|null
     *
     * @throws ModelNotFoundException
     */
    public function find(
        $idOrCriteria = null,
        array $columns = ['*'],
        bool $throwNotFound = false
    );

    /**
     * Create a new record.
     *
     * @param  array  $data
     *      Mass-assignable attributes.
     *
     * @return Model
     */
    public function create(array $data = []);

    /**
     * Update a record by ID or criteria.
     *
     * @param  mixed|null  $idOrCriteria
     *      - int|string: primary key
     *      - array: where conditions
     *      - callable(Builder $query): custom query logic
     *
     * @param  array  $data
     *
     * @return Model|int|null
     *      - Model: when updating a single record
     *      - int: number of affected rows
     */
    public function update($idOrCriteria = null, array $data = []);

    public function upsert($values, $uniqueBy, $update = null);

    /**
     * Soft delete a record.
     *
     * @param  mixed|null      $idOrCriteria
     * @param  callable|null  $beforeDelete
     *      Callback executed before deleting.
     *
     * @return bool|int
     */
    public function delete(
        $idOrCriteria = null,
        ?callable $beforeDelete = null
    );

    /**
     * Restore a soft-deleted record.
     *
     * @param  mixed|null  $idOrCriteria
     *
     * @return bool|int
     */
    public function restore($idOrCriteria = null);

    /**
     * Permanently delete a record.
     *
     * @param  mixed|null      $idOrCriteria
     * @param  callable|null  $beforeDelete
     *
     * @return bool|int
     */
    public function forceDelete(
        $idOrCriteria = null,
        ?callable $beforeDelete = null
    );

    /**
     * Calculate the sum of a column.
     *
     * @param  string|array  $columns
     * @param  mixed|null    $criteria
     *
     * @return int|float
     */
    public function sum($columns, $criteria = null);

    /**
     * Count records.
     *
     * @param  string       $columns
     * @param  mixed|null   $criteria
     *
     * @return int
     */
    public function count($columns = '*', $criteria = null);

    /**
     * Calculate the average value of a column.
     *
     * @param  string|array  $columns
     * @param  mixed|null    $criteria
     *
     * @return float|null
     */
    public function avg($columns, $criteria = null);

    /**
     * Determine if any record exists for the given criteria.
     *
     * @param  mixed  $criteria
     *
     * @return bool
     */
    public function exists($criteria);

    /**
     * Get the underlying Eloquent model instance.
     *
     * @return Model
     */
    public function getModel();
}
