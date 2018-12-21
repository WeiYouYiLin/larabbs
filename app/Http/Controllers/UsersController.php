<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Handlers\ImageUploadHandler;

class UsersController extends Controller
{
	// 权限控制
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['show']]);
    }
    // 个人页面展示
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }
    // 编辑个人资料
  	public function edit(User $user)
    {
        // 调用权限策略，只能编辑自身的信息
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }
    // 更新用户信息
    public function update(UserRequest $request, ImageUploadHandler $uploader, User $user)
    {
        $this->authorize('update', $user);
        $data = $request->all();

        if ($request->avatar) {
            $result = $uploader->save($request->avatar, 'avatars', $user->id, 362);
            if ($result) {
                $data['avatar'] = $result['path'];
            }
        }

        $user->update($data);
        return redirect()->route('users.show', $user->id)->with('success', '个人资料更新成功！');
    }
}
