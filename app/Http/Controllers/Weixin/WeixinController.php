<?php

namespace App\Http\Controllers\Weixin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App\Model\Weixin\Weixin;
use GuzzleHttp\Client;
class WeixinController extends Controller
{

    public function atoken(){
        echo $this->token();
    }
    //处理首次接入GET请求
    public function valid(){
        echo $_GET['echostr'];
    }
    //接收微信推送 post
    public function event(){
        $content = file_get_contents("php://input");
        $time = date('Y-m-d H:i:s');
        $str = $time.$content."\n";
        is_dir('logs') or mkdir('logs',0777,true);
        file_put_contents("logs/wx_event.log",$str,FILE_APPEND);
        $data = simplexml_load_string($content);
        $openid = $data->FromUserName;
        $wxid = $data->ToUserName;
        $event = $data->Event;

        //扫码关注
        if($event=='subscribe'){
            //根据openid判断用户是否已存在
            $localuser = Weixin::where(['openid'=>$openid])->first();
            if($localuser){
                //用户关注过
                echo '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$wxid.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. '欢迎回来 '. $localuser['nickname'] .']]></Content></xml>';
            }else{
                //用户关注aa
                //获取用户信息
                $aa = $this->getuser($openid);
                echo '<pre>';print_r($aa);echo '</pre>';

                //用户信息入户
                $aa_info = [
                    'openid' => $aa['openid'],
                    'nickname' => $aa['nickname'],
                    'sex' => $aa['sex'],
                    'headimgurl' => $aa['headimgurl'],
                    'subscribe_time' => $aa['subscribe_time'],
                ];
                $id = Weixin::insertGetId($aa_info);
                echo '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$wxid.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. '欢迎关注 '. $aa_info['nickname'] .']]></Content></xml>';
            }
        }else{

        }



    }
    //获取token
    public function token(){
        $key = 'wx_access_token';
        $token = Redis::get($key);
        if($token){
            echo "you:";
        }else{
            echo "meiyou:";
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".env('WX_APPID')."&secret=".env('WX_APPSECRET');
            $response = file_get_contents($url);
            $arr  = json_decode($response,true);
            Redis::set($key,$arr['access_token']);
            Redis::expire($key,200);
            $token = $arr['access_token'];
        }
        return $token;
    }
    //获取微信用户信息
    public function getuser($openid){
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$this->token()."&openid=".$openid."&lang=zh_CN";
        $data = file_get_contents($url);
        $aa = json_decode($data,true);
        return  $aa;
    }
    //创建菜单
    public function createmenu()
    {
        // url
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->token();
        // 接口数据
        $post_arr = [
            'button'    => [
                [
                    'type'  => 'click',
                    'name'  => '高祥栋最帅是不是',
                    'key'   => 'key_menu_001'
                ],
                [
                    'type'  => 'click',
                    'name'  => '是',
                    'key'   => 'key_menu_002'
                ],
            ]
        ];
        $json_str = json_encode($post_arr,JSON_UNESCAPED_UNICODE);  //处理中文编码
        // 发送请求
        $clinet = new Client();
        $response = $clinet->request('POST',$url,[      //发送 json字符串
            'body'  => $json_str
        ]);
        //处理响应
        $res_str = $response->getBody();
        $arr = json_decode($res_str,true);
        //判断错误信息
        if($arr['errcode']>0){
            echo "创建失败";
        }else{
            echo "创建成功";
        }
    }

}
