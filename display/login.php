<?php
if (isset($_GET['authentication-token'])) {
    $title = loginDone($_GET['authentication-token']);
    return;
}
$config = json_decode(file_get_contents('config.json'));
?><p>Please wait as you are redirected to the login. Click the link if you aren't in 3 seconds.</p><?php
$clientprop = "wiki-client";
$loginServer = $config->$clientprop;
$appname = $config->sitename;
$params = urlencode($_SERVER['REQUEST_URI']);
$cb = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
$cb = urlencode($cb);
$fail = urlencode("http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}&do=login_bad");
$url = "$loginServer?title=Special:WikiAuth&appname=$appname&callback=$cb&failure-callback=$fail";
$actualurl = htmlspecialchars($url);
echo "<meta http-equiv=\"Refresh\" content=\"60; url='{$actualurl}'\" />";
echo "<a href=\"$actualurl\">Not redirected?</a>";
function loginDone($token) {
    $response = fetch("https://wiki.weeklyd3.repl.co/index.php?title=Special:WikiAuth&getdata=" . urlencode($_GET['authentication-token']));
    if (!($response instanceof response)) {
        $r = htmlspecialchars($response);
        $title = 'Login faliure';
        echo "<p>An error occurred while getting user data. Error: {$r}.</p><p>Try your luck again by pressing the LOG IN button again.</p>";
        return $title;
    }
    $response = $response->json();
    if ($response == null) {
        $title = 'Bad token';
        echo "<p>Your request was invalid. DO NOT RETRY IT.</p><p>(OK, I lied, you can reload this page to retry it several times as it could be a server internet faliure.)</p><p>Things that could have caused this:</p><ul><li>When you are sent back from the cross-login page, that link only works once.</li><li>You are trying to heck.</li></ul>";
        return $title;
    }
    $id = $response->userid;
    $obj = array();
    $obj['stats'] = array();
    $obj['joined'] = time();
    $obj['refreshed'] = time();
    foreach ((array) $response as $key => $value) {
        $_SESSION[$key] = $value;
        $obj['stats'][$key] = $value;
    }
    if (file_exists("data/users/$id")) {
        $o = json_decode(file_get_contents("data/users/$id/info.json"));
        if (!$o) {
            echo "<p>This user account is corrupt. Please ask the owner of this site to delete the local account, and then try logging in again.</p><p>Note that deleting this account will erase all contribution history, but existing posts will not be affected.</p>";
            return "Error";
        }
        $o->refreshed = time();
        file_put_contents("data/users/$id/info.json", json_encode($o));
        echo "<p>You should be logged in to your existing local account.</p><a href=\"";
        echo htmlspecialchars(isset($_GET['return']) ? $_GET['return'] : 'index.php');
        echo "\">Return</a>";
        return "All set!";
    }
    mkdir("data/users/$id");
    $obj['posts'] = [];
    file_put_contents("data/users/$id/info.json", json_encode($obj));
    echo "<p>Done! New local account created and logged in.</p>";
    return "User account created";
}
function fetch(string $url, array $options = array()) {
    $options = (object) $options;
    $ch = curl_init($url);
    $fp = fopen('tmp.txt', "w");
    
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_POST, ($options->method ?? 'GET') === 'POST');
    if (($options->method ?? 'GET') === 'POST') curl_setopt($ch, CURLOPT_POSTFIELDS, $options->body);
    
    curl_exec($ch);
    if (curl_error($ch)) {
        $err = curl_error($ch);
        if ($options->verbose ?? true) return curl_error($ch);
        else return false;
    }
    curl_close($ch);
    fclose($fp);    
    return new response(file_get_contents('tmp.txt'));
}
class response {
    public function json() {
        return json_decode($this->contents);
    }
    public function __construct(string $contents) {
        $this->contents = $contents;
    }
	public function text() {
		return $this->contents;
	}
}
