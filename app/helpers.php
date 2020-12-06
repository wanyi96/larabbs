<?php

function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}

function category_nav_active($category_id)
{
    //active_class()方法，如果传参满足参数1，则会返回参数二'active'，否则返回参数3''
    return active_class((if_route('categories.show') && if_route_param('category',$category_id)));
}
