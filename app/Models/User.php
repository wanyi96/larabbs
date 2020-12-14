<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Auth;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use HasRoles;
    use MustVerifyEmailTrait;
    use Notifiable {
        notify as protected laravelNotify;
    }


    protected $fillable = [
        'name', 'email', 'password','introduction','avatar',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    public function isAuthOf($model)
    {
        return $this->id == $model->user_id;
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function notify($instance)
    {
        //如果要通知的人是当前用户，就不必通知了,自己发的动态，自己评论的时候不会产生通知
        if($this->id == Auth::id()){
            return ;
        }

        //只有数据库类型通知才需要提醒,传入的实例里有todatabase方法的就是数据库类型，
        //直接发送Email或者其他的都pass
        if(method_exists($instance,'toDatabase')){
            $this->increment('notification_count');  //user表中通知字段notification_count总数+1
        }

        $this->laravelNotify($instance);
    }

    public function markAsRead()
    {
        //将所有通知状态设定为已读，并清空未读消息数
        $this->notification_count = 0;
        $this->save();
        $this->unreadNotifications->markAsRead();
    }
}
