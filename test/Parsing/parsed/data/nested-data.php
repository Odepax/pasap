<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8"/>
		<title>Document</title>
	</head>
	<body>
		<group-set <?= \Pasap\Pasap::data([ 'two' => 2, 'three' => 3 ]) ?>>
			<list-set <?= \Pasap\Pasap::data([ 'one' => 1, 'two' => 22 ]) ?> />
		</group-set>

		<group-set <?= \Pasap\Pasap::data([ 'two' => 2, 'three' => 3 ]) ?>>
			<list-scope <?= \Pasap\Pasap::scope([ 'one' => 1, 'two' => 22 ]) ?> />
		</group-set>

		<group-scope <?= \Pasap\Pasap::scope([ 'two' => 2, 'three' => 3 ]) ?>>
			<list-set <?= \Pasap\Pasap::data([ 'one' => 1, 'two' => 22 ]) ?> />
		</group-scope>

		<group-scope <?= \Pasap\Pasap::scope([ 'two' => 2, 'three' => 3]) ?>>
			<list-scope <?= \Pasap\Pasap::scope([ 'one' => 1, 'two' => 22 ]) ?> />
		</group-scope>
	</body>
</html>
