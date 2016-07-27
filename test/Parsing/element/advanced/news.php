<article class="News">
	<h1 class="title"><?= $this->children('content')->children('title')->children() ?></h1>
	<div class="about">
		<em>
			By <?= $this->children('about')->children('author')->children() ?>,
			At <?= $this->children('about')->children('created')->children() ?>
		</em>
	</div>
	<div class="content">
		<?= $this->children('content')->children()->but('title') ?>
	</div>
</article>
