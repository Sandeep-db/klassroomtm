<?php
include_once('protected/components/config.php');
require 'vendor/autoload.php';

use Aws\Exception\AwsException;
use Aws\Sns\SnsClient;

class SNSHelper
{

    public static function sendEmail()
    {

        // Set up the AWS SDK credentials and region
        $credentials = array(
            'version' => 'latest',
            'region' => 'us-east-1',
            'credentials' => [
                'key' => "AKIAVZH4B37COJVTQUE3",
                'secret' => "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",
            ],

        );

        // Create a new SNS client
        // $client = SnsClient::factory($credentials);
        $snsClient = new SnsClient($credentials);


        // Set the SNS topic ARN
        $topicArn = 'arn:aws:sns:us-east-2:397813080004:test-topic'; // Update with your topic ARN
        // $attachmentFilePath = 'https://www.google.com/url?sa=i&url=https%3A%2F%2Fblog.darwinbox.com%2Fintroducing-darwinbox-2-better-smarter&psig=AOvVaw3y8ja-ryZZP_WB1z1wsUNl&ust=1684549948422000&source=images&cd=vfe&ved=0CBEQjRxqFwoTCKj6hdyrgP8CFQAAAAAdAAAAABAO'; // Replace with the path to your attachment file
        // Prepare the message parameters


        try {
            // $attachmentData = file_get_contents($attachmentFilePath);
            // $attachment = base64_encode($attachmentData);

            $snsClient->publish([
                'TopicArn' => $topicArn,
                'Message' => 'This is a test email sent using AWS SNS.',
                'Subject' => 'Welcome!',
                'MessageAttributes' => [
                    'SenderID' => [
                        'DataType' => 'String',
                        'StringValue' => 'newsLetter'
                    ],

                ]
            ]);

            echo 'Email sent successfully!';
        } catch (AwsException $e) {
            echo 'Error: ' . $e->getMessage();
        }


        // Publish the message to the SNS topic
        // $result = $client->publish($params);

        // Check if the message was published successfully
        // if ($result['MessageId']) {
        // 	echo 'Email sent successfully.'.$result;
        // } else {
        // 	echo 'Failed to send email.';
        // }
    }
}
