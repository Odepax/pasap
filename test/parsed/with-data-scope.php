<?php use Pasap\Pasap; ?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8"/>
		<title>Pasap Test</title>
	</head>
	<datascope:parent <?= Pasap::scope([
		"number" => 12,
		"name" => "big bro",
		"wow" => "amaze"
	]) ?>>
		<datascope:child <?= Pasap::scope([
			"name" => "lit man",
			"more" => "fun"
		]) ?> />
	</datascope:parent>
	<datascope:child <?= Pasap::scope([
		"name" => "yay",
		"number" => 17
	]) ?> />
</html>
