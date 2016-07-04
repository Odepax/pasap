<?php

use Pasap\Pasap;

$o = new stdClass();

$o->name = "o";
$o->size =  1;

?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8"/>
		<title>Pasap Test</title>
	</head>
	<dataset:one <?= Pasap::data([ "one" => "1", "numbers" => [ 1, 2, 3 ] ]) ?>>Amaze</dataset:one>
	<dataset:two <?= Pasap::data([ "object" => $o ]) ?>>
		<dataset:three <?= Pasap::data([ "position" => "inside" ]) ?>/>
	</dataset:two>
</html>
