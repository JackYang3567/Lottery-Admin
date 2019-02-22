<?php
namespace app\home\controller;

use think\Controller;
use think\Db;
use think\facade\Request;
use app\home\model\ApiConfig;
use app\home\model\ApiBetting;
use app\home\model\ApiGame;
use app\home\model\SystemConfig;
use app\home\model\User;
use app\home\model\CapitalAudit;
use think\Exception;

class ApiFish extends Common 
{
  protected $mtoken = '1234567';
  public function test(){
    // print_r($this->rlist);
  }
  public function _initialize(){
    // print_r(Requset::method());die();
    // if(Requset::method()){
    //   $post_data = input('post.');
    //   $model = SystemConfig::get(59);
    //   $model->value = json_encode($post_data);
    //   $model->save();
    //   die();
    // }
  }
  
  /**
   * 取得游戏连接
   */
  public function playerLine(){
      $url = 'http://api.cqgame.games/gameboy/player/sw/lobbylink';
      $post_data = [
        'account' => '123456',    //玩家账号
        'lang' => 'zh-cn',         //语言
      ];
      $rs = $this->request_on($url,$post_data);
      print_r(json_decode($rs,true));
  }

  public function unicodeDecode($unicode_str){
    $json = '{"str":"'.$unicode_str.'"}';
    $arr = json_decode($json,true);
    if(empty($arr)) return '';
    return $arr['str'];
  }



  /**
   * 查询游戏厂商列表 
   */
  public function halls(){
    $rs = $this->request_on('http://api.cqgame.games/gameboy/game/halls','',false);
    print_r($rs);
  }

  /**
   * 获取游戏列表
   */
  public function gamehall(){
    $rs = $this->request_on('http://api.cqgame.games/gameboy/game/list/cq9','',false);
    print_r($rs);
  }
//-------------------------------------这一部分为对外接口-----------------------------------------------------
  /**
   * 登录时会使用检测是否有该用户
   */
  public function player($account){
    $return_data = [
      "data" => false,
      "status" => [
        "code" => '1006',
        "message" => "Not this account",
        "datetime" => date('Y-m-d\Th:i:sP')
      ]
    ];
    //Db::table('system_config')->where('id',59)->update(['value'=>json_encode($data)]);
    if(Db::table('user')->where('username','=',$account)->find()){
      $return_data['data'] = true;
      $return_data['status']['code'] = '0';
      $return_data['status']['message'] = 'Success';
    }
    return $return_data;
  }

  /**
   * 获取用户余额 
   * @param string $account
   */
  public function balance($account){
    //在连接是获取用户余额
    $muser = User::where('username','=',$account)->find();
    $return_data = [
      'data' => [
        'balance' => 0,
        'currency' => 'CNY'
      ],
      'status' => [
        'code' => '0',
        'message' => 'Success',
        'datetime' => date('Y-m-d\Th:i:sP'),
      ]
    ];
    if(empty($muser)){
      $return_data['status']['code'] = '1006';
      $return_data['status']['messgae'] = 'error';
    }else{
      $return_data['data']['balance'] = round($muser->money,2);
    }
    return $return_data;
  }

  /**
   * 老虎机下注
   */
  public function bet(){
    $post_data = input('post.');
    $user = User::userNameGet($post_data['account']);
    $return_data = [
      'balance' => 0,
      'currency' => 'CNY',
      'status' => [
        'code' => '1005',
        'message' => 'error',
        'datetime' => date('Y-m-d\Th:i:sP')
      ]  
    ];
    if(empty($user)){
      return $return_data;
    }else if($post_data['amount'] <= 0){
      $return_data['status']['code'] = '1003';
      return $return_data;
    }
    $api_config = ApiConfig::codeGetConfig($post_data['gamehall']);
    if(empty($api_config)){
      return $return_data;
    }elseif( !ApiBetting::checkGameCode($post_data['mtcode'],4) ){
      $return_data['status']['code'] = '2009';
      return $return_data;
    }
    //添加注单
    $add = [
      'username' => $user->username,
      'user_id' => $user->id,
      'mtcode' => $post_data['mtcode'],
      'game_id' => $post_data['roundid'],
      'kind_id' => $post_data['gamecode'],
      'api_id' => $api_config->id,
      'cell_score' => $post_data['amount'],
      'all_bet' => $post_data['amount'],
      'game_start_time' => date('Y-m-d H:i:s',strtotime($post_data['eventTime'])),
      'type' => 1
    ];
    if(!ApiBetting::create($add)){
      return $return_data;
    }
    $config = [
      'uid' => $user->id,
      'money' => $post_data['amount'],
      'type' => 0,
      'explain' => 'cq9老虎机'
    ];
    if(moneyAction($config)['code']){
      $return_data['balance'] = round(($user->money - $post_data['amount']),2);
      $return_data['status']['code'] = '0';
      $return_data['status']['message'] = 'Success';
    }
    return $return_data;
  }

