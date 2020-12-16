<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CalculateActiveUser extends Command
{
    //供我们调用命令
    protected $signature = 'larabbs:calculate-active-user';

    //命令的描述
    protected $description = '生成活跃用户';

    //命令的描述
    public function __construct()
    {
        parent::__construct();
    }

    //最终执行的方法
    public function handle()
    {
        //在命令行打印一行信息
        $this->info('开始计算。。。');

        $user->calculateAndCacheActiveUsers();

        $this->infk('成功生成！');
    }
}
