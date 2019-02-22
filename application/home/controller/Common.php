<?php
namespace app\home\controller;
use think\Controller;
use Firebase\JWT\JWT;
use think\captcha\Captcha;
use think\Db;
use app\home\model\SystemConfig;

class Common extends Controller
{
  // 如果有构造函数tp的 initialize  会失效
  // public function __construct()
  // {
  //   // $config = $this->getBasic();
  //   // if ($config['web_state'] == 0) {
  //   //   echo json_encode($config);
  //   //   exit();
  //   // }
  // }

  // jwt key 默认
  public $JwtKey = 'sb288789';
  // 用户登陆有效时间（分钟，如果用户有动作，则刷新失效时间）
  public $JwtTime = 2000;
  // 身份令牌
  public $token = '';

  public function initialize(){
    // 这里设置 session id
    $is_session_id = input('param._chat_id');
    // if(SystemConfig::get('3')->value == 0){
    //   return ['code'=>-1,'msg'=>'系统维护'];
    // }
    if(empty($is_session_id)){
      // 这里如果后台调用前台方法时,session机制会报错，这里屏蔽错误
      try{ session_start(); } catch (\Exception $e) {}
    }else{
      session_id($is_session_id);
    }
    $headers = apache_request_headers();
    $get_token = input('get.token');
    if(!empty($get_token)){
      // 这里条件如果为真,下方条件不再管
    }
    else if(!empty(cookie('token'))){
      $get_token = cookie('token');
    }
    else if(isset($headers['Accept']) && !empty($headers['Accept'])){
      $get_token = $headers['Accept'];
      cookie('token', null);
    }
    if(!empty($get_token)){
      try {
        $decode = JWT::decode((empty($get_token) ? $headers['Accept'] : $get_token),$this->JwtKey);
        if(is_object($decode)){
          $decode = (array) $decode;
          if(isset($decode['uid']) || !empty($decode['uid'])){
            $this->infoAll($decode['uid']);
          }
        }
      }catch (\Exception $e) {

        $this->token = $e->getMessage();
        if($this->token == 'Expired token'){
          header('pragma:autoOut');
          cookie('token', null);
        }
      }
    }
    if(method_exists($this,'_initialize')){
      $this ->_initialize();
    }
  }

  // 更新令牌过期时间和更新会员信息
  public function infoAll($uid){
    $value = $this->JwtEncode(['uid'=>$uid]);
    $this->token = $uid;
    $data = $this->checkLogin();
    $data['data']['web_state'] = SystemConfig::get(3)->value;
    $data['data']['web_login'] = SystemConfig::get(40)->value;
    //  = $bit->value;
    // print_r($bit);
    // 这里是进入后台地址后的令牌
    if(cookie('token') || input('get.token')){
      cookie('token', $value);
    }
    if($data['code']){
      unset($data['data']['password']);
      $value .= '||' . json_encode($data['data']);
      // 预留更新表登录
      $chack_login = Db::table('login_log')->where('user_id',$uid)->order('create_time','DESC')->find();

      if($data['data']['status'] == 1){
        // 判断冻结
        header('pragma:frozen');
        cookie('token', null);
      }else if(1 || $chack_login['ip'] == request()->ip() && str_replace(' ', '',$chack_login['browser']) == str_replace(' ', '',$_SERVER['HTTP_USER_AGENT']) ){
        header('pragma:' . $value);
        Db::table('user')->where('id',$uid)->update(['active_time'=>time(),'active_ip'=>request()->ip()]);
      }else{
        // 判断挤下线
        header('pragma:drop');
      }
    }
  }

  /* 加密JWT令牌 */
  public function JwtEncode($data = []){
    $data += [
      'iat' => time(),
      'exp' => time() + $this->JwtTime * 60
    ];
    return JWT::encode($data,$this->JwtKey);
  }

  /* 用户等级 $uid 用户id */
  public function userLevel($uid){
    //查询设置的等级
      $level = Db::table('user_rank')->order('rank','ASC')->select();
      $userlevel = 1;
    //查询该用户的累计流水
      $get = Db::table('accumulation')->order('create_time','desc')->where('user_id',$uid)->find();
      if($get){
        foreach ($level as $key => $value) {
          $value['condition'] = json_decode($value['condition'],true);
                //流水判断                                                                    //判断充值
          if( ($get['use_money']+$get['winning']) < $value['condition']['account'] || ($get['in_money']+$get['online_money']) < $value['condition']['recharge'] )
          {
            break;
          }
          $userlevel = $value['rank'];
        }
      }
    return $userlevel;
  }

  /**
   *  验证用户有没有登陆，登陆了，返回用户信息;
   */
  public function checkLogin(){
    $return_data = [
      'code' => 0,
      'msg' => '您还没有登陆',
      'data' => []
    ];
    // echo $this->token;
    if(!empty($this->token)){
      if(is_numeric($this->token)){
        $user = db('user',[],false)->field('id,username,money,password,point,type,status,no_money,photo,group,active_time')->where(['id'=>$this->token])->find();
        if(empty($user)){
          $return_data['msg'] = '登陆异常,请您重新登陆';
        }else{
          $user['level'] = 1;//gradeJudgement($user['id']);
          $return_data['code'] = 1;
          $return_data['msg'] = '身份通过';
          $return_data['data'] = $user;
        }
      }
    }
    return $return_data;
  }

  /* 验证码获取 */
  public function verify($config = ''){
    $_config =    [
      // 验证码字符集
      'codeSet'     => '0123456789',
      // 验证码字体大小
      'fontSize'    =>    40,
      // 验证码位数
      'length'      =>    4,
      // 关闭验证码杂点
      'useNoise'    =>    false,
      // 开关混淆曲线
      'useCurve'    =>    false,
      // 开关杂点
      'useNoise'    =>    true,
    ];
    if(!empty($config)){
      $_config = $config + $_config;
    }
    else if(!empty($this->verify_config)){
      $_config = $this->verify_config + $_config;
    }
    ob_clean();
    $captcha = new Captcha($_config);
    return $captcha->entry();
  }

  /* 验证码验证 */
  public function verifyCheck($value){
    return (new Captcha())->check($value);
  }
}
