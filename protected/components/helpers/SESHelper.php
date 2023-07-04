<?php

use Aws\Sdk;
use Aws\Ses\SesClient;

require_once('/data/live/protected/components/config.php');

class SESHelper extends CComponent
{
    private $sesClient;
    public function __construct()
    {
        $aws = new Sdk([
            'region' => 'us-east-2',
            'credentials' => [
                'key' => AWS_ACCESS_KEY,
                'secret' => AWS_SECRET_KEY,
            ],
            'version' => 'latest',
        ]);
        $this->sesClient = $aws->createSes();
    }

    public function getParams($recEmails, $subject, $body)
    {
        $params = [
            'Source' => 'sandyblaze954@gmail.com',
            'Destination' => [
                'ToAddresses' => $recEmails,
            ],
            'Message' => [
                'Subject' => [
                    'Data' => $subject,
                    'Charset' => 'UTF-8',
                ],
                'Body' => [
                    'Text' => [
                        'Data' => '',
                        'Charset' => 'UTF-8',
                    ],
                    'Html' => [
                        'Data' => $body,
                        'Charset' => 'UTF-8',
                    ],
                ],
            ],
        ];
        return $params;
    }

    public function sendMails($recEmails, $subject, $body)
    {
        $params = $this->getParams($recEmails, $subject, $body);
        $result = $this->sesClient->sendEmail($params);
        return $result;
    }

}
