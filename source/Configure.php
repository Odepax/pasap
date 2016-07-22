<?php

namespace Pasap;

/**
 * This class has been created in order to handle and centralize the
 * configuration of the parser.
 *
 * It provides methods to customize how elements are recognized and how they are
 * output. The configuration is used by the parser or by the
 *
 * @since 2.0.0
 */
abstract class Configure
{
	const LEAVE_AS_THIS= 1;
	const PRETTIFY     = 2;
	const MINIFY       = 4;
	const ALWAYS_HTML5 = 8;

	/**
	 * Contains the configuration values. This array has the following keys:
	 *
	 * - `namespaceSource` (`array`): A map between the namespaces and their
	 *   source folders.
	 *
	 * - `namespaceSource[string]` (`string`): Each key is a namespace name, and
	 *   corresponding values are folder paths.
	 *
	 * - `nativeNamespace` (`array`): Contains the native namespaces'
	 *   self-closing tag definitions.
	 *
	 * - `nativeNamespace[string]` (`string[]`): Each key is a namespace name,
	 *   and each values is a list of self-closing tags.
	 *
	 * - `output` (`int`): Defines what to do with output.
	 *
	 * - `doctype` (`int`): Defines how to handle a doctype.
	 *
	 * - `executePHP` (`int`): Indicates when PHP code has to be executed.
	 *
	 * @var array
	 *
	 * @since 2.0.0
	 *
	 * @see Configure::namespaceSource()
	 * @see Configure::nativeNamespace()
	 * @see Configure::output()
	 * @see Configure::doctype()
	 * @see Configure::executePHP()
	 */
	protected static $config = [
		'namespaceSource' => [],
		'nativeNamespace' => [],
		'output' => self::LEAVE_AS_THIS,
		'doctype' => self::ALWAYS_HTML5
	];

	/**
	 * Gets a configuration value.
	 *
	 * @param string $key
	 * The key to look for. This key is the unique identifier of the data.
	 * Possible keys are ones that are defined in `Configure::$config`.
	 *
	 * @param mixed $fallback
	 * A fallback value. If the specified key does not exist, then this value
	 * will be returned.
	 *
	 * @return mixed
	 * Returns the value of the specified key, or the fallback value if the
	 * key is not found.
	 *
	 * @since 2.0.0
	 *
	 * @see Configure::$config
	 */
	public static function get(string $key, $fallback = null)
	{
		if (array_key_exists($key, self::$config)) {
			return self::$config[$key];
		} else {
			return $fallback;
		}
	}
	

	/**
	 * Registers a root namespace.
	 *
	 * This method has been thought to make sharing of elements easier. It is
	 * possible to link custom tags from an external composer package.
	 *
	 * Example:
	 *
	 * ```php
	 * Configure::namespaceSource('', './element');
	 * Configure::namespaceSource('intuitive-form', './vendor/odepax/pasap-intuitive-form/element');
	 * ```
	 *
	 * @param string $rootNs
	 * The name of the root namespace to register.
	 * This method does not support nested namespace names.
	 *
	 * @param string $definitionFilesFolder
	 * The path to the folder containing the definition files of the elements
	 * custom tags of the namespace.
	 *
	 * @since 2.0.0
	 *
	 * @see https://github.com/Odepax/pasap/wiki/Namespaces#user-defined-namespaces
	 * @see https://github.com/Odepax/pasap/wiki/Namespaces#the-empty-namespace
	 */
	public static function namespaceSource(string $rootNs, string $definitionFilesFolder)
	{
		self::$config['namespaceSource'][$rootNs] = $definitionFilesFolder;
	}
	
	/**
	 * Registers a native namespace.
	 *
	 * A native namespace is used to force some elements not to be rendered as a
	 * custom tag, but as a native one. This means native tags will be left as
	 * this.
	 *
	 * Example:
	 *
	 * ```php
	 * Configure::nativeNamespace('html', ...[ 'br', 'hr', 'img', 'meta' ]);
	 * Configure::nativeNamespace('svg', 'path', 'polygon');
	 * ```
	 *
	 * @param string $namespace
	 * The name of the native namespace.
	 * This method does not support nested namespace names.
	 *
	 * @param string[] ...$selfClosingTags
	 * A list that defines the self-closing tags of this namespace.
	 *
	 * @since 2.0.0
	 *
	 * @see https://github.com/Odepax/pasap/wiki/Namespaces#native-namespaces
	 */
	public static function nativeNamespace(string $namespace, string ...$selfClosingTags)
	{
		self::$config['nativeNamespace'][$namespace] = $selfClosingTags;
	}
	

	/**
	 * Adds an extra step after rendering to modify the output.
	 *
	 * @param int $mode
	 * Possible values:
	 *
	 * - `Configure::LEAVE_AS_THIS`: No extra step is added, the generated code
	 *   is returned as this.
	 *
	 * - `Configure::PRETTIFY`: Reworks the code indentation.
	 *
	 * - `Configure::MINIFY`: Extra spaces and comments are deleted.
	 *
	 * @since 2.0.0
	 */
	public static function output(int $mode)
	{
		self::$config['output'] = $mode;
	}
	
	/**
	 * Configures how the document doctype is handled.
	 *
	 * Example:
	 *
	 * ```php
	 * Configure::doctype(Configure::LEAVE_AS_THIS | Configure::ALWAYS_HTML5);
	 * ```
	 *
	 * @param int $mode
	 * Possible values:
	 *
	 * - `Configure::LEAVE_AS_THIS`: The doctype is not modified. If there is no
	 *   doctype in the input string, nothing is output.
	 *
	 * - `Configure::ALWAYS_HTML5`: The input doctype is replaced by the
	 *   standard HTML5 doctype. If there is no doctype in the input string, it
	 *   is created.
	 *
	 * @since 2.0.0
	 */
	public static function doctype(int $mode)
	{
		self::$config['doctype'] = $mode;
	}
}
