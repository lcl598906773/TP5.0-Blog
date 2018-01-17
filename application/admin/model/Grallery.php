<?php
namespace app\admin\model;
use think\Model;

class Grallery extends Model
{
    public function insertdata($data)
    {
        return db('grallery')->insertGetId($data);
    }
    public function selectPhoto()
    {
        return db('grallery')->field('id,desc')->where('parent_id',0)->select();
    }

}