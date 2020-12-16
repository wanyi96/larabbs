<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Observers\UserObserver;
use App\Observers\TopicObserver;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Support\Facades\Schema; //add fixed sql

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (app()->isLocal()) {
            $this->app->register(\VIACreative\SudoSu\ServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
	{
		\App\Models\User::observe(\App\Observers\UserObserver::class);
		\App\Models\Reply::observe(\App\Observers\ReplyObserver::class);
		\App\Models\Topic::observe(\App\Observers\TopicObserver::class);
		\App\Models\Link::observe(\App\Observers\LinkObserver::class);
        Schema::defaultStringLength(191); //add fixed sql,队列执行报错，似乎是mysql版本过低，上网查了下，补充这行代码
        //

        //Horizon访问权限
        \Horizon::auth(function($request){
            //判断是否是站长
            return \Auth::user()->hasRole('Founder');
        });
    }

}
