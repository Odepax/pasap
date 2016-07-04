<?php

namespace Pasap;

class Element
{
	/** @var array Contains the history of namespaces passed through `pasap:ns` attributes. */
	protected static $namespaceStack = [];

	/**
	 * Gets the full default namespace of all the elements.
	 */
	protected static function getNamespace ()
	{
		return ($ns = end(static::$namespaceStack)) === false ? "" : $ns;
	}

	/**
	 * Sets the default namespace of all the elements.
	 *
	 * @param string $ns
	 * The new full namespace to consider.
	 */
	protected static function setNamespace (string $ns)
	{
		static::$namespaceStack[] = $ns;
	}

	/**
	 * Rollback to the previous default namespace.
	 */
	protected static function popNamespace ()
	{
		array_pop(static::$namespaceStack);
	}

	/** @var \DOMElement|\DOMText|\DOMComment The DOM element behind this interface. */
	protected $source = null;

	/** @var AttrCollection Caching for this element's attribute collection. */
	protected $attrCollection = null;

	/** @var ElementCollection Caching for this element's children collection. */
	protected $childrenCollection = null;

	/** @var array The data set of this element. */
	protected $dataSet = null;

	/** @var Element The parent of this element. */
	protected $parent = null;

	/**
	 * Creates, caches and returns this element's attribute collection.
	 * @return AttrCollection
	 */
	protected function getAttrCollection ()
	{
		if (is_null($this->attrCollection)) {
			$this->attrCollection = new AttrCollection($this->source->attributes);
		}

		return $this->attrCollection;
	}

	/**
	 * Creates, caches and returns this element's attribute collection.
	 * @return ElementCollection
	 */
	protected function getChildrenCollection ()
	{
		if (is_null($this->childrenCollection)) {
			$this->childrenCollection = new ElementCollection($this->source->childNodes, $this);
		}

		return $this->childrenCollection;
	}

	/**
	 * Retrieves, caches and returns this element's data set.
	 * @return array
	 */
	protected function getDataSet ()
	{
		if (is_null($this->dataSet)) {
			if (is_null($this->attr('pasap:data'))) {
				$this->dataSet = [];
			} else {
				$this->dataSet = Pasap::getDataSet($this->attr('pasap:data'));
			}
		}

		return $this->dataSet;
	}

	/**
	 * Element constructor.
	 * This class provides an interface to native DOM elements, handling DOM
	 * elements and DOM text nodes.
	 *
	 * @param \DOMElement|\DOMText|\DOMComment|\string $source
	 * The DOM element behind this interface.
	 *
	 * @param Element|null $parent
	 * The parent of this element.
	 *
	 * @since 1.3.0 New parameter: `$parent`.
	 * @since 0.0.0
	 */
	public function __construct ($source, $parent = null)
	{
		if ($source instanceof \DOMElement || $source instanceof \DOMText || $source instanceof \DOMComment) {
			$this->source = $source;
		} else if (is_string($source)) {
			$this->source = new \DOMText($source);
		} else {
			throw new \（ノಥ益ಥ）ノ︵┻━┻("Why you no provide the right arg type (DOMElement|DOMText|string) !?");
		}

		if (!is_null($parent)) {
			if ($parent instanceof Element) {
				$this->parent = $parent;
			} else {
				throw new \（ノಥ益ಥ）ノ︵┻━┻("Why you no provide the right parent type (Element) !?");
			}
		}
	}

	/**
	 * Converts this element into HTML and gets the output.
	 *
	 * @return string
	 * This element, once parsed, recursively.
	 *
	 * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
	 * @since 0.0.0
	 */
	public function __toString ()
	{
		// Text node.
		if ($this->source instanceof \DOMText) {
			return $this->source->nodeValue;
		}

		// Comment.
		if ($this->source instanceof \DOMComment) {
			return "<!-- {$this->source->nodeValue} -->";
		}

		$tagParts = explode(":", $this->tag());

		if (!is_null($this->attr("pasap:ns"))) {
			static::setNamespace($this->attr("pasap:ns"));
			$hasToPopNamespace = true;
		}

		if (in_array($tagParts[0], [ "html", "svg", "xml", "xslt" ])) {
			// Case 1: we force the element to be considered as native.
			$native = true;
		} else {
			// Case 2: the element is native if no definition file is found.
			if ($tagParts[0] === "") {
				// Case 2.1: we force the namespace to start at root (via the tag or the ns stack).
				array_shift($tagParts);

				$tag = implode(":", $tagParts);
			} else {
				// Case 2.2: build the tag depending on the namespace stack.
				if (empty(static::getNamespace())) {
					$tag = $this->tag();
				} else {
					$tag = static::getNamespace() . ":" . $this->tag();
				}
			}

			$native = is_null($definitionFile = Pasap::definitionFilePath($tag));
		}

		// Element.
		if ($native) {
			// This is a native element. Just left it as this.

			$tag = is_null($tmpTag = end($tagParts)) ? $tag : $tmpTag;

			// Put a space before a non-empty list of attributes.
			$attr = empty($attr = $this->attr()->__toString()) ? "" : " " . $attr;

			// Special self-closing HTML tags treatment.
			// See https://developer.mozilla.org/en-US/docs/Glossary/Empty_element
			if (in_array($tag, [
				"area", "base", "br", "col", "colgroup", "command", "embed",
				"hr", "img", "input", "keygen", "link", "meta", "param",
				"source", "track", "wbr"
			], true)) {
				$output = "<{$tag}{$attr}/>";

				if (isset($hasToPopNamespace)) {
					static::popNamespace();
				}

				return $output;
			} else {
				$output = "<{$tag}{$attr}>{$this->children()}</{$tag}>";

				if (isset($hasToPopNamespace)) {
					static::popNamespace();
				}

				return $output;
			}

		} else {
			ob_start();

			include $definitionFile;

			if (isset($hasToPopNamespace)) {
				static::popNamespace();
			}

			return ob_get_clean();
		}
	}