  /**
   * 结束回合统计赢分
   */
  public function endround(){
    $post_data = input('post.');

    $user = User::userNameGet($post_data['account']);
    $return_data = [      
      'balance' => 0,
      'currency' => 'CNY',
      'status' => [
        'code' => '1005',
        'message' => 'error',
        'datetime' => date('Y-m-d\Th:i:sP')
      ]  
    ];
    if(empty($user)){
      return $return_data;
    }
    $api_config = ApiConfig::codeGetConfig($post_data['gamehall']);
    if(empty($api_config)){
      return $return_data;
    }
    $add = [];
    $win = 0;
    $post_data['data'] = json_decode($post_data['data'],true);
    foreach($post_data['data'] as $v){
      if($v['amount'] <= 0){
        $return_data['status']['code'] = '1003';
        return $return_data;
      }else if(!ApiBetting::checkGameCode($v['mtcode'],4)){
        $return_data['status']['code'] = '2009';
        return $return_data;
      }
      $win += $v['amount'];
      $add[] = [
        'username' => $user->username,
        'user_id' => $user->id,
        'mtcode' => $v['mtcode'],
        'game_id' => $post_data['roundid'],
        'kind_id' => $post_data['gamecode'],
        'api_id' => $api_config->id,
        'cell_score' => $v['amount'],
        'all_bet' => $v['amount'],
        'game_start_time' => date('Y-m-d H:i:s',strtotime($v['eventtime'])),
        'type' => 1
      ];
    }
    if(!(new ApiBetting)->saveAll($add)){
      return $return_data;
    }
    $config = [
      'uid' => $user->id,
      'money' => $win,
      'type' => 3,
      'explain' => 'cq9统计赢分'
    ];
    if(moneyAction($config)['code']){
      $return_data['balance'] = round(($user->money + $win),2);
      $return_data['status']['code'] = '0';
      $return_data['status']['message'] = 'Success';
    }
    return $return_data;
  }
  
  /**
   * 进入棋牌 携带金额 供棋牌调用
   */
  public function rollout(){
    $post_data = input('post.');
    $user = User::userNameGet($post_data['account']);
    $return_data = [
      'balance' => 0,
      'currency' => 'CNY',
      'status' => [
        'code' => '1005',
        'message' => 'error',
        'datetime' => date('Y-m-d\Th:i:sP')
      ]  
    ];
    if(empty($user)){
      return $return_data;
    }elseif($post_data['amount'] <= 0){
      return $return_data;
    }

    $config = [
      'uid' => $user->id,
      'money' => $post_data['amount'],
      'type' => 19,
      'explain' => 'cq9棋牌上分'
    ];
    if(moneyAction($config)['code']){
      $return_data['balance'] = round(($user->money - $post_data['amount']),2);
      $return_data['status']['code'] = '0';
      $return_data['status']['message'] = 'Success';
    }
    return $return_data;
  }

  /**
   * 把玩家所有钱领出,转入捕鱼
   */
  public function takeall(){
    $post_data = input('post.');
    $user = User::userNameGet($post_data['account']);
    $return_data = [
      'data' => [
        'amount' => $user->money,
        'balance' => 0,
        'currency' => 'CNY'
      ],
      'status' => [
        'code' => '1005',
        'message' => 'error',
        'datetime' => date('Y-m-d\Th:i:sP')
      ]  
    ];
    if(empty($user)){
      return $return_data;
    }

    $config = [
      'uid' => $user->id,
      'money' => $user->money,
      'type' => 19,
      'explain' => 'cq9捕鱼上分'
    ];
    $mtip = moneyAction($config);
    if($mtip['code']){
      $return_data['data']['balance'] = round( User::get($user->id)->money ,2);
      $return_data['status']['code'] = '0';
      $return_data['status']['message'] = 'Success';
    }
    return $return_data;
  }

