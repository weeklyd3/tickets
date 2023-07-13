<?php
class thread {
    function __construct($title, $posts, $id, $time, $closed = false) {
        $this->title = $title;
        $this->closed = $closed;
        $this->posts = array();
        $this->id = $id;
        $this->time = $time;
        foreach ($posts as $p) {
            error_log($p);
            array_push($this->posts, getPostById($p));
        }
    }
    function __toString() {
        $o = array();
        $o['title'] = $this->title;
        $o['closed'] = $this->closed;
        $o['time'] = $this->time;
        $o['posts'] = array();
        foreach ($this->posts as $p) {
            array_push($o['posts'], $p->id);
        }
        return json_encode($o);
    }
}
class post {
    function __construct($user, $content, $id, $parent, $time) {
        $this->parent = $parent;
        $this->author = $user;
        $this->content = $content;
        $this->id = $id;
        $this->time = $time;
    }
    function __toString() {
        $clone = unserialize(serialize($this));
        return json_encode($clone);
    }
}
class user {
    function __construct($id, $remoteinfo, $joined, $refreshed) {
        $this->id = $id;
        $this->joined = $joined;
        $this->remote = $remoteinfo;
        $this->refreshed = $refreshed;
    }
}
$postids = json_decode(file_get_contents('data/posts/ids.json'));
function getUserById(int $id): ?user {
    if (!file_exists("data/users/$id")) return null;
    $userinfo = json_decode(file_get_contents("data/users/$id/info.json"));
    return new user($userinfo->stats->userid, $userinfo->stats, $userinfo->joined, $userinfo->refreshed);
}
function getPostById(int $id): ?post {
    if (!file_exists("data/posts/$id")) return null;
    $postinfo = json_decode(file_get_contents("data/posts/$id/info.json"));
    return new post($postinfo->author, $postinfo->content, $id, $postinfo->parent, $postinfo->time);
}
function getThreadById(int $id): ?thread {
    if (!file_exists("data/tickets/$id")) return null;
    $postinfo = json_decode(file_get_contents("data/tickets/$id/info.json"));
    return new thread($postinfo->title, $postinfo->posts, $id, $postinfo->time, $postinfo->closed); // title, posts, id, closed
}
function newPost($thread, $content) {
    if (!isset($_SESSION['userid'])) return false;
    $postid = json_decode(file_get_contents("data/posts/ids.json"));
    mkdir("data/posts/$postid");
    $post = new post($_SESSION['userid'], $content, $postid, $thread->id, time());
    array_push($thread->posts, $post);
    file_put_contents("data/tickets/{$thread->id}/info.json", (string) $thread);
    file_put_contents("data/posts/$postid/info.json", (string) $post);
    $postid++;
    file_put_contents("data/posts/ids.json", $postid);
}
function newThread($title, $firstpost) {
    if (!isset($_SESSION['userid'])) return false;
    $threadid = json_decode(file_get_contents("data/tickets/idsAndOrder.json"));
    $curid = $threadid->currentid;
    mkdir("data/tickets/$curid");
    array_unshift($threadid->all, $curid);
    $t = new thread($title, array(), $curid, time(), false);
    newPost($t, $firstpost);
    file_put_contents("data/tickets/$curid/info.json", (string) $t);
    $threadid->currentid++;
    file_put_contents("data/tickets/idsAndOrder.json", json_encode($threadid));
}
function getTime(int $time) {
    ?><span class="time" title="<?php echo $time; ?>"><?php echo date('l j F Y h:i:s A', $time); ?></span><?php
}
function displayUser($user) {
    if (!$user) {
        ?><span class="baduser">nonexistent user</span><?php
        return;
    }
    ?><a href="index.php?do=user&amp;uid=<?php echo $user->id; ?>"><?php echo htmlspecialchars($user->remote->name); ?></a><?php
}