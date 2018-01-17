<?php
namespace app\admin\controller;

use think\Controller;
use app\admin\model\User as UserModel;
class User extends Controller
{
    public function login()
    {
        return $this->fetch();
    }
    public function dologin(UserModel $user)
    {
        $username = $_POST['name'];
        $pwd = md5($_POST['password']);
        $data = $user->checkUser($username, $pwd);
        if ($data) {
            $_SESSION['uid'] = $data[0]['id'];
            $this->success('登录成功！','/admin/index/index');
        }else{
            exit("<script>alert('账号或密码错误');window.location.href='/admin/user/login'</script>");
        }
    }
    public function user(UserModel $user)
    {
        if(empty($_POST)){
            $result = $user->select();
            $this->assign('result', $result);
        }else{
            $id = $_POST['id'];
            $id = join(',', $id);
            $result = $user->where("id in ($id)")->delete();
            if($result){
                header('location:/admin/user/user');
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
}