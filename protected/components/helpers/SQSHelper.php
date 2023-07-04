<?php

use Aws\Sqs\SqsClient;

class SQSHelper extends CComponent
{
    public $queueUrl;
    public $sqsClient;

    public function __construct()
    {

        $this->sqsClient = new SqsClient([
            'version' => 'latest',
            'region' => 'us-east-2',
            'credentials' => [
                'key' => 'AKIAVZH4B37COJVTQUE3',
                'secret' => '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',
            ],
        ]);
        $this->queueUrl = 'https://sqs.us-east-2.amazonaws.com/397813080004/dqueue';
    }

    public  function sendMessage($messageBody)
    {
        $result = $this->sqsClient->sendMessage([
            'QueueUrl' => $this->queueUrl,
            'MessageBody' => $messageBody,
        ]);
        if ($result['MessageId']) {
            return "Message added successfully with ID: " . $result['MessageId'];
        } else {
            return "Error adding message to SQS queue";
        }
    }

    public function receiveMessage()
    {
        $result = $this->sqsClient->receiveMessage([
            'QueueUrl' => $this->queueUrl,
            'MaxNumberOfMessages' => 10,
            'WaitTimeSeconds' => 5,
        ]);

        $msgs = [];

        if ($result['Messages']) {
            foreach ($result['Messages'] as $message) {
                // Process the received message
                $messageBody = $message['Body'];
                $messageReceiptHandle = $message['ReceiptHandle'];

                // Do something with the message body
                $msgs[] = $messageBody;

                // Delete the message from the queue
                $this->deleteMessage($messageReceiptHandle);
            }
        } else {
            $msgs[] = "No messages available in the queue";
        }

        return $msgs;
    }

    public function deleteMessage($receiptHandle)
    {
        $this->sqsClient->deleteMessage([
            'QueueUrl' => $this->queueUrl,
            'ReceiptHandle' => $receiptHandle,
        ]);
    }
}

