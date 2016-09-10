<?php

namespace Vista\Router\Interfaces;

use Countable;
use ArrayAccess;
use IteratorAggregate;

interface RouteCollection extends IteratorAggregate, ArrayAccess, Countable
{
    
}