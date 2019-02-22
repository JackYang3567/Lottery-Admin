<?php
namespace app\home\controller;
use think\Db;

class LotteryX extends Lottery
{
  public function rule(){
    return [
      'q1zx' => function($code){
        return count($code[0]);
      },
      'q2zx' => function($code){
        $len0 = count($code[0]);
        $len1 = count($code[1]);
        if($len0 < 1 || $len1 < 1){
          return 0;
        }
        $zhushu = 0;
        for ($i = 0; $i < $len0; $i++){
          for ($k = 0; $k < $len1; $k++){
            if($code[0][$i] != $code[1][$k]){
              $zhushu++;
            }
          }
        }
        return $zhushu;
      },
      'q3zx' => function($code){
        $len0 = count($code[0]);
        $len1 = count($code[1]);
        $len2 = count($code[2]);
        if($len0 < 1 || $len1 < 1 || $len2 < 1){
          return 0;
        }
        $zhushu = 0;
        for($i = 0; $i < $len0; $i++){
          for ($k = 0; $k < $len1; $k++){
             for ($n = 0; $n < $len2; $n++){
              if($code[0][$i] != $code[2][$n] && $code[1][$k] != $code[2][$n] && $code[0][$i] != $code[1][$k]){
                $zhushu++;
              }
            }
          }
        }
        return $zhushu;
      },
      'rx' => function($code,$m){
        $len = count($code[0]);
        $zhushu = 0;
        if(is_numeric($m)){
          $zhushu = $this->combinationBasic($len,$m);
        }
        return $zhushu;
      }
    ];
  }

  public function verification($key1,$key2,$code){
    $m = 0;
    switch ($key2) {
      case 'rx1':
        $key2 = 'q1zx';
        break;
      case 'rx2':
      case 'rx3':
      case 'rx4':
      case 'rx5':
      case 'rx6':
      case 'rx7':
      case 'rx8':
        $m = preg_replace('/[^0-9]/','',$key2);
        $key2 = 'rx';
        break;
    }
    return $this->rule()[$key2]($code,$m);
  }

  // 如果彩种没有规律，在这里处理倒计时和期数
  // return ['expect' => '','time' => '']
  public function nowData(){
  }

  /**
   * 输入注单的所有验证,如果有验证写入此处！！验证通过为true
   * @param string $key1  投注key1值
   * @param string $key2  
   * @param string $code  投注组合的值
   * @param array $config   对应彩种数据结构
   * @return blooean 是否验证通过
   */
  public function checkAll($key1,$key2,$code,$config){
    if($key1 == 'bzwf'){
      foreach($code as $vo1){
        foreach($vo1 as $vo2){
          if( !is_int((int)$vo2) || $vo2 > 11  || $vo2 < 1 ){
            return false;
          }
        }
      }
    }
    return true;
  }

  public function betting(){
    // 这里传入需要验证的玩法
    return $this->bettingAction(['bzwf']);
  }

}
