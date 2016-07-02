<article class="news">
	<h1 class="news_title"><?= $this->attr('title') ?></h1>
	<em class="news_author"><?= $this->attr('author') ?></em>
	<div class="news_content">
		<?= $this->children() ?>
	</div>
</article>
