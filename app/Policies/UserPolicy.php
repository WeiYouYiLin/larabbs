<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    // 当前用户只能修改自身的信息
    public function update(User $currentUser, User $user)
    {
        return $currentUser->id === $user->id;
    }
}
