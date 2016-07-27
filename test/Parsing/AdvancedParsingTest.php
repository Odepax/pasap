<?php

namespace Pasap\Test\Parsing;

class AdvancedParsingTest extends ParsingTestCase
{
	public static function setUpBeforeClass ()
	{
		static::$subFolder = 'advanced';

		parent::setUpBeforeClass();
	}

	public function testParseAttrFallback () { $this->parseAndCompare("attr-fallback"); }
	public function testParseChildren ()     { $this->parseAndCompare("children"); }
}
