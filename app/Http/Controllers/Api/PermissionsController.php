<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Transformers\PermissionTransformer;

class PermissionsController extends Controller
{
    // 当前登录用户权限列表
    public function index()
    {
       $permissions = $this->user()->getAllPermissions();

       return $this->response->collection($permissions, new PermissionTransformer());
    }
}
