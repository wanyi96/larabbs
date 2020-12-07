<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Topic;

class TopicPolicy extends Policy
{
    //默认传当前用户对象为参数1；判断帖子的user_id是否是当前用户
    public function update(User $user, Topic $topic)
    {
        return $user->isAuthOf($topic);
    }

    public function destroy(User $user, Topic $topic)
    {
        return $user->isAuthOf($topic);
    }
}
