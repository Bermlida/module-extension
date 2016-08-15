<?php

namespace App\Services;

class TestService
{
    public function callMe($controller)
    {
        print 'Call Me From TestServiceProvider In ' . $controller;
    }
}