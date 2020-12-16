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
    use Traits\ActiveUserHelper;
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

    public function setPasswordAttribute($value)
    {
        //因为之前的插件，ResetPasswordController重置密码的时候，它会自动hash加密密码，
        //所以要讲加密，未加密两种情况区分开来（以后说不定会有更多的地方会修改密码）
        if(strlen($value != 60))
        {
            //长度不是60，则未经过加密
            $value = bcrypt($value);
        }
        $this->attributes['password'] = $value;
    }

    public function setAvatarAttribute($path)
    {
        //如果不是'http'子串开头，那就是从管理后台上传的，需要补全URL
        if(! \Str::startsWith($path, 'http')){

            //拼接完整的url
            $path = config('app.url') . '/uploads/images/avatars/' . $path;
        }

        $this->attributes['avatar'] = $path;
    }
}
