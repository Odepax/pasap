<?php

namespace Pasap;

/**
 * Class Pasap.
 * This is a facade. It allows users to parse an xml string and elements to have
 * access to the file system.
 *
 * @package Pasap
 */
abstract class Pasap
{
	/** @var array A 2D array that saves data sets of all elements. */
	protected static $data = [];

	/**
	 * ...
	 *
	 * @param string $input
	 * ...
	 *
	 * @return string
	 * ...
	 *
	 * @since 2.0.0
	 *
	 * // @see https://github.com/wasinger/html-pretty-min
	 *
	 * @see https://gist.github.com/tovic/d7b310dea3b33e4732c0
	 */
	public static function minify (string $input): string
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
		/* if (strpos($input, ' style=') !== false) {
			$input = preg_replace_callback('#<([^<]+?)\s+style=([\'"])(.*?)\2(?=[\/\s>])#s', function ($matches) {
				return '<'
				. $matches[1]
				. ' style='
				. $matches[2]
				. minify_css($matches[3])
				. $matches[2];
			}, $input);
		} */

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

		// return (new PrettyMin())->load($input)->minify()->saveHtml();
	}

	/**
	 * ...
	 *
	 * @param string $input
	 * ...
	 *
	 * @return string
	 * ...
	 *
	 * @since 2.0.0
	 *
	 * @see https://github.com/wasinger/html-pretty-min
	 */
	public static function prettify (string $input): string
	{
		return $input;

		// return (new PrettyMin())->load($input)->indent()->saveHtml();
	}

	/**
	 * Parses XML as a string and convert the defined custom tags into HTML.
	 *
	 * @param string $xml
	 * The XML string to be parsed. It represents a full document.
	 *
	 * @param string|null $emptyNamespaceSource
	 * Where do we look for custom tag definition files.
	 *
	 * @return string
	 * A string. It's supposed to be HTML.
	 *
	 * @throws \Error
	 * If XML is not well formed.
	 *
	 * @since 2.0.0 Second parameter becomes optional.
	 * @since 0.0.0
	 *
	 * @see https://github.com/Odepax/pasap/wiki/Namespaces#the-empty-namespace
	 * @see http://www.w3schools.com/xml/xml_validator.asp
	 */
	public static function parse (string $xml, $emptyNamespaceSource = null): string
	{
		if ($emptyNamespaceSource !== null) {
			Configure::namespaceSource('', $emptyNamespaceSource);
		}

		$document = new \DOMDocument("1.0", "UTF-8");

		// Get rid of errors about HTML5.
		libxml_use_internal_errors(true);

		if (!$document->loadXML($xml)) {
			throw new \Error("Why you no check your XML before parsing !?");
		}

		libxml_use_internal_errors(false);

		if (Configure::get('doctype') === Configure::ALWAYS_HTML5) {
			$output = '<!DOCTYPE html>';
		} else if (!is_null($document->doctype)) {
			if ($document->doctype->publicId === '' || $document->doctype->systemId === '') {
				$output = "<!DOCTYPE {$document->doctype->name}>";
			} else {
				$output = "<!DOCTYPE {$document->doctype->name} PUBLIC \"{$document->doctype->publicId}\" \"{$document->doctype->systemId}\">";
			}
		} else {
			$output = '';
		}

		$output .= new Element($document->documentElement);

		switch (Configure::get('output')) {
			case Configure::MINIFY:   return static::minify($output);
			case Configure::PRETTIFY: return static::prettify($output);
			default:                  return $output;
		}
	}

	/**
	 * ...
	 *
	 * @param string $tag
	 * The tag name of an element.
	 *
	 * @return string|bool
	 * Returns the path of the definition file ...
	 * Returns `false` ...
	 *
	 * @since 2.0.0 `definitionFilePath` is renamed `definitionFile`.
	 * @since 0.0.0
	 *
	 * @see https://github.com/Odepax/pasap/wiki/Namespaces#how-elements-are-resolved-and-rendered
	 */
	public static function definitionFile (string $tag)
	{
		$tagParts = explode(':', $tag);
		$rootNs = array_shift($tagParts);

		if (!array_key_exists($rootNs, Configure::get('namespaceSource'))) {
			return null;
		}

		$definitionFile = Configure::get('namespaceSource')[$rootNs] . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $tagParts) . '.php';

		if (is_file($definitionFile)) {
			return $definitionFile;
		} else {
			return null;
		}
	}

	/**
	 * Initializes, registers and saves a data set and returns it's ID.
	 *
	 * @param array $data
	 * The `string => mixed` array that is used to initialize the data set.
	 *
	 * @return string
	 * Returns the code of the `pasap:data` attribute that contains th ID of the
	 * created data set. Ready to be `echo`!
	 *
	 * @since 1.2.0
	 *
	 * @see IElement::data()
	 * @see https://github.com/Odepax/pasap/wiki/Data-Set-and-Scope#data-set
	 */
	public static function data (array $data): string
	{
		$id = uniqid('pspst' . rand());

		static::$data[$id] = $data;

		return "pasap:data=\"$id\"";
	}

	/**
	 * Initializes, registers and saves a data scope and returns it's ID.
	 *
	 * @param array $data
	 * The `string => mixed` array that is used to initialize the data scope.
	 *
	 * @return string
	 * Returns the code of the `pasap:scope` attribute that contains th ID of the
	 * created data scope. Ready to be `echo`!
	 *
	 * @since 1.3.0
	 *
	 * @see IElement::scope()
	 * @see https://github.com/Odepax/pasap/wiki/Data-Set-and-Scope#data-scope
	 */
	public static function scope (array $data): string
	{
		$id = uniqid('pspscp' . rand());

		static::$data[$id] = $data;

		return "pasap:scope=\"$id\"";
	}

	/**
	 * Used by elements to retrieve a data set.
	 *
	 * @param string $id
	 * The unique ID of the data set.
	 *
	 * @return array
	 * Returns an array containing the initialized data.
	 * Returns an empty array if the data set does not exist (wrong ID).
	 *
	 * @since 2.0.0 `getDataSet``is renamed `getData`.
	 * @since 1.2.0
	 */
	public static function getData (string $id): array
	{
		if (array_key_exists($id, static::$data)) {
			return static::$data[$id];
		} else {
			return [];
		}
	}
}
