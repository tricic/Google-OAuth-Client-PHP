# Google-OAuth-Client-PHP
A PHP class to fetch user data from Google APIs using OAuth2, for simple Gmail login implementation. Only one class, no dependencies.

Note: The class was created in 2018, but posted to Github in 2019.

# Example
```php
// login.php
<?php
require_once "Google_OAuth_Client.php";

$gClient = new Google_OAuth_Client;
$gClient->setClientId("YOUR_CLIENT_ID");
$gClient->setClientSecret("YOUR_CLIENT_SECRET");
$gClient->setRedirectUri("http://localhost/PROJECT_NAME/login.php"); // Better idea is to use seperate file for callback
$gClient->setScopes("profile email");

if (isset($_GET["code"]))
{
    if ($gClient->fetchAccessTokenWithAuthCode($_GET["code"]) && $gClient->fetchUserInfo())
    {
        $data = $gClient->getFetchedData();
        var_dump($data);
    }
    else
    {
        print $gClient->getLastResponse();
    }
}
else
{
    print "<a href='{$gClient->createAuthUrl()}'>Login via Google</a>";
}
```
