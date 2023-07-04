<?php

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

require_once('/data/live/protected/components/config.php');

class S3Helper extends CComponent
{

    public $s3client;
    public function __construct()
    {
        $this->s3client = new S3Client([
            'version' => 'latest',
            'region' => 'us-east-2',
            'credentials' => [
                'key' => AWS_ACCESS_KEY,
                'secret' => AWS_SECRET_KEY,
            ],
        ]);
    }

    public $bucket_name = 'php-bucket-db';

    public function putObject($name, $file)
    {
        try {
            $res = $this->s3client->putObject([
                'Bucket' => $this->bucket_name,
                'Key' => $name,
                'Body' => fopen($file->getTempName(), 'rb'),
            ]);
            return ['imageURL' => $res['ObjectURL']];
        } catch (Exception $exception) {
            return ['errno' => $exception->getMessage()];
        }
    }

    public function deleteFolder($folder)
    {
        if ($folder === "nothing_to_delete") {
            return ['delete' => true];
        }
        try {
            $this->s3client->deleteMatchingObjects($this->bucket_name, $folder);
            $this->s3client->deleteObject([
                'Bucket' => $this->bucket_name,
                'Key' => $folder,
            ]);
            return ['delete' => true];
        } catch (AwsException $e) {
            return $e->getMessage();
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
            echo "Downloaded the file and it begins with: {$body->read(17)}.\n";
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
