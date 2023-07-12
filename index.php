<?php
define('started', true);
$config = json_decode(file_get_contents('config.json'));
$title = "Home";
require_once 'router.php';
\ob_start();
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
        <div class="sitename"><?php echo htmlspecialchars($title); ?></div></div>
        <div class="shortdesc"><?php echo htmlspecialchars($config->sitename); ?> - <?php echo htmlspecialchars($config->desc); ?></div>
    </header>
    <main><?php echo $contents; ?></main>
</body>
</html>