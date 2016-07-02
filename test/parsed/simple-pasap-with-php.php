<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Pasap Test</title>
	</head>
	<body>
		<h1>Amaze</h1>
		<list>
			<?php foreach ([ "One", "Two", "Three" ] as $item): ?>
				<list-item><?= $item ?></list-item>
			<?php endforeach; ?>
		</list>
	</body>
</html>
