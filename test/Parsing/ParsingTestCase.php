<?php

namespace Pasap\Test\Parsing;

use Pasap\Configure;
use Pasap\Pasap;
use PHPUnit\Framework\TestCase;

abstract class ParsingTestCase extends TestCase
{
	/** @var string The name of the sub-folder containing the files. */
	protected static $subFolder = null;

	public static function setUpBeforeClass ()
	{
		if (is_null(static::$subFolder)) {
			static::$subFolder = end(explode('\\', static::class));
		}

		Configure::nativeNamespace('html',
			'area', 'base', 'br', 'col', 'command', 'embed', 'hr',
			'img', 'input', 'keygen', 'link', 'meta', 'param', 'source',
			'track', 'wbr'
		);

		Configure::namespaceSource('', __DIR__ . DIRECTORY_SEPARATOR . 'element' . DIRECTORY_SEPARATOR . static::$subFolder);

		Configure::output(Configure::MINIFY);
		Configure::doctype(Configure::LEAVE_AS_THIS);
	}

	/**
	 * Compares the content of `expected/$fileName.php` as this and
	 * `parsed/$fileName.php` once parsed.
	 *
	 * Makes the test fail at the first difference.
	 *
	 * @param string $fileName
	 */
	protected function parseAndCompare ($fileName)
	{
		ob_start();

		include __DIR__ . DIRECTORY_SEPARATOR . 'parsed' . DIRECTORY_SEPARATOR . static::$subFolder . DIRECTORY_SEPARATOR . $fileName . '.php';

		$parsed = Pasap::parse(ob_get_clean());
		$expected = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'expected' . DIRECTORY_SEPARATOR . static::$subFolder . DIRECTORY_SEPARATOR . $fileName . '.html');

		$this->assertEquals(Pasap::minify($expected), $parsed);
	}
}
