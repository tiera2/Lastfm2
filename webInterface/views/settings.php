      <!-- Main component for a primary marketing message or call to action -->
      <div class="panel panel-default">
        <!-- Default panel contents -->
        <div class="panel-heading"><?php echo $_SESSION['user_name'] ?></div>
        <?php if($edit) { ?>
        <form method="post" name="loginform">
          <label for="input_username">Username</label>
          <input id="input_username" class="login_input" type="text" name="user_name" required value="<?php echo $args['name'] ?>" readonly="true" /><br />
          <label for="input-apikey">API key</label>
          <input id="input-apikey" class="login_input" type="text" name="user_apikey" autocomplete="off" required value="<?php echo $lastfminfo['apikey']?>" /><br />
          <label for="input-apisecret">API secret</label>
          <input id="input-apisecret" class="login_input" type="text" name="user_apisecret" autocomplete="off" required value="<?php echo $lastfminfo['apisecret']?>" /><br />
          <input type="submit" name="save" value="Save" />
        </form>
        <?php } else { ?>
          <div><span>Username: </span><span><?php echo $args['name'] ?></span></div>
          <?php if($currentUser) { ?>
          <div><span>API key: </span><span><?php echo $lastfminfo['apikey'] ?></span></div>
          <div><span>API secret: </span><span><?php echo $lastfminfo['apisecret'] ?></span></div>
          <div><a href="<?php echo $_SESSION['user_name'] . '/edit' ?>">Change</a></div>
        <?php } } ?>
      </div>