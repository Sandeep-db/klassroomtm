<?php

use MongoDB\BSON\ObjectId;

class CalendarNotifyCommand extends CConsoleCommand
{
    public function run($args)
    {
        // $course_id = 'flask-2023';
        // $schedule_id = new ObjectId('64870bc84ad87020f00ac602');

        $course_id = $args[0];
        $schedule_id = new ObjectId($args[1]);

        $schdeule = Schedules::model()->findByPk($schedule_id);
        $event_variable = [];
        $event_variable['start_date'] = $schdeule->startdate . "T00:00:00";
        $event_variable['end_date'] = $schdeule->enddate . "T23:59:59";
        $event_variable['summary'] = "Topics you will learn are " . join(", ", $schdeule->topic);

        $users = CalendarEvents::model()->startAggregation()
            ->match(['schedule_id' => $schedule_id])
            ->addStage(['$lookup' => [
                'from' => 'googletokens',
                'localField' => 'email',
                'foreignField' => 'email',
                'as' => 'tokens'
            ]])
            ->addStage(['$unwind' => [
                'path' => '$tokens'
            ]])
            ->aggregate();

        $OauthComponent = new OAuth();
        $client = $OauthComponent::$client;

        foreach ($users['result'] as $userCode) {
            try {
                $_id  = $userCode['_id'];
                print_r($_id);
                $eventId = $userCode['event_id'];
                $tmp = json_decode($userCode['tokens']['auth_data']);
                $accessToken = $OauthComponent->processAccessTokens($tmp);
                echo $eventId . "\n";
                $client->setAccessToken($accessToken);
                $calendarService = new \Google_Service_Calendar($client);
                $calendarService->events->delete('primary', $eventId);
                echo "Event deleted successfully.\n";
            } catch (Exception $e) {
                if ($e->getCode() != 410) {
                    $this->deleteQueue($userCode['tokens']['email'], $eventId);
                }
                echo "An error occurred: " . $e->getCode() . "\n";
            }
            $result = CalendarEvents::model()->deleteByPk($_id);
        }

        $course = Courses::model()->findByAttributes([
            'course_code' => $course_id
        ]);

        foreach ($course->course_students as $user_details) {
            $user_mail = $user_details['email'];
            $userCode = GoogleTokens::model()->findByAttributes([
                'email' => $user_mail
            ]);
            if (!$userCode) {
                $this->insertQueue($user_mail, $event_variable, $schedule_id);
                continue;
            }
            try {
                $tmp = json_decode($userCode->auth_data);
                $accessToken = $OauthComponent->processAccessTokens($tmp);
                $client->setAccessToken($accessToken);
                
                $calendarService = new \Google_Service_Calendar($client);
                $event = new \Google_Service_Calendar_Event([
                    'summary' => $event_variable['summary'],
                    'start' => [
                        'dateTime' => $event_variable['start_date'],
                        'timeZone' => 'Asia/Kolkata',
                    ],
                    'end' => [
                        'dateTime' => $event_variable['end_date'],
                        'timeZone' => 'Asia/Kolkata',
                    ],
                ]);

                $calendarId = 'primary';
                $newEvent = $calendarService->events->insert($calendarId, $event);
                $eventId = $newEvent->getId();

                echo "\n $eventId \n";

                $calendarEvent = new CalendarEvents();
                $calendarEvent->schedule_id = $schedule_id;
                $calendarEvent->email = $user_mail;
                $calendarEvent->event_id = $eventId;

                $criteria = [
                    'schedule_id' => $schedule_id,
                    'email' => $user_mail,
                ];

                $calendarEvent->save(false, [
                    'upsert' => true,
                    'criteria' => $criteria
                ]);
            } catch (Exception $e) {
                $this->insertQueue($user_mail, $event_variable, $schedule_id);
                echo "New event not added: " . $e->getMessage() . "\n";
            }
        }
    }

    private function insertQueue($user_mail, $event_variable, $schedule_id)
    {
        $queueElement = new EventQueue();
        $queueElement->email = $user_mail;
        $queueElement->type = 'insert';
        $queueElement->schedule_id = $schedule_id;
        $queueElement->event = $event_variable;
        $queueElement->save();
    }

    private function deleteQueue($user_mail, $eventId)
    {
        $queueElement = new EventQueue();
        $queueElement->email = $user_mail;
        $queueElement->type = 'delete';
        $queueElement->event_id = $eventId;
        $queueElement->save();
    }
}
