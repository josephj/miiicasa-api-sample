<?php
// 接下來要用 Request Token 來取得 Access Token
// http://developer.miiicasa.com/doc/sample/profile/me
?>
<?php
define("API_KEY",      "0000000b0g");
define("SECRET_KEY",   "17d60f652c0d2e97f95132d2428fba16");
define("REDIRECT_URL", "http://josephj.com/training/2012/miiicasa-open-api/oauth-access-token-finish.php");
define("AUTH_URL",     "http://api.miiicasa.com/oauth/authorize");
define("TOKEN_URL",    "http://api.miiicasa.com/oauth/access_token");
define("API_URL",      "http://api.miiicasa.com/op/profile/me");
$code     = (isset($_GET["code"]) && $_GET["code"] !== "") ? $_GET["code"] : FALSE;
$state    = (isset($_GET["state"]) && $_GET["state"] !== "") ? $_GET["state"] : FALSE;
if ($code && $state)
{
    // 用 cURL 的方式向 miiiCasa 交換 Access Token
    $ch = curl_init();
    $fields = array(
        "grant_type=authorization_code",
        "code=" . $code, // 把 Request Token 當成一個參數，往 TOKEN_URL 送
        "client_id=" . API_KEY,
        "client_secret=" . SECRET_KEY,
        "redirect_uri=" . REDIRECT_URL,
    );
    curl_setopt($ch, CURLOPT_URL, TOKEN_URL);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, implode($fields, "&"));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $result = curl_exec($ch);
    $result = json_decode($result, TRUE);
    curl_close($ch);

    // 若成功即可拿到 Access Token (可重複使用、壽命 1 小時)
    print_r($result);
}
else
{
    $auth_url = AUTH_URL . "?client_id=" . API_KEY . "&response_type=code&redirect_uri=" . REDIRECT_URL . "&scope=user_basic_info&state=true";
    header("Location: " . $auth_url);
}
?>
