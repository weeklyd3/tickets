<?php
function display_user($user) {
    $config = get_config();
    ?><div class="user-profile-top">
        <div class="username"><?php echo htmlspecialchars($user->remote->name); ?> <small>(<a href="<?php echo htmlspecialchars($config->{'wiki-client'}); ?>?title=User:<?php echo htmlspecialchars(urlencode($user->remote->name)); ?>">remote</a>)</small></div>
        <ul class="stats">
            <?php 
            $c = $user->remote->creationDate;
            $j = $user->joined;
            $r = $user->refreshed;
            ?>
            <li>Remote account created <?php getTime($c); ?></li>
            <li>Local account created <?php getTime($j); ?></li>
            <li>Last refreshed <?php getTime($r); ?></li>
        </ul>
    </div><?php
}