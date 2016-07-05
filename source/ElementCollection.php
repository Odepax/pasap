<?php

namespace Pasap;

class ElementCollection implements \Iterator
{
	/** @var \DOMNodeList The list which contains the elements. */
	protected $source = null;

	/** @var int This value is used in the implementation of the \Iterator interface. */
	protected $iteratorIndex = 0;

	/** @var Element The parent of the children contained in this collection. */
	protected $origin = null;

	/**
	 * ElementCollection constructor.
	 * This class provides a way to iterate over the children of a xml tag
	 * with a `foreach` statement, but also to parse and `echo` all children as
	 * a xml string.
	 *
	 * This class is used by the Pasap library, but you should normally not have
	 * to instantiate it directly.
	 *
	 * @param \DOMNodeList $source
	 * The list which contains the elements.
	 *
	 * @param Element $origin
	 * The element these children are children of.
	 *
	 * @since 0.0.0
	 */
	public function __construct ($source, Element $origin)
	{
		if ($source instanceof \DOMNodeList) {
			$this->source = $source;
		} else {
			throw new \（ノಥ益ಥ）ノ︵┻━┻("Why you no provide the right arg type (DOMNodeList) !?");
		}

		$this->origin = $origin;
	}

	/**
	 * Gets a string which represents the xml children in this collection.
	 *
	 * @return string
	 *
	 * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
	 * @since 0.0.0
	 */
	function __toString ()
	{
		$output = "";

		foreach ($this as $element) {
			$output .= $element->__toString();
		}

		return $output;
	}

	/**
	 * Rewind the Iterator to the first element.
	 * @link http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public function rewind ()
	{
		$this->iteratorIndex = 0;
	}

	/**
	 * Move forward to next element.
	 * @link http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public function next ()
	{
		++$this->iteratorIndex;
	}

	/**
	 * Checks if current position is valid.
	 * @link http://php.net/manual/en/iterator.valid.php
	 * @return boolean The return value will be casted to boolean and then evaluated.
	 * Returns true on success or false on failure.
	 * @since 5.0.0
	 */
	public function valid ()
	{
		return $this->iteratorIndex < $this->source->length;
	}

	/**
	 * Return the key of the current element.
	 * @link http://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 * @since 5.0.0
	 */
	public function key ()
	{
		return $this->iteratorIndex;
	}

	/**
	 * Return value of the current element.
	 * @link http://php.net/manual/en/iterator.current.php
	 * @return mixed Can return any type.
	 * @since 5.0.0
	 */
	public function current ()
	{
		$node = $this->source->item($this->iteratorIndex);

		if ($node instanceof \DOMElement || $node instanceof \DOMText || $node instanceof \DOMComment) {
			return new Element($node, $this->origin);
		}

		throw new \（ノ゜Д゜）ノ︵┻━┻("How u wanna me build an element with that (" . get_class($node) . ") !?");
	}
}
