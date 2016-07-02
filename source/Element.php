<?php

namespace Pasap;

class Element
{
	/** @var \DOMElement|\DOMText The DOM element behind this interface. */
	protected $source = null;

	/** @var AttrCollection Caching for this element's attribute collection. */
	protected $attrCollection = null;

	/** @var ElementCollection Caching for this element's children collection. */
	protected $childrenCollection = null;

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
			$this->childrenCollection = new ElementCollection($this->source->childNodes);
		}

		return $this->childrenCollection;
	}

	/**
	 * Element constructor.
	 * This class provides an interface to native DOM elements, handling DOM
	 * elements and DOM text nodes.
	 *
	 * @param \DOMElement|\DOMText|\string $source
	 * The DOM element behind this interface.
	 */
	public function __construct ($source)
	{
		if ($source instanceof \DOMElement || $source instanceof \DOMText) {
			$this->source = $source;
		} else if (is_string($source)) {
			$this->source = new \DOMText($source);
		} else {
			throw new \（ノಥ益ಥ）ノ︵┻━┻("Why you no provide the right arg type (DOMElement|DOMText|string) !?");
		}
	}

	/**
	 * Converts this element into HTML and gets the output.
	 *
	 * @return string
	 * This element, once parsed, recursively.
	 *
	 * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
	 */
	public function __toString ()
	{
		if ($this->source instanceof \DOMText) {
			return $this->source->nodeValue;
		}

		if (is_null($definitionFile = Pasap::definitionFilePath($this->tag()))) {
			// This is a native element. Just left it as this.

			// Put a space before a non-empty list of attributes.
			$attr = empty($attr = $this->attr()->__toString()) ? "" : " " . $attr;

			if ($this->is("#orphan")) {
				return "<{$this->tag()}{$attr}/>";
			} else {
				return "<{$this->tag()}{$attr}>{$this->children()}</{$this->tag()}>";
			}

		} else {
			ob_start();

			include $definitionFile;

			return ob_get_clean();
		}
	}

	/**
	 * Gets the tag name of this element.
	 *
	 * @return string
	 * Returns "a" for a `<a>`, "li" for a `<li>`, etc...
	 * Returns "#text" for text nodes.
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
	 * If the parameter is set to `"#orphan"`, this method will indicate if this
	 * element an orphan tag (like `<br/>`, or `<img/>`).
	 *
	 * @return bool
	 * Returns `$this->tag() === $tag` exactly.
	 *
	 * @see Element::tag()
	 * This is a shortcut to perform a test on the `tag` method's return value.
	 *
	 * @see https://developer.mozilla.org/en-US/docs/Glossary/Empty_element
	 * Empty element. These elements make `is("#orphan")` return `TRUE`.
	 */
	public function is (string $tag)
	{
		if($tag === "#orphan") {
			return in_array($this->tag(), [
			    "area", "base", "br", "col", "colgroup", "command", "embed",
			    "hr", "img", "input", "keygen", "link", "meta", "param",
				"source", "track", "wbr"
			], true);
		}

		return $this->tag() === $tag;
	}

	/**
	 * Gets the value of an attribute of this element.
	 *
	 * @param null|string $key
	 * The name of the attribute to seek.
	 *
	 * @return AttrCollection|string|null
	 * Returns `NULL` if this element is a text node since text nodes don't have
	 * any attribute.
	 * Returns an attribute collection if the parameter is NULL.
	 * Returns `NULL` if the attribute does not exist.
	 * Returns the value of the requested attribute as a string, which might be
	 * empty.
	 */
	public function attr ($key = null)
	{
		if ($this->source instanceof \DOMText) {
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
	 */
	public function children ()
	{
		if ($this->source instanceof \DOMText) {
			return null;
		} else {
			return $this->getChildrenCollection();
		}
	}
}
