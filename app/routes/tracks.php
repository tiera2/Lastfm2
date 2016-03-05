<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->group('/tracks', function () {
	$this->get('', function (Request $request, Response $response, $args) {
	//	echo 'test';
	//});
		
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
		
		$rows = DB::query("SELECT today.date, today.place, yesterday.place as yp, today.track_name, today.artist_name, today.track_mbid, today.playcount, yesterday.playcount AS yc, artist.name FROM (
								SELECT place, date, artist_name, artist_mbid, track_name, track_mbid, playcount FROM tracks_top WHERE date = DATE(NOW())
							) AS today
							LEFT OUTER JOIN (SELECT * FROM tracks_top WHERE date = '2016-02-10') AS yesterday ON yesterday.track_mbid = today.track_mbid 
								AND yesterday.track_name = today.track_name AND yesterday.artist_name = today.artist_name
							INNER JOIN artist ON artist.mbid = today.artist_mbid AND artist.name=today.artist_name ORDER BY today.place");
		
		$user_name = $_SESSION['user_name'];
		$panel_heading = "Topp 500 låtar";
		$page_title = $panel_heading;
		$image = null;
		$headers = array();
		$headers[0] = "#";
		$headers[1] = "Artist";
		$headers[2] = "Track";
		$headers[3] = "Play count";
		$headers['diff'] = "# diff";
		$tablerows = array();
		foreach($rows as $row) {
			if(strlen($row['track_mbid']) > 0) {
				$link = $row['track_mbid'];
			} else {
				$link = $row['artist_name'] . '/' . $row['track_name'];
			}
			$diff = $row['yp'] - $row['place'];
			$tablerow = array();
			$tablerow[0] = $row['place'];
			$tablerow[1] = '<a href="artists/'.$row['artist_name'].'">'.$row['artist_name'].'</a>';
			$tablerow[2] = '<a href="tracks/'.$link.'">'.$row['track_name'].'</a>';;
			$tablerow[3] = $row['playcount'];
			$tablerow['diff'] = ($row['yp'] > 0 ? $diff : 'ny');
			$tablerows[] = $tablerow;
		}
		include('webinterface/views/header.php');
		include('webinterface/views/list.php');
		include('webinterface/views/footer.php');
	});

	$this->get('/{mbid}', function (Request $request, Response $response, $args) {
		$login = new Login();

		$track = Track::getInfo(null, null, $args['mbid']);
		$track_name = $track->getName();
		$artist_name = $track->getArtist()->getName();
		$playcount = $track->getPlaycount();
		$last_played = $track->getLastPlayed();
		$min = DB::queryFirstField("SELECT MIN(place) FROM tracks_top WHERE track_mbid = %s", $args['mbid']);
		$position = DB::queryFirstField("SELECT MIN(place) FROM tracks_top WHERE track_mbid = %s AND date = DATE(NOW())", $args['mbid']);
		if($min == null) {
			$min = '-';
			$position = '-';
		}
		$panel_heading = "<a href='/lastfm2/artists/$artist_name'>$artist_name</a> - $track_name
		<br />Nuvarande plats: $position
		<br />Högsta plats: $min
		<br />Senast spelad $last_played";
		$page_title = $artist_name . ' - ' . $track_name ;
		$image = null;
		$headers = array();
		$tablerows = array();
		include('webinterface/views/header.php');
		include('webinterface/views/list.php');
		include('webinterface/views/footer.php');
	});

	$this->get('/{artist}/{track}', function (Request $request, Response $response, $args) {
		$login = new Login();

		$track = Track::getInfo($args['artist'], $args['track']);
		$track_name = $track->getName();
		$artist_name = $track->getArtist()->getName();
		$playcount = $track->getPlaycount();
		$last_played = $track->getLastPlayed();
		$min = DB::queryFirstField("SELECT MIN(place) FROM tracks_top WHERE track_name = %s AND artist_name = %s", $args['track'], $args['artist']);
		$position = DB::queryFirstField("SELECT MIN(place) FROM tracks_top 
											WHERE track_name = %s AND artist_name = %s AND date = DATE(NOW())", 
											$args['track'],
											$args['artist']
		);
		$panel_heading = "<a href='/lastfm2/artists/$artist_name'>$artist_name</a> - $track_name
		<br />Nuvarande plats: $position
		<br />Högsta plats: $min
		<br />Senast spelad $last_played";
		$page_title = $artist_name . ' - ' . $track_name ;
		$image = null;
		$headers = array();
		$tablerows = array();
		include('webinterface/views/header.php');
		include('webinterface/views/list.php');
		include('webinterface/views/footer.php');
	});
});