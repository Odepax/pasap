<?php foreach (explode(", ", $this->attr("files")) as $file): ?>
	<link rel="stylesheet/less" href="<?= $file ?>"/>
<?php endforeach; ?>

<script src="<?= $this->attr("js") ?>"></script>
