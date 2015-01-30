<?php
require '../src/library/CRSS.php';

$feed = new \matl14\library\CRSS([
	'http://feeds.feedburner.com/SleepioBlog'
]);
?>

<!doctype html>
<meta charset=utf8>
<title>RSS Example</title>
<h1>RSS Example</h1>
<?=$feed->printFeed()?>