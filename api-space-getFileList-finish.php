<?php
// miiiCasa 目前最重要的就是取得 Device 上的檔案資料啦！
// http://developer.miiicasa.com/doc/sample/space/getFileList

// 要跟 Space 一樣列出圖片，得經過下面四個步驟：
//
// 1. 取得裝置
// 2. 取得儲存裝置（Partition 也算一個儲存裝置)
// 3. 取得檔案列表
// 4. 組出原圖或縮圖路徑 (這一部分比較沒記載)
?>
<?php
define("API_KEY",      "0000000b0g");
define("SECRET_KEY",   "17d60f652c0d2e97f95132d2428fba16");
define("REDIRECT_URL", "http://josephj.com/training/2012/miiicasa-open-api/api-space-getFileList-finish.php");
define("AUTH_URL",     "http://api.miiicasa.com/oauth/authorize");
define("TOKEN_URL",    "http://api.miiicasa.com/oauth/access_token");
define("API_URL",      "http://api.miiicasa.com/op/space/getDeviceList");
define("API_URL2",     "http://api.miiicasa.com/op/space/getStorageList");
define("API_URL3",     "http://api.miiicasa.com/op/space/getFileList");
$code  = (isset($_GET["code"]) && $_GET["code"] !== "") ? $_GET["code"] : FALSE;
$state = (isset($_GET["state"]) && $_GET["state"] !== "") ? $_GET["state"] : FALSE;
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

    $query = "access_token={$access_token}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, API_URL . "?" . $query);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $result = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($result);
    $devices = $result->devices;
}
else
{
    $auth_url = AUTH_URL . "?client_id=" . API_KEY . "&response_type=code&redirect_uri=" . REDIRECT_URL . "&scope=user_space&state=true";
    header("Location: " . $auth_url);
    exit;
}
?>
<ul id="device-list">
    <li>
<?php foreach ($devices as $device) : ?>
        <h2><?php echo $device->device_annotate; ?></h2>
        <ul>
<?php
        // Get storages by device_id.
        $query = "access_token={$access_token}&device_id={$device->device_id}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, API_URL2 . "?" . $query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result);
        $storages = $result->storages;

        foreach ($storages as $storage) :
?>
        <li>
            <h3><?php echo $storage->model;?></h3>
            <ul>
            <?php
                foreach ($storage->mountpoints as $mountpoint) :

                    // Get file list by storage mountpoint + path
                    $path = $mountpoint->mountpoint . "/miiiCasa_Photos";
                    $query = "access_token={$access_token}&device_id={$device->device_id}&path={$path}";
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, API_URL3 . "?" . $query);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    $result = curl_exec($ch);
                    curl_close($ch);
                    $result = json_decode($result);
                    $files = $result->files;
                    foreach ($files as $file) :
?>
                <li><?php echo $file->name; ?></li>
<?php               endforeach; ?>
<?php          endforeach; ?>
            </ul>
        </li>
<?php   endforeach; ?>
        </ul>
<?php endforeach; ?>
    </li>
</ul>
