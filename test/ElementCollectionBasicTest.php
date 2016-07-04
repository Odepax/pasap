<?php

namespace Pasap\Test;

use Pasap\ElementCollection;
use Pasap\Element;

class ElementCollectionTest extends BasicTest
{
	public function testConstructorWithDOMNodeList ()
	{
		new ElementCollection(static::$a->childNodes, new Element(static::$pasap));
		new ElementCollection(static::$article->childNodes, new Element(static::$pasap));
		new ElementCollection(static::$body->childNodes, new Element(static::$pasap));
		new ElementCollection(static::$doge->childNodes, new Element(static::$pasap));
		new ElementCollection(static::$news->childNodes, new Element(static::$pasap));
	}

	public function testConstructorWithDOMElement ()
	{
		$this->expectException(\（ノಥ益ಥ）ノ︵┻━┻::class);

		new ElementCollection(static::$article, new Element(static::$pasap));
	}

	public function testConstructorWithDOMText ()
	{
		$this->expectException(\（ノಥ益ಥ）ノ︵┻━┻::class);

		new ElementCollection(new \DOMText("Hi all"), new Element(static::$pasap));
	}

	public function testConstructorWithString ()
	{
		$this->expectException(\（ノಥ益ಥ）ノ︵┻━┻::class);

		new ElementCollection("<a href=\"http://github.com\">...</a>", new Element(static::$pasap));
	}

	public function testConstructorWithDOMNode ()
	{
		$this->expectException(\（ノಥ益ಥ）ノ︵┻━┻::class);

		new ElementCollection(new \DOMNode(), new Element(static::$pasap));
	}

	public function testConstructorWithDOMDocumentType ()
	{
		$this->expectException(\（ノಥ益ಥ）ノ︵┻━┻::class);

		new ElementCollection(new \DOMDocumentType(), new Element(static::$pasap));
	}

	public function testConstructorWithStdClass ()
	{
		$this->expectException(\（ノಥ益ಥ）ノ︵┻━┻::class);

		new ElementCollection(new \stdClass(), new Element(static::$pasap));
	}

	public function testConstructorWithPasapElement ()
	{
		$this->expectException(\（ノಥ益ಥ）ノ︵┻━┻::class);

		new ElementCollection(new Element(static::$doge), new Element(static::$pasap));
	}

	public function testConstructorWithArray ()
	{
		$this->expectException(\（ノಥ益ಥ）ノ︵┻━┻::class);

		new ElementCollection([
			12, "Hi all", new \DOMAttr("class", "button-active")
		], new Element(static::$pasap));
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

		foreach (new ElementCollection(static::$a->childNodes, new Element(static::$pasap)) as $element) {
			echo "$element\n";
		}

		foreach (new ElementCollection(static::$article->childNodes, new Element(static::$pasap)) as $element) {
			echo "$element\n";
		}

		foreach (new ElementCollection(static::$body->childNodes, new Element(static::$pasap)) as $element) {
			echo "$element\n";
		}

		foreach (new ElementCollection(static::$doge->childNodes, new Element(static::$pasap)) as $element) {
			echo "$element\n";
		}

		foreach (new ElementCollection(static::$news->childNodes, new Element(static::$pasap)) as $element) {
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

		echo new ElementCollection(static::$a->childNodes, new Element(static::$pasap)) . "\n";
		echo new ElementCollection(static::$article->childNodes, new Element(static::$pasap)) . "\n";
		echo new ElementCollection(static::$body->childNodes, new Element(static::$pasap)) . "\n";
		echo new ElementCollection(static::$doge->childNodes, new Element(static::$pasap)) . "\n";
		echo new ElementCollection(static::$news->childNodes, new Element(static::$pasap)) . "\n";
	}

	public function testParent ()
	{
		foreach (new ElementCollection(static::$a->childNodes, new Element(static::$pasap)) as $child) {
			$this->assertInstanceOf(Element::class, $child->parent());
		}

		foreach (new ElementCollection(static::$article->childNodes, new Element(static::$pasap)) as $child) {
			$this->assertInstanceOf(Element::class, $child->parent());
		}

		foreach (new ElementCollection(static::$body->childNodes, new Element(static::$pasap)) as $child) {
			$this->assertInstanceOf(Element::class, $child->parent());
		}

		foreach (new ElementCollection(static::$doge->childNodes, new Element(static::$pasap)) as $child) {
			$this->assertInstanceOf(Element::class, $child->parent());
		}

		foreach (new ElementCollection(static::$news->childNodes, new Element(static::$pasap)) as $child) {
			$this->assertInstanceOf(Element::class, $child->parent());
		}
	}
}
