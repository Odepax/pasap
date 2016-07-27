<section class="DataScope">
	<dl>
		<dt>One</dt> -- <dd><?= $this->scope('one') ?></dd>
		<dt>Two</dt> -- <dd><?= $this->scope('two') ?></dd>
		<dt>Three</dt> -- <dd><?= $this->scope('three') ?></dd>
	</dl>
	<div class="children">
		<?= $this->children() ?>
	</div>
</section>
