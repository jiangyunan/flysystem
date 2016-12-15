<?php
/**
 * Created by PhpStorm.
 * User: Jya
 * Date: 2016/12/1551
 * Time: 09:55
 */

namespace Jiangyunan\Flysystem\Adapter;

use League\Flysystem\Config;
use OSS\OssClient;

class OssAdapter extends AbstractOssAdapter
{
    protected $client;
    protected $bucket;

    public function __construct(OssClient $client, $bucket)
    {
        $this->client = $client;
        $this->bucket = $bucket;
    }

    public function setBucket($bucket) {
        $this->bucket = $bucket;
    }

    public function write($path, $contents, Config $config)
    {
        $result = $this->client->putObject($this->bucket, $path, $contents);
        return compact('path', 'contents');
    }

    public function writeStream($path, $resource, Config $config)
    {
        $content = stream_get_contents($resource);
        $result = $this->client->putObject($this->bucket, $path, $content);
        return compact('path');
    }

    public function update($path, $contents, Config $config)
    {
        return $this->write($path, $contents, $config);
    }

    public function updateStream($path, $resource, Config $config)
    {
        return $this->writeStream($path, $resource, $config);
    }

    public function rename($path, $newpath)
    {
        if ($path != $newpath) {
            $this->client->copyObject($this->bucket, $path, $this->bucket, $newpath);
            $this->client->deleteObject($this->bucket, $path);
            return true;
        } else {
            return false;
        }
    }

    public function copy($path, $newpath)
    {
        $this->client->copyObject($this->bucket, $path, $this->bucket, $newpath);
        return true;
    }

    public function delete($path)
    {
        $this->client->deleteObject($this->bucket, $path);
        return true;
    }

    public function deleteDir($dirname)
    {
        // TODO: Implement deleteDir() method.
    }

    public function createDir($dirname, Config $config)
    {
        // TODO: Implement createDir() method.
    }

    public function setVisibility($path, $visibility)
    {
        // TODO: Implement setVisibility() method.
    }

    public function has($path)
    {
        return $this->client->doesObjectExist($this->bucket, $path);
    }

    public function read($path)
    {
        $contents = $this->client->getObject($this->bucket, $path);
        return compact('path', 'contents');
    }

    public function readStream($path)
    {
        $localfile = storage_path('tmp/'. $path);
        $options = [
            OssClient::OSS_FILE_DOWNLOAD => $localfile
        ];

        $this->client->getObject($this->bucket, $path, $options);

        $stream = fopen($localfile, 'rb');
        return compact('path', 'stream');
    }

    public function listContents($directory = '', $recursive = false)
    {
        // TODO: Implement listContents() method.
    }

    public function getMetadata($path)
    {
        $objectMeta = $this->client->getObjectMeta($this->bucket, $path);

        return [
            'size' => $objectMeta['Content-Length'],
            'type' => $objectMeta['Content-Type'],
            'path' => $path,
            'timestamp' => $objectMeta['Last-Modified']
        ];
    }

    public function getSize($path)
    {
        return $this->getMetadata($path);
    }

    public function getMimetype($path)
    {
        return $this->getMetadata($path);
    }

    public function getTimestamp($path)
    {
        return $this->getMetadata($path);
    }

    public function getVisibility($path)
    {
        // TODO: Implement getVisibility() method.
    }

}