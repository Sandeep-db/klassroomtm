<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

class HomeController extends Controller
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
                'actions' => ['index'],
                'expression' => 'Yii::app()->user->getState("role") === "admin"',
                'deniedCallback' => function () {
                    $url = Yii::app()->createUrl('admin/index');
                    Yii::app()->request->redirect($url);
                },
            ],
            [
                'deny',
                'actions' => ['class', 'index'],
                'expression' => 'Yii::app()->user->getState("role") == ""',
                'deniedCallback' => function () {
                    $url = Yii::app()->createUrl('user/login');
                    Yii::app()->request->redirect($url);
                },
            ],
            [
                'deny',
                'actions' => ['createclass', 'addstudent'],
                'expression' => 'Yii::app()->user->getState("role") == "student"',
                'deniedCallback' => function () {
                    $url = Yii::app()->createUrl('user/login');
                    Yii::app()->request->redirect($url);
                },
            ],
        ];
    }

    public function actionIndex()
    {
        $this->layout = 'main';
        $user_mail = $_COOKIE['email'];
        $data = Yii::app()->cache->get($user_mail);
        if ($data) {
            return $this->render('index', ['classes' => $data]);
        }
        $user = Users::model()->findByAttributes([
            'email' => $user_mail,
        ]);
        $user = json_encode($user);
        Yii::app()->cache->set($user_mail, $user);
        return $this->render('index', ['classes' => $user]);
    }

    public function actionClass()
    {
        if (Yii::app()->user->getState('role') === 'admin') {
            $this->layout = 'admin';
        }
        $type = Yii::app()->request->getParam('filter');
        $topic_name = Yii::app()->request->getParam('topic_name');
        $class_id = Yii::app()->request->getParam('class_id');
        $option = Yii::app()->request->getParam('page');
        if (!$option) {
            $user_mail = $_COOKIE['email'];
            $data = Yii::app()->cache->get($user_mail);
            if ($data) {
                return $this->render('class/index', ['classes' => $data]);
            }
            $user = Users::model()->findByAttributes([
                'email' => $user_mail,
            ]);
            $user = json_encode($user);
            Yii::app()->cache->set($user_mail, $user);
            return $this->render('class/index', ['classes' => $user]);
        }
        $function = $option . "Page";
        $data = $this->$function($class_id, $topic_name, $type);
        return $this->render('class/' . $option, ['class_id' => $class_id, 'data' => $data]);
    }

    public function actionSearchTeacher()
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'search-teacher') {
            $name = strtolower($_POST['name']);
            $pattern = '/^' . $name . '/';
            $users = Users::model()->startAggregation()
                ->match([
                    'sname' => [
                        '$regex' => new MongoRegex($pattern),
                    ],
                    'role' => 'teacher',
                ])
                ->addStage(['$limit' => 5])
                ->addStage(['$project' => [
                    'name' => 1,
                    'email' => 1,
                    '_id' => 0,
                ]])
                ->aggregate();
            echo json_encode($users['result']);
        }
    }

    public function actionCreateClass()
    {
        if (Yii::app()->user->getState('role') === 'admin') {
            $this->layout = 'admin';
        }
        $model = new CourseForm();
        if (isset($_POST['CourseForm'])) {
            $new_course = new Courses();
            $user = Users::model()->findByAttributes([
                'email' => $_POST['CourseForm']['course_instructor']
            ]);
            if (!$user) {
                throw new Exception("Instructor not found");
            }
            $new_course->course_name = $_POST['CourseForm']['course_name'];
            $new_course->course_code = $_POST['CourseForm']['course_code'];
            $new_course->course_description = $_POST['CourseForm']['course_description'];
            $new_course->course_start_date = $_POST['CourseForm']['course_start_date'];
            $new_course->course_end_date = $_POST['CourseForm']['course_end_date'];

            $new_course->course_instructor = new Instructor();
            $new_course->course_instructor->_id = $user->_id;
            $new_course->course_instructor->email = $user->email;
            $new_course->course_instructor->name = $user->name;
            try {
                if ($new_course->validate() && $new_course->save()) {
                    $new_teach = new Teaching();
                    $new_teach->course_id = $new_course->_id;
                    $new_teach->course_code = $new_course->course_code;
                    $new_teach->course_name = $new_course->course_name;
                    $new_teach->course_description = $new_course->course_description;
                    $user->teaching[] = $new_teach;
                    $user->save();
                    Yii::app()->cache->delete($_POST['CourseForm']['course_instructor']);
                    Yii::app()->user->setFlash('success', "Class created");
                } else {
                    Yii::app()->user->setFlash('error', "Data invalid");
                }
            } catch (Exception $e) {
                Yii::app()->user->setFlash('error', "Data invalid");
            }
        }
        return $this->render('createclass', ['model' => $model]);
    }

    public function actionAddStudent()
    {
        if (isset($_FILES['file']) && $_FILES['file']['tmp_name'] != '') {
            $class_id = $_POST['course_id'];
            $user_emails = $this->parseXslt($_FILES['file']['tmp_name']);
            $flag = $this->addStudentsToCourse($user_emails, $class_id);
            if ($flag[0]) {
                if ($flag[1] == 0) {
                    Yii::app()->user->setFlash('success', 'User(s) Enrolled Successfully');
                } else {
                    Yii::app()->user->setFlash('error', "{$flag[1]} User(s) Enrollement Unsuccessfully");
                }
            } else {
                Yii::app()->user->setFlash('error', "Invalid Course");
            }
            $this->redirect('class?class_id=' . $class_id .  '&page=people');
            return;
        }
        if (isset($_POST['email']) && isset($_POST['course_id'])) {
            $user_mail = $_POST['email'];
            $class_id = $_POST['course_id'];
            $user = Users::model()->findByAttributes(['email' => $user_mail]);
            $course = Courses::model()->findByAttributes(['course_code' => $class_id]);
            if ($course->course_instructor['email'] == $user->mail) {
                Yii::app()->user->setFlash('error', 'User is Instructor');
                return $this->redirect('class?class_id=' . $class_id .  '&page=people');
            }
            if (!$user || !$course) {
                Yii::app()->user->setFlash('error', 'User not found');
                return $this->redirect('class?class_id=' . $class_id .  '&page=people');
            }
            foreach ($course->course_students as $student) {
                if ($student['email'] == $user_mail) {
                    Yii::app()->user->setFlash('error', 'User already enrolled');
                    return $this->redirect('class?class_id=' . $class_id .  '&page=people');
                }
            }

            $enrolled = new Enrolled();
            $enrolled->course_id = $course->_id;
            $enrolled->course_name = $course->course_name;
            $enrolled->course_code = $course->course_code;
            $enrolled->course_instuctor_name = $course->course_instructor['name'];
            $enrolled->course_instuctor_email = $course->course_instructor['email'];
            $enrolled->course_description = $course->course_description;
            $user->enrolled[] = $enrolled;

            $student = new Student();
            $student->_id = $user->_id;
            $student->name = $user->name;
            $student->email = $user->email;
            $course->course_students[] = $student;
            $course->course_students_no += 1;

            $user->save();
            $course->save();

            Yii::app()->user->setFlash('success', 'User Enrolled Successfully');
            $this->redirect('class?class_id=' . $class_id .  '&page=people');
        }
    }

    public function parseXslt($file_path)
    {
        $emails = [];
        $Xsheet = IOFactory::load($file_path);
        $worksheet = $Xsheet->getActiveSheet();
        $rows = $worksheet->getHighestRow();
        $columns = $worksheet->getHighestColumn();
        for ($row = 1; $row <= $rows; $row++) {
            $email = $worksheet->getCell([1, $row])->getvalue();
            $emails[] = $email;
        }
        return $emails;
    }

    public function addStudentsToCourse($user_mails, $class_id)
    {
        $mails = [];
        $course = Courses::model()->findByAttributes(['course_code' => $class_id]);
        if (!$course) {
            return [false, 0];
        }
        $mails[$course->course_instructor['email']] = true;
        foreach ($course->course_students as $student) {
            $mails[$student['email']] = true;
        }
        $errors = 0;
        foreach ($user_mails as $user_mail) {
            if (isset($mails[$user_mail])) {
                continue;
            }
            $user = Users::model()->findByAttributes(['email' => $user_mail]);
            if (!$user) {
                $errors++;
                continue;
            }
            $enrolled = new Enrolled();
            $enrolled->course_id = $course->_id;
            $enrolled->course_name = $course->course_name;
            $enrolled->course_code = $course->course_code;
            $enrolled->course_instuctor_name = $course->course_instructor['name'];
            $enrolled->course_instuctor_email = $course->course_instructor['email'];
            $enrolled->course_description = $course->course_description;
            $user->enrolled[] = $enrolled;

            $student = new Student();
            $student->_id = $user->_id;
            $student->name = $user->name;
            $student->email = $user->email;
            $course->course_students[] = $student;
            $course->course_students_no += 1;

            $user->save();
        }
        $course->save();
        return [true, $errors];
    }

    public function actionViewEvent($page_no, $class_id)
    {
        if (Yii::app()->user->getState('role') === 'admin') {
            $this->layout = 'admin';
        } else {
            $this->layout = 'main';
        }
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
        // $class_id = 'course-0000';
        $limit = 4;
        $skip = ($page_no - 1) * $limit;
        $events = Events::model()->startAggregation()
            ->match(['course_code' => $class_id])
            ->addStage(['$skip' => $skip])
            ->addStage(['$limit' => $limit])
            ->aggregate();
        $course = Courses::model()->findByAttributes(['course_code' => $class_id]);
        return $this->render('viewevent', [
            'instructor' => $course->course_instructor['email'],
            'events' => $events['result']
        ]);
    }

    public function streamPage($course_code)
    {
        $data = Streams::model()->findAllByAttributes([
            'course_code' => $course_code
        ]);
        $data = json_decode(json_encode($data));
        return $data;
    }

    public function classworkPage($course_code, $topic_name, $type)
    {
        $view_type = isset($type) ? $type : 'topic';
        $course = Courses::model()->findByAttributes(['course_code' => $course_code]);
        if ($view_type == 'topic') {
            $data = Topics::model()->findAllByAttributes(['course_code' => $course_code]);
        } else {
            $data = SubTopics::model()->findAllByAttributes([
                'course_code' => $course_code,
                'topic_name' => $topic_name,
                'type' => $type
            ]);
        }
        $data = json_decode(json_encode($data));
        return ['instructor' => $course->course_instructor['email'], 'data' => $data];
    }

    public function peoplePage($course_code)
    {
        $course = Courses::model()->findByAttributes(['course_code' => $course_code]);
        $course = json_decode(json_encode($course));
        return $course;
    }

    public function schedulePage($class_id)
    {
        $course = Courses::model()->findByAttributes(['course_code' => $class_id]);
        $schedules = Schedules::model()->findAllByAttributes(['course_code' => $class_id]);
        $schedules = json_decode(json_encode($schedules));
        return ['instructor' => $course->course_instructor['email'], 'data' => $schedules];
    }

    public function meetPage($course_code)
    {
        return true;
    }
}
