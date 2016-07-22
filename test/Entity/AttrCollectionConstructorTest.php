<?php

namespace Pasap\Test\Entity;

use Pasap\AttrCollection;
use Pasap\Element;

class AttrCollectionConstructorTest extends AttrCollectionTestCase
{
	public function testConstructorWithRightParameter ()
	{
		new AttrCollection(static::$_->attributes);
	}

	public function testConstructorWithWrongParameter1 ()
	{
		$this->expectException(\TypeError::class);

		new AttrCollection(static::$_);
	}

	public function testConstructorWithWrongParameter2 ()
	{
		$this->expectException(\TypeError::class);

		new AttrCollection(new \DOMText('Hi all'));
	}

	public function testConstructorWithWrongParameter3 ()
	{
		$this->expectException(\TypeError::class);

		new AttrCollection('<a href="http://github.com">...</a>');
	}

	public function testConstructorWithWrongParameter4 ()
	{
		$this->expectException(\TypeError::class);

		new AttrCollection(new \DOMNode());
	}

	public function testConstructorWithWrongParameter5 ()
	{
		$this->expectException(\TypeError::class);

		new AttrCollection(new \DOMDocumentType());
	}

	public function testConstructorWithWrongParameter6 ()
	{
		$this->expectException(\TypeError::class);

		new AttrCollection(new \stdClass());
	}

	public function testConstructorWithWrongParameter7 ()
	{
		$this->expectException(\TypeError::class);

		new AttrCollection(new Element(static::$_));
	}

	public function testConstructorWithWrongParameter8 ()
	{
		$this->expectException(\TypeError::class);

		new AttrCollection([
			'one' => '12',
			'two' => 'Hi all',
			new \DOMAttr('class', 'button-active')
		]);
	}
}
