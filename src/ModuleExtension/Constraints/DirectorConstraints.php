<?php

namespace ModuleExtension\Constraints;

interface DirectorConstraints
{
    public function register();

    public function delete(int $id);

    public function update(array $data, int $id);

    public function get(int $id);

    public function reader();
}