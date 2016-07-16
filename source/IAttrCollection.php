<?php

namespace Pasap;

/**
 * This interface defines what methods an attribute collection should provide to
 * be compatible with Pasap.
 *
 * An attribute collection can be obtained calling `IElement::attr()`. This
 * object type has been created in order to be the container of an element's
 * attributes.
 *
 * The attribute collection provides an easy way to iterate through all the
 * attributes of an element.
 *
 * @since 2.0.0
 *
 * @see IElement::attr()
 */
interface IAttrCollection extends \Iterator
{
	/**
	 * Creates an attribute collection.
	 *
	 * @param \DOMNamedNodeMap $source
	 * This is the native PHP attribute collection to be wrapped in this
	 * collection.
	 *
	 * @since 2.0.0
	 */
	public function __construct(\DOMNamedNodeMap $source);

	/**
	 * Renders the attributes of this collection recursively and returns the
	 * output string.
	 *
	 * For instance for a `foo => oof, bar => rab` collection, this method will
	 * output the following XML code: `foo="oof" bar="rab"`.
	 *
	 * @return string
	 * Returns the output of the render process of these attributes.
	 *
	 * @since 2.0.0
	 */
	public function __toString(): string;

	/**
	 * Locks an attribute.
	 *
	 * A locked attribute will be skipped by `IAttrCollection::__toString()`,
	 * `Iterator::rewind()` and `Iterator::next()` methods.
	 * This means it will disappear from `echo` and `foreach` operations.
	 *
	 * @param string $attr
	 * The name that designates the attribute to be locked.
	 *
	 * @since 2.0.0
	 *
	 * @see https://github.com/Odepax/pasap/wiki/Definition-Files#lock-items-of-a-collection
	 */
	public function lock(string $attr);

	/**
	 * Unlocks an attribute.
	 *
	 * @param string $attr
	 * The name that designates the attribute to be unlocked.
	 *
	 * @since 2.0.0
	 *
	 * @see IAttrCollection::lock()
	 */
	public function unlock(string $attr);

	/**
	 * Locks attributes.
	 *
	 * Resets the locker: locked attributes are no longer locked.
	 *
	 * This method is a shortcut to execute several locks in a single line. It
	 * also returns its callee, for more convenience.
	 *
	 * Example, in a definition file:
	 *
	 * ```php
	 * <!-- Prevent the class attribute from being present two times. -->
	 * <div class="foo" <?= $this->attr()->but('class') ?>>
	 *    <?= $this->children() ?>
	 * </div>
	 * ```
	 *
	 * @param string[] ...$attributes
	 * The names that designate the attributes to be locked.
	 *
	 * @return IAttrCollection
	 * Returns a reference to the attribute collection you called this method on
	 * (`$this`).
	 *
	 * @since 2.0.0
	 *
	 * @see IAttrCollection::lock()
	 */
	public function but(string ...$attributes): IAttrCollection;

	/**
	 * Unlocks attributes.
	 *
	 * Resets the locker: unlocked attributes are no longer unlocked.
	 *
	 * This method is a shortcut to lock all attributes but some of them in a
	 * single line. It also returns its callee, for more convenience.
	 *
	 * @param string[] ...$attributes
	 * The names that designate the attributes to be unlocked.
	 *
	 * @return IAttrCollection
	 * Returns a reference to the attribute collection you called this method on
	 * (`$this`).
	 *
	 * @since 2.0.0
	 *
	 * @see IAttrCollection::unlock()
	 * @see IAttrCollection::lock()
	 */
	public function only(string ...$attributes): IAttrCollection;
}
