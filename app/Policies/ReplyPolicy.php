<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Reply;

class ReplyPolicy extends Policy
{
    public function update(User $user, Reply $reply)
    {
        // return $reply->user_id == $user->id;
        return true;
    }

    public function destroy(User $user, Reply $reply)
    {
        //判断id是当是该回复的作者，或者是该帖子的作者才能进行删除动作
        return $user->isAuthOf($reply) || $user->isAuthOf($reply->topic);
    }
}
