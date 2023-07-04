<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha' => array(
				'class' => 'CCaptchaAction',
				'backColor' => 0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page' => array(
				'class' => 'CViewAction',
			),
		);
	}

	public function actionTest()
	{
		echo "start";
		$s3 = new S3Helper();
		// $res = $s3->putObject('test-file', '/data/live/css/bg.gif');
		$res = $s3->getObjectUrl('test-file');
		print_r($res);
		echo "end";
	}

	public function actionDataTables()
	{
		$this->render('datatables');
	}

	public function actionFile()
	{
		$model = new FilesForm();
		if (isset($_POST['FilesForm'])) {
			$s3 = new S3Helper();
			$model->attributes = $_POST['FilesForm'];
			$this->parseXslt($_FILES['FilesForm']['tmp_name']['file']);
			$uploadedFile = CUploadedFile::getInstance($model, 'file');
			if ($uploadedFile !== null) {
				$res = $s3->putObject($_POST['FilesForm']['name'], $uploadedFile);
			}
			print_r($res);
		}
		$this->render('fileUpload', ['model' => $model]);
	}

	public function actionEmail()
	{
		$email_client = new SESHelper();
		echo "unlock it";
		return;
		echo $email_client->sendTestMails();
	}

	public function actionCurl()
	{

		$curl = curl_init();
		curl_setopt_array($curl, [
			CURLOPT_URL => 'http://172.16.112.227:3000',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
		]);

		$responce = curl_exec($curl);

		curl_close($curl);

		$this->render('curltest', ['data' => $responce]);
	}

	public function actionCurlPost()
	{
		$curl = curl_init();
		curl_setopt_array($curl, [
			CURLOPT_URL => 'http://192.168.1.19:3000/test',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => json_encode([
				'param1' => 'value1',
				'param2' => 'value2',
			]),
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json'
			),
		]);

		$response = curl_exec($curl);

		curl_close($curl);

		$this->render('curltest', ['data' => $response]);
	}

	public function actionCommand()
	{
		$command_class = 'test';
		$command_args = "hello";
		$command = "/usr/bin/php /data/live/protected/utils/job_entry.php "
			. $command_class
			. " "
			. $command_args
			. "> /dev/null 2>&1 &";
		exec($command);
		echo "command";
	}

	public function parseXslt($file_path)
	{
		$Xsheet = IOFactory::load($file_path);
		$worksheet = $Xsheet->getActiveSheet();
		$rows = $worksheet->getHighestRow();
		$columns = $worksheet->getHighestColumn();
		for ($row = 2; $row <= $rows; $row++) {
			$email = $worksheet->getCell([1, $row])->getvalue();
			Yii::log($email, 'error', 'system');
		}
	}

	public function actionQueueing()
	{
		$sqs_client = new SQSHelper();
		// $mid = $sqs_client->sendMessage('Test Hello 1');
		// print_r($mid);
		// echo "<br />";
		// $mid = $sqs_client->sendMessage('Test Hello 2');
		// print_r($mid);
		// echo "<br />";
		// $mid = $sqs_client->sendMessage('Test Hello 3');
		// print_r($mid);
		// echo "<br />";
		// $mid = $sqs_client->sendMessage('Test Hello 4');
		// print_r($mid);
		// echo "<br />";
		$msgs = $sqs_client->receiveMessage();
		print_r($msgs);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */

	public function actionIndex()
	{
		Yii::log('demo info', 'info', 'system');
		Yii::log('demo error', 'error', 'system');
		Yii::log('demo warning', 'warning', 'system');
		Yii::trace('demo trace');
		return $this->render('index');
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if ($error = Yii::app()->errorHandler->error) {
			if (Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	// public function actionMessage()
	// {
	//     if (isset($_POST['email'])) {
	//         $result = Users::model()->findByAttributes(
	//             ['email' => $_POST['email']],
	//         );
	//         $result->comments[] = $_POST['message'];
	//         $result->save();
	//     }
	//     return $this->render('message');
	// }

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model = new LoginForm;

		// if it is ajax validation request
		if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if (isset($_POST['LoginForm'])) {
			$model->attributes = $_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if ($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login', array('model' => $model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}

	public function actionHelloWorld()
	{
		echo "Hello World!";
	}

	public function actionHelp()
	{
		echo "Help!";
	}

	public function actionExit()
	{
		echo "Exit!";
	}
}