	/**
	 * Gets the tag name of this element.
	 * It's important to know that the returned tag is the raw value which is
	 * used in the input code and not the fully namespaced tag name which is
	 * computed during rendering.
	 *
	 * @return string
	 * Returns "a" for a `<a>`, "li" for a `<li>`, etc...
	 * Returns "#text" for text nodes.
	 * Returns "#comment" for comments.
	 *
	 * @since 0.0.0
	 */
	public function tag ()
	{
		return $this->source->nodeName;
	}

	/**
	 * Indicates if this element is a `$tag.`
	 *
	 * @param string $tag
	 * The tag name to test against.
	 * If the parameter is set to `"#text"`, this method will indicate if this
	 * element is a text node or not.
	 * If the parameter is set to `"#comment"`, this method will indicate if this
	 * element is a comment or not.
	 *
	 * @return bool
	 * Returns `$this->tag() === $tag` exactly.
	 *
	 * @see Element::tag()
	 * This is a shortcut to perform a test on the `tag` method's return value.
	 *
	 * @since 0.1.0 `$this->is("#orphan")` is not functional anymore.
	 * @since 0.0.0
	 */
	public function is (string $tag)
	{
		return $this->tag() === $tag;
	}

	/**
	 * Gets the value of an attribute of this element.
	 *
	 * @param null|string $key
	 * The name of the attribute to seek.
	 *
	 * @return AttrCollection|string|null
	 * Returns `NULL` if this element is a text node or a comment since they
	 * don't have any attribute.
	 * Returns an attribute collection if the parameter is NULL.
	 * Returns `NULL` if the attribute does not exist.
	 * Returns the value of the requested attribute as a string, which might be
	 * empty.
	 *
	 * @since 0.0.0
	 */
	public function attr ($key = null)
	{
		if ($this->source instanceof \DOMText || $this->source instanceof \DOMComment) {
			return null;
		}

		if (is_null($key)) {
			return $this->getAttrCollection();
		}

		if (!is_string($key)) {
			throw new \（ノಥ益ಥ）ノ︵┻━┻("Why you no provide the right arg type (string) !?");
		}

		if ($this->source->hasAttribute($key)) {
			return $this->source->getAttribute($key);
		} else {
			return null;
		}
	}

	/**
	 * Gets the children of this element.
	 *
	 * @return ElementCollection|null
	 * Returns `NULL` if this element is a text node since text nodes don't have
	 * any child.
	 * Returns an element collection otherwise.
	 *
	 * @since 0.0.0
	 */
	public function children ()
	{
		if ($this->source instanceof \DOMText) {
			return null;
		} else {
			return $this->getChildrenCollection();
		}
	}

	/**
	 * Gets the value behind a key of this element's data set.
	 *
	 * @param mixed $key
	 * The key of the item to look for.
	 *
	 * @return mixed
	 * Returns the value of the requested key.
	 * Returns `NULL` if the key does not exist.
	 *
	 * @since 1.2.0
	 */
	public function data ($key)
	{
		if (array_key_exists($key, $data = $this->getDataSet())) {
			return $data[$key];
		} else {
			return null;
		}
	}

	/**
	 * Gets the parent of this element.
	 *
	 * @return Element|null
	 * Returns an element which is the parent of this one in the DOM tree.
	 * Returns `NULL` when this element is the root element of the considered
	 * document or if no parent is provided.
	 *
	 * @since 1.3.0
	 */
	public function parent ()
	{
		return $this->parent;
	}
}
