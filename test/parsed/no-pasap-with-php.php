<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Pasap Test</title>
	</head>
	<body>
		<header>
			<nav>
				<?php foreach([ "Wow", "Amaze", "Youpi" ] as $a): ?>
					<a href=""><?= $a ?></a>
				<?php endforeach; ?>
			</nav>
		</header>
		<main>
			<h1>The list</h1>
			<ul>
				<?php foreach([ "Wow", "Amaze", "Youpi" ] as $a): ?>
					<li><?= $a ?></li>
				<?php endforeach; ?>
			</ul>
		</main>
	</body>
</html>
