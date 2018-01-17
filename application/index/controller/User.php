<?php
namespace app\index\controller;

use app\index\model\User as UserModel;
use think\Controller;
use my\Email;

class User extends Controller
{
    public function login(UserModel $user)
    {
        if (!empty($_POST)) {
            if(empty($_POST['username'])){
                exit("<script>alert('请输入用户名');window.location.href='/index/user/login	'</script>");
            }
            $username = trim($_POST['username']);
            $pwd = md5(trim($_POST['pwd']));
            $result = $user->checklogin($username, $pwd);
            if ($result && count($result[0])>0) {
                session('level',$result[0]['level']);
                session('username',$result[0]['name']);
                session('uid',$result[0]['id']);
                $this->success('登录成功！','/index/index/index');
            } else{
                $this->error('用户名或密码错误！');
            }
        }
        return $this->fetch();
    }
    public function logout()
    {
        session('level',null);
        session('username',null);
        session('uid',null);
        $this->success('退出成功','/index/index/index');
    }
    public function register(UserModel $user)
    {
        if (!empty($_POST)) {
            //验证名字是否有重复
            $username = trim($_POST['username']);
            $name = $user->selectuser('name',$username);
            if (mb_strlen($username) >= 12){
                exit("<script>alert('名字过长');window.location.href='/index/user/register'</script>");
            }
            if($name){
                exit("<script>alert('名字已被注册');window.location.href='/index/user/register'</script>");
            }
            //验证密码
            $pwd = trim($_POST['pwd']);
            $repwd = trim($_POST['repwd']);
            if(strcmp($pwd,$repwd)){
                exit("<script>alert('密码不一致');window.location.href='/index/user/register'</script>");
            }
            if(strlen($pwd) < 6){
                exit("<script>alert('密码不够长');window.location.href='/index/user/register'</script>");
            }
            $pwd = md5($pwd);
            //验证邮箱
            $email = trim($_POST['email']);
            $pat = '/\w+@\w+\.(com|cn|net)$/';
            if (!preg_match($pat, $email)) {
                exit("<script>alert('邮箱格式不正确');window.location.href='/index/user/register'</script>");
            }
            //验证手机号
            $phone = trim($_POST['phone']);
            $pat = '/^1[34578]\d{9}$/';
            if (!preg_match($pat, $phone)) {
                exit("<script>alert('手机格式不正确');window.location.href='/index/user/register'</script>");
            }
            //ip
            $ip = $_SERVER['REMOTE_ADDR'];
            if (!strcmp($ip,'::1')){
                $ip = '127.0.0.1';
            }
            $ip = ip2long($ip);
            //验证码的验证
            $captcha = $_POST['yanzheng'];
            if(!captcha_check($captcha)){
                $this->error('验证码不正确！');
            };
            $data = [
                'name' => $username,
                'pwd' => $pwd,
                'email' =>$email,
                'phone'=>$phone,
                'regip'=>$ip,
                'regtime'=>time(),
                'lasttime'=>time()
            ];
            $result = $user->insertGetId($data);
            if(!$result)
            {
                exit("<script>alert('注册失败，请联系管理员');window.location.href='/index/user/register'</script>");
            }else{
                $result = $user->where("id='$result'")->select();
                session('uid',$result[0]['id']);
                session('username',$result[0]['name']);
                session('level',$result[0]['level']);
                $this->success('注册成功','/index/index/index');
            }
        }else{
            return $this->fetch();
        }
    }
    public function findpwd()
    {
        return $this->fetch();
    }
    public function dofindpwd(UserModel $user)
    {
        $username = trim($_POST['username']);
        $phone = trim($_POST['phone']);
        $code = trim($_POST['code']);
        $realcode = session('smscode');
        $result = $user->where("name='$username'")->select();
        if (!$result) {
            $this->error('用户名不存在！');
        }else{
            if ($phone != $result[0]['email']) {
                $this->error('手机号错误！');
            }
        }
        if ($code != $realcode) {
            $this->error('验证码错误！');
        }
        $this->success('验证成功','/index/user/setpwd');
    }
    public function sendSMS()
    {
        $email = input('mobile');
        $mail = new Email();
        $mail->setServer("smtp.exmail.qq.com", "", "");//填写企业邮箱地址和密码
        $mail->setFrom(""); //填写企业邮箱的地址
        $mail->setReceiver("$email");
        $num = rand(100000,999999);
        session('smscode', $num);
        $mail->setMailInfo("验证信息", "<b>您的验证码为$num , 3分钟内有效！</b>");
        return $mail->sendMail();
    }
    public function setpwd(UserModel $user)
    {
        if (empty($_POST)) {
            return $this->fetch();
        }else{
            $username = trim($_POST['username']);
            $result = $user->where("name='$username'")->select();
            if (!$result) {
                $this->error('用户名不存在！');
            }
            $pwd = trim($_POST['pwd']);
            $repwd = trim($_POST['repwd']);
            if(strcmp($pwd,$repwd)){
                exit("<script>alert('密码不一致');window.location.href='/index/user/setpwd'</script>");
            }
            if(strlen($pwd) < 6){
                exit("<script>alert('密码不够长');window.location.href='/index/user/setpwd'</script>");
            }
            $pwd = md5($pwd);
            $data['pwd'] = $pwd;
            $res = $user->where("name='$username'")->update($data);
            if ($res) {
                $this->success('重置成功！请重新登录','/index/user/login');
            }else{
                $this->error('重置失败！','/index/user/setpwd');
            }
        }
    }
}