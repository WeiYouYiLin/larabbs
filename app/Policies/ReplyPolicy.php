<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Reply;
use App\Models\Topic;

class ReplyPolicy extends Policy
{
    public function update(User $user, Reply $reply)
    {
        // return $reply->user_id == $user->id;
        return true;
    }

 	// 拥有删除回复权限的用户，应当是『回复的作者』或者『回复话题的作者』
    // 只调用 reply 表 会出现 n+1 问题
    public function destroy(User $user, Reply $reply)
    {
        return $user->isAuthorOf($reply) || $user->isAuthorOf($reply->topic);
    }
    // 调用 reply 和 topic 两张表，可以解决 n+1 问题
    public function showdel(User $user, Reply $reply, Topic $topic)
    {
        return $user->isAuthorOf($reply) || $user->isAuthorOf($topic);
    }
}
