<?php
if (!defined('started')) die('STALP HECKING');
$action = isset($_GET['do']) ? $_GET['do'] : 'default';
function execute($action) {
    switch ($action) {
        case "login_bad":
            $title = "Bad login";
            echo "<p>You may have cancelled it.</p>";
            break;
        case 'login':
            $title = "Log in";
            require "display/login.php";
            break;
        case 'out':
            $title = "Logged out!";
            echo "<p>You have been logged out successfully.</p>";
            $return = isset($_GET['return']) ? $_GET['return'] : 'index.php';
            echo "<a href=\"" . htmlspecialchars($return) . "\">Return to previous URL</a>";
            unset($_SESSION);
            session_destroy();
            break;
        case 'user':
            if (!isset($_GET['uid'])) {
                $title = "No user to view";
                echo "<p>Add a <code>uid</code> URL parameter to view a certain user's profile.</p>";
                break;
            }
            $user = getUserById((int) $_GET['uid']);
            if (!$user) {
                $title = "No such user";
                echo "<p>Invalid user ID.</p>";
                break;
            }
            $title = "{$user->remote->name} - Profile";
            require_once "display/user.php";
            display_user($user);
            break;
        case 'new':
            $title = "New thread";
            if (!isset($_SESSION['userid'])) {
                $title = "Login required";
                echo "<p>You need to log in to create threads.</p>";
                break;
            }
            require_once 'display/newthread.php';
            break;
        case 'viewthread':
            $title = "View thread";
            require_once 'display/viewthread.php';
            break;
        default:
            $title = "Latest issues";
            require_once "display/latest_issues.php";
            $pagenum = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            $pagenum--;
            displayLatestIssues($pagenum);
            break;
    }
    return $title;
}