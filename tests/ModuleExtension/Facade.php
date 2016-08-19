<?php

namespace App\Models\Facades;

use App\Repositories\UserRepository;
use \Illuminate\Support\Facades\Facade as BaseFacade;
use ModuleExtension\Foundations\EntityProxy;

class Facade extends BaseFacade
{

    protected $script_result = [
        [1,2,3.5,[]],
        [1],
        [[]],
        123456,
        [123,456]
    ];
    $this->scripts = $available_script;
}