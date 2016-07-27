<?php

namespace Pasap\Test\Parsing;

class DataParsingTest extends ParsingTestCase
{
	public static function setUpBeforeClass ()
	{
		static::$subFolder = 'data';
		
		parent::setUpBeforeClass();
	}

	public function testParseWithDataSet ()   { $this->parseAndCompare("data-set"); }
	public function testParseWithDataScope()  { $this->parseAndCompare("data-scope"); }
	public function testParseWithNestedData() { $this->parseAndCompare("nested-data"); }
}
