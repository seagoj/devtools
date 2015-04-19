<?php namespace Devtools;

abstract class DataMapperEntity implements DataMapper
{
    public function create();
    public function update();
    public function delete();
}
