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
	/** @var string Path to the folder that contains element definition files. */
	protected static $elementDefinitionsFolder = null;

	/**
	 * Parses XML as a string and convert the defined custom tags into HTML.
	 *
	 * @param string $xml
	 * The XML string to be parsed. It represents a full document.
	 *
	 * @param string $elementDefinitionsFolder
	 * Where do we look for custom tag definition files.
	 *
	 * @return string
	 * A string. It's supposed to be HTML.
	 */
	public static function parse (string $xml, string $elementDefinitionsFolder): string
	{
		static::$elementDefinitionsFolder = $elementDefinitionsFolder;

		$document = new \DOMDocument("1.0", "UTF-8");

		// Get rid of errors about HTML5.
		libxml_use_internal_errors(true);
		$document->loadHTML($xml);
		libxml_use_internal_errors(false);

		return "<!DOCTYPE {$document->doctype->name}>" . new Element($document->documentElement);
	}

	/**
	 * Gets the full path of the definition file of a custom element.
	 * This method is used by Pasap elements; you should not need to manipulate
	 * it from the outside.
	 *
	 * @param string $elementTag
	 * The tag name of an element.
	 *
	 * @return string|null
	 * Returns the path of the definition file is the file exists, meaning the
	 * element is considered as a custom element and must be parsed.
	 * Returns `NULL` if the file does not exist, meaning the element is
	 * considered as a native HTML element and must be left as this.
	 */
	public static function definitionFilePath (string $elementTag)
	{
		return is_file($path = static::$elementDefinitionsFolder . DIRECTORY_SEPARATOR . $elementTag . ".php") ? $path : null;
	}
}
