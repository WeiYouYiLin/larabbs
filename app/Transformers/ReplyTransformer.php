<?php

namespace App\Transformers;

use App\Models\Reply;
use League\Fractal\TransformerAbstract;

class ReplyTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['user','topic'];

    public function transform(Reply $reply)
    {
        return [
            'id' => $reply->id,
            'user_id' => (int) $reply->user_id,
            'topic_id' => (int) $reply->topic_id,
            'content' => $reply->content,
            'created_at' => $reply->created_at->toDateTimeString(),
            'updated_at' => $reply->updated_at->toDateTimeString(),
        ];
    }
    // 我们需要的不仅仅是回复数据，还需要显示回复人姓名，头像等用户数据。
    public function includeUser(Reply $reply)
    {
        return $this->item($reply->user, new UserTransformer());
    }
    // 注意回复列表中，需要显示回复话题的标题，也就是我们需要 回复资源 关联的 话题资源。
    public function includeTopic(Reply $reply)
    {
        return $this->item($reply->topic, new TopicTransformer());
    }
}