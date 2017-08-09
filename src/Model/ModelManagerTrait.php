<?php

namespace AuthBucket\OAuth2\Model;

trait ModelManagerTrait
{
    protected $models;

    public function createModel(ModelInterface $model)
    {
        $this->models[$model->getId()] = $model;

        return $model;
    }

    public function readModelAll()
    {
        return $this->models;
    }

    public function readModelBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $models = [];

        foreach ($this->models as $model) {
            foreach ($criteria as $key => $value) {
                $getter = 'get'.ucfirst($key);

                if (method_exists($model, $getter) && $model->$getter() === $value) {
                    $models[$model->getId()] = $model;
                }
            }
        }

        // For simplified implementation, we only order by first key/value pair here.
        if ($orderBy !== null && is_array($orderBy)) {
            usort(
                $models,
                function ($a, $b) {
                    $getter = 'get'.ucfirst(key($orderBy));

                    return strtolower(reset($orderBy)) !== 'asc'
                        ? strcmp($b->$getter(), $a->$getter())
                        : strcmp($a->$getter(), $b->$getter());
                }
            );
        }

        $models = array_slice($models, $offset, $limit);

        return $models ?: null;
    }

    public function readModelOneBy(array $criteria, array $orderBy = null)
    {
        $models = $this->readModelBy($criteria, $orderBy, 1, 0);

        return is_array($models) ? reset($models) : $models;
    }

    public function updateModel(ModelInterface $model)
    {
        $this->models[$model->getId()] = $model;

        return $model;
    }

    public function deleteModel(ModelInterface $model)
    {
        $this->models[$model->getId()] = null;

        return $model;
    }
}
