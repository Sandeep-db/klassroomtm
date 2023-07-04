<?php

use MongoDB\BSON\ObjectId;

class ClassworkController extends Controller
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
                'actions' => ['submission', 'deletesubmission'],
                'expression' => 'Yii::app()->user->getState("role") == ""',
                'deniedCallback' => function () {
                    $url = Yii::app()->createUrl('user/login');
                    Yii::app()->request->redirect($url);
                },
            ],
            [
                'deny',
                'actions' => ['createtopic', 'studentsubmissions', 'createsubtopic', 'addschedule'],
                'expression' => 'Yii::app()->user->getState("role") !== "teacher"',
                'deniedCallback' => function () {
                    $url = Yii::app()->createUrl('home/index');
                    Yii::app()->request->redirect($url);
                },
            ],
        ];
    }

    public function actionCreateTopic()
    {
        if (isset($_POST['course_code'])) {
            unset($_POST['yt0']);
            try {
                $topic = new Topics();
                $topic->attributes = $_POST;
                $course = Courses::model()->findByAttributes(
                    ['course_code' => $_POST['course_code']],
                    ['select' => 'course_name']
                );
                $topic->course_name = $course->course_name;
                $topic->save();

                $stream = new Streams();
                $stream->course_code = $topic->course_code;
                $stream->sub_or_topic_id = $topic->_id;
                $stream->topic_name = $topic->name;
                $stream->type = 'topic';
                $stream->save();
                $this->streamNotify($topic->course_code);

                Yii::app()->user->setFlash('success', 'Topic Creation Successfully');
            } catch (Exception $e) {
                Yii::app()->user->setFlash('error', 'Topic Creation Failed');
            }
        }
        $this->redirect('/index.php/home/class?class_id=' . $_POST['course_code'] .  '&page=classwork');
    }

    public function actionCreateSubTopic()
    {
        if (isset($_POST['course_code']) && isset($_POST['topic_name'])) {
            unset($_POST['yt1']);
            $subtopic = new SubTopics();
            $subtopic->attributes = $_POST;
            $subtopic->uid = uniqid();
            $files = CUploadedFile::getInstancesByName('attachment');
            if ($files) {
                $s3 = new S3Helper();
                foreach ($files as $file) {
                    if (!$file->getTempName() || $file->getTempName() == '') {
                        continue;
                    }
                    $file_name = $_POST['course_code'] . '/' . $_POST['topic_name'] . '/' . $subtopic->uid . '/' . uniqid();
                    $file_link = $s3->putObject($file_name, $file);
                    $file_link['name'] = $file->name;
                    $subtopic->attachments[] = $file_link;
                }
            }
            $subtopic->save();

            $stream = new Streams();
            $stream->course_code = $subtopic->course_code;
            $stream->sub_or_topic_id = $subtopic->_id;
            $stream->topic_name = $subtopic->topic_name;
            $stream->type = $subtopic->type;
            $stream->save();
            $this->streamNotify($subtopic->course_code);

            $this->redirect('/index.php/home/class?class_id=' . $_POST['course_code'] .  '&page=classwork&topic_name=' . rawurlencode($_POST['topic_name']) . '&filter=' . $_POST['type']);
        }
    }

    public function actionSubmission()
    {
        $rtype = Yii::app()->request->getParam('rtype');
        $page_no = Yii::app()->request->getParam('page_no');
        if (!$page_no) {
            $page_no = 1;
        }
        $params = [];
        $condition = [
            'user_email' => $_COOKIE['email'],
        ];
        if ($rtype) {
            $condition['rtype'] = $rtype;
            $params['rtype'] = $rtype;
        }
        $limit = 2;
        $skip = ($page_no - 1) * $limit;
        $data = Submissions::model()->startAggregation()
            ->match($condition)
            ->addStage(['$skip' => $skip])
            ->addStage(['$limit' => $limit])
            ->addStage(['$lookup' => [
                'from' => 'subtopics',
                'localField' => 'sub_topic_id',
                'foreignField' => '_id',
                'as' => 'result'
            ]])
            ->addStage(['$unwind' => [
                'path' => '$result'
            ]])
            ->addStage(['$sort' => [
                'last_updated_on' => EMongoCriteria::SORT_DESC
            ]])
            ->Aggregate();
        $data = json_decode(json_encode($data['result']));
        $this->render('submission', ['data' => $data], false, $params);
    }

    public function actionNewSubmission()
    {
        if (isset($_POST['sub_topic_id'])) {
            $sub_topic_id = new ObjectId($_POST['sub_topic_id']);
            $rtype = $_POST['rtype'];
            $submission = new Submissions();
            $submission->attributes = $_POST;
            $submission->sub_topic_id = $sub_topic_id;
            if ($submission->rtype == 'assignment') {
                $submission->uid = 'assignment';
            } else {
                $submission->uid = uniqid();
            }
            $files = CUploadedFile::getInstancesByName('attachment');
            if ($files) {
                $s3 = new S3Helper();
                foreach ($files as $file) {
                    if (!$file->getTempName() || $file->getTempName() == '') {
                        continue;
                    }
                    $file_name = $_POST['sub_topic_id'] . '/' . $_POST['user_email'] . '/' . $submission->uid . '/' . uniqid();
                    $file_link = $s3->putObject($file_name, $file);
                    $file_link['name'] = $file->name;
                    $submission->attachments[] = $file_link;
                }
            }
            if ($submission->save()) {
                if ($submission->rtype == 'assignment') {
                    $this->submissionNotify($submission->_id);
                }
                Yii::app()->user->setFlash('success', 'Submitted successfully');
            } else {
                Yii::app()->user->setFlash('error', 'Submission Failed');
            }
        }
        $this->redirect('/index.php/classwork/submission');
    }

    public function actionStudentSubmissions($sub_topic_id, $page_no)
    {
        $limit = 2;
        $skip = ($page_no - 1) * $limit;
        $sub_topic_id = new ObjectId($sub_topic_id);
        $submissions = Submissions::model()->startAggregation()
            ->match(['sub_topic_id' => $sub_topic_id])
            ->addStage(['$skip' => $skip])
            ->addStage(['$limit' => $limit])
            ->addStage(['$lookup' => [
                'from' => 'subtopics',
                'localField' => 'sub_topic_id',
                'foreignField' => '_id',
                'as' => 'result'
            ]])
            ->addStage(['$unwind' => [
                'path' => '$result'
            ]])
            ->Aggregate();
        $submissions = json_decode(json_encode($submissions['result']));
        $this->render('studentsubmissions', ['data' => $submissions]);
    }

    public function actionDeleteSubmission()
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] == 'delete-submission') {
            $submissionId = new ObjectId($_POST['_id']);
            try {
                // $s3 = new S3Helper();
                // print_r($s3->deleteFolder($_POST['delete_path']));
                $result = Submissions::model()->deleteByPk($submissionId);
                echo json_encode(['_id' => $_POST['_id']]);
            } catch (Exception $e) {
                echo "exception: " . $e->getMessage();
            }
        }
    }

    public function actionAddschedule()
    {
        if ($_POST['operation'] == 'delete' || isset($_POST['startdate']) && isset($_POST['enddate'])) {
            // print_r($_POST);
            // return;
            if ($_POST['_id'] == '') {
                unset($_POST['_id']);
            }
            $courseCode = $_POST['course_code'];
            if ($_POST['operation'] != 'delete') {
                $startDate = $_POST['startdate'];
                $endDate = $_POST['enddate'];
                $topic = $_POST['topic'];
                $learningOutcome = $_POST['learningOutcome'];
                $hours = $_POST['hours'];
            }
            $course = Courses::model()->findByAttributes(['course_code' => $courseCode]);
            if ($_POST['operation'] != 'delete' && $topic[0] == '') {
                Yii::app()->user->setFlash('scheduleerror', 'Please fill all the fields.');
                $this->redirect('/index.php/home/class?class_id=' . $_POST['course_code'] .  '&page=schedule');
                return;
            }
            if (isset($_POST['_id'])) {
                $_id = new ObjectId($_POST['_id']);
            }
            if ($_POST['operation'] == 'delete' || strtotime($course->course_start_date) <= strtotime($startDate) && strtotime($course->course_end_date) >= strtotime($endDate)) {
                print_r($_POST);
                if (!isset($_id)) {
                    $existingSchedules = Schedules::model()->findAllByAttributes([
                        'course_code' => $courseCode,
                        'startdate' => ['$lte' => $endDate],
                        'enddate' => ['$gte' => $startDate]
                    ]);
                }
                if (empty($existingSchedules) || isset($_id)) {
                    if (!isset($_id)) {
                        $schedule = new Schedules();
                    } else {
                        $schedule = Schedules::model()->findByPk($_id);
                    }
                    if ($_POST['operation'] == 'delete') {
                        Yii::log('yes delete', 'error', 'system');
                        $schedule = Schedules::model()->deleteByPk($_id);
                        $this->calenderNotify($courseCode, $_id);
                    } else {
                        Yii::log('no delete', 'error', 'system');
                        $schedule->course_code = $courseCode;
                        $schedule->startdate = $startDate;
                        $schedule->enddate = $endDate;
                        $schedule->topic = $topic;
                        $schedule->learningOutcome = $learningOutcome;
                        $schedule->hours = $hours;
                        $schedule->save();
                        $this->calenderNotify($courseCode, $schedule->_id);
                    }
                    // print_r($schedule);
                } else {
                    Yii::app()->user->setFlash('scheduleerror', 'Colliding with exsisting Schedules.');
                }
            } else {
                echo "Invalid start date or end date.";
            }
        }
        $this->redirect('/index.php/home/class?class_id=' . $_POST['course_code'] .  '&page=schedule');
    }

    public function streamNotify($course_code)
    {
        $command_class = 'streamnotify';
        $command_args = "\"$course_code\"";
        $command = "/usr/bin/php /data/live/protected/utils/job_entry.php "
            . $command_class . " "
            . $command_args
            . "> /dev/null 2>&1 &";
        exec($command);
    }

    public function calenderNotify($course_code, $schedule_id)
    {
        $schedule_id = strval($schedule_id);
        $command_class = 'calendarnotify';
        $command_args = "\"$course_code\" \"$schedule_id\"";
        $command = "/usr/bin/php /data/live/protected/utils/job_entry.php "
            . $command_class . " "
            . $command_args
            . "> /dev/null 2>&1 &";
        exec($command);
    }

    public function submissionNotify($submission_id)
    {
        $schedule_id = strval($submission_id);
        $command_class = 'evaluate';
        $command_args = "\"$submission_id\"";
        $command = "/usr/bin/php /data/live/protected/utils/job_entry.php "
            . $command_class . " "
            . $command_args
            . "> /dev/null 2>&1 &";
        exec($command);
    }
}
