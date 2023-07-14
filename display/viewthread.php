<?php
if (!isset($_GET['id'])) {
    header('Location: index.php');
    return;
}
$id = $_GET['id'];
$thread = getThreadById($id);
if (!$thread) {
    http_response_code(404);
    $title = "Bad thread ID";
    ?><p>Return to the <a href="index.php">homepage</a> to view more threads.</p><?php
    return;
}
$title = $thread->title;
$firstpost = $thread->posts[0];
if (!isset($_SESSION['commentposttoken']) && isset($_SESSION['userid'])) $_SESSION['commentposttoken'] = sha1(time() . $_SESSION['userid'] . random_int(10, 100));

if (isset($_POST['action']) && !isset($_SESSION['userid'])) {
    ?><div class="error">We tried to post your response, but it appears that you have been logged out automatically. Below is the text you tried to submit. Please save it somewhere, log in, and attempt to submit it again.<pre><?php echo htmlspecialchars($_POST['text']); ?></pre></div><?php
} else if (isset($_POST['action']) && $_SESSION['commentposttoken'] != $_POST['token']) {
    ?><div class="error">Bad CSRF token. The text you tried to submit is shown below. If you wish to submit it, please add it again to the text box on the bottom of this page.<pre><?php echo htmlspecialchars($_POST['text']); ?></pre></div><?php
}
if (isset($_POST['action']) && $_SESSION['commentposttoken'] == $_POST['token']) {
    $_SESSION['commentposttoken'] = sha1(time() . $_SESSION['userid'] . random_int(10, 100));
    if (!isset($_SESSION['userid'])) {
        ?><div class="error">We tried to post your response, but it appears that you have been logged out automatically. Please open the login page IN A NEW TAB, log in, and then reload this page. (Send the form data again if asked.)</div><?php
    } else {
        $textToPost = $_POST['text'];
        if ($_POST['action'] == '1' && !$thread->closed) {
            $thread->closed = true;
            $textToPost .= "\n\n{$_SESSION['name']} closed this";
        }
        if ($_POST['action'] == '2' && $thread->closed) {
            $thread->closed = false;
            $textToPost .= "\n\n{$_SESSION['name']} reopened this";
        }
        newPost($thread, $textToPost);
        ?><div>Your comment was <strong>posted</strong>!</div><?php
    }
}
?>
<ul>
    <li>Created by <?php displayUser(getUserById($firstpost->author)); ?></li>
    <li>Created <?php getTime($firstpost->time); ?></li>
    <li><?php echo $thread->closed ? "Closed" : "Open"; ?></li>
</ul>
<a href="#post">To the bottom!</a>
<?php if ($thread->closed) {
    ?><div class="error"><strong>Note</strong>: This thread has been closed. It has probably been resolved.
    Replies may be ignored. Any admin on the main wiki site can re-open it. (If you're an admin and can't, try logging out and logging back in.)</div><?php
} ?>
<h2>Comments:</h2>
<ul class="thread-post-list">
<?php
foreach ($thread->posts as $post) {
    ?><li><?php displayUser(getUserById($post->author)); ?>
    at <?php getTime($post->time); ?>:<pre><?php echo htmlspecialchars($post->content); ?></pre></li><?php
}
?>
</ul>
<h2 id="post">Add a comment</h2>
<?php
if (!isset($_SESSION['userid'])) {
    ?><p>Please log in to post to this thread.</p><?php
    return;
}
?>
<p>Please add something to the discussion if you wish to comment.</p>
<form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post">
<label>Your comment:
    <div><textarea name="text" cols="50" rows="15"></textarea></div></label>
<div>
    <?php 
    if ($_SESSION['isadmin']) {
        ?><label>Administrative action to take: <select name="action">
            <option value="0">Just comment, do nothing else</option>
            <option value="1">Add comment and set closed</option>
            <option value="2">Add comment and set opened</option>
        </select></label><?php
    } else {
        ?><input type="hidden" name="action" value="0" /><?php
    } ?>
</div>
<input type="hidden" name="token" value="<?php echo $_SESSION['commentposttoken']; ?>" />
<button>Post it!</button>
</form>