<?php
namespace app\admin\controller;

use app\admin\model\Article;
use app\admin\model\User;
use app\admin\model\Reply;
use app\admin\model\Comment;
use app\admin\model\Grallery;
use think\Controller;
use think\File;

class Index extends Controller
{
    public function grallery(Grallery $grallery)
    {
        if (!empty(input())){
            if (input('photo') == 0){
                $file = request()->file('img');
                if($file){
                    $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                    if($info){
                        $path = $info->getSaveName();
                        $path = '/uploads'. '/' . str_replace('\\','/',$path);
                    }else{
                        $error = $file->getError();
                        $this->error($error);
                    }
                }
                $data = [
                    'parent_id'=>0,
                    'master_img'=>$path,
                    'desc'=>input('desc'),
                    'type'=> input('type'),
                ];
                $res =  $grallery->insertdata($data);
                if ($res) {
                    $this->success('操作成功！');
                }
            }else{
                $file = request()->file('img');
                if($file){
                    $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                    if($info){
                        $path = $info->getSaveName();
                        $path = '/uploads'. '/' . str_replace('\\','/',$path);
                    }else{
                        $error = $file->getError();
                        $this->error($error);
                    }
                }
                $data = [
                    'parent_id'=>input('photo'),
                    'master_img'=>$path,
                    'desc'=>input('desc'),
                    'type'=> input('type'),
                ];
                $res =  $grallery->insertdata($data);
                if ($res) {
                    $this->success('操作成功！');
                }
            }
        }else{
            $desc =  $grallery->selectPhoto();
//            var_dump($desc);
            $this->assign('desc', $desc);
            return $this->fetch();
        }

    }
    public function index()
    {
        return $this->fetch();
    }
    public function pass(User $user)
    {
        if(!empty($_POST)){
            $pwd = trim($_POST['mpass']);
            $newpwd = md5(trim($_POST['newpass']));
            $renewpass = md5(trim($_POST['renewpass']));
            $res = $user->where("name='lcl'")->select();
            if(!strcmp($res[0]['pwd'], $pwd)){
                $this->error('旧密码不正确！');
            }
            if ($newpwd != $renewpass){
                $this->error('两次密码不一致！');
            }
            $data = [
                'pwd' => $newpwd,
            ];
            $result = $user->where("name='lcl'")->update($data);
            if($result){
                $this->success('修改成功！');
            }
        }
        return $this->fetch();
    }
    public function book(Reply $reply,Article $article)
    {
        $res = $reply->selectdata();
        $this->assign('res', $res);
        return $this->fetch();
    }
    public function debook(Reply $reply,Article $article)
    {
        $arr = $_POST['id'];
        foreach ($arr as $key=> $value)
        {
            $tid = array_keys($value);
            $id = array_values($value);
            $result = $reply->where("id in ($id[0])")->delete();
            if ($result){
                $res = $article->where("id=$tid[0]")->setDec('replycount', 1);
                if ($res) {
                    header('location:/admin/index/book');
                }
            }
        }
    }
    public function edit(Article $article)
    {
        $id = $_GET['id'];
        $res = $article->where("id=$id")->select();
        $this->assign('res', $res);
        return $this->fetch();
    }
    public function guestbook(Comment $comment)
    {
        if(empty($_POST)){
            $res = $comment->select();
            $this->assign('res', $res);
            return $this->fetch();
        }else{
            $id = $_POST['id'];
            $id = join(',', $id);
            $result = $comment->where("id in ($id)")->delete();
            if($result){
                header('location:/admin/index/guestbook');
            }
        }
    }
    public function list(Article $article)
    {
        if(empty($_POST)){
            $article = $article->selectArticle();
            $this->assign('article',$article);
        }else{
            $id = $_POST['id'];
            $id = join(',', $id);
            $result = $article->where("id in ($id)")->delete();
            if($result){
                header('location:/admin/index/list');
            }
        }
        return $this->fetch();
    }
    public function riji()
    {
        return $this->fetch();
    }
    public function webinfo(User $user)
    {
        if(empty($_POST)){
            $result = $user->where('level=1')->select();
            $this->assign('result', $result);
            return $this->fetch();
        }else{
            $phone = $_POST['phone'];
            $email = $_POST['email'];
            $weibo = $_POST['weibo'];
            $intro = $_POST['intro'];
            $data = [
                'phone' =>$phone,
                'email' =>$email,
                'weibo' =>$weibo,
                'intro' =>$intro,
            ] ;
            $res = $user->where('level=1')->update($data);
            if($res){
                header('location:/admin/index/webinfo');
            }
        }

    }
    public function post(Article $article)
    {
        $con = htmlspecialchars($_POST["test-editormd-markdown-doc"]);
        $title = $_POST['title'];
        $file = request()->file('file');
        if($file) {
            $info = $file->move(ROOT_PATH . 'public' . DS . 'upload');
            if ($info) {
                $path = '/upload/' . $info->getSaveName();
            } else {
                $this->error($file->getError());
            }
            $data['title'] = $title;
            $data['content'] = addslashes($con);
            $data['addtime'] = time();
            $data['icon'] = $path;
            $res = $article->insert($data);
            if ($res) {
                $this->success('发表成功！');
            } else {
                $this->error('发表失败！');
            }
        }
    }
    public function editarticle(Article $article)
    {
        $id = $_GET['id'];
        $content = $_POST['test-editormd-markdown-doc'];
        $title = $_POST['title'];
        $data['content'] = addslashes($content);
        $data['title'] = trim($title);
        $result = $article->where("id=$id")->update($data);
        if ($result) {
            $this->success('修改成功！','/admin/index/list');
        }
    }
}
