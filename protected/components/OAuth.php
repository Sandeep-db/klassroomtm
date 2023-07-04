<?php

use Google\Client;
require_once('/data/live/protected/components/config.php');

class OAuth extends CComponent
{
    public static $client;
    
    public function __construct()
    {
        if (!isset($this::$client)) {
            $this::$client = new Client();
            $this::$client->setClientId(GOOGLE_CLIENT_ID);
            $this::$client->setClientSecret(GOOGLE_CLIENT_SECRET);
            $this::$client->setRedirectUri('http://localhost/index.php/auth/googlecallback');
            $this::$client->addScope('email');
            $this::$client->addScope('profile');
            $this::$client->addScope('https://www.googleapis.com/auth/calendar');
            $this::$client->setAccessType('offline');
        }
    }

    public function getAccessTokens($email)
    {
        $userCode = GoogleTokens::model()->findByAttributes([
            'email' => $email
        ]);
        if (!$userCode) {
            return false;
        }
        $tmp = json_decode($userCode->auth_data);
        return $this->processAccessTokens($tmp);
    }

    public function processAccessTokens($auth_data)
    {
        $accessToken = [];    
        foreach (['access_token', 'expires_in', 'scope', 'token_type', 'id_token', 'created'] as $val) {
            $accessToken[$val] = $auth_data->$val;
        }
        return $accessToken;
    }

}
