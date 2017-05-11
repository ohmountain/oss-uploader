<?php

/******************/

//
//  实现简单的向阿里OSS上传文件的功能
//
//  这个实现需要配合授权系统才能完成上传功能
//
//  @author renshan<1005110700@qq.com>

/******************/

namespace Nineteen\Uploader;

use OSS\OssClient;

class OssUploader
{
    private $env;
    private $domain;
    private $module;
    private $project;
    private $credentials;
    private $permission_server;

    private $endpoint = 'oss-cn-hangzhou.aliyuncs.com';

    /**
     * 构造器
     *
     * @param string $permission_server 上传授权服务器地址，即前端JS调用那个JS的域名如：http://storage.f5fz.cn:端口^_^
     * @param string $project           上传的哪一个项目，目前有年会大师(nhds)和会神(huishen)
     * @param string $module            上传的兵器，如照片墙picture-wall
     * @param string $env               程序环境：dev、test、pro
     */
    public function __construct(string $permission_server, string $project, string $module, string $env = 'dev')
    {
        $this->module               = $module;
        $this->project              = $project;
        $this->permisssion_server   = $permission_server;

        $env = strtolower($env);

        if ($env !== 'dev' && $env !== 'pro' && $env !== 'test') {
            $env = 'dev';
        }

        $this->env = $env;
    }

    /**
     * 向授权服务器请求上传权限
     *
     * @return array
     */
    private function requestPermission()
    {
        if (count($this->credentials) > 0) {
            return $this->credentials;
        }

        $ch = curl_init();

        $url = rtrim($this->permisssion_server, '/') . '/write';

        $data = array(
            'project'   => $this->project,
            'module'    => $this->module,
            'feature'   => $this->env,
            'username'  => 'some'
        );

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $output = curl_exec($ch);

        $json = json_decode($output, true);

        if (json_last_error() !== 0) {
            $json = array();
        }

        if (array_key_exists('domain', $json)) {
            $this->domain = $json['domain'];
        }

        if (array_key_exists('credentials', $json)) {
            $this->credentials = $json['credentials'];
        }
    }

    /**
     * 上传文件入口
     *
     * @param string $file    文件地址
     *
     * @throws OSS\Core\OssException
     *
     * @return string         返回文件URL
     */
    public function upload(string $file): string
    {
        if (!is_file($file)) {
            throw new \OSS\Core\OssException("文件不存在: $file");
        }

        $this->requestPermission();

        if ($this->credentials == null || $this->domain == null) {
            throw new \OSS\Core\OssException("请求权限失败");
        }

        $access_key_id     = $this->credentials['AccessKeyId'];
        $access_key_secret = $this->credentials['AccessKeySecret'];
        $security_token    = $this->credentials['SecurityToken'];

        $client = new OssClient($access_key_id, $access_key_secret, $this->endpoint, false, $security_token);

        $name   = uniqid(mt_rand()) .'.'. explode('.', $file)[count(explode('.', $file)) -  1];
        $object = $this->module. '/' . $this->env . '/customize/' . $name;

        $ret_object = $client->uploadFile($this->project, $object, $file);

        return $ret_object['info']['url'];
    }
}
