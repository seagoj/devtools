<?php namespace Devtools;

abstract class BaseEntity
{
    protected $repository;
    protected $log;

    public function __construct(
        Repository $repository,
        Log $log
    ) {
        $this->repository = $repository;
        $this->log = $log;
    }

    public function __get($property)
    {
        return $this->repository->$property
            ? $this->repository->$property
            : null;
    }

    public function __clone()
    {
        $this->repository = clone $this->repository;
    }

    public function byId($id)
    {
        $this->repository->find($id)->get();
        return clone $this;
    }

    public function byName($name)
    {
        $this->repository->find($name)->get();
        return clone $this;
    }

    public function where($whereClause)
    {
        $this->repository->where($whereClause)->get();
        return clone $this;
    }
}
