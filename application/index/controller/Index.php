<?php
namespace app\index\controller;

use OSS\Core\OssException;
use OSS\OssClient;

use think\facade\Config;
use think\Image;
use think\Request;


class Index extends Base
{
    //应用入口
    public function index()
    {
        $this->redirect('Img/index');
    }

}
