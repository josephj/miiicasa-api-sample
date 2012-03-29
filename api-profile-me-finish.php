<?php
// 有了 Access Token 就可以取得這個使用者的資料啦!
// http://developer.miiicasa.com/doc/sample/profile/me
?>
<?php
define("API_KEY",      "0000000b0g");
define("SECRET_KEY",   "17d60f652c0d2e97f95132d2428fba16");
define("REDIRECT_URL", "http://josephj.com/training/2012/miiicasa-open-api/api-profile-me-finish.php");
define("AUTH_URL",     "http://api.miiicasa.com/oauth/authorize");
define("TOKEN_URL",    "http://api.miiicasa.com/oauth/access_token");
define("API_URL",      "http://api.miiicasa.com/op/profile/me");
$code     = (isset($_GET["code"]) && $_GET["code"] !== "") ? $_GET["code"] : FALSE;
$state    = (isset($_GET["state"]) && $_GET["state"] !== "") ? $_GET["state"] : FALSE;
if ($code && $state)
{
    $ch = curl_init();
    $fields = array(
        "grant_type=authorization_code",
        "code=" . $code,
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
    $access_token = $result["access_token"];

    // API_URL 加上 access_token 即可取得資料！
    $query = "access_token={$access_token}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, API_URL . "?" . $query);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $result = curl_exec($ch);
    curl_close($ch);

    echo "<pre>";
    print_r(json_decode($result));
}
else
{
    $auth_url = AUTH_URL . "?client_id=" . API_KEY . "&response_type=code&redirect_uri=" . REDIRECT_URL . "&scope=user_basic_info&state=true";
    header("Location: " . $auth_url);
}
?>
