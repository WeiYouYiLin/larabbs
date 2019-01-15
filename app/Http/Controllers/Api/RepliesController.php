<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Topic;
use App\Models\Reply;
use App\Http\Requests\Api\ReplyRequest;
use App\Transformers\ReplyTransformer;

class RepliesController extends Controller
{
    // 回复列表
    public function index(Topic $topic)
	{
	    $replies = $topic->replies()->paginate(20);

	    return $this->response->paginator($replies, new ReplyTransformer());
	}

	// 某个用户回复列表
	public function userIndex(User $user)
	{
	    $replies = $user->replies()->paginate(20);

	    return $this->response->paginator($replies, new ReplyTransformer());
	}

    // 发布回复
    public function store(ReplyRequest $request, Topic $topic, Reply $reply)
    {
        $reply->content = $request->content;
        $reply->topic_id = $topic->id;
        $reply->user_id = $this->user()->id;
        $reply->save();

        return $this->response->item($reply, new ReplyTransformer())
            ->setStatusCode(201);
    }

    // 删除回复
    public function destroy(Topic $topic, Reply $reply)
    {
        if ($reply->topic_id != $topic->id) {
            return $this->response->errorBadRequest();
        }

        $this->authorize('destroy', $reply);
        $reply->delete();

        return $this->response->noContent();
    }
}
