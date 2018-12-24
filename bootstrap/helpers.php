<?php
// 每个页面的头部 class
function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}
// 提取话题的摘录
function make_excerpt($value, $length = 200)
{
    $excerpt = trim(preg_replace('/\r\n|\r|\n+/', ' ', strip_tags($value)));
    return str_limit($excerpt, $length);
}