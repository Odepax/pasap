<?php

namespace Pasap\Test\Entity;

use PHPUnit\Framework\TestCase;

/** Here to provide a `setUpBeforeClass` method and to be extended. */
abstract class EntityTestCase extends TestCase
{
	/** @var \DOMDocument */
	protected static $document;

	/** @var \DOMElement */
	protected static $_;

	/** Initializes a DOM element for the tests. */
	public static function setUpBeforeClass ()
	{
		static::$document = new \DOMDocument("1.0", "UTF-8");

		static::$_ = static::$document->createElement('e');
	}
}
