<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->group('/replicate', function () {
	require 'app/vendor/php-last-fm/lastfm.api.php';
	require_once 'app/vendor/MeekroDB/meekrodb.2.3.class.php';

	$config = parse_ini_file('/../../config.ini', true); // Assuming your php is in the root directory of your web server, so placing the file where it can't be seen by prying eyes!
	$host = $config['database']['host'];
	$user = $config['database']['user'];
	$password = $config['database']['password'];
	$lastfmapikey = $config['lastfm']['apikey'];
	$lastfmapisecret = $config['lastfm']['apisecret'];

	DB::$user = $user;
	DB::$password = $password;
	DB::$dbName = 'lastfm';
	DB::$encoding = 'utf8';

	lastfm_autoload('User');
	lastfm_autoload('Caller');
	$caller = CurlCaller::getInstance();

	$caller->setApiKey($lastfmapikey);
	$caller->setApiSecret($lastfmapisecret);

	$this->get('/artists', function (Request $request, Response $response, $args) {
		$count = DB::queryFirstField("SELECT COUNT(*) FROM Artist_top WHERE date = DATE(NOW())");
		echo "Replikera artister<br />count = $count<br />";
		

		$tracks = User::getTopArtists('tobbee86');
		$place = 1;
		foreach($tracks as $key=>$value) {
			echo "<img src='" . $value->getImage(0) . "' >";
			$countArtist = DB::queryFirstField("SELECT COUNT(*) FROM Artist WHERE mbid = %s AND name = %s", 
				$value->getMbid(), 
				$value->getName()
			);
			if($countArtist == 0) {
				DB::query("INSERT INTO Artist VALUES(%s, %s, %s)",  
					$value->getMbid(), 
					$value->getName(), 
					$value->getImage(0)
				);
			}
			if($count == 0) {
				DB::query("INSERT INTO Artist_top VALUES(0, NOW(), %i, %s, %s, %i)", 
					$place, 
					$value->getMbid(), 
					$value->getName(), 
					$value->getPlayCount()
				);
			} else {
				DB::query("UPDATE Artist_top Set mbid=%s, name=%s, playcount=%i WHERE date=DATE(NOW()) AND place = %i", 
					$value->getMbid(), 
					$value->getName(), 
					$value->getPlayCount(), 
					$place
				);
			}
			echo $place++ . '. <a href="./artists/' .$value->getName() . '">' .$value->getName() . '</a> ' . $value->getPlayCount() . ' ' . $value->getMbid() . '<br /><br />';
		}
		$response = 'test';
	});

	$this->get('/albums', function (Request $request, Response $response, $args) {
		$response->getBody()->write("Album not yet implemented");
		return $response;
	});

	$this->get('/tracks', function (Request $request, Response $response, $args) {
		$tracks = User::getTopTracks('tobbee86');
		$place = 1;
		$count = DB::queryFirstField("SELECT COUNT(*) FROM tracks_top WHERE date = DATE(NOW())");
		foreach($tracks as $key=>$value) {
			$artist = $value->getArtist()->getName();
			$track_mbid = $value->getMbid();
			$track_name = $value->getName();
			$artist_mbid = $value->getArtist()->getMbid();
			$artist_name = $value->getName();
			$playcount = $value->getPlayCount();

			$countArtist = DB::queryFirstField("SELECT COUNT(*) FROM Artist WHERE mbid = %s AND name = %s", 
				$value->getArtist()->getMbid(), 
				$value->getArtist()->getName()
			);
			if($countArtist == 0) {
				DB::query("INSERT INTO Artist VALUES(%s, %s, %s)",  
					$value->getArtist()->getMbid(), 
					$value->getArtist()->getName(), 
					$value->getArtist()->getImage(0)
				);
			}
			if($count == 0) {
				DB::query("INSERT INTO tracks_top VALUES(0, NOW(), %i, %s, %s, %s, %s, %i)", 
					$place, 
					$value->getArtist()->getMbid(), 
					$value->getArtist()->getName(),
					$value->getMbid(), 
					$value->getName(),
					$value->getPlayCount()
				);
			} else {
				DB::query("UPDATE tracks_top Set artist_mbid=%s, artist_name=%s, track_mbid=%s, track_name=%s, playcount=%i 
							WHERE date=DATE(NOW()) AND place = %i", 
					$value->getArtist()->getMbid(), 
					$value->getArtist()->getName(),
					$value->getMbid(), 
					$value->getName(), 
					$value->getPlayCount(), 
					$place
				);
			}

			echo "$place. $artist_name($artist_mbid) $track_name($track_mbid) $playcount<br />";
			$place++;
		}
		return $response;
	});
});