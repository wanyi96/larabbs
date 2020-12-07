<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //告知laravel此模型在创建和更新时不需要维护created_at和updated_at这两个字段
    public $timestamps = false;

    protected $fillable = [
        'name','description',
    ];
}
