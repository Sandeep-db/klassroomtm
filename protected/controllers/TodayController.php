<?php

class TodayController extends Controller
{
    public function filters()
    {
        return [
            'accessControl',
        ];
    }

    public function accessRules()
    {
        return [
            [
                'deny',
                'actions' => ['index', 'schedule', 'ping'],
                'expression' => 'Yii::app()->user->getState("role") == ""',
                'deniedCallback' => function () {
                    $url = Yii::app()->createUrl('user/login');
                    Yii::app()->request->redirect($url);
                },
            ],
        ];
    }

    public function actionIndex()
    {
        $user_mail = $_COOKIE['email'];
        $data = Yii::app()->cache->get($user_mail);
        if ($data) {
            return $this->render('classes', ['classes' => $data]);
        }
        $this->redirect($this->createAbsoluteUrl('home/index'));
    }

    public function actionSchedule($class_id)
    {
        $today_date = date('Y-m-d');
        $criteria = new EMongoCriteria();
        $criteria->addCond('course_code', '==', $class_id);
        $criteria->addCond('enddate', '>=', $today_date);
        $schedule = Schedules::model()->find($criteria);
        if ($schedule && $schedule->startdate <= $today_date && $today_date <= $schedule->enddate) {
            $schedule = json_decode(json_encode($schedule));
            return $this->render('schedule', ['data' => $schedule]);
        }
        return $this->render('schedule', ['data' => '']);
    }

    public function actionPing()
    {
        if (isset($_POST['query'])) {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'http://192.168.35.74:3000/bard',
                // CURLOPT_URL => 'http://192.168.1.1:3000/bard',
                // CURLOPT_URL => 'http://172.16.112.227:3000/bard',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode([
                    'query' => $_POST['query'],
                ]),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ]);
            $response = curl_exec($curl);
            curl_close($curl);
            echo $response;
        } else {
            echo json_encode(['error' => 'Invalid query']);
        }
    }
}
