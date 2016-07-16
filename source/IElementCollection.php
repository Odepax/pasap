<?php

namespace Pasap;

/**
 * This interface defines what methods an element collection should provide to
 * be compatible with Pasap.
 *
 * An element collection can be obtained calling `IElement::children()`. This
 * object type has been created in order to be the container of an element's
 * children.
 *
 * The element collection provides an easy way to iterate through all the
 * children of an element.
 *
 * @since 2.0.0
 *
 * @see IElement::children()
 */
interface IElementCollection extends \Iterator, \Countable
{
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
	public function __construct(\DOMNodeList $source, IElement $parent);

	/**
	 * Renders the elements of this collection recursively and returns the
	 * output string.
	 *
	 * @return string
	 * Returns the output of the render process of these elements.
	 *
	 * @since 2.0.0
	 */
	public function __toString(): string;

	/**
	 * Locks tags by name.
	 *
	 * A locked tag will be skipped by `IElementCollection::__toString()`,
	 * `Iterator::rewind()` and `Iterator::next()` methods.
	 * This means it will disappear from `echo` and `foreach` operations.
	 *
	 * @param string $tag
	 * The name that designates the tag name to be locked. Tags are compared
	 * with `$e->resolvedTag()`.
	 *
	 * @since 2.0.0
	 *
	 * @see IElement::tag()
	 * @see https://github.com/Odepax/pasap/wiki/Namespaces#native-namespaces
	 * @see https://github.com/Odepax/pasap/wiki/Definition-Files#lock-items-of-a-collection
	 */
	public function lock(string $tag);

	/**
	 * Unlocks tags by name.
	 *
	 * @param string $tag
	 * The name that designates the tag name to be unlocked. Tags are compared
	 * with `$e->resolvedTag()`.
	 *
	 * @since 2.0.0
	 *
	 * @see IElementCollection::lock()
	 * @see IElement::tag()
	 */
	public function unlock(string $tag);

	/**
	 * Locks tags by name.
	 *
	 * Resets the locker: locked tags are no longer locked.
	 *
	 * This method is a shortcut to execute several locks in a single line. It
	 * also returns its callee, for more convenience.
	 *
	 * @param string[] ...$tags
	 * The names that designate the tag names to be locked. Tags are compared
	 * with `$e->resolvedTag()`.
	 *
	 * @return IElementCollection
	 * Returns a reference to the element collection you called this method on
	 * (`$this`).
	 *
	 * @since 2.0.0
	 *
	 * @see IElementCollection::lock()
	 * @see IElement::tag()
	 */
	public function but(string ...$tags): IElementCollection;

	/**
	 * Unlocks tags by name.
	 *
	 * Resets the locker: unlocked tags are no longer unlocked.
	 *
	 * This method is a shortcut to lock all tags but some of them in a single
	 * line. It also returns its callee, for more convenience.
	 *
	 * Example, in a definition file:
	 *
	 * ```php
	 * <div class="foo">
	 *    <!-- Select children to be output and ignore the others. -->
	 *    <?= $this->children()->only('title', 'content', 'author') ?>
	 * </div>
	 * ```
	 *
	 * @param string[] ...$tags
	 * The names that designate the tag names to be unlocked. Tags are compared
	 * with `$e->resolvedTag()`.
	 *
	 * @return IElementCollection
	 * Returns a reference to the element collection you called this method on
	 * (`$this`).
	 *
	 * @since 2.0.0
	 *
	 * @see IElementCollection::unlock()
	 * @see IElementCollection::lock()
	 * @see IElement::tag()
	 */
	public function only(string ...$tags): IElementCollection;

	/**
	 * Aggregates and returns the children of all the the elements of this
	 * collection.
	 *
	 * @param string $tag
	 * If specified, this method becomes a shortcut for
	 * `$e->children()->only($tag)`.
	 *
	 * @return IElementCollection
	 * Returns a new element collection, that contains the grandchildren of the
	 * original parent.
	 */
	public function children(string $tag): IElementCollection;
}
