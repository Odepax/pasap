<?php

namespace Pasap\Test\Entity;

abstract class AttrCollectionTestCase extends EntityTestCase
{
	/** Initializes a DOM element with some attributes for the tests. */
	public static function setUpBeforeClass ()
	{
		parent::setUpBeforeClass();

		static::$_->appendChild(new \DOMAttr('one', '1'));
		static::$_->appendChild(new \DOMAttr('two', '2'));
		static::$_->appendChild(new \DOMAttr('three', '3'));
		static::$_->appendChild(new \DOMAttr('four', '4'));
		static::$_->appendChild(new \DOMAttr('five', '5'));
		static::$_->appendChild(new \DOMAttr('six', '6'));
		static::$_->appendChild(new \DOMAttr('seven', '7'));
		static::$_->appendChild(new \DOMAttr('eight', '8'));
		static::$_->appendChild(new \DOMAttr('nine', '9'));
	}
}
