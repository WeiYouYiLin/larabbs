<?php

namespace App\Observers;

use App\Models\Reply;
use App\Notifications\TopicReplied;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class ReplyObserver
{   
    // 新增回复时，过滤掉回复的内容
    public function creating(Reply $reply)
    {
        $reply->content = clean($reply->content, 'user_topic_body');
    }
    // 新增回复后，回复数量加 1
    public function created(Reply $reply)
    {
        $topic = $reply->topic;
        $topic->increment('reply_count', 1);

        // 如果评论的作者不是话题的作者，才需要通知
        if ( ! $reply->user->isAuthorOf($topic)) {
            // 通知作者话题被回复了
            $topic->user->notify(new TopicReplied($reply));
        }
    }
    // 删除回复后，回复数量减 1 
    public function deleted(Reply $reply)
    {
        $reply->topic->decrement('reply_count', 1);
    }
}