  /**
   * 捕鱼游戏结束(包括棋牌结束) 金额转入钱包
   */
  public function rollin(){
    $post_data = input('post.');
    $model = SystemConfig::get(59);
    $model = json_encode($post_data);
    $model->save();
    $user = User::userNameGet($post_data['account']);
    $return_data = [
      'balance' => 0,
      'currency' => 'CNY',
      'status' => [
        'code' => '1005',
        'message' => 'error',
        'datetime' => date('Y-m-d\Th:i:sP')
      ]  
    ];
    if(empty($user)){
      return $return_data;
    }elseif($post_data['amount'] <= 0){
      return $return_data;
    }
    if($post_data['gametype'] == 'fish'){
      //捕鱼游戏转出
      $config = [
        'uid' => $user->id,
        'money' => $post_data['amount'],
        'type' => 20,
        'explain' => 'cq9捕鱼下分'
      ];
      $api_config = ApiConfig::codeGetConfig($post_data['gamehall']);
      if(empty($api_config)){
        return $return_data;
      }
      //记录捕鱼游戏内容
      if($post_data['bet'] > 0){
        $add = [
          'username' => $user->username,
          'user_id' => $user->id,
          'mtcode' => $post_data['mtcode'],
          'game_id' => $post_data['roundid'],
          'kind_id' => $post_data['gamecode'],
          'api_id' => $api_config->id,
          'cell_score' => $post_data['bet'],
          'all_bet' => $post_data['bet'],
          'type' => 2,
          'game_start_time' => date('Y-m-d H:i:s',strtotime($post_data['eventTime']))
        ];
      }
    }elseif($post_data['gametype'] == 'table'){
      //棋牌结束下分
      $config = [
        'uid' => $user->id,
        'money' => $post_data['amount'],
        'type' => 20,
        'explain' => 'cq9棋牌下分'
      ];
      $api_config = ApiConfig::codeGetConfig($post_data['gamehall']);
      if(empty($api_config)){
        return $return_data;
      }
      if(!ApiBetting::checkGameCode($post_data['mtcode'],4)){
        $return_data['status']['code'] = '2009';
        return $return_data;
      }
      //记录本次棋牌内容
      if($post_data['bet'] > 0){
        $add = [
          'username' => $user->username,
          'user_id' => $user->id,
          'mtcode' => $post_data['mtcode'],
          'game_id' => $post_data['roundid'],
          'kind_id' => $post_data['gamecode'],
          'api_id' => $api_config->id,
          'cell_score' => $post_data['bet'],
          'all_bet' => $post_data['bet'],
          'profit' => $post_data['win'],
          'revenue' => $post_data['rake'],
          'game_start_time' => date('Y-m-d H:i:s',strtotime($post_data['eventTime']))
        ];
      }
    }
    if(isset($add)){
      ApiBetting::create($add);
    }
    if(moneyAction($config)['code']){
      $return_data['balance'] = round(($user->money + $post_data['amount']),2);
      $return_data['status']['code'] = '0';
      $return_data['status']['message'] = 'Success';
    }
    return $return_data;
  }

  /**
   * 做补款和扣款接口 (暂时未确认)
   * @param array $post_data post获取参数
   * @param boolean $type 做补款还是做扣款
   */
  public static function dcbit($post_data,$type=false){
    $user = User::userNameGet($post_data['account']);
    $return_data = [
      'balance' => 0,
      'currency' => 'CNY',
      'status' => [
        'code' => '1005',
        'message' => 'error',
        'datetime' => date('Y-m-d\Th:i:sP')
      ]  
    ];
    if(empty($user)){
      return $return_data;
    }elseif($post_data['amount'] <= 0){
      $return_data['status']['code'] = '1003';
      return $return_data;
    }
    $api_config = ApiConfig::codeGetConfig($post_data['gamehall']);
    if(empty($api_config)){
      return $return_data;
    }elseif(!ApiBetting::checkGameCode($post_data['mtcode'],4)){
      $return_data['status']['code'] = '2009';
      return $return_data;
    }
    $add = [
      'username' => $user->username,
      'user_id' => $user->id,
      'mtcode' => $post_data['mtcode'],
      'game_id' => $post_data['roundid'],
      'kind_id' => $post_data['gamecode'],
      'api_id' => $api_config->id,
      'cell_score' => $post_data['amount'],
      'all_bet' => $post_data['amount'],
      'type' => 1,
      'game_start_time' => date('Y-m-d H:i:s',strtotime($post_data['eventTime']))
    ];
    ApiBetting::create($add);
    //棋牌结束下分
    $config = [
      'uid' => $user->id,
      'money' => $post_data['amount'],
      'type' => $type ? 3 : 0,
      'explain' => $type ? 'cq9订单补款' : 'cq9订单扣款'
    ];
    if(moneyAction($config)['code']){
      if($type){
        $return_data['balance'] = round(($user->money+$post_data['amount']),2);
      }else{
        $return_data['balance'] = round(($user->money-$post_data['amount']),2);
      }
      $return_data['status']['code'] = '0';
      $return_data['status']['messgae'] = 'Success';
    }
    return $return_data;
  }

  /**
   * 针对完成的订单做扣款
   */
  public function debit(){
    $post_data = input('post.');
    return $this->dcbit($post_data,false);
  }

