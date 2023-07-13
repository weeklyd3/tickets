<?php
$all = json_decode(file_get_contents('data/tickets/idsAndOrder.json'));
function getPage($all, $perpage = 50) {
    $num = isset($_GET['page']) ? (int) $_GET['page'] : 0;
    // in the interface, they start at 1
    // here, they start at 0
    $list = $all->all;
    return array_slice($list, $num * $perpage, $perpage);
}
function latestIssuesHeader($page = 0) {
    $all = json_decode(file_get_contents('data/tickets/idsAndOrder.json'));
    $pagedisplay = $page + 1;
    $title = "Issues - page $pagedisplay";
    $p = getPage($all);
    if (!count($p)) {
        $title = "No issues";
        ?><p>Nothing was found on this page. <?php
        if (isset($_SESSION['userid'])) { ?><a href="index.php?do=new">Create a new thread?</a></p><?php }
        return;
    } else {
        if (isset($_SESSION['userid'])) {
            ?><a href="index.php?do=new">New thread</a><?php
        }
    }
    return $p;
}
function displayLatestIssues($page) {
    $l = latestIssuesHeader($page);
    echo "<ul class=\"issue-list\">";
    foreach ($l as $i) {
        $t = getThreadById($i);
        echo "<li";
        if ($t->closed) echo " class=\"thread-closed\"";
        echo "><div class=\"thread-title";
        echo "\"><a href=\"";
        echo "index.php?do=viewthread&id=";
        echo htmlspecialchars(urlencode($i));
        echo "\">";
        if ($t->closed) echo "<em>[CLOSED]</em> ";
        echo htmlspecialchars($t->title);
        echo "</a> <small>(";
        echo $t->id;
        echo ")</small>";
        echo "</div>";
        echo "<div class=\"thread-author\">";
        $firstpostauthor = $t->posts[0]->author;
        displayUser(getUserById($firstpostauthor));
        echo "</div>";
        echo "<div style=\"clear: both;\"></div>";
        echo "<div class=\"thread-preview\">";
        $firstpost = $t->posts[0];
        echo htmlspecialchars(substr($firstpost->content, 0, 100));
        echo "</div>";
        echo "<div>Created: ";
        getTime($t->time);
        echo "</div>";
        echo "</li>";
    }
    echo "</ul>";
}