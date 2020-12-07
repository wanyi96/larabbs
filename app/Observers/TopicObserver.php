<?php

namespace App\Observers;

use App\Models\Topic;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class TopicObserver
{
    public function creating(Topic $topic)
    {
        //
    }

    public function updating(Topic $topic)
    {
        //
    }

    // 观察者类里的方法名对应 Eloquent 想监听的事件
    public function saving(Topic $topic)
    {
        //根据body字段的内容来生成摘要字段的内容
        $topic->excerpt = make_excerpt($topic->body);
    }
}