  /**
   * 针对完成的订单做补款
   */
  public function credit(){
    $post_data = input('post.');
    return $this->dcbit($post_data,true);
  }
  /**
   * 活动派彩 (暂时未确认)
   */
  public function payoff(){
    $post_data = input('post.');
    $user = User::userNameGet($post_data['account']);
    $return_data = [
      'balance' => 0,
      'currency' => 'CNY',
      'status' => [
        'code' => '1005',
        'message' => 'error',
        'datetime' => date('Y-m-d\Th:i:sP')
      ]  
    ];
    if(empty($user)){
      return $return_data;
    }elseif($post_data['amount'] <= 0){
      $return_data['status']['code'] = '1003';
      return $return_data;
    }
    $api_config = ApiConfig::codeGetConfig($post_data['gamehall']);
    if(empty($api_config)){
      return $return_data;
    }elseif(!ApiBetting::checkGameCode($post_data['mtcode'],4)){
      $return_data['status']['code'] = '2009';
      return $return_data;
    }
    $add = [
      'username' => $user->username,
      'user_id' => $user->id,
      'mtcode' => $post_data['mtcode'],
      'api_id' => $api_config->id,
      'type' => 3,
      'game_start_time' => date('Y-m-d H:i:s',strtotime($post_data['eventTime']))
    ];
    ApiBetting::create($add);
    //棋牌结束下分
    $config = [
      'uid' => $user->id,
      'money' => $post_data['amount'],
      'type' => 5,
      'explain' => $post_data['remark']
    ];
    if(moneyAction($config)['code']){
      $return_data['balance'] = round(($user->money+$post_data['amount']),2);
      $return_data['status']['code'] = '0';
      $return_data['status']['messgae'] = 'Success';
    }
    return $return_data;
  }
  /**
   * 押注退还  (暂时未确认)
   */
  public function refund(){
    $post_data = input('post.');
    $return_data = [
      'balance' => 0,
      'currency' => 'CNY',
      'status' => [
        'code' => '1005',
        'message' => 'error',
        'datetime' => date('Y-m-d\Th:i:sP')
      ]  
    ];
    $rs = ApiBetting::where('mtcode','=',$post_data['mtcode'])->where('type','=',4)->find();
    if(empty($rs)){
      return $return_data;
    }
    //押金退还
    $config = [
      'uid' => $rs->user_id,
      'money' => $rs->cell_score,
      'type' => 21,
      'explain' => '押金退还'
    ];
    if(moneyAction($config)['code']){
      $return_data['balance'] = User::get($rs->user_id)->money;
      $return_data['status']['code'] = '0';
      $return_data['status']['messgae'] = 'Success';
    }
    return $return_data;
  }
  /**
   * 查询交易记录
   */
  public function record($mtcode){
    $rs = ApiBetting::where('mtcode','=',$mtcode)->where('type','=',4)->find();
    $return_data = [
      'data' => [
        '_id' => ''
      ],
      'status' => [

      ]
    ];
  }
  //cq9游戏接口的token 暂时固定
  private $auth = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyaWQiOiI1YzNjNTE0MmE1NDhiMzAwMDE4MDgwOTkiLCJhY2NvdW50IjoiR0siLCJvd25lciI6IjViYTljYjY0ZTQxYTJlMDAwMTdiNDkxYiIsInBhcmVudCI6IjViYTljYjY0ZTQxYTJlMDAwMTdiNDkxYiIsImN1cnJlbmN5IjoiQ05ZIiwianRpIjoiOTgzNTc5MzAiLCJpYXQiOjE1NDc0NTY4MzQsImlzcyI6IkN5cHJlc3MiLCJzdWIiOiJTU1Rva2VuIn0.VxQ-N3OWNKI4kMW4DesaifYlyuW0Lj5PIMn6pfn0iN0'; 
  /**
   * 模拟post进行url请求
   * @param string $url
   * @param string $param
   * @param string $type  定义请求为post 还是get
   */
  private function request_on($url = '', $param = '',$type = true) {
    if (empty($url)) {
        return false;
    }
    //设置头
    $header = [
      'Content-Type: application/x-www-form-urlencoded',
      'Authorization: '.$this->auth
    ];

    $ch = curl_init();//初始化curl
    //当需要通过curl_getinfo来获取发出请求的header信息时，该选项需要设置为true
    // curl_setopt($ch, CURLINFO_HEADER_OUT, true);

    curl_setopt($ch, CURLOPT_URL,$url);//抓取指定网页
    if($header != ''){ //是否设置头
      curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }
    // 返回 response_header, 如果不为 true, 只会获得响应的正文
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
    if($type){
      curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
      $postData = http_build_query($param);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    }else{
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //  跳过证书检查
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
    }
    $data = curl_exec($ch);//运行curl
    curl_close($ch);
    return $data;
  }
}