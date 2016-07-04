<h1><?= $this->children() ?></h1>
<ul class="data-set_one">
	<li><b>one:</b> <?= $this->data("one") ?></li>
	<li><b>numbers:</b>
		<ul>
			<?php foreach ($this->data("numbers") as $i): ?>
				<li><?= $i ?></li>
			<?php endforeach; ?>
		</ul>
	</li>
	<li><b>lalala:</b> <?= $this->data("lalala") ?></li>
</ul>
