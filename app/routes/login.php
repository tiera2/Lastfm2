<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/login', function (Request $request, Response $response) {
	$login = new Login();

	if ($login->isUserLoggedIn() == true) {
	    header("Location: ./");
		die();

	} else {
	    include("webinterface/views/login.php");
	}
})->setName('login-get');;

$app->post('/login', function (Request $request, Response $response) {
	$_POST['user_name'] = $_POST['user_email'];
	$login = new Login();
	if ($login->isUserLoggedIn() == true) {
	    header("Location: ./");
		die();
	} else {
		include("webinterface/views/login.php");
	    //header("Location: ./login");
		//die();
	}
});

$app->get('/logout', function (Request $request, Response $response) {
	$login = new Login();
	if($login->isUserLoggedIn()) {
		$login->doLogOut();
	}
	header("Location: ./");
	die();
});

$app->get('/register', function (Request $request, Response $response) {
	$login = new Login();

	if ($login->isUserLoggedIn() == true) {
	    header("Location: ./");
		die();

	} else {
	    include("app/vendor/php-login-minimal/views/register.php");
	}
});

$app->post('/register', function (Request $request, Response $response) {
	require_once("/app/vendor/php-login-minimal/classes/Registration.php");
	$registration = new Registration();
	
	if (isset($registration)) {
	    if ($registration->errors) {
	        foreach ($registration->errors as $error) {
	            echo $error;
	        }
	    }
	    if ($registration->messages) {
	        foreach ($registration->messages as $message) {
	            echo $message;
	        }
	    }
	}
	echo '<a href="./login">Back to Login Page</a>';
});