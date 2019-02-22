<?php
namespace app\prize\controller;

class Ks extends Lottery
{
  public $data_chat;
  public $prize_code_type;

  public function prize(){
    $this->actionPrize();
  }

  public function action($key1,$key2,$value,$money,$other,$odds){
    $key_chat = $key1;
    $return_data = [
      'code' => 0,
      'num' => 0
    ];
    switch ($key1) {
      case 'zh':
        $this->data_chat = array_sum($this->prize_code);
        break;
    }

    $this->prize_code_type = [
      'bz' => ($this->prize_code[0] == $this->prize_code[1] && $this->prize_code[1] == $this->prize_code[2]),
      'sz' => (function($prize_code){
        sort($prize_code);
        $return_data = false;
        if(($prize_code[0] + 1) == $prize_code[1] && ($prize_code[1] + 1) == $prize_code[2]){
          $return_data = true;
        }
        return $return_data;
      })($this->prize_code),
      'dz' => ($this->prize_code[0] == $this->prize_code[1] || $this->prize_code[1] == $this->prize_code[2] || $this->prize_code[2] == $this->prize_code[0]),
      'bs' => (function($prize_code){
        sort($prize_code);
        $return_data = false;
        if(($prize_code[0] + 1) == $prize_code[1] || ($prize_code[1] + 1) == $prize_code[2]){
          $return_data = true;
        }
        return $return_data;
      })($this->prize_code),
    ];

    if($this->rule()[$key_chat][$key2]($value)){
      $return_data['code'] = 1;
      $return_data['num'] = $money * $odds;
    }
    return $return_data;
  }

  public function rule(){
    return [
      'xt' => [
        // 开奖号码全部相同,则中奖
        'bz' => function($value){
          if($this->prize_code_type['bz']){
            return true;
          }else{
            return false;
          }
        },
        // 开奖号码的个位十位百位数字能相连，不分顺序。
        'sz' => function($value){
          if($this->prize_code_type['sz']){
            return true;
          }else{
            return false;
          }
        },
        // 开奖号码有两个号码相等,不包括豹子
        'dz' => function($value){
          if(!$this->prize_code_type['bz'] && $this->prize_code_type['dz']){
            return true;
          }else{
            return false;
          }
        },
        // 中奖号码的个位十位百位任意两位数字相连，不分顺序。（不包括顺子、对子。）
        'bs' => function($value){
          if(!$this->prize_code_type['sz'] && !$this->prize_code_type['dz'] && $this->prize_code_type['bs']){
            return true;
          }else{
            return false;
          }
        },
        // 不包括豹子、对子、顺子、半顺的所有中奖号码
        'zl' => function($value){
          if(!$this->prize_code_type['bz'] && !$this->prize_code_type['dz'] && !$this->prize_code_type['sz'] && !$this->prize_code_type['bs']){
            return true;
          }else{
            return false;
          }
        },
      ],
      'zh' => [
        'code_3' => function($value){
          if($this->data_chat == 3){
            return true;
          }else{
            return false;
          }
        },
        'code_4' => function($value){
          if($this->data_chat == 4){
            return true;
          }else{
            return false;
          }
        },
        'code_5' => function($value){
          if($this->data_chat == 5){
            return true;
          }else{
            return false;
          }
        },
        'code_6' => function($value){
          if($this->data_chat == 6){
            return true;
          }else{
            return false;
          }
        },
        'code_7' => function($value){
          if($this->data_chat == 7){
            return true;
          }else{
            return false;
          }
        },
        'code_8' => function($value){
          if($this->data_chat == 8){
            return true;
          }else{
            return false;
          }
        },
        'code_9' => function($value){
          if($this->data_chat == 9){
            return true;
          }else{
            return false;
          }
        },
        'code_10' => function($value){
          if($this->data_chat == 10){
            return true;
          }else{
            return false;
          }
        },
        'code_11' => function($value){
          if($this->data_chat == 11){
            return true;
          }else{
            return false;
          }
        },
        'code_12' => function($value){
          if($this->data_chat == 12){
            return true;
          }else{
            return false;
          }
        },
        'code_13' => function($value){
          if($this->data_chat == 13){
            return true;
          }else{
            return false;
          }
        },
        'code_14' => function($value){
          if($this->data_chat == 14){
            return true;
          }else{
            return false;
          }
        },
        'code_15' => function($value){
          if($this->data_chat == 15){
            return true;
          }else{
            return false;
          }
        },
         'code_16' => function($value){
           if($this->data_chat == 16){
             return true;
           }else{
             return false;
           }
         },
        'code_17' => function($value){
          if($this->data_chat == 17){
            return true;
          }else{
            return false;
          }
        },
         'code_18' => function($value){
           if($this->data_chat == 18){
             return true;
           }else{
             return false;
           }
         },
        'da' => function($value){
          if($this->data_chat > 10){
            return true;
          }else{
            return false;
          }
        },
        'x' => function($value){
          if($this->data_chat < 11){
            return true;
          }else{
            return false;
          }
        },
        'dan' => function($value){
          if($this->data_chat % 2){
            return true;
          }else{
            return false;
          }
        },
        's' => function($value){
          if($this->data_chat % 2){
            return false;
          }else{
            return true;
          }
        }
      ]
    ];
  }
}
