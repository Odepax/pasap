<?php

namespace Pasap\Test;

use Pasap\AttrCollection;
use Pasap\Element;

class AttrCollectionTest extends BasicTest
{
	public function testConstructorWithDOMNamedNodeMap ()
	{
		new AttrCollection(static::$a->attributes);
		new AttrCollection(static::$article->attributes);
		new AttrCollection(static::$body->attributes);
		new AttrCollection(static::$doge->attributes);
		new AttrCollection(static::$news->attributes);
	}

	public function testConstructorWithDOMElement ()
	{
		$this->expectException(\（ノಥ益ಥ）ノ︵┻━┻::class);

		new AttrCollection(static::$article);
	}

	public function testConstructorWithDOMText ()
	{
		$this->expectException(\（ノಥ益ಥ）ノ︵┻━┻::class);

		new AttrCollection(new \DOMText("Hi all"));
	}

	public function testConstructorWithString ()
	{
		$this->expectException(\（ノಥ益ಥ）ノ︵┻━┻::class);

		new AttrCollection("<a href=\"http://github.com\">...</a>");
	}

	public function testConstructorWithDOMNode ()
	{
		$this->expectException(\（ノಥ益ಥ）ノ︵┻━┻::class);

		new AttrCollection(new \DOMNode());
	}

	public function testConstructorWithDOMDocumentType ()
	{
		$this->expectException(\（ノಥ益ಥ）ノ︵┻━┻::class);

		new AttrCollection(new \DOMDocumentType());
	}

	public function testConstructorWithStdClass ()
	{
		$this->expectException(\（ノಥ益ಥ）ノ︵┻━┻::class);

		new AttrCollection(new \stdClass());
	}

	public function testConstructorWithPasapElement ()
	{
		$this->expectException(\（ノಥ益ಥ）ノ︵┻━┻::class);

		new AttrCollection(new Element(static::$doge));
	}

	public function testConstructorWithArray ()
	{
		$this->expectException(\（ノಥ益ಥ）ノ︵┻━┻::class);

		new AttrCollection([
			12,
			"Hi all",
			new \DOMAttr("class", "button-active")
		]);
	}

	public function testToString ()
	{
		$this->assertEquals("href=\"http://gihub.com\" class=\"button\"", (new AttrCollection(static::$a->attributes))->__toString());
		$this->assertEquals("class=\"news_content\"", (new AttrCollection(static::$article->attributes))->__toString());
		$this->assertEquals("", (new AttrCollection(static::$body->attributes))->__toString());
		$this->assertEquals("wow=\"amaze\" such=\"custom tag\"", (new AttrCollection(static::$doge->attributes))->__toString());
		$this->assertEquals("title=\"This is a Revolution\" created=\"12-12-12\"", (new AttrCollection(static::$news->attributes))->__toString());
	}

	public function testEcho ()
	{
		$this->expectOutputString(""
			. "href=\"http://gihub.com\" class=\"button\"\n"
			. "class=\"news_content\"\n"
			. "\n"
			. "wow=\"amaze\" such=\"custom tag\"\n"
			. "title=\"This is a Revolution\" created=\"12-12-12\"\n"
		);

		echo new AttrCollection(static::$a->attributes) . "\n";
		echo new AttrCollection(static::$article->attributes) . "\n";
		echo new AttrCollection(static::$body->attributes) . "\n";
		echo new AttrCollection(static::$doge->attributes) . "\n";
		echo new AttrCollection(static::$news->attributes) . "\n";
	}

	public function testForeach ()
	{
		$this->expectOutputString(""
			. "href => http://gihub.com\n"
			. "class => button\n"
			. "class => news_content\n"
			. "such => custom tag\n"
			. "title => This is a Revolution\n"
			. "created => 12-12-12\n"
		);

		foreach (new AttrCollection(static::$a->attributes) as $k => $v) {
			if ($k !== "wow") echo "$k => $v\n";
		}

		foreach (new AttrCollection(static::$article->attributes) as $k => $v) {
			if ($k !== "wow") echo "$k => $v\n";
		}

		foreach (new AttrCollection(static::$body->attributes) as $k => $v) {
			if ($k !== "wow") echo "$k => $v\n";
		}

		foreach (new AttrCollection(static::$doge->attributes) as $k => $v) {
			if ($k !== "wow") echo "$k => $v\n";
		}

		foreach (new AttrCollection(static::$news->attributes) as $k => $v) {
			if ($k !== "wow") echo "$k => $v\n";
		}
	}
}
