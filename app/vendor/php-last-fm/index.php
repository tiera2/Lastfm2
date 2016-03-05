<?php

/*include 'Media.php';
include 'Artist.php';
include 'caller\CallerFactory.php';
include 'caller\CallerFactory.php';

Artist::getTopTracks('Nirvana');*/
include 'lastfm.api.php';
lastfm_autoload('User');
lastfm_autoload('Caller');
$caller = CurlCaller::getInstance();
//var_dump($caller);

$caller->setApiKey('8c47de18baa4ab721e656ef552353db6');
$caller->setApiSecret('2b2d9f687d954a240c38f2de89e8a0d2');

$tracks = User::getTopArtists('tobbee86');
$i = 1;
foreach($tracks as $key=>$value) {
	
	//echo $key . '<br /><br />';
	echo "<img src='" . $value->getImage(0) . "' >";
	echo $i++ . '. ' .$value->getName() . ' ' . $value->getPlayCount() . '<br /><br />';
	
	//var_dump($value);
}

var_dump($tracks);