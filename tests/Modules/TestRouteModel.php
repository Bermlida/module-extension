<?php

namespace Vista\Router\Tests\Modules;

use Vista\Router\Interfaces\RouteModelInterface;

/**
 * @codeCoverageIgnore
 */
class TestRouteModel implements RouteModelInterface
{
    private $user_id;
    private $item_name;
    private $item_property;
    
    public function __construct($user, $item_name, $item_property, $sort, $top)
    {
        $this->user_id = $user->user_id;
        $this->item_name = $item_name;
        $this->item_property = $item_property;

        $this->sort = $sort;
        $this->top = $top;
    }

    public function get_all_data()
    {
        return [
            'user_id' => $this->user_id,
            'item_name' => $this->item_name,
            'item_property' => $this->item_property,
            'sort' => $this->sort,
            'top' => $this->top
        ];
    }
}
