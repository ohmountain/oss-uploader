<?php

require_once(__DIR__.'/../vendor/autoload.php');

use Plume\Uploader\OssUploader;

$server  = 'http://storage.f5fz.cn:12080';      // 授权系统域名
$project = 'nhds';                                  // 项目
$module  = 'lottery';                               // 兵器名称
$env     = 'dev';                                   // 环境

$uploader = new OssUploader($server, $project, $module, $env);


$url = $uploader->upload(__DIR__.'/file.png');

echo $url;

