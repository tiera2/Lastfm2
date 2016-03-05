<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/user/{name}', function (Request $request, Response $response, $args) {
	require_once 'app/vendor/MeekroDB/meekrodb.2.3.class.php';

	$config = parse_ini_file('/../../config.ini', true); // Assuming your php is in the root directory of your web server, so placing the file where it can't be seen by prying eyes!
	$host = $config['database']['host'];
	$user = $config['database']['user'];
	$password = $config['database']['password'];
	$login = new Login();
	if($login->isUserLoggedIn()) {
		DB::$user = $user;
		DB::$password = $password;
		DB::$dbName = 'lastfm';
		DB::$encoding = 'utf8';
		$lastfminfo = DB::queryFirstRow("SELECT apikey, apisecret FROM lastfminfo INNER JOIN users ON user_id = id WHERE user_name = %s", $args['name']);
		$currentUser = $args['name'] === $_SESSION['user_name'];
		$edit = false;
		$page_title = $args['name'];
		include('webinterface/views/header.php');
		include("webinterface/views/settings.php");
		include('webinterface/views/footer.php');
		
	} else {
		header("Location: ./");
		die();
	}
});

$app->get('/user/{name}/edit', function (Request $request, Response $response, $args) {
	require_once 'app/vendor/MeekroDB/meekrodb.2.3.class.php';

	$config = parse_ini_file('/../../config.ini', true); // Assuming your php is in the root directory of your web server, so placing the file where it can't be seen by prying eyes!
	$host = $config['database']['host'];
	$user = $config['database']['user'];
	$password = $config['database']['password'];
	$login = new Login();
	if($login->isUserLoggedIn() && $args['name'] === $_SESSION['user_name']) {
		DB::$user = $user;
		DB::$password = $password;
		DB::$dbName = 'lastfm';
		DB::$encoding = 'utf8';
		$lastfminfo = DB::queryFirstRow("SELECT apikey, apisecret FROM lastfminfo INNER JOIN users ON user_id = id WHERE user_name = %s", $args['name']);
		$currentUser = $args['name'] === $_SESSION['user_name'];
		$edit = true;
		$page_title = $args['name'];
		include('webinterface/views/header.php');
		include("webinterface/views/settings.php");
		include('webinterface/views/footer.php');
	} else {
		header("Location: ./");
		die();
	}
	
});

$app->post('/user/{name}/edit', function (Request $request, Response $response, $args) {
	require_once 'app/vendor/MeekroDB/meekrodb.2.3.class.php';
	$config = parse_ini_file('/../../config.ini', true); // Assuming your php is in the root directory of your web server, so placing the file where it can't be seen by prying eyes!
	$host = $config['database']['host'];
	$user = $config['database']['user'];
	$password = $config['database']['password'];
	$login = new Login();
	if($login->isUserLoggedIn() && $args['name'] === $_SESSION['user_name']) {
		DB::$user = $user;
		DB::$password = $password;
		DB::$dbName = 'lastfm';
		DB::$encoding = 'utf8';
		$count = DB::queryFirstField("SELECT COUNT(*) FROM lastfminfo INNER JOIN users ON user_id = id WHERE user_name = %s", $args['name']);
		if($count == 0) {
			$lastfminfo = DB::queryFirstRow("INSERT INTO lastfminfo SELECT user_id, %s, %s FROM users WHERE user_name = %s", 
			$_POST['user_apikey'],
			$_POST['user_apisecret'],
			$args['name']);
		} else {
		$lastfminfo = DB::queryFirstRow("UPDATE lastfminfo INNER JOIN users ON user_id = id SET apikey=%s, apisecret=%s WHERE user_name = %s", 
			$_POST['user_apikey'],
			$_POST['user_apisecret'],
			$args['name']);
		}
	}
	header("Location: ./");
	die();
});