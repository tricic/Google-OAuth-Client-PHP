<?php

/**
 * The class is designed to fetch user data from Google APIs using OAuth.
 * It is mainly created for Gmail login purposes.
 * 
 * @author Ismar Tričić ismar.tricic[at]gmail.com
 * @copyright Copyright 2018 Ismar Tričić ismar.tricic[at]gmail.com
 * @license https://opensource.org/licenses/MIT The MIT License
 */

class Google_OAuth_Client
{
    private $client_id;
    private $client_secret;
    private $redirect_uri;
    private $scopes;
    private $state;
    private $last_response;
    private $access_token;
    private $data;

    public function setClientId(string $client_id)
    {
        $this->client_id = $client_id;
    }

    public function setClientSecret(string $client_secret)
    {
        $this->client_secret = $client_secret;
    }
    
    public function setRedirectUri(string $redirect_uri)
    {
        $this->redirect_uri = $redirect_uri;
    }

    public function setScopes(string $scopes)
    {
        $this->scopes = $scopes;
    }

    public function setState(string $state)
    {
        $this->state = $state;
    }

    public function getLastResponse()
    {
        return $this->last_response;
    }

    public function getFetchedData()
    {
        return $this->data;
    }

    public function createAuthUrl()
    {
        return sprintf(
            "https://accounts.google.com/o/oauth2/v2/auth?response_type=code&client_id=%s&redirect_uri=%s&scope=%s&state=%s",
            $this->client_id,
            $this->redirect_uri,
            $this->scopes,
            $this->state
        );
    }

    public function fetchAccessTokenWithAuthCode(string $code)
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_POST => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => "https://www.googleapis.com/oauth2/v4/token",
            CURLOPT_POSTFIELDS => http_build_query([
                "code" => $_GET["code"],
                "client_id" => $this->client_id,
                "client_secret" => $this->client_secret,
                "redirect_uri" => $this->redirect_uri,
                "grant_type" => "authorization_code"
            ])
        ]);
    
        $this->last_response = curl_exec($ch);
        
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200)
        {
            curl_close($ch);
            return false;
        }
        
        $this->access_token = json_decode($this->last_response, true)["access_token"];
        curl_close($ch);
        return true;
    }

    public function fetchUserInfo()
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => sprintf(
                "https://www.googleapis.com/oauth2/v3/userinfo?alt=json&access_token=%s",
                $this->access_token
            )
        ]);

        $this->last_response = curl_exec($ch);
        
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200)
        {
            curl_close($ch);
            return false;
        }

        $this->data = json_decode($this->last_response, true);
        curl_close($ch);
        return true;
    }
}