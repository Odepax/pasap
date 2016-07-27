<section class="DataSet">
	<dl>
		<dt>One</dt> -- <dd><?= $this->data('one') ?></dd>
		<dt>Two</dt> -- <dd><?= $this->data('two') ?></dd>
		<dt>Three</dt> -- <dd><?= $this->data('three') ?></dd>
	</dl>
	<div class="children">
		<?= $this->children() ?>
	</div>
</section>
