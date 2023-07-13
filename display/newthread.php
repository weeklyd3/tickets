<?php
if (isset($_POST['doit'])) {
    $title = $_POST['title'];
    $content = $_POST['firstpost'];
    newThread($title, $content);
    ?><p>Thread created!</p><?php
    return;
}
?>
<p>Fill out the form below to create a new thread.</p>
<form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post">
<input type="hidden" name="doit" value="yeah" />
    <label>Title:
        <input type="text" required="required" name="title" /></label>
        <div>
            <label>First post content:
                <div><textarea name="firstpost" cols="50" rows="15" required="required"></textarea></div></label>
        </div>
        <button>Post it!</button>
</form>