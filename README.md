#使用说明
目前只支持普通上传  

###配置
```
//filesystem.php
'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => 'your-key',
            'secret' => 'your-secret',
            'region' => 'your-region',
            'bucket' => 'your-bucket',
        ],

        'oss' => [
            'driver' => 'oss',
            'accessKeyId' => 'xxxx',
            'accessKeySecret' => 'xxxx',
            'endpoint' => 'xxxx',
            'bucket' => 'xxxx'
        ]

    ],
```
###注册服务容器
```
namespace App\Providers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Jiangyunan\Flysystem\Adapter\OssAdapter;
use League\Flysystem\Filesystem;
use OSS\OssClient;

class OssServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Storage::extend('oss', function($app, $config){
            $client = new OssClient($config['accessKeyId'], $config['accessKeySecret'], $config['endpoint']);
            return new Filesystem(new OssAdapter($client, $config['bucket']));
        });
    }

    public function register()
    {
        //
    }
}
```