<?php

namespace Pasap\Test;

use Pasap\ElementCollection;
use Pasap\Element;

class ElementCollectionTest extends BasicTest
{
	public function testConstructorWithDOMNodeList ()
	{
		new ElementCollection(static::$a->childNodes);
		new ElementCollection(static::$article->childNodes);
		new ElementCollection(static::$body->childNodes);
		new ElementCollection(static::$doge->childNodes);
		new ElementCollection(static::$news->childNodes);
	}

	public function testConstructorWithDOMElement ()
	{
		$this->expectException(\（ノಥ益ಥ）ノ︵┻━┻::class);

		new ElementCollection(static::$article);
	}

	public function testConstructorWithDOMText ()
	{
		$this->expectException(\（ノಥ益ಥ）ノ︵┻━┻::class);

		new ElementCollection(new \DOMText("Hi all"));
	}

	public function testConstructorWithString ()
	{
		$this->expectException(\（ノಥ益ಥ）ノ︵┻━┻::class);

		new ElementCollection("<a href=\"http://github.com\">...</a>");
	}

	public function testConstructorWithDOMNode ()
	{
		$this->expectException(\（ノಥ益ಥ）ノ︵┻━┻::class);

		new ElementCollection(new \DOMNode());
	}

	public function testConstructorWithDOMDocumentType ()
	{
		$this->expectException(\（ノಥ益ಥ）ノ︵┻━┻::class);

		new ElementCollection(new \DOMDocumentType());
	}

	public function testConstructorWithStdClass ()
	{
		$this->expectException(\（ノಥ益ಥ）ノ︵┻━┻::class);

		new ElementCollection(new \stdClass());
	}

	public function testConstructorWithPasapElement ()
	{
		$this->expectException(\（ノಥ益ಥ）ノ︵┻━┻::class);

		new ElementCollection(new Element(static::$doge));
	}

	public function testConstructorWithArray ()
	{
		$this->expectException(\（ノಥ益ಥ）ノ︵┻━┻::class);

		new ElementCollection([
			12, "Hi all", new \DOMAttr("class", "button-active")
		]);
	}

	public function testForeach ()
	{
		$this->expectOutputString(""
			. "Click here: \n"
			. "<a href=\"http://gihub.com\" class=\"button\"></a>\n"
			. "<article class=\"news_content\">Click here: <a href=\"http://gihub.com\" class=\"button\"></a></article>\n"
			. "<news title=\"This is a Revolution\" created=\"12-12-12\"><doge wow=\"amaze\" such=\"custom tag\"></doge></news>\n"
			. "<doge wow=\"amaze\" such=\"custom tag\"></doge>\n"
		);

		foreach (new ElementCollection(static::$a->childNodes) as $element) {
			echo "$element\n";
		}

		foreach (new ElementCollection(static::$article->childNodes) as $element) {
			echo "$element\n";
		}

		foreach (new ElementCollection(static::$body->childNodes) as $element) {
			echo "$element\n";
		}

		foreach (new ElementCollection(static::$doge->childNodes) as $element) {
			echo "$element\n";
		}

		foreach (new ElementCollection(static::$news->childNodes) as $element) {
			echo "$element\n";
		}
	}

	public function testEcho ()
	{
		$this->expectOutputString(""
			. "\n"
			. "Click here: <a href=\"http://gihub.com\" class=\"button\"></a>\n"
			. "<article class=\"news_content\">Click here: <a href=\"http://gihub.com\" class=\"button\"></a></article><news title=\"This is a Revolution\" created=\"12-12-12\"><doge wow=\"amaze\" such=\"custom tag\"></doge></news>\n"
			. "\n"
			. "<doge wow=\"amaze\" such=\"custom tag\"></doge>\n"
		);

		echo new ElementCollection(static::$a->childNodes) . "\n";
		echo new ElementCollection(static::$article->childNodes) . "\n";
		echo new ElementCollection(static::$body->childNodes) . "\n";
		echo new ElementCollection(static::$doge->childNodes) . "\n";
		echo new ElementCollection(static::$news->childNodes) . "\n";
	}
}
