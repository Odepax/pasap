<?php

namespace Pasap\Test;

use PHPUnit\Framework\TestCase;

/**
 * Here to provide a `setUpBeforeClass` method and be extended.
 */
abstract class BasicTest extends TestCase
{
	/** @var \DOMDocument A document used to generate the elements below. */
	protected static $document;

	/** @var \DOMElement A native element (`a` tag) used for testing. */
	protected static $a;

	/** @var \DOMElement A native element (`article` tag) used for testing. */
	protected static $article;

	/** @var \DOMElement A native element (`body` tag) used for testing. */
	protected static $body;

	/** @var \DOMElement A native element (`doge` tag) used for testing. */
	protected static $doge;

	/** @var \DOMElement A native element (`news` tag) used for testing. */
	protected static $news;

	/** Ah, look at all those static variables... */
	public static function setUpBeforeClass ()
	{
		static::$document = new \DOMDocument("1.0", "UTF-8");

		static::$a = static::$document->createElement("a");
		static::$a->appendChild(new \DOMAttr("href", "http://gihub.com"));
		static::$a->appendChild(new \DOMAttr("class", "button"));

		static::$article = static::$document->createElement("article");
		static::$article->appendChild(new \DOMAttr("class", "news_content"));

		static::$body = static::$document->createElement("body");

		static::$doge = static::$document->createElement("doge");
		static::$doge->appendChild(new \DOMAttr("wow", "amaze"));
		static::$doge->appendChild(new \DOMAttr("such", "custom tag"));

		static::$news = static::$document->createElement("news");
		static::$news->appendChild(new \DOMAttr("title", "This is a Revolution"));
		static::$news->appendChild(new \DOMAttr("created", "12-12-12"));

		static::$body->appendChild(static::$article);
		static::$article->appendChild(new \DOMText("Click here: "));
		static::$article->appendChild(static::$a);

		static::$body->appendChild(static::$news);
		static::$news->appendChild(static::$doge);
	}
}
