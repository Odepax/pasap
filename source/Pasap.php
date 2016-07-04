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

	/** @var array A 2D array that saves data sets of all elements. */
	protected static $dataSet = [];

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
	 *
	 * @since 0.0.0
	 */
	public static function parse (string $xml, string $elementDefinitionsFolder): string
	{
		static::$elementDefinitionsFolder = $elementDefinitionsFolder;

		$document = new \DOMDocument("1.0", "UTF-8");

		// Get rid of errors about HTML5.
		libxml_use_internal_errors(true);
		// @TODO: Could these guys be responsible for the empty result of libxml_get_errors()?

		if (!$document->loadXML($xml)) {
			throw new \（ノಥ益ಥ）ノ︵┻━┻("Why you no check your XML before parsing !?");
		}

		libxml_use_internal_errors(false);

		$e = new Element($document->documentElement);
		$c = $e->children()->__toString();

		return "<!DOCTYPE {$document->doctype->name}>" . new Element($document->documentElement);
	}

	/**
	 * Gets the full path of the definition file of a custom element.
	 * This method is used by Pasap elements; you should not need to manipulate
	 * it from the outside.
	 *
	 * @param string $elementTag
	 * The fully namespaced tag name of an element.
	 *
	 * @return string|null
	 * Returns the path of the definition file is the file exists, meaning the
	 * element is considered as a custom element and must be parsed.
	 * Returns `NULL` if the file does not exist, meaning the element is
	 * considered as a native HTML element and must be left as this.
	 *
	 * @since 0.0.0
	 */
	public static function definitionFilePath (string $elementTag)
	{
		$definitionPath = implode(DIRECTORY_SEPARATOR, explode(':', $elementTag));

		return is_file($path = static::$elementDefinitionsFolder . DIRECTORY_SEPARATOR . $definitionPath . ".php") ? $path : null;
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
	 */
	public static function data (array $data): string
	{
		$id = uniqid("pspst" . rand());

		static::$dataSet[$id] = $data;

		return "pasap:data=\"$id\"";
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
	 * @since 1.2.0
	 */
	public static function getDataSet (string $id): array
	{
		if (array_key_exists($id, static::$dataSet)) {
			return static::$dataSet[$id];
		} else {
			return [];
		}
	}
}
