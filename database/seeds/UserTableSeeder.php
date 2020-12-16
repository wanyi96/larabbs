<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //生成数据集合
        $users = factory(User::class)->times(10)->create();

        //单独处理第一个用户的数据
        $user = User::find(1);
        $user->name = 'Wanyi';
        $user->email = '408629610@qq.com';
        $user->avatar = 'https://cdn.learnku.com/uploads/images/201710/14/1/ZqM7iaP4CR.png';
        $user->save();

        //初始化用户角色，将1号用户指派为站长;assignRole()方法在HasRoles中定义
        $user->assignRole('Founder');

        //将2号用户指派为管理员
        $user = User::find(2);
        $user->assignRole('Maintainer');
    }
}
