<?php
namespace app\home\model;
use think\Model;

class ApiBetting extends Model
{
    /**
     * 检查是否有重试code码
     * @param string $mtcode
     * @param number $id 对应的厂商id
     */
    public static function checkGameCode($mtcode,$id){
        if(self::where('api_id','=',$id)->where('mtcode','=',$mtcode)->find()){
            return false;
        }
        return true;
    }
}
