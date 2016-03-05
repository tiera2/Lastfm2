<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Last.FM login</title>

    <!-- Bootstrap -->
    <link href="webinterface/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="webinterface/css/signin.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>


		<div class="container">
      <?php
    // show potential errors / feedback (from login object)
    if (isset($login)) {
        if ($login->errors) {
            foreach ($login->errors as $error) {
                echo "<div class='alert alert-danger' role='alert'>$error</div>";
            }
        }
        if ($login->messages) {
            foreach ($login->messages as $message) {
                echo "<div class='alert alert-danger' role='alert'>$message</div>";
            }
        }
        echo '</div>';
    }
    ?>
      <form class="form-signin" method="post" action="./login" name="loginform">
        <h2 class="form-signin-heading">Please sign in</h2>
        <label for="inputEmail" class="sr-only">Email address</label>
        <input type="email" id="inputEmail" class="form-control" placeholder="Email address" name='user_email' required autofocus>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" id="inputPassword" class="form-control" placeholder="Password" name='user_password' required>
        <div class="checkbox">
          <label>
            <input type="checkbox" value="remember-me"> Remember me
          </label>
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="submit" name="login">Sign in</button>
      </form>
      <div id='register'><a href="./register">Register new account</a></div>
    </div> <!-- /container -->

		

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>