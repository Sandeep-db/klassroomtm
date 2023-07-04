<?php

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class S3helper extends CComponent
{

    public $s3client;
    public function __construct()
    {
        $this->s3client = new S3Client([
            'version' => 'latest',
            'region' => 'us-east-2',
            'credentials' => [
                'key' => 'AKIAVZH4B37COJVTQUE3',
                'secret' => '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',
            ],
        ]);
    }

    public $bucket_name = 'php-bucket-db';
    public $file_name = 'Demofile';
    public $filePath = '/data/live/css/bg.gif';

    public function putObject($name, $path)
    {

        try {
            $res = $this->s3client->putObject([
                'Bucket' => $this->bucket_name,
                'Key' => $name,
                'SourceFile' => $path
            ]);
            // $this->saveindb($res['ObjectURL']);
            // $event = new S3uploadevent($this);
            // $this->raiseEvent('onTest', $event);
            return "Uploaded $this->file_name to $this->bucket_name. with url {$res['ObjectURL']}\n";
        } catch (Exception $exception) {
            // exit("Please fix error with file upload before continuing.");
            return "Failed to upload $this->file_name with error: " . $exception->getMessage();
        }
    }

    public function saveindb($url)
    {
        $model = new S3demo();
        $model->name = $this->file_name;
        $model->url = $this->filePath;
        if ($model->save()) {
            echo "Saved successfully";
        }
    }

    public function getObject()
    {
        try {
            $file = $this->s3client->getObject([
                'Bucket' => $this->bucket_name,
                'Key' => $this->file_name,
            ]);
            $body = $file->get('Body');
            $body->rewind();
            echo "<pre>";
            print_r($body);
            echo "Downloaded the file and it begins with: {$body->read(26)}.\n";
        } catch (Exception $exception) {
            echo "Failed to download $this->file_name from $this->bucket_name with error: " . $exception->getMessage();
            exit("Please fix error with file downloading before continuing.");
        }
    }

    public function getObjectUrl($name)
    {
        echo "<img src=" . $this->s3client->getObjectUrl($this->bucket_name, $name) . ">";
    }

    public function onTest($event)
    {
        // $this->raiseEvent('onTest', $event);
    }
}
