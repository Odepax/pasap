<?php

namespace Pasap\Test\Parsing;

class SimpleParsingTest extends ParsingTestCase
{
	public static function setUpBeforeClass ()
	{
		static::$subFolder = 'simple';

		parent::setUpBeforeClass();
	}

	public function testParseNoPasap ()         { $this->parseAndCompare("no-pasap"); }
	public function testParseNoDoctype ()       { $this->parseAndCompare("no-doctype"); }
	public function testParseSimplePasap ()     { $this->parseAndCompare("simple-pasap"); }
	public function testParseNestedPasap ()     { $this->parseAndCompare("nested-pasap"); }
	public function testParseCustomRootPasap () { $this->parseAndCompare("custom-root"); }
}
