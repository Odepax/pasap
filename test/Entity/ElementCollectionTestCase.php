<?php

namespace Pasap\Test\Entity;

abstract class ElementCollectionTestCase extends EntityTestCase
{
	/** Initializes a DOM element with some children for the tests. */
	public static function setUpBeforeClass ()
	{
		parent::setUpBeforeClass();

		static::$_->appendChild(new \DOMElement('one', '1'));
		static::$_->appendChild(new \DOMElement('two', '2'));
		static::$_->appendChild(new \DOMElement('three', '3'));
		static::$_->appendChild(new \DOMElement('four', '4'));
		static::$_->appendChild(new \DOMElement('five', '5'));
		static::$_->appendChild(new \DOMElement('six', '6'));
		static::$_->appendChild(new \DOMElement('seven', '7'));
		static::$_->appendChild(new \DOMElement('eight', '8'));
		static::$_->appendChild(new \DOMElement('nine', '9'));
	}
}
