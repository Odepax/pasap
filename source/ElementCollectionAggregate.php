<?php

namespace Pasap;

class ElementCollectionAggregate implements IElementCollection
{
	/**
	 * This field stores the basic element collections wrapped in this
	 * aggregate.
	 *
	 * @var IElementCollection[]
	 *
	 * @since 2.0.0
	 */
	protected $source;

	/**
	 * Creates a new element aggregate. An aggregate is a recursive collection
	 * of collections. It's used to manipulate grandchildren of elements for
	 * instance.
	 *
	 * @param IElementCollection[] $source
	 * The basic element collections wrapped in this aggregate.
	 *
	 * @since 2.0.0
	 */
	public function __construct (array $source)
	{
		$this->source = $source;
	}

	// Pasap\IElementCollection Methods
	// ----------------------------------------------------------------

	/** @inheritDoc */
	public function __toString (): string
	{
		$output = '';

		foreach ($this->source as $collection) {
			$output .= $collection;
		}

		return $output;
	}

	/** @inheritDoc */
	public function lock (string $tag)
	{
		foreach ($this->source as $collection) {
			$collection->lock($tag);
		}
	}

	/** @inheritDoc */
	public function unlock (string $tag)
	{
		foreach ($this->source as $collection) {
			$collection->unlock($tag);
		}
	}

	/** @inheritDoc */
	public function but (string ...$tags): IElementCollection
	{
		foreach ($this->source as $collection) {
			$collection->but($tags);
		}
	}

	/** @inheritDoc */
	public function only (string ...$tags): IElementCollection
	{
		foreach ($this->source as $collection) {
			$collection->only($tags);
		}
	}

	/** @inheritDoc */
	public function children ($tag = null): IElementCollection
	{
		$grandChildrenCollection = [];

		foreach ($this->source as $collection) {
			if ($collection->children()->count() != 0) {
				$grandChildrenCollection[] = $collection->children();
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
		foreach ($this->source as $collection) {
			yield $collection->getIterator();
		}
	}

	// \Countable Methods
	// ----------------------------------------------------------------

	/** @inheritDoc */
	public function count ()
	{
		$count = 0;

		foreach ($this->source as $collection) {
			$count += $collection->count();
		}

		return $count;
	}
}
