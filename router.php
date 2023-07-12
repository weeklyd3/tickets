<?php
if (!defined('started')) die('STALP HECKING');
$action = isset($_GET['do']) ? $_GET['do'] : 'default';
function execute($action) {
    switch ($action) {
        default:
            $title = "Latest issues";
            require_once "display/latest_issues.php";
            latestIssuesHeader();
            displayLatestIssues();
            break;
    }
    return $title;
}