<?php
namespace app\index\model;
use think\Model;

class Article extends Model
{
    public function blogList()
    {
        return db('article')->field('id,title,content,addtime,replycount,icon')->order('addtime desc')->limit(5)->select();
    }
    public function blogDetails($id)
    {
        return db('article')
            ->alias('a')
            ->field('a.title,a.id,a.content,a.addtime,a.replycount,a.icon')
            ->where('a.id',$id)
            ->select();
    }
    public function blogComment($id)
    {
        return db('reply')
            ->where('tid',$id)
            ->select();
    }

    public function selectCount()
    {
        return db('article')->select();
    }
}