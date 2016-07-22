<?php

namespace Pasap\Test\Entity;

abstract class ElementTestCase extends EntityTestCase
{
	/** Initializes a DOM element with some attributes and children for the tests. */
	public static function setUpBeforeClass ()
	{
		parent::setUpBeforeClass();

		static::$_->appendChild(new \DOMAttr('one', '1'));
		static::$_->appendChild(new \DOMAttr('two', '2'));
		static::$_->appendChild(new \DOMAttr('three', '3'));
		static::$_->appendChild(new \DOMAttr('four', '4'));
		static::$_->appendChild(new \DOMAttr('five', '5'));
		static::$_->appendChild(new \DOMAttr('six', '6'));

		static::$_->appendChild(new \DOMElement('one', '1'));
		static::$_->appendChild(new \DOMElement('two', '2'));
		static::$_->appendChild(new \DOMElement('three', '3'));
		static::$_->appendChild(new \DOMElement('four', '4'));
		static::$_->appendChild(new \DOMElement('five', '5'));
		static::$_->appendChild(new \DOMElement('six', '6'));
	}
}
