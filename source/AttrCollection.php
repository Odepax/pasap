<?php

namespace Pasap;

class AttrCollection implements \Iterator
{
	/** @var \DOMNamedNodeMap The map which contains the attributes. */
	protected $source = null;

	/** @var int This value is used in the implementation of the \Iterator interface. */
	protected $iteratorIndex = 0;

	/**
	 * AttrCollection constructor.
	 * This class provides a way to iterate over the attributes of a xml tag
	 * with a `foreach` statement, but also to `echo` all attributes as a xml
	 * string.
	 *
	 * This class is used by the Pasap library, but you should normally not have
	 * to instantiate it directly.
	 *
	 * @param \DOMNamedNodeMap $source
	 * The map which contains the attributes.
	 */
	public function __construct ($source)
	{
		if ($source instanceof \DOMNamedNodeMap) {
			$this->source = $source;
		} else {
			throw new \（ノಥ益ಥ）ノ︵┻━┻("Why you no provide the right arg type (DOMNamedNodeMap) !?");
		}
	}

	/**
	 * Gets a string which represents the xml attributes in this collection.
	 *
	 * @return string
	 * A string representing all the `attr="value"`.
	 *
	 * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
	 */
	function __toString ()
	{
		$output = "";

		foreach ($this->source as $attr) {
			$output .= " {$attr->name}=\"{$attr->value}\"";
		}

		// Since `substr` returns `FALSE` on failure, i.e. if `$output` is empty...
		return ($result = substr($output, 1)) ? $result : "";
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
		return $this->source->item($this->iteratorIndex)->nodeName;
	}

	/**
	 * Return value of the current element.
	 * @link http://php.net/manual/en/iterator.current.php
	 * @return mixed Can return any type.
	 * @since 5.0.0
	 */
	public function current ()
	{
		return $this->source->item($this->iteratorIndex)->nodeValue;
	}
}
