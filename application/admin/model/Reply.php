<?php
namespace app\admin\model;
use think\Model;

class Reply extends Model
{
    public function selectdata()
    {
        $sql = "select a.name,b.content,b.replytime,b.id,c.title,b.tid from blog_reply as b left join blog_user as a on a.id=b.authorid left join blog_article as c on b.tid=c.id";
        return db('reply')->query($sql);
    }
}