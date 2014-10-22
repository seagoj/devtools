<?php namespace Devtools;

interface Repository
{
    public function all();
    public function findBy($filter);
    public function findOrFail($filter);
    public function where(Array $clause);
    public function whereRaw($clause, Array $params);
    public function first();
    public function take($numberToReturn);
    public function get();
    public function count();
    /* public function on($connectionName); */
    public function save();
    public function __get($property);
    public function create(Array $userValues);
    public function update(Array $values);
    public function delete();

    /* public function getAllBy(Array $filter); */
    /* public function getBy(Array $filter, $fields); */
    /* public function persist(\Bot\Entity\EntityInterface $entity); */
}
