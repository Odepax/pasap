<?php

namespace Pasap;

class ElementCollection implements IElementCollection
{
	/**
	 * The map which contains the elements.
	 *
	 * @var \DOMNodeList
	 *
	 * @since 2.0.0
	 *
	 * @see IElementCollection::__construct()
	 */
	protected $source = null;

	/**
	 * The parent of the elements contained in this collection.
	 *
	 * @var IElement
	 *
	 * @since 2.0.0
	 *
	 * @see IElementCollection::__construct()
	 */
	protected $parent = null;

	/**
	 * This field stores the default behaviour of the elements locker.
	 *
	 * @var bool
	 *
	 * @since 2.0.0
	 *
	 * @see IElementCollection::lock()
	 * @see IElementCollection::unlock()
	 * @see IElementCollection::but()
	 * @see IElementCollection::only()
	 * @see IElementCollection::isLocked()
	 */
	protected $lockerDefault = false;

	/**
	 * An array of string that stores names of elements that must be
	 * considered as exceptions by the element locker.
	 *
	 * @var array
	 *
	 * @since 2.0.0
	 *
	 * @see IElementCollection::lock()
	 * @see IElementCollection::unlock()
	 * @see IElementCollection::but()
	 * @see IElementCollection::only()
	 * @see IElementCollection::isLocked()
	 */
	protected $lockerExceptions = [];

	/**
	 * This field contains Pasap elements corresponding to the native nodes
	 * wrapped in this collection.
	 *
	 * @var IElement[]
	 *
	 * @since 2.0.0
	 */
	protected $cache = [];

	/**
	 * Creates an element collection.
	 *
	 * @param \DOMNodeList $source
	 * This is the native PHP node collection to be wrapped in this collection.
	 *
	 * @param IElement $parent
	 * This object is the parent element of the child elements contained in this
	 * collection.
	 *
	 * @since 2.0.0
	 */
	public function __construct (\DOMNodeList $source, IElement $parent)
	{
		$this->source = $source;
		$this->parent = $parent;
	}

	/**
	 * Indicates if a tag name is locked.
	 *
	 * @param string $tag
	 * The name that designates the tag name to be tested.
	 *
	 * @return bool
	 * Returns `TRUE` if the specified tag name is locked, `FALSE` otherwise.
	 *
	 * @since 2.0.0
	 *
	 * @see IAttrCollection::lock()
	 */
	public function isLocked (string $tag): bool
	{
		// Returns the default locker values or its opposite, depending on
		// currently configured exceptions.
		if (array_key_exists($tag, $this->lockerExceptions)) {
			return !$this->lockerDefault;
		} else {
			return $this->lockerDefault;
		}
	}

	// Pasap\IElementCollection Methods
	// ----------------------------------------------------------------

	/** @inheritDoc */
	public function __toString (): string
	{
		$output = '';

		$i = 0;
		foreach ($this->source as $node) {
			if (!array_key_exists($i, $this->cache)) {
				$this->cache[$i] = new Element($node, $this->parent);
			}

			// Output the element if it is not locked.
			if (!$this->isLocked($this->cache[$i]->tag())) {
				$output .= $this->cache[$i];
			}

			++$i;
		}

		return $output;
	}

	/** @inheritDoc */
	public function lock (string $tag)
	{
		// DEV: If this method is modified, please consider changing but and
		// only methods too, since they are not using lock and unlock!

		if ($this->lockerDefault === false) {
			// OK, case 1: by default, nothing is locked. Let's add an exception.
			$this->lockerExceptions[$tag] = null;
		} else {
			// Case 2: by default, everything is locked. Let's remove the
			// exception if there is one.
			if (array_key_exists($tag, $this->lockerExceptions)) {
				unset($this->lockerExceptions[$tag]);
			}
		}
	}

	/** @inheritDoc */
	public function unlock (string $tag)
	{
		// DEV: If this method is modified, please consider changing but and
		// only methods too, since they are not using lock and unlock!

		if ($this->lockerDefault === true) {
			// OK, case 1: by default, everything is locked. Let's add an
			// exception.
			$this->lockerExceptions[$tag] = null;
		} else {
			// Case 2: by default, nothing is locked. Let's remove the exception
			// if there is one.
			if (array_key_exists($tag, $this->lockerExceptions)) {
				unset($this->lockerExceptions[$tag]);
			}
		}
	}

	/** @inheritDoc */
	public function but (string ...$tags): IElementCollection
	{
		// Change default behaviour: don't lock anything.
		$this->lockerDefault = false;

		// Locker reset.
		$this->lockerExceptions = [];

		// Populate with new exceptions.
		foreach ($tags as $tag) {
			$this->lockerExceptions[$tag] = null;
		}

		return $this;
	}

	/** @inheritDoc */
	public function only (string ...$tags): IElementCollection
	{
		// Change default behaviour: lock everything.
		$this->lockerDefault = true;

		// Locker reset.
		$this->lockerExceptions = [];

		// Populate with new exceptions.
		foreach ($tags as $tag) {
			$this->lockerExceptions[$tag] = null;
		}

		return $this;
	}

	/** @inheritDoc */
	public function children ($tag = null): IElementCollection
	{
		$grandChildrenCollection = [];

		foreach ($this as $child) {
			if ($child->is('#element') && $child->children()->count() != 0) {
				$grandChildrenCollection[] = $child->children();
			}
		}

		if (count($grandChildrenCollection) === 1) {
			$aggregate = $grandChildrenCollection[0];
		} else {
			$aggregate = new ElementCollectionAggregate($grandChildrenCollection);
		}

		if (is_null($tag)) {
			return $aggregate;
		} else {
			return $aggregate->only($tag);
		}
	}

	// \IteratorAggregate Methods
	// --------------------------------------------------------------	}

	/** @inheritDoc */
	public function getIterator ()
	{
		foreach ($this->source as $i => $node) {
			if (!array_key_exists($i, $this->cache)) {
				$this->cache[$i] = new Element($node, $this->parent);
			}

			if (!$this->isLocked($this->cache[$i]->tag())) {
				yield $this->cache[$i];
			}
		}
	}

	// \Countable Methods
	// ----------------------------------------------------------------

	/** @inheritDoc */
	public function count ()
	{
		return $this->source->length;
	}
}
