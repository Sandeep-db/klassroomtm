<?php

class UserController extends Controller
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
                'actions' => ['profile'],
                'expression' => 'strval(Yii::app()->user->getState("role")) == ""',
                'deniedCallback' => function () {
                    $url = Yii::app()->createUrl('user/login');
                    Yii::app()->request->redirect($url);
                },
            ],
            [
                'deny',
                'actions' => ['index', 'login', 'register'],
                'expression' => 'strval(Yii::app()->user->getState("role")) !== ""',
                'deniedCallback' => function () {
                    $url = Yii::app()->createUrl('home/index');
                    Yii::app()->request->redirect($url);
                },
            ],
        ];
    }

    public function actionIndex()
    {
        $this->redirect($this->createAbsoluteUrl('user/login'));
    }

    public function actionRegister()
    {
        $this->layout = false;
        if (isset($_POST['email'])) {
            if ($_POST['passwd'] !== $_POST['cpasswd']) {
                Yii::app()->user->setFlash('error', "password is didn't match");
                return $this->render('register');
            }
            $flag = $this->registerUser(
                $_POST['name'],
                $_POST['email'],
                $_POST['passwd'],
            );
            if ($flag[0]) {
                Yii::app()->user->setFlash('success', 'registered successful.');
            } else {
                Yii::app()->user->setFlash('error', $flag[1]);
            }
        }
        return $this->render('register');
    }

    public function registerUser($name, $email, $passwd)
    {
        $model = new Users();
        $model->name = $name;
        $model->email = $email;
        $model->role = "student";
        $model->passwd = $passwd;
        try {
            if ($model->validate()) {
                $model->save();
                return [true, ""];
            } else {
                $errors = $model->getErrors();
                $msg = "";
                foreach ($errors as $message) {
                    $msg .= $message[0] . "<br />";
                }
                return [false, $msg];
            }
        } catch (Exception $e) {
            return [false, $e->getMessage()];
        }
    }

    public function actionLogin()
    {
        $this->layout = false;
        $model = new LoginForm();
        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];
            $flag = $model->validate();
            if (!$flag) {
                Yii::app()->user->setFlash('error', "email or password incorrect");
                return $this->render('login', ['model' => $model]);
            } else {
                $eventq = EventQueue::model()->findByAttributes([
                    'email' => $_POST['LoginForm']['email']
                ]);
                if ($eventq) {
                    $this->redirect($this->createAbsoluteUrl('/auth/googlesignin'));
                    return;
                }
                $this->redirect($this->createAbsoluteUrl('/home/index'));
                return;
            }
        }
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
            $data = $this->validateUser($_POST['LoginForm']['email'], $_POST['LoginForm']['passwd'], false);
            if ($data) {
                print_r(CJSON::encode($data));
            } else {
                print_r(CJSON::encode(['_id' => false]));
            }
            Yii::app()->end();
        }
        return $this->render('login', ['model' => $model]);
    }

    public function actionProfile()
    {
        if (isset($_POST['delete-image'])) {
            $user = Users::model()->findByAttributes([
                'email' => $_POST['email'],
                'passwd' => $_POST['passwd']
            ]);
            if (!$user) {
                Yii::app()->user->setFlash('error', 'password is incorrect');
                return $this->render('profile');
            }
            $image = 'https://php-bucket-db.s3.us-east-2.amazonaws.com/default-profile-image';
            $user->image = $image;
            $user->save();
            Yii::app()->user->setState('image', $image);
        }
        if (isset($_POST['image-change'])) {
            if (isset($_FILES['image'])) {
                $user = Users::model()->findByAttributes([
                    'email' => $_POST['email'],
                    'passwd' => $_POST['passwd']
                ]);
                if (!$user) {
                    Yii::app()->user->setFlash('error', 'password is incorrect');
                    return $this->render('profile');
                }
                $uploadedFile = CUploadedFile::getInstanceByName('image');
                $s3 = new S3Helper();
                if ($uploadedFile !== null) {
                    $result = $s3->putObject($_POST['email'] . '-profile-image', $uploadedFile);
                    if (isset($result['imageURL'])) {
                        $user->image = $result['imageURL'];
                        $user->save();
                        Yii::app()->user->setState('image', $result['imageURL']);
                    } else {
                        Yii::app()->user->setFlash('error', 'error in uploading image');
                        return $this->render('profile');
                    }
                }
                Yii::app()->user->setFlash('success', 'profile updated successfully');
                // return $this->refresh();
            } else {
                Yii::app()->user->setFlash('error', 'Upload a image');
                return $this->render('profile');
            }
        }
        if (isset($_POST['profile-change'])) {
            $user = Users::model()->findByAttributes([
                'email' => $_POST['email'],
                'passwd' => $_POST['passwd']
            ]);
            if (!$user) {
                Yii::app()->user->setFlash('error', 'password is incorrect');
                return $this->render('profile');
            }
            if (isset($_POST['name']) && $_POST['name'] != '') {
                $user->name = $_POST['name'];
                setcookie('name', $user->name, time() + (86400 * 30));
            }
            if (isset($_POST['npasswd']) && $_POST['npasswd'] != '') {
                if (isset($_POST['cpasswd']) && $_POST['cpasswd'] === $_POST['npasswd']) {
                    $user->passwd = $_POST['npasswd'];
                } else {
                    Yii::app()->user->setFlash('error', 'password didn\'t match');
                    return $this->render('profile');
                }
            }
            $user->save();
            Yii::app()->user->setFlash('success', 'profile updated successfully');
            return $this->refresh();
        }
        return $this->render('profile');
    }

    public function actionLogout()
    {

        Yii::app()->cache->delete($_COOKIE['email']);

        $_idCookie = new CHttpCookie('_id', '');
        $_idCookie->expire = time() - 1;
        Yii::app()->request->cookies['_id'] = $_idCookie;

        $nameCookie = new CHttpCookie('name', '');
        $nameCookie->expire = time() - 1;
        Yii::app()->request->cookies['name'] = $nameCookie;
        
        $emailCookie = new CHttpCookie('email', '');
        $emailCookie->expire = time() - 1;
        Yii::app()->request->cookies['email'] = $emailCookie;

        $companyNameCookie = new CHttpCookie('company-name', '');
		$companyNameCookie->expire = time() + (30 * 86400);
		Yii::app()->request->cookies['company-name'] = $companyNameCookie;

		$companyIdCookie = new CHttpCookie('company-id', '');
		$companyIdCookie->expire = time() + (30 * 86400);
		Yii::app()->request->cookies['company-id'] = $companyIdCookie;

        Yii::app()->user->setState('role', '');
		Yii::app()->user->setState('image', '');

        Yii::app()->user->logout();
        header("Location:" . "login");
        return;
    }

}
