<?php

class ReloginCommand extends CConsoleCommand
{
    public function run($args)
    {
        $user_mail = $args[0];
        $pendings = EventQueue::model()->findAllByAttributes([
            'email' => $user_mail
        ]);
        $OauthComponent = new OAuth();
        $client = $OauthComponent::$client;
        $userCode = GoogleTokens::model()->findByAttributes([
            'email' => $user_mail
        ]);
        $tmp = json_decode($userCode->auth_data);
        $accessToken = $OauthComponent->processAccessTokens($tmp);
        foreach ($pendings as $pending) {
            if ($pending->type == 'insert') {
                $this->insertEvent($client, $accessToken, $pending->event, $pending->schedule_id, $user_mail);
            } else if ($pending->type == 'delete') {
                $this->deleteEvent($client, $accessToken, $pending->event_id);
            }
            $result = EventQueue::model()->deleteByPk($pending->_id);
        }
    }

    private function deleteEvent($client, $accessToken, $eventId)
    {
        $client->setAccessToken($accessToken);
        $calendarService = new \Google_Service_Calendar($client);
        try {
            $calendarService->events->delete('primary', $eventId);
            echo "Event deleted successfully.\n";
        } catch (Exception $e) {
            echo "An error occurred: " . $e->getCode() . "\n";
        }
    }

    private function insertEvent($client, $accessToken, $event, $schedule_id, $user_mail)
    {
        $client->setAccessToken($accessToken);
        $calendarService = new \Google_Service_Calendar($client);
        $event = new \Google_Service_Calendar_Event([
            'summary' => $event['summary'],
            'start' => [
                'dateTime' => $event['start_date'],
                'timeZone' => 'Asia/Kolkata',
            ],
            'end' => [
                'dateTime' => $event['end_date'],
                'timeZone' => 'Asia/Kolkata',
            ],
        ]);
        try {
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
            echo "New event not added: " . $e->getMessage() . "\n";
        }
    }
}
