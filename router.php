<?php
if (!defined('started')) die('STALP HECKING');
$action = isset($_GET['do']) ? $_GET['do'] : 'default';
function execute($action) {
    switch ($action) {
        default:
            $title = "Latest issues";
            echo 'blablabla';
            break;
    }
    return $title;
}