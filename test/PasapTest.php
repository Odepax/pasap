<?php

namespace Pasap\Test;

use Pasap\Pasap;
use PHPUnit\Framework\TestCase;

class PasapTest extends TestCase
{
	/**
	 * @see https://gist.github.com/tovic/d7b310dea3b33e4732c0
	 */
	protected function minifyHTML ($input)
	{
		if (trim($input) === "") {
			return $input;
		}

		// Remove extra white-space(s) between HTML attribute(s)
		$input = preg_replace_callback('#<([^\/\s<>!]+)(?:\s+([^<>]*?)\s*|\s*)(\/?)>#s', function ($matches) {
			return '<'
			. $matches[1]
			. preg_replace('#([^\s=]+)(\=([\'"]?)(.*?)\3)?(\s+|$)#s', ' $1$2', $matches[2])
			. $matches[3]
			. '>';
		}, str_replace("\r", "", $input));

		// Minify inline CSS declaration(s)
		if (strpos($input, ' style=') !== false) {
			$input = preg_replace_callback('#<([^<]+?)\s+style=([\'"])(.*?)\2(?=[\/\s>])#s', function ($matches) {
				return '<'
				. $matches[1]
				. ' style='
				. $matches[2]
				. minify_css($matches[3])
				. $matches[2];
			}, $input);
		}

		return preg_replace(
			array(
				// t = text
				// o = tag open
				// c = tag close
				// Keep important white-space(s) after self-closing HTML tag(s)
				'#<(img|input)(>| .*?>)#s',
				// Remove a line break and two or more white-space(s) between tag(s)
				'#(<!--.*?-->)|(>)(?:\n*|\s{2,})(<)|^\s*|\s*$#s',
				'#(<!--.*?-->)|(?<!\>)\s+(<\/.*?>)|(<[^\/]*?>)\s+(?!\<)#s',
				// t+c || o+t
				'#(<!--.*?-->)|(<[^\/]*?>)\s+(<[^\/]*?>)|(<\/.*?>)\s+(<\/.*?>)#s',
				// o+o || c+c
				'#(<!--.*?-->)|(<\/.*?>)\s+(\s)(?!\<)|(?<!\>)\s+(\s)(<[^\/]*?\/?>)|(<[^\/]*?\/?>)\s+(\s)(?!\<)#s',
				// c+t || t+o || o+t -- separated by long white-space(s)
				'#(<!--.*?-->)|(<[^\/]*?>)\s+(<\/.*?>)#s',
				// empty tag
				'#<(img|input)(>| .*?>)<\/\1\x1A>#s',
				// reset previous fix
				'#(&nbsp;)&nbsp;(?![<\s])#',
				// clean up ...
				// Force line-break with `&#10;` or `&#xa;`
				'#&\#(?:10|xa);#',
				// Force white-space with `&#32;` or `&#x20;`
				'#&\#(?:32|x20);#',
				// Remove HTML comment(s) except IE comment(s)
				'#\s*<!--(?!\[if\s).*?-->\s*|(?<!\>)\n+(?=\<[^!])#s'
			),
			array(
				"<$1$2</$1\x1A>",
				'$1$2$3',
				'$1$2$3',
				'$1$2$3$4$5',
				'$1$2$3$4$5$6$7',
				'$1$2$3',
				'<$1$2',
				'$1 ',
				"\n",
				' ',
				""
			),
			$input
		);
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
		$here = __DIR__ . DIRECTORY_SEPARATOR;

		ob_start();

		include $here . "parsed" . DIRECTORY_SEPARATOR . $fileName . ".php";

		$parsed = ob_get_clean();

		$expected = file_get_contents($here . "expected" . DIRECTORY_SEPARATOR . $fileName . ".html");

		$this->expectOutputString($this->minifyHTML($expected));

		echo $this->minifyHTML(Pasap::parse($parsed, $here . "element"));
	}

	public function testParseNoPasap ()            { $this->parseAndCompare("no-pasap"); }
	public function testParseNoPasapWithPHP ()     { $this->parseAndCompare("no-pasap-with-php"); }
	public function testParseSimplePasap ()        { $this->parseAndCompare("simple-pasap"); }
	public function testParseSimplePasapWithPHP () { $this->parseAndCompare("simple-pasap-with-php"); }
	public function testParseReadmeExample ()      { $this->parseAndCompare("readme-example"); }
	public function testParseCustomRootPasap ()    { $this->parseAndCompare("custom-root-pasap"); }
	public function testParseSimpleNamespaces ()   { $this->parseAndCompare("simple-namespaces"); }
	public function testParseRootNamespaces ()     { $this->parseAndCompare("root-namespaces"); }
	public function testParseAdvancedNamespaces () { $this->parseAndCompare("advanced-namespaces"); }
	// public function testParseNestedPasap ()        { $this->parseAndCompare("nested-pasap"); }
	// public function testParse ()                   { $this->parseAndCompare(""); }

	// @TODO: Make testParseNestedPasap pass. Less important. Allow usage of custom tags in definition files.
}
