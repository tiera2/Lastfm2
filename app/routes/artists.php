<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->group('/artists', function () {
	require_once 'app/vendor/php-last-fm/lastfm.api.php';
	require_once 'app/vendor/MeekroDB/meekrodb.2.3.class.php';

	$this->get('/{name}', function (Request $request, Response $response, $args) {
		$config = parse_ini_file('/../../config.ini', true); // Assuming your php is in the root directory of your web server, so placing the file where it can't be seen by prying eyes!
		$login = new Login();
		$host = $config['database']['host'];
		$user = $config['database']['user'];
		$password = $config['database']['password'];
		$lastfmapikey = $config['lastfm']['apikey'];
		$lastfmapisecret = $config['lastfm']['apisecret'];
		DB::$user = $user;
		DB::$password = $password;
		DB::$dbName = 'lastfm';
		DB::$encoding = 'utf8';
		DB::query("SELECT * FROM tracks");
		lastfm_autoload('Artist');
		lastfm_autoload('Caller');
		$caller = CurlCaller::getInstance();
		//var_dump($caller);

		$caller->setApiKey($lastfmapikey);
		$caller->setApiSecret($lastfmapisecret);

		$artist = Artist::getInfo($args['name']);
		$tracks = Artist::getTopTracks($args['name']);
		$place = 1;
		$user_name = $_SESSION['user_name'];
		$panel_heading = "Topplåtar " . $args['name'];
		$page_title = $panel_heading;
		$image = $artist->getImage(1);
		$headers = array();
		$headers[0] = "#";
		$headers[1] = "Track";
		$headers[2] = "Play count";
		$tablerows = array();
		foreach($tracks as $key=>$value) {
			$track_name = $value->getName();
			$track_mbid = $value->getMbid();
			$artist = $value->getArtist()->getName();
			if(strlen($track_mbid) > 0) {
				$link = $track_mbid;
			} else {
				$link = $artist . '/' . $track_name;
			}
			$tablerow = array();
			$tablerow[0] = $place++;
			$tablerow[1] = "<a href='/lastfm2/tracks/$link'>$track_name</a>";
			$tablerow[2] = $value->getPlayCount();
			$tablerows[] = $tablerow;
		}
		include('webinterface/views/header.php');
		include('webinterface/views/list.php');
		include('webinterface/views/footer.php');
	});

	$this->get('', function (Request $request, Response $response, $args) {
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
		
		/*$rows = DB::query("SELECT today.date, today.place, today.mbid, today.playcount, yesterday.place as yp, yesterday.playcount AS yc, artist.name 
			FROM (SELECT * FROM `artist_top` WHERE date = DATE(NOW())) AS today 
			LEFT OUTER JOIN (SELECT * FROM artist_top WHERE date = '2016-02-06') AS yesterday ON yesterday.mbid = today.mbid AND yesterday.name = today.name
			INNER JOIN artist ON artist.mbid = today.mbid AND artist.name=today.name ORDER BY today.place");
		*/
		$rows = DB::query("SELECT todayplace AS place, name, todayplaycount AS playcount, histplace AS yp
							FROM `view_compare_historic` WHERE date = '2016-02-06'");
		$user_name = $_SESSION['user_name'];
		$panel_heading = "Topp 250 artister";
		$page_title = $panel_heading;
		$image = null;
		$headers = array();
		$headers[0] = "#";
		$headers[1] = "Name";
		$headers[2] = "Play count";
		$headers['diff'] = "# diff";
		$tablerows = array();
		foreach($rows as $row) {
			$diff = $row['yp'] - $row['place'];
			$tablerow = array();
			$tablerow[0] = $row['place'];
			$tablerow[1] = '<a href="artists/'.$row['name'].'">'.$row['name'].'</a>';
			$tablerow[2] = $row['playcount'];
			$tablerow['diff'] = ($row['yp'] > 0 ? $diff : 'ny');
			$tablerows[] = $tablerow;
		}
		include('webinterface/views/header.php');
		include('webinterface/views/list.php');
		include('webinterface/views/footer.php');
	});
});