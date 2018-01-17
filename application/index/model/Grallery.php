<?php
namespace app\index\model;
use think\Model;

class Grallery extends Model
{
    public function selectImg()
    {
        return db('grallery')->where('parent_id',0)->select();
    }



}