<?php
// 第一步要讓使用者登入、取得 Request Token 先！
// http://developer.miiicasa.com/doc/sample/profile/me
?>
<?php
// 要使用 miiiCasa API、API_KEY, SECRET_KEY, REDIRECT_URL 都是必須的！
define("API_KEY",      "0000000b0g");
define("SECRET_KEY",   "17d60f652c0d2e97f95132d2428fba16");
define("REDIRECT_URL", "http://josephj.com/training/2012/miiicasa-open-api/oauth-request-token-finish.php");

// miiiCasa OAuth 讓使用者認證的 URL
define("AUTH_URL",     "http://api.miiicasa.com/oauth/authorize");

$code  = (isset($_GET["code"]) && $_GET["code"] !== "") ? $_GET["code"] : FALSE;
$state = (isset($_GET["state"]) && $_GET["state"] !== "") ? $_GET["state"] : FALSE;

if ($code && $state)
{
    // 2. Redirect 回來，就會看到以下的資訊：
    //    其中 code 是 Request Token，是取得 Access Token 的入場卷
    //    有了 Access Token 才能取得 miiiCasa 的資料
    print_r($_GET);
}
else
{
    // 1. 將使用者導到 miiiCasa oauth 認證頁
    $auth_url = AUTH_URL . "?client_id=" . API_KEY . "&response_type=code&redirect_uri=" . REDIRECT_URL . "&state=true";
    header("Location: " . $auth_url);
}
?>
