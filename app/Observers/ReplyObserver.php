<?php

namespace App\Observers;

use App\Models\Reply;
use App\Notifications\TopicReplied;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class ReplyObserver
{
    public function creating(Reply $reply)
    {
        //过滤表单提交内容，HTMLPurifier白名单中未指定的html属性，css属性，html标签都会被过滤
        //参数1是传入的数据，参数二是要过滤的规则，在配置文件中配置好
        $reply->content = clean($reply->content,'user_topic_body');
    }

    public function created(Reply $reply)
    {
        //评论创建之后，在评论对应的帖子上将字段reply_count评论数量+1。
        // $reply->topic->increment('reply_count',1);

        //创建成功后计算本话题下评论总数，然后再对其reply_count字段赋值
        // $reply->topic->reply_count = $reply->topic->replies->count();
        // $reply->topic->save();

        //运行迁移命令时不执行下列操作
        if(!app()->runningInConsole() ){
            $reply->topic->updateReplyCount();

            //通知话题作者有新的评论
            $reply->topic->user->notify(new TopicReplied($reply));
        }
    }

    public function updating(Reply $reply)
    {
        //
    }

    public function deleted(Reply $reply)
    {
        //删除回复后，帖子的回复总量应该更新
        // $reply->topic->reply_count = $reply->topic->replies->count();
        // return $reply->topic->save();
        $reply->topic->updateReplyCount();
    }
}
