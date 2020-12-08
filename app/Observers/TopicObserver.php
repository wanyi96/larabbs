<?php

namespace App\Observers;

use App\Models\Topic;
use App\Handlers\SlugTranslateHandler;
use App\Jobs\TranslateSlug;

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
        //过滤表单提交内容，HTMLPurifier白名单中未指定的html属性，css属性，html标签都会被过滤
        //参数1是传入的数据，参数二是要过滤的规则，在配置文件中配置好
        $topic->body = clean($topic->body,'user_topic_body');

        //根据body字段的内容来生成摘要字段的内容
        $topic->excerpt = make_excerpt($topic->body);

    }

    public function saved(Topic $topic)
    {
        //如果slug字段无内容，即使用翻译器对title进行翻译
        if(! $topic->slug){
            //将slug翻译的调用修改为队列执行的方式
            // $topic->slug = app(SlugTranslateHandler::class)->translate($topic->title);
            dispatch(new TranslateSlug($topic));
        }
    }
}
