<?php
namespace app\admin\model;
use think\Model;

class User extends Model
{
    public function checkUser($username, $pwd)
    {
        $data = $this->where("name='$username' and pwd='$pwd' and level=1")
            ->field('id,name,level')
            ->limit('1')
            ->select();
        return $data;
    }

}