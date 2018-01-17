<?php
namespace app\admin\model;
use think\Model;

class Article extends Model
{
    public function insert($data)
    {
        return db('article')->insertGetId($data);
    }
    public function selectArticle()
    {
        return db('article')->select();
    }

}