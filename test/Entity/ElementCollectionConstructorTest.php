<?php

namespace Pasap\Test\Entity;

use Pasap\ElementCollection;
use Pasap\Element;

class ElementCollectionConstructorTest extends ElementCollectionTestCase
{
	public function testConstructorWithRightParameter ()
	{
		new ElementCollection(static::$_->childNodes, new Element(static::$_));
	}

	public function testConstructorWithWrongParameter1 ()
	{
		$this->expectException(\TypeError::class);

		new ElementCollection(static::$_, new Element(static::$_));
	}

	public function testConstructorWithWrongParameter2 ()
	{
		$this->expectException(\TypeError::class);

		new ElementCollection(new \DOMText('Hi all'), new Element(static::$_));
	}

	public function testConstructorWithWrongParameter3 ()
	{
		$this->expectException(\TypeError::class);

		new ElementCollection('<a href="http://github.com">...</a>', new Element(static::$_));
	}

	public function testConstructorWithWrongParameter4 ()
	{
		$this->expectException(\TypeError::class);

		new ElementCollection(new \DOMNode(), new Element(static::$_));
	}

	public function testConstructorWithWrongParameter5 ()
	{
		$this->expectException(\TypeError::class);

		new ElementCollection(new \DOMDocumentType(), new Element(static::$_));
	}

	public function testConstructorWithWrongParameter6 ()
	{
		$this->expectException(\TypeError::class);

		new ElementCollection(new \stdClass(), new Element(static::$_));
	}

	public function testConstructorWithWrongParameter7 ()
	{
		$this->expectException(\TypeError::class);

		new ElementCollection(new Element(static::$_), new Element(static::$_));
	}

	public function testConstructorWithWrongParameter8 ()
	{
		$this->expectException(\TypeError::class);

		new ElementCollection([
			'one' => '12',
			'two' => 'Hi all',
			new \DOMAttr('class', 'button-active')
		], new Element(static::$_));
	}

	public function testConstructorWithWrongParameter9 ()
	{
		$this->expectException(\TypeError::class);

		new ElementCollection(static::$_->childNodes, static::$_);
	}

	public function testConstructorWithWrongParameter10 ()
	{
		$this->expectException(\TypeError::class);

		new ElementCollection(static::$_->childNodes, new \DOMText('Hi all'));
	}

	public function testConstructorWithWrongParameter11 ()
	{
		$this->expectException(\TypeError::class);

		new ElementCollection(static::$_->childNodes, '<a href="http://github.com">...</a>');
	}

	public function testConstructorWithWrongParameter12 ()
	{
		$this->expectException(\TypeError::class);

		new ElementCollection(static::$_->childNodes, new \DOMNode());
	}

	public function testConstructorWithWrongParameter13 ()
	{
		$this->expectException(\TypeError::class);

		new ElementCollection(static::$_->childNodes, new \DOMDocumentType());
	}

	public function testConstructorWithWrongParameter14 ()
	{
		$this->expectException(\TypeError::class);

		new ElementCollection(static::$_->childNodes, new \stdClass());
	}

	public function testConstructorWithWrongParameter15 ()
	{
		$this->expectException(\TypeError::class);

		new ElementCollection(static::$_->childNodes, [
			'one' => '12',
			'two' => 'Hi all',
			new \DOMAttr('class', 'button-active')
		]);
	}
}
