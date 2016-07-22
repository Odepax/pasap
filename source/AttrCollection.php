<?php

namespace Pasap;

class AttrCollection implements IAttrCollection
{
	/**
	 * The map which contains the attributes.
	 *
	 * @var \DOMNamedNodeMap
	 *
	 * @since 2.0.0
	 *
	 * @see IAttrCollection::__construct()
	 */
	protected $source = null;

	/**
	 * This value is used in the implementation of the \Iterator interface.
	 *
	 * @var int
	 *
	 * @since 2.0.0
	 *
	 * @see \Iterator
	 */
	protected $iteratorIndex = 0;

	/**
	 * This field stores the default behaviour of the attribute locker.
	 *
	 * @var bool
	 *
	 * @since 2.0.0
	 *
	 * @see IAttrCollection::lock()
	 * @see IAttrCollection::unlock()
	 * @see IAttrCollection::but()
	 * @see IAttrCollection::only()
	 * @see AttrCollection::isLocked()
	 */
	protected $lockerDefault = false;

	/**
	 * An array of string that stores names of attributes that must be
	 * considered as exceptions by the attribute locker.
	 *
	 * @var array
	 *
	 * @since 2.0.0
	 *
	 * @see IAttrCollection::lock()
	 * @see IAttrCollection::unlock()
	 * @see IAttrCollection::but()
	 * @see IAttrCollection::only()
	 * @see AttrCollection::isLocked()
	 */
	protected $lockerExceptions = [];

	/**
	 * Creates an attribute collection.
	 *
	 * @param \DOMNamedNodeMap $source
	 * This is the native PHP attribute collection to be wrapped in this
	 * collection.
	 *
	 * @since 2.0.0
	 */
	public function __construct (\DOMNamedNodeMap $source)
	{
		$this->source = $source;
	}

	/**
	 * Indicates if an attribute is locked.
	 *
	 * @param string $attr
	 * The name that designates the attribute to be tested.
	 *
	 * @return bool
	 * Returns `TRUE` if the specified attribute is locked, `FALSE` otherwise.
	 *
	 * @since 2.0.0
	 *
	 * @see IAttrCollection::lock()
	 */
	public function isLocked (string $attr): bool
	{
		// Returns the default locker values or its opposite, depending on
		// currently configured exceptions.
		if (array_key_exists($attr, $this->lockerExceptions)) {
			return !$this->lockerDefault;
		} else {
			return $this->lockerDefault;
		}
	}

	// Pasap\IAttrCollection Methods
	// ----------------------------------------------------------------

	/** @inheritDoc */
	public function __toString (): string
	{
		$output = '';

		foreach ($this->source as $attr) {
			// Output the attribute if it is not locked AND if it's not a
			// pasap:something reserved attribute.
			if (!$this->isLocked($attr->name) && substr($attr->name, 0, 5) !== 'pasap') {
				$output .= " {$attr->name}=\"{$attr->value}\"";
			}
		}

		// Since `substr` returns `FALSE` on failure, i.e. if `$output` is empty...
		return ($result = substr($output, 1)) ? $result : '';
	}

	/** @inheritDoc */
	public function lock (string $attr)
	{
		// DEV: If this method is modified, please consider changing but and
		// only methods too, since they are not using lock and unlock!

		if ($this->lockerDefault === false) {
			// OK, case 1: by default, nothing is locked. Let's add an exception.
			$this->lockerExceptions[$attr] = null;
		} else {
			// Case 2: by default, everything is locked. Let's remove the
			// exception if there is one.
			if (array_key_exists($attr, $this->lockerExceptions)) {
				unset($this->lockerExceptions[$attr]);
			}
		}
	}

	/** @inheritDoc */
	public function unlock (string $attr)
	{
		// DEV: If this method is modified, please consider changing but and
		// only methods too, since they are not using lock and unlock!

		if ($this->lockerDefault === true) {
			// OK, case 1: by default, everything is locked. Let's add an
			// exception.
			$this->lockerExceptions[$attr] = null;
		} else {
			// Case 2: by default, nothing is locked. Let's remove the exception
			// if there is one.
			if (array_key_exists($attr, $this->lockerExceptions)) {
				unset($this->lockerExceptions[$attr]);
			}
		}
	}

	/** @inheritDoc */
	public function but (string ...$attributes): IAttrCollection
	{
		// Change default behaviour: don't lock anything.
		$this->lockerDefault = false;

		// Locker reset.
		$this->lockerExceptions = [];

		// Populate with new exceptions.
		foreach ($attributes as $attr) {
			$this->lockerExceptions[$attr] = null;
		}

		return $this;
	}

	/** @inheritDoc */
	public function only (string ...$attributes): IAttrCollection
	{
		// Change default behaviour: lock everything.
		$this->lockerDefault = true;

		// Locker reset.
		$this->lockerExceptions = [];

		// Populate with new exceptions.
		foreach ($attributes as $attr) {
			$this->lockerExceptions[$attr] = null;
		}

		return $this;
	}

	// \Iterator Methods
	// ----------------------------------------------------------------

	/** @inheritDoc */
	public function rewind ()
	{
		// Let's look for the first position that is not locked and not reserved.
		for($this->iteratorIndex = 0; $this->valid(); ++$this->iteratorIndex) {
			if (!$this->isLocked($this->key()) && substr($this->key(), 0, 5) !== 'pasap') {
				break;
			}
		}
	}

	/** @inheritDoc */
	public function next ()
	{
		// Let's look for the next position that is not locked and not reserved.
		for(++$this->iteratorIndex; $this->valid(); ++$this->iteratorIndex) {
			if (!$this->isLocked($this->key()) && substr($this->key(), 0, 5) !== 'pasap') {
				break;
			}
		}
	}

	/** @inheritDoc */
	public function valid ()
	{
		return $this->iteratorIndex < $this->source->length;
	}

	/** @inheritDoc */
	public function key ()
	{
		return $this->source->item($this->iteratorIndex)->nodeName;
	}

	/** @inheritDoc */
	public function current ()
	{
		return $this->source->item($this->iteratorIndex)->nodeValue;
	}

	// \Countable Methods
	// ----------------------------------------------------------------

	/** @inheritDoc */
	public function count ()
	{
		return $this->source->length;
	}
}
