<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'app/vendor/autoload.php';
// include the configs / constants for the database connection
require_once("app/vendor/php-login-minimal/config/db.php");
// load the login class
require_once("app/vendor/php-login-minimal/classes/Login.php");

$app = new \Slim\App;

$app->add(function (Request $request, Response $response, callable $next) {
    $uri = $request->getUri();
    $path = $uri->getPath();
    if ($path != '/' && substr($path, -1) == '/') {
        // permanently redirect paths with a trailing slash
        // to their non-trailing counterpart
        $uri = $uri->withPath(substr($path, 0, -1));
        return $response->withRedirect((string)$uri, 301);
    }

    return $next($request, $response);
});


$app->get('/', function (Request $request, Response $response) {
	$login = new Login();

	// ... ask if we are logged in here:
	if ($login->isUserLoggedIn() == true) {
		header("Location: ./artists");
		die();
	} else {
		header("Location: ./login");
		die();
	}
	
});

$app->get('/api/tracks', function (Request $request, Response $response, $args) {
		
		require_once 'app/vendor/MeekroDB/meekrodb.2.3.class.php';
		$config = parse_ini_file('/../../config.ini', true); // Assuming your php is in the root directory of your web server, so placing the file where it can't be seen by prying eyes!
		$login = new Login();
		$host = $config['database']['host'];
		$user = $config['database']['user'];
		$password = $config['database']['password'];

		DB::$user = $user;
		DB::$password = $password;
		DB::$dbName = 'lastfm';
		DB::$encoding = 'utf8';
		
		$rows = DB::query("SELECT today.date, yesterday.date AS compdate, today.place, yesterday.place as yp, today.track_name, 
									today.artist_name, today.track_mbid, 
									today.playcount, yesterday.playcount AS yc, artist.name, today.artist_mbid FROM (
								SELECT place, date, artist_name, artist_mbid, track_name, track_mbid, playcount FROM tracks_top WHERE date = DATE(NOW())
							) AS today
							LEFT OUTER JOIN (SELECT * FROM tracks_top WHERE date = '2016-02-10') AS yesterday ON yesterday.track_mbid = today.track_mbid 
								AND yesterday.track_name = today.track_name AND yesterday.artist_name = today.artist_name
							INNER JOIN artist ON artist.mbid = today.artist_mbid AND artist.name=today.artist_name ORDER BY today.place");
		$tablerows = array();
		foreach($rows as $row) {
			$diff = $row['yp'] - $row['place'];
			$tablerow = array();
			$artist = array();
			$track = array();
			$history = array();
			$place = array();
			$oldplace = array();
			$artist['name'] = $row['artist_name'];
			$artist['mbid'] = $row['artist_mbid'];
			$track['name'] = $row['track_name'];
			$track['mbid'] = $row['track_mbid'];
			$place['place'] = $row['place'];
			$place['playcount'] = $row['playcount'];
			$place['date'] = $row['date'];
			$oldplace['place'] = $row['yp'];
			$oldplace['playcount'] = $row['yc'];
			$oldplace['date'] = $row['compdate'];
			$history['today'] = $place;
			$history['old'] = $oldplace;
			$tablerow['place'] = $place['place'];
			$tablerow['artist'] = $artist;
			$tablerow['track'] = $track;
			$tablerow['history'] = $history;
			$tablerows[] = $tablerow;
		}
		header("Content-Type: application/json");
		echo json_encode($tablerows, JSON_UNESCAPED_UNICODE);
		exit;
	});

/** LOGIN Routes **/
require 'app/routes/login.php';

/** REPLICATE Routes **/
require 'app/routes/replicate.php';

/** USER Routes **/
require 'app/routes/user.php';

/** ARTISTS Routes **/
require 'app/routes/artists.php';

/** ARTISTS Routes **/
require 'app/routes/tracks.php';




$app->run();