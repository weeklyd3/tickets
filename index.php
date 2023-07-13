<?php
if (!file_exists('data')) {
    // create data folder
    mkdir('data');
    mkdir('data/tickets');
    mkdir('data/posts');
    mkdir('data/users');
    file_put_contents("data/tickets/idsAndOrder.json", "{\"currentid\": 0, \"all\": []}");
    file_put_contents("data/posts/ids.json", "0");
}
session_start();
$_SESSION['active'] = true;
define('started', true);
global $config;
global $get_config;
$config = json_decode(file_get_contents('config.json'));
function get_config() {
    return json_decode(file_get_contents('config.json'));
}
$title = "Home";
require_once 'postmanager.php';
require_once 'router.php';
\ob_start();
if (isset($_SESSION['userid'])) {
    if (!file_exists("data/users/{$_SESSION['userid']}")) {
        ?><div>WARNING: Your user account appears to be corrupt. Please log out and log back in.</div><?php
    }
}
$title = execute($action);
$contents = \ob_get_clean();
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="style.css" /><meta name="viewport" content="width=device-width,initial-scale=1.0" />
<title><?php echo htmlspecialchars($title); ?> - <?php echo htmlspecialchars($config->sitename); ?></title></head>
<body>
    <header>
        <div class="header">
        <div class="sitename" style="float: left;"><a style="color: inherit;" href="index.php"><?php echo htmlspecialchars($config->sitename); ?></a></div>
        <div style="float: right;">
        <?php 
        if (!isset($_SESSION['userid'])) {
            ?>not logged in (<a href="index.php?do=login&amp;return=<?php echo htmlspecialchars(urlencode($_SERVER['REQUEST_URI'])); ?>">log in</a>)<?php
        } else {
            ?><span class="username"><?php echo htmlspecialchars($_SESSION['name']); ?></span> (<span class="uid"><?php 
                if ($_SESSION['isadmin']) echo 'admin ';
                echo $_SESSION['userid']; 
            ?></span>) (<a href="index.php?do=out&amp;return=<?php echo htmlspecialchars(urlencode($_SERVER['REQUEST_URI'])); ?>">log out</a>)<?php
        }
        ?>
        </div>
    <div style="clear: both;"></div>
    </div>
        <div class="shortdesc"><?php echo htmlspecialchars($config->desc); ?></div>
    </header>
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <main><?php echo $contents; ?></main>
</body>
</html>