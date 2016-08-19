<?php

namespace ModuleExtension\Constraints;

interface EntityProxyConstraint
{
    public function __construct(EntityConstraint $entity);

    public function create(array $data);

    public function delete(int $id);

    public function update(array $data, int $id);

    public function get(int $id);
    
    public function read();
}