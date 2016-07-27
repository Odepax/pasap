<?php

namespace Pasap\Test\Parsing;

class NamespaceParsingTest extends ParsingTestCase
{
	public static function setUpBeforeClass ()
	{
		static::$subFolder = 'namespace';

		parent::setUpBeforeClass();
	}

	public function testParsePasapNs () { $this->parseAndCompare("pasap-ns"); }
}
