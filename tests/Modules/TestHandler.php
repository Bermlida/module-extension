<?php

namespace Vista\Router\Tests\Modules;

/**
 * @codeCoverageIgnore
 */
class TestHandler
{
    public function __invoke($user, $item_name, $item_property, $sort, $top)
    {
        $user->item_name = $item_name;
        $user->item_property = $item_property;
        $user->sort = $sort;
        $user->top = $top;

        return (array)$user;
    }

    public function process($user, $item_name, $item_property, $sort, $top)
    {
        $return_value = compact(['item_name', 'item_property', 'sort', 'top']);
        
        $return_value['user_id'] = $user->user_id;
        
        return $return_value;
    }

    public function processWithModel(TestRouteModel $model)
    {
        return $model->get_all_data();
    }
}

