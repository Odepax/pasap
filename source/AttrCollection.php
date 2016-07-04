<?php

namespace Pasap;

class AttrCollection implements \Iterator
{
	/** @var \DOMNamedNodeMap The map which contains the attributes. */
	protected $source = null;

	/** @var int This value is used in the implementation of the \Iterator interface. */
	protected $iteratorIndex = 0;

	/** @var array An array of string that stores names of attributes that must not be output. */
	protected $locked = [];

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
	 *
	 * @since 0.0.0
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
	 * @since 0.0.0
	 */
	function __toString ()
	{
		$output = "";

		foreach ($this->source as $attr) {
			if (!array_key_exists($attr->name, $this->locked) && $attr->name !== "pasap:ns") {
				$output .= " {$attr->name}=\"{$attr->value}\"";
			}
		}

		// Since `substr` returns `FALSE` on failure, i.e. if `$output` is empty...
		return ($result = substr($output, 1)) ? $result : "";
	}

	/**
	 * Locks an attribute.
	 * A locked attribute will be skipped by `__toString`, `rewind` and `next`
	 * methods. This means it won't appear in `echo` and `foreach` operations.
	 *
	 * @param string $attr
	 * The name of the attribute to lock.
	 *
	 * @since 0.2.0
	 */
	public function lock (string $attr)
	{
		$this->locked[$attr] = 1;
	}

	/**
	 * Unlocks an attribute.
	 * Undoes what the `lock` method did.
	 *
	 * @param string $attr
	 * The name of the attribute to unlock.
	 *
	 * @since 0.2.0
	 */
	public function unlock (string $attr)
	{
		if (array_key_exists($attr, $this->locked)) {
			unset($this->locked[$attr]);
		}
	}

	/**
	 * Locks one or more attributes.
	 *
	 * @param string[] ...$attributes
	 * The names of the attributes to lock.
	 *
	 * @return AttrCollection
	 * Returns a reference to the attribute collection you called this method on
	 * (`$this`).
	 *
	 * @see \Pasap\AttrCollection::lock()
	 * @since 0.2.0
	 */
	public function but (string ...$attributes): AttrCollection
	{
		foreach ($attributes as $a) {
			$this->lock($a);
		}

		return $this;
	}

	/**
	 * Rewind the Iterator to the first element which is not locked.
	 * @link http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public function rewind ()
	{
		// Let's look for the first position that is not locked.
		for($this->iteratorIndex = 0; $this->valid(); ++$this->iteratorIndex) {
			if (!array_key_exists($this->key(), $this->locked) && $this->key() !== "pasap:ns") {
				break;
			}
		}
	}

	/**
	 * Move forward to next element which is not locked.
	 * @link http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public function next ()
	{
		// Let's look for the newt position that is not locked.
		for(++$this->iteratorIndex; $this->valid(); ++$this->iteratorIndex) {
			if (!array_key_exists($this->key(), $this->locked) && $this->key() !== "pasap:ns") {
				break;
			}
		}
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
