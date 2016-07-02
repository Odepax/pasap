<article class="news">
	<header class="news_meta">
		<news-title><?= $this->attr("title") ?></news-title>
		<em><?= $this->attr("author") ?></em>
	</header>
	<main class="news_content">
		<?= $this->children() ?>
	</main>
</article>
