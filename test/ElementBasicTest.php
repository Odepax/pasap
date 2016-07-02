<?php

namespace Pasap\Test;

use Pasap\AttrCollection;
use Pasap\Element;
use Pasap\ElementCollection;

class ElementBasicTest extends BasicTest
{
	/** Generates some Pasap elements from native DOM elements. */
	public function testConstructorWithDOMElement ()
	{
		new Element(static::$a);
		new Element(static::$article);
		new Element(static::$body);
		new Element(static::$doge);
		new Element(static::$news);
	}

	public function testConstructorWithDOMText ()
	{
		new Element(new \DOMText("Why you no throw exception."));
		new Element(new \DOMText("   \n    "));
		new Element(new \DOMText(""));
		new Element(new \DOMText());
	}

	public function testConstructorWithString ()
	{
		new Element("Why you no throw exception.");
		new Element("   \n    ");
		new Element("");
	}

	public function testConstructorWithDOMNode ()
	{
		$this->expectException(\（ノಥ益ಥ）ノ︵┻━┻::class);

		new Element(new \DOMNode());
	}

	public function testConstructorWithDOMDocumentType ()
	{
		$this->expectException(\（ノಥ益ಥ）ノ︵┻━┻::class);

		new Element(new \DOMDocumentType());
	}

	public function testConstructorWithStdClass ()
	{
		$this->expectException(\（ノಥ益ಥ）ノ︵┻━┻::class);

		new Element(new \stdClass());
	}

	public function testConstructorWithPasapElement ()
	{
		$e = new Element("Bomb");

		$this->expectException(\（ノಥ益ಥ）ノ︵┻━┻::class);

		new Element($e);
	}

	public function testConstructorWithArray ()
	{
		$this->expectException(\（ノಥ益ಥ）ノ︵┻━┻::class);

		new Element([
			"Hi", 12, new \DOMElement("em", "All")
		]);
	}

	/**
	 * @see \Pasap\Test\ElementCollectionBasicTest::testEcho() If this test fails.
	 * @see \Pasap\Test\ElementCollectionBasicTest::testForeach() If this test fails.
	 */
	public function testEcho ()
	{
		$this->expectOutputString(""
			. "<a href=\"http://gihub.com\" class=\"button\"></a>\n"
			. "<article class=\"news_content\">Click here: <a href=\"http://gihub.com\" class=\"button\"></a></article>\n"
			. "<body><article class=\"news_content\">Click here: <a href=\"http://gihub.com\" class=\"button\"></a></article><news title=\"This is a Revolution\" created=\"12-12-12\"><doge wow=\"amaze\" such=\"custom tag\"></doge></news></body>\n"
			. "<doge wow=\"amaze\" such=\"custom tag\"></doge>\n"
			. "<news title=\"This is a Revolution\" created=\"12-12-12\"><doge wow=\"amaze\" such=\"custom tag\"></doge></news>\n"
		);

		echo new Element(static::$a) . "\n";
		echo new Element(static::$article) . "\n";
		echo new Element(static::$body) . "\n";
		echo new Element(static::$doge) . "\n";
		echo new Element(static::$news) . "\n";
	}

	public function testTag ()
	{
		$this->assertEquals("a", (new Element(static::$a))->tag());
		$this->assertEquals("article", (new Element(static::$article))->tag());
		$this->assertEquals("body", (new Element(static::$body))->tag());
		$this->assertEquals("doge", (new Element(static::$doge))->tag());
		$this->assertEquals("news", (new Element(static::$news))->tag());
		$this->assertEquals("#text", (new Element(new \DOMText("Why you no throw exception.")))->tag());
		$this->assertEquals("#text", (new Element(new \DOMText("   \n    ")))->tag());
		$this->assertEquals("#text", (new Element(new \DOMText("")))->tag());
		$this->assertEquals("#text", (new Element(new \DOMText()))->tag());
		$this->assertEquals("#text", (new Element("Why you no throw exception."))->tag());
		$this->assertEquals("#text", (new Element("   \n    "))->tag());
		$this->assertEquals("#text", (new Element(""))->tag());
	}

