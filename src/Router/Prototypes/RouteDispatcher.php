<?php

namespace Vista\Router\Prototypes;

use RuntimeException;
use Vista\Router\Interfaces\RouteDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class RouteDispatcher implements RouteDispatcherInterface
{
    protected $executed = false;

    protected $result;

    abstract public function handle(ServerRequestInterface $request);

    abstract public function valid_settings();

    public function setting(string $item, $value)
    {
        if (isset($this->$item)) {
            if (strpos('_', $item) !== false) {
                $item = implode(array_map(
                    function ($segment) {
                        return ucfirst(strtolower($segment))
                    },
                    explode('_', $item)
                ));
            } else {
                $item = ucfirst($item);
            }        

            $method = 'set' . $item;
            return call_user_func_array([$this, $method], $arguments);
        } else {
            throw new RuntimeException('');
        }
   
            $this->$item = $value;
            return $this;
        } else {
            throw new RuntimeException('');
        }
    }

    public function executed()
    {
        return $this->executed;
    }


    public function result()
    {
        if ($this->executed) {
            $this->executed = false;
            return $this->result;
        } else {
            throw new RuntimeException('');
        }
    }
}