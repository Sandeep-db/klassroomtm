<?php

class DailyNotifyCommand extends CConsoleCommand
{
    public function run($args)
    {
        $email_client = new SESHelper();
        $courses = Courses::model()->findAll();
        var_dump(count($courses));
        echo "\n";
        $today_date = Date("Y-m-d");
        foreach ($courses as $course) {
            $criteria = new EMongoCriteria();
            $criteria->addCond('course_code', '==', $course->course_code);
            $criteria->addCond('startdate', '<=', $today_date);
            $criteria->addCond('enddate', '>=', $today_date);
            $today_schedule = Schedules::model()->find($criteria);
            if (!$today_schedule) {
                continue;
            }
            $body = $this->getTable($today_schedule);
            foreach ($course->course_students as $student) {
                try {
                    $result = $email_client->sendMails(
                        [$student['email']],
                        "Daily remainder on course: " . $course->course_code,
                        $body
                    );
                    Yii::log($student['email'] . " " . $result['MessageId'], 'error', 'system');
                } catch (Exception $e) {
                    Yii::log($student['email'] . " not sent", 'error', 'system');
                }
            }
            echo "Course_code: {$course->course_code}\n";
        }
    }

    private function getTable($schedule)
    {
        $n = count($schedule->learningOutcome);
        $table = <<< EOL
            <table border="1">
                <thead>
                    <tr>
                        <th>Topics</th>
                        <th>Learning Outcomes</th>
                        <th>Hours</th>
                    </tr>
                </thead>
                <tbody>
        EOL;
        for ($i = 0; $i < $n; $i++) {
            $table .= <<< EOL

                <tr>
                    <td>{$schedule->topic[$i]}</td>
                    <td>{$schedule->learningOutcome[$i]}</td>
                    <td>{$schedule->hours[$i]}</td>
                </tr>
            EOL;
        }
        $table .= <<< EOL

                </tbody>
            </table>
        EOL;
        return $table;
    }
}