	public function testAttrWithoutKey ()
	{
		$this->assertInstanceOf(AttrCollection::class, (new Element(static::$a))->attr());
		$this->assertInstanceOf(AttrCollection::class, (new Element(static::$a))->attr());
		$this->assertInstanceOf(AttrCollection::class, (new Element(static::$article))->attr());
		$this->assertInstanceOf(AttrCollection::class, (new Element(static::$doge))->attr());
		$this->assertInstanceOf(AttrCollection::class, (new Element(static::$doge))->attr());
		$this->assertInstanceOf(AttrCollection::class, (new Element(static::$news))->attr());
		$this->assertInstanceOf(AttrCollection::class, (new Element(static::$news))->attr());

		$this->assertNull((new Element(new \DOMText("Why you no throw exception.")))->attr());
		$this->assertNull((new Element(new \DOMText("   \n    ")))->attr());
		$this->assertNull((new Element(new \DOMText("")))->attr());
		$this->assertNull((new Element(new \DOMText()))->attr());
		$this->assertNull((new Element("Why you no throw exception."))->attr());
		$this->assertNull((new Element("   \n    "))->attr());
		$this->assertNull((new Element(""))->attr());
	}

	public function testAttrWithStringKey ()
	{
		$this->assertEquals("http://gihub.com", (new Element(static::$a))->attr("href"));
		$this->assertEquals("button", (new Element(static::$a))->attr("class"));
		$this->assertEquals("news_content", (new Element(static::$article))->attr("class"));
		$this->assertEquals("amaze", (new Element(static::$doge))->attr("wow"));
		$this->assertEquals("custom tag", (new Element(static::$doge))->attr("such"));
		$this->assertEquals("This is a Revolution", (new Element(static::$news))->attr("title"));
		$this->assertEquals("12-12-12", (new Element(static::$news))->attr("created"));

		$this->assertNull((new Element(static::$a))->attr("id"));
		$this->assertNull((new Element(static::$article))->attr("data-god"));
		$this->assertNull((new Element(static::$body))->attr(""));
		$this->assertNull((new Element(static::$body))->attr("troll"));
		$this->assertNull((new Element(static::$body))->attr("class"));
		$this->assertNull((new Element(static::$news))->attr("author"));

		$this->assertNull((new Element(new \DOMText("Why you no throw exception.")))->attr("one"));
		$this->assertNull((new Element(new \DOMText("   \n    ")))->attr("href"));
		$this->assertNull((new Element(new \DOMText("")))->attr("two"));
		$this->assertNull((new Element(new \DOMText()))->attr("class"));
		$this->assertNull((new Element("Why you no throw exception."))->attr("data-doge"));
		$this->assertNull((new Element("   \n    "))->attr("id"));
		$this->assertNull((new Element(""))->attr("three"));
	}

	public function testAttrWithStdClassKey ()
	{
		$this->expectException(\（ノಥ益ಥ）ノ︵┻━┻::class);

		(new Element(static::$a))->attr(new \stdClass());
	}

	public function testAttrWithArrayKey ()
	{
		$this->expectException(\（ノಥ益ಥ）ノ︵┻━┻::class);

		(new Element(static::$a))->attr([
			12, "Hi", new \DOMElement("li", "Let's fail!")
		]);
	}

	public function testAttrWithNumericKey ()
	{
		$this->expectException(\（ノಥ益ಥ）ノ︵┻━┻::class);

		(new Element(static::$a))->attr(12);
	}

	public function testChildren ()
	{
		$this->assertInstanceOf(ElementCollection::class, (new Element(static::$a))->children());
		$this->assertInstanceOf(ElementCollection::class, (new Element(static::$a))->children());
		$this->assertInstanceOf(ElementCollection::class, (new Element(static::$article))->children());
		$this->assertInstanceOf(ElementCollection::class, (new Element(static::$doge))->children());
		$this->assertInstanceOf(ElementCollection::class, (new Element(static::$doge))->children());
		$this->assertInstanceOf(ElementCollection::class, (new Element(static::$news))->children());
		$this->assertInstanceOf(ElementCollection::class, (new Element(static::$news))->children());

		$this->assertNull((new Element(new \DOMText("Why you no throw exception.")))->children());
		$this->assertNull((new Element(new \DOMText("   \n    ")))->children());
		$this->assertNull((new Element(new \DOMText("")))->children());
		$this->assertNull((new Element(new \DOMText()))->children());
		$this->assertNull((new Element("Why you no throw exception."))->children());
		$this->assertNull((new Element("   \n    "))->children());
		$this->assertNull((new Element(""))->children());
	}
}
