# OssUploader #

> 实现简单上传文件到阿里OSS，这个实现需要配合授权系统

## 安装 ##
```shell
composer require plume/oss-uploader
```

## 说明 ##

### 配置 ###
需要使用者配置的项：
1. 授权系统域名
2. 项目名称(如:'nhds')
3. 兵器名称
4. 环境('dev/test/pro')

### 请求说明 ###
内部实现使用了curl请求权限，curl超时时间为10秒，如果请求时间超过10秒，则请求权限失败。
目前项目只有'nhds'和'huishen'可用，如果请求其他的项目权限，则请求权限失败。

### 例子 ###
[例子](examples/)
