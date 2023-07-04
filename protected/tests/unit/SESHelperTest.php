<?php

use PHPUnit\Framework\TestCase;

class SESHelperTest extends TestCase
{
    public function testSendMails()
    {
        // Create an instance of SESHelper
        $sesHelper = new SESHelper();

        // Define the recipient email, subject, and body
        $recEmails = ['20jayanth02@gmail.com'];
        $subject = 'Test Email';
        $body = 'This is a test email.';

        // Send the email
        $result = $sesHelper->sendMails($recEmails, $subject, $body);

        // Assert that the email was sent successfully
        $this->assertArrayHasKey('MessageId', $result);
        $this->assertNotNull($result['MessageId']);
    }
}
