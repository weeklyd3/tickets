<?php
$all = json_decode(file_get_contents('tickets/idsAndOrder.json'));
function getPage($from = $_GET, $perpage = 50) {
    $num = isset($from['page']) ? (int) $from['page'] : 0;
    // in the interface, they start at 1
    // here, they start at 0
    $list = $all->all;
    return array_slice($list, $num * $perpage, $perpage);
}
function latestIssuesHeader($page) {
    
}