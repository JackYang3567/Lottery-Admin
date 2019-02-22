<?php
namespace app\home\model;
use think\Model;

class ApiGame extends Model
{
    public static function game($api){
        return self::field('id as code,name')->where('api_id','=',$api)->where('switch','=',1)->select();
    }
}
