<?php

class StreamNotifyCommand extends CConsoleCommand
{
    public function run($args)
    {
        $course_code = $args[0];
        $email_client = new SESHelper();
        $course = Courses::model()->findByAttributes([
            'course_code' => $course_code,
        ]);
        foreach ($course->course_students as $student) {
            $recEmails[] = $student['email'];
            $body = <<< EOF
                
                <div style="padding: 20px">
                    <p>Cheak out for the new update in the stream of course: <strong>$course_code</strong></p>
                    <div>
                        <a style="background-color: #4CAF50;border: none;color: white;padding: 15px 32px;text-align:center;text-decoration: none;display: inline-block;font-size: 16px; margin-top: 20px">Checkout</a>
                    </div>  
                </div>
            EOF;
            try {
                $result = $email_client->sendMails(
                    [$student['email']],
                    "Stream updated in course: " . $course_code,
                    $body
                );
                Yii::log($student['email'] . " " . $result['MessageId'], 'error', 'system');
            } catch (Exception $e) {
                Yii::log($student['email'] . " not sent", 'error', 'system');
            }
            echo "\n";
        }
    }
}
