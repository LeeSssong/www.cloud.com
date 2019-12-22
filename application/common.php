<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

use think\Request;
use OSS\OssClient;
use think\Config;
use OSS\Core\OssException;

$config = Config::get('aliyun_oss');
Request::instance()->ossClient = new OssClient($config['KeyId'],$config['KeySecret'],$config['Endpoint']);
