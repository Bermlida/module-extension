<?php

namespace App\Services;

class TestService
{
    public function __construct($message = '')
    {
        print "message:::::" . $message . '<br>';
    }

    public function callMe($controller)
    {
        print 'Call Me From TestServiceProvider In ' . $controller;
    }
}