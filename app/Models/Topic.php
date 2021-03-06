<?php

namespace App\Models;

class Topic extends Model
{
    protected $fillable = ['title', 'body', 'category_id', 'excerpt', 'slug'];
    // 获取话题对应的分类
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    // 获取话题对应的作者
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // 按不同的排序，使用不同的数据读取逻辑
    public function scopeWithOrder($query, $order)
    {
        // 不同的排序，使用不同的数据读取逻辑
        switch ($order) {
            case 'recent':
                $query->recent();
                break;

            default:
                $query->recentReplied();
                break;
        }
        // 预加载防止 N+1 问题
        return $query->with('user', 'category');
    }
    // 按更新时间排序
    public function scopeRecentReplied($query)
    {
        // 当话题有新回复时，我们将编写逻辑来更新话题模型的 reply_count 属性，
        // 此时会自动触发框架对数据模型 updated_at 时间戳的更新
        return $query->orderBy('updated_at', 'desc');
    }
    // 按照创建时间排序
    public function scopeRecent($query)
    {
        // 按照创建时间排序
        return $query->orderBy('created_at', 'desc');
    }
    // 将show改为link
    public function link($params = [])
    {
        return route('topics.show', array_merge([$this->id, $this->slug], $params));
    }
    // 一篇帖子下有多条回复
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }
    // 话题的前 5 条回复数据
    public function topReplies()
    {
        return $this->replies()->limit(5);
    }
}
