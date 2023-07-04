<?php

class AuthController extends Controller
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
                'actions' => ['googlesignin', 'googlecallback'],
                'expression' => 'Yii::app()->user->getState("role") == ""',
                'deniedCallback' => function () {
                    $url = Yii::app()->createUrl('home/index');
                    Yii::app()->request->redirect($url);
                },
            ],
        ];
    }

    public function actionIndex()
    {
        echo "OAuth";
    }

    public function actionGoogleSignIn()
    {
        $OauthComponent = new OAuth();
        $client = $OauthComponent::$client;
        $authUrl = $client->createAuthUrl();
        $this->redirect($authUrl);
    }

    public function actionGoogleCallback()
    {
        $OauthComponent = new OAuth();
        $client = $OauthComponent::$client;
        $code = Yii::app()->request->getQuery('code');
        $userCode = GoogleTokens::model()->findByAttributes([
            'email' => $_COOKIE['email']
        ]);
        if (!$code) {
            if (!$userCode) {
                $this->redirect(Yii::app()->createAbsoluteUrl('/auth/googlesignin'));
            } else {
                $tmp = json_decode($userCode->auth_data);
                $accessToken = $OauthComponent->processAccessTokens($tmp);
            }
        } else {
            $accessToken = $client->fetchAccessTokenWithAuthCode($code);
            if (!$userCode) {
                $newAuth = new GoogleTokens();
                $newAuth->email = $_COOKIE['email'];
                $newAuth->auth_data = json_encode($accessToken);
                // $newAuth->save();
            } else {
                $userCode->auth_data = json_encode($accessToken);
                // $userCode->save();
            }
        }
        $client->setAccessToken($accessToken);
        $oauth2 = new \Google_Service_Oauth2($client);
        $userInfo = $oauth2->userinfo->get();
        $name = $userInfo->getName();
        $email = $userInfo->getEmail();
        if ($email !== $_COOKIE['email']) {
            echo "This is not you account";
            return;
        }
        if (isset($newAuth)) {
            $newAuth->save();
        }
        if (isset($userCode)) {
            $userCode->save();
        }
        $this->clearQueue($email);
        $this->redirect(Yii::app()->createAbsoluteUrl('/home/index'));
    }

    private function clearQueue($user_mail)
    {
        $command_class = 'relogin';
        $command_args = "\"$user_mail\"";
        $command = "/usr/bin/php /data/live/protected/utils/job_entry.php "
            . $command_class . " "
            . $command_args
            . "> /dev/null 2>&1 &";
        exec($command);
    }
}
