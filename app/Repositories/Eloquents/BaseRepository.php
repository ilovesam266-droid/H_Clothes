<?php

namespace App\Repositories\Eloquents;

use App\Helpers\Repository;
use App\Repositories\Constracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->setModel();
    }

    abstract public function getModel();

    public function setModel(){
        $this->model = app()->make($this->getModel());
    }

    public function buildCriteria(QueryBuilder|EloquentBuilder &$query, array|callable $criteria): void
    {
        is_callable($criteria) ? $criteria($query) : Repository::handleCriteria($query, $criteria);
    }

    public function all($criteria = null, int $perPage = 20, array $columns = ['*'], string $pageName = 'page')
    {
        $query = $this->model->query();
        if($criteria) $this->buildCriteria($query, $criteria);

        return is_int($perPage) ? $query->paginate($perPage, $columns, $pageName) : $query->get($columns);
    }

    public function first($criteria = null, $columns = ['*'], $throwNotFound = false)
    {
        $query = $this->model->query();
        if($criteria) $this->buildCriteria($query, $criteria);

        return $throwNotFound ? $query->firstOrFail($columns) : $query->first($columns);
    }

    public function find($idOrCriteria = null, array $columns = ['*'], bool $throwNotFound = false)
    {
        $query = $this->model->query();

        if(is_int($idOrCriteria) || is_string($idOrCriteria))
        {
            return $throwNotFound ? $query->findOrFail($idOrCriteria) : $query->find($idOrCriteria);
        } else{
            $this->buildCriteria($query, $idOrCriteria);
        }

        return $query->get($columns);

    }

    public function create($attributes = [])
    {
        return $this->model->create($attributes);
    }

    public function update($idOrCriteria = null, array $data = [])
    {
        $query = $this->model->query();

        if(is_int($idOrCriteria) || is_string($idOrCriteria)) {
            $updatedModel = $query->find($idOrCriteria);

            return $updatedModel?->update($data);
        }
    }

    public function upsert($values, $uniqueBy, $update = null){
        return $this->model->upsert($values, $uniqueBy, $update);
    }

    public function delete($idOrCriteria = null, ?callable $beforeDelete = null)
    {
        $query = $this->model->query();

        if(is_int($idOrCriteria) || is_string($idOrCriteria)){
            $targetModel = $query->find($idOrCriteria);
            $beforeDelete && $beforeDelete($targetModel);

            return $targetModel->delete();
        }else{
            $this->buildCriteria($query, $idOrCriteria);
            $beforeDelete && $beforeDelete($query->get());
        }

        return $query->delete();
    }

    public function restore($idOrCriteria = null)
    {
        $query = $this->model->query();

        if(!in_array(SoftDeletes::class, class_uses($this->getModel()) ?: [])) {
            config('app.env') !== "production" && trigger_error("Warning: You are calling restore() on model [{$this->getModel()}] which does not use SoftDeletes.", E_USER_WARNING);
        } else {
            is_null($idOrCriteria) ? $query->onlyTrashed() : $query->withTrashed();
        }

        if(is_int($idOrCriteria) || is_string($idOrCriteria)){
            return $query->find($idOrCriteria)->restore();
        } else {
            is_null($idOrCriteria) ?: $this->buildCriteria($query, $idOrCriteria);
        }

        return $query->restore();
    }

    public function forceDelete($idOrCriteria = null, ?callable $beforeDelete = null)
    {
        $query = $this->model->query();
        if(!in_array(SoftDeletes::class, class_uses($this->getModel()) ?: [])) {
            config('app.env') !== "production" && trigger_error("Warning: You are calling forceDelete() on model [{$this->getModel()}] which does not use SoftDeletes.", E_USER_WARNING);
        }else {
            is_null($idOrCriteria) ? $query->onlyTrashed() : $query->withTrashed();
        }

        if(is_int($idOrCriteria) || is_string($idOrCriteria)) {
            $targetModel = $query->find($idOrCriteria);
            $beforeDelete && $beforeDelete($targetModel);

            return $targetModel->forceDelete();
        }else {
            is_null($idOrCriteria) ?: $this->buildCriteria($query, $idOrCriteria);
            $beforeDelete && $beforeDelete($query->get());
        }

        return $query->forceDelete();
    }


}
