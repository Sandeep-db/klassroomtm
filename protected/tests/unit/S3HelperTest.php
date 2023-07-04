<?php

use PHPUnit\Framework\TestCase;

class S3HelperTest extends TestCase
{
    public function testPutObject()
    {

        $s3Helper = new S3Helper();

        $filePath = '/data/live/images/instructions/no-honking.svg.jpg';
        $fileName = 'no-honking.svg.jpg';

        $uploadedFile = new CUploadedFile(
            $filePath,
            $filePath,
            'image/jpeg',
            filesize($filePath),
            UPLOAD_ERR_OK
        );


        $result = $s3Helper->putObject($filePath, $uploadedFile);

        $this->assertArrayHasKey('imageURL', $result);
    }
}
