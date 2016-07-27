<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8"/>
		<title>Document</title>
	</head>
	<body>
		<list-scope <?= \Pasap\Pasap::scope([
			'one'   => 1,
			'two'   => 2,
			'three' => 3
		]) ?> />
	</body>
</html>
