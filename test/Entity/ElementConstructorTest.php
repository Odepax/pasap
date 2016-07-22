<?php

namespace Pasap\Test\Entity;

use Pasap\Element;

class ElementConstructorTest extends ElementTestCase
{
	public function testConstructorWithRightParameter ()
	{
		new Element(static::$_);
		new Element(new \DOMText('Hi all'));
		new Element(new \DOMCdataSection('Hi CData'));
		new Element(new \DOMComment('All commented'));
		new Element(new \DOMProcessingInstruction('php', 'echo $anotherValue;'));
	}

	public function testConstructorWithWrongParameter1 ()
	{
		$this->expectException(\TypeError::class);

		new Element(static::$_->attributes);
	}

	public function testConstructorWithWrongParameter2 ()
	{
		$this->expectException(\TypeError::class);

		new Element(static::$_->childNodes);
	}

	public function testConstructorWithWrongParameter3 ()
	{
		$this->expectException(\TypeError::class);

		new Element('<a href="http://github.com">...</a>');
	}

	public function testConstructorWithWrongParameter4 ()
	{
		$this->expectException(\TypeError::class);

		new Element(new Element(static::$_));
	}

	public function testConstructorWithWrongParameter6 ()
	{
		$this->expectException(\TypeError::class);

		new Element(new \stdClass());
	}

	public function testConstructorWithWrongParameter7 ()
	{
		$this->expectException(\TypeError::class);

		new Element(new \DOMNode());
	}

	public function testConstructorWithWrongParameter8 ()
	{
		$this->expectException(\TypeError::class);

		new Element([
			'one' => '12',
			'two' => 'Hi all',
			new \DOMAttr('class', 'button-active')
		]);
	}

	public function testConstructorWithWrongParameter9 ()
	{
		$this->expectException(\TypeError::class);

		new Element(new \DOMDocumentType());
	}
}
