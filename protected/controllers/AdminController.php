<?php

class AdminController extends Controller
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
                'actions' => ['index', 'users', 'user', 'class', 'searchusers, viewevent', 'searchclass'],
                'expression' => 'Yii::app()->user->getState("role") !== "admin"',
                'deniedCallback' => function () {
                    $url = Yii::app()->createUrl('home/index');
                    Yii::app()->request->redirect($url);
                },
            ],
        ];
    }

    public function actionIndex()
    {
        $this->layout = 'admin';
        $today = date('Y-m-d');
        $no_of_class = Courses::model()->startAggregation()
            ->addStage(['$count' => 'total'])
            ->aggregate();
        $finshed_classes = Courses::model()->startAggregation()
            ->match(['course_end_date' => [
                '$lt' => $today
            ]])
            ->addStage(['$count' => 'total'])
            ->aggregate();
        $notstarted_classes = Courses::model()->startAggregation()
            ->match(['course_start_date' => [
                '$gt' => $today
            ]])
            ->addStage(['$count' => 'total'])
            ->aggregate();
        $teachers = Users::model()->startAggregation()
            ->match(['role' => 'teacher'])
            ->addStage(['$count' => 'total'])
            ->aggregate();
        $students = Users::model()->startAggregation()
            ->match(['role' => 'student'])
            ->addStage(['$count' => 'total'])
            ->aggregate();
        $data = [
            'total_classes' => $no_of_class['result'][0]['total'],
            'finshed_classes' => count($finshed_classes['result'])
                ? $finshed_classes['result'][0]['total'] : 0,
            'notstarted_classes' => count($notstarted_classes['result'])
                ? $notstarted_classes['result'][0]['total'] : 0,
            'teachers' => $teachers['result'][0]['total'],
            'students' => $students['result'][0]['total'],
        ];
        return $this->render('index', [
            'data' => $data
        ]);
    }

    public function actionClass($page_no)
    {
        $this->layout = 'admin';
        $limit = 6;
        $skip = ($page_no - 1) * $limit;
        $classes = Courses::model()->startAggregation()
            ->addStage(['$skip' => $skip])
            ->addStage(['$limit' => $limit])
            ->addStage(['$project' => [
                'course_name' => 1,
                'course_code' => 1,
                'course_description' => 1,
                'course_instructor' => 1,
            ]])
            ->aggregate();
        return $this->render('class', ['classes' => $classes['result']]);
    }

    public function actionUsers($page_no)
    {
        $this->layout = 'admin';
        $skip = ($page_no - 1) * 8;
        $limit = 9;
        $pipeline = [
            ['$skip' => $skip],
            ['$limit' => $limit],
        ];
        $model = new Users();
        $cursor = $model->getCollection()->aggregate($pipeline);
        $arr = array_map(function ($v) {
            return [
                'name' => $v->name,
                'email' => $v->email,
                'role' => $v->role,
                '_id' => strval($v->_id)
            ];
        }, iterator_to_array($cursor));
        return $this->render('users', ['users' => $arr]);
    }

    public function actionSearchUsers()
    {
        $this->layout = 'admin';
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'search-user') {
            $name = strtolower($_POST['name']);
            $pattern = '/^' . $name . '/';
            $users = Users::model()->startAggregation()
                ->match(['sname' => [
                    '$regex' => new MongoRegex($pattern),
                ]])
                ->addStage(['$limit' => 9])
                ->addStage(['$project' => [
                    'name' => 1,
                    'email' => 1,
                    'role' => 1,
                ]])
                ->aggregate();
            echo json_encode($users['result']);
        }
    }

    public function actionSearchClass()
    {
        $this->layout = 'admin';
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'search-class') {
            $course_code = $_POST['class_id'];
            $pattern = '/^' . $course_code . '/';
            $classes = Courses::model()->startAggregation()
                ->match(['course_code' => [
                    '$regex' => new MongoRegex($pattern),
                ]])
                ->addStage(['$limit' => 8])
                ->addStage(['$project' => [
                    'course_name' => 1,
                    'course_code' => 1,
                    'course_description' => 1,
                    'course_instructor' => 1,
                ]])
                ->aggregate();
            echo json_encode($classes['result']);
        }
    }

    public function actionUser()
    {
        $this->layout = 'admin';
        if (!isset($_POST['user_mail'])) {
            return $this->redirect('index');
        }
        $email = $_POST['user_mail'];
        $user = Users::model()->findByAttributes([
            'email' => $email,
        ]);
        $user = json_encode($user);
        $this->render('user', ['classes' => $user]);
    }

    public function actionViewEvent($page_no)
    {
        $this->layout = 'admin';
        if (isset($_POST['course_code'])) {
            $event = new Events();
            $event->attributes = $_POST;
            if ($_POST['source'] != 'youtube') {
                $file = CUploadedFile::getInstanceByName('file_link');
                if ($file) {
                    $s3 = new S3Helper();
                    if ($file->getTempName() && $file->getTempName() != '') {
                        $file_name = $_POST['course_code'] . '/event' . '/' . uniqid();
                        $file_link = $s3->putObject($file_name, $file);
                        $file_link['name'] = $file->name;
                        $event->file_link = $file_link;
                    }
                }
            }
            $event->save();
        }
        $limit = 4;
        $skip = ($page_no - 1) * $limit;
        $events = Events::model()->startAggregation()
            ->match(['course_code' => 'course-0000'])
            ->addStage(['$skip' => $skip])
            ->addStage(['$limit' => $limit])
            ->aggregate();
        return $this->render('viewevent', ['events' => $events['result']]);
    }

    public function actionTest()
    {
        $pipeline = [
            [
                '$group' => [
                    '_id' => [
                        'role' => '$role'
                    ],
                    'count' => [
                        '$sum' => 1
                    ]
                ]
            ],
        ];
        $model = new Users();
        $cursor = $model->getCollection()->aggregate($pipeline);
        $x = iterator_to_array($cursor);
        foreach ($x as $value) {
            print_r($value->toArray());
            echo "<br />";
        }
        return;
        $arr = array_map(function ($v) {
            return [
                'name' => $v->name,
                'email' => $v->email,
                'role' => $v->role,
                '_id' => strval($v->_id)
            ];
        }, iterator_to_array($cursor));
        foreach ($arr as $value) {
            print_r($value);
            echo "<br />";
        }
    }
}
