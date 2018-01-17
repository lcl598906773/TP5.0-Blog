<?php
namespace app\index\controller;

use app\index\model\User;
use app\index\model\Article;
use app\index\model\Reply;
use app\index\model\Comment;
use app\index\model\Grallery;
use think\Controller;

class Index extends Controller
{
    public function index(Article $article)
    {
        $data = $article->blogList();
        $this->assign('data', $data);
        return $this->fetch();
    }
    public function about()
    {
        return $this->fetch();
    }
    public function blog(Article $article)
    {
        $data = $article->selectCount();
        $this->assign('data', $data);
        return $this->fetch();
    }
    public function gallery(Grallery $grallery)
    {
       $img =  $grallery->selectImg();
        $this->assign('img', $img);
        return $this->fetch();
    }
    public function contacts(User $user)
    {
        $data = $user->selectuser(['id'=>1]);
        $this->assign('data', $data);
        return $this->fetch();
    }
    public function docontacts(Comment $comments)
    {
        $data['name'] = trim($_POST['name']);
        $data['email'] = trim($_POST['email']);
        $data['comments']= $_POST['comments'];
        if($comments->insert($data)){
            $this->success('回复成功！');
        }
    }
    public function blog_details(Article $article)
    {
        if (empty($_GET['id'])) {
            $this->error('非法操作！');
        }
        $id = (int)$_GET['id'];
        $data =  $article->blogDetails($id);
        $result = $article->blogComment($id);
        $this->assign('data', $data);
        $this->assign('result', $result);
        return $this->fetch();
    }
    public function leaveComment(Reply $reply, Article $article)
    {
        if (empty(session('uid'))) {
            $this->error('请先登录！');
        }
        $data['tid'] = $_GET['id'];
        $data['authorid'] = session('uid');
        $data['content']  = $_POST['message'];
        $data['replytime'] = time();
        if($reply->insert($data)){
            $tid = $_GET['id'];
            $article->where("id=$tid")->setInc('replycount',1);
            $this->success('回复成功！');
        }else{
            $this->error('回复失败！');
        }
    }
}
