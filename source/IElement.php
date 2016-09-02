<?php

namespace Pasap;

/**
 * This interface defines what methods an element should provide to be
 * compatible with Pasap.
 *
 * An element is an interface, an object that wraps a native PHP `DOMNode`,
 * providing simple methods to access essential data, and adding more
 * features to it.
 *
 * All along this documentation, `$e` will designate an element. `$e` is a
 * `<writing:util:markdown>` custom tag, written `<util:markdown>` because under
 * a `<pasap pasap:ns="writing">` parent:
 *
 * ```xml
 * <pasap pasap:ns="writing">
 *    <util:markdown> ... </util:markdown>
 * </pasap:ns>
 * ```
 *
 * We consider "_writing_" is the root namespace that was configured with
 * `Configure::namespaceSource()`.
 *
 * @since 2.0.0
 *
 * @see Configure::namespaceSource()
 */
interface IElement
{
	/**
	 * Creates a new element.
	 *
	 * @param \DOMNode $source
	 * This is the native PHP `DOMNode` to be wrapped. It is an object of one of
	 * those types:
	 *
	 * - `DOMElement`
	 * - `DOMText`, which includes (`DOMCdataSection`)
	 * - `DOMComment`
	 * - `DOMProcessingInstruction`
	 *
	 * @param IElement|null $parent
	 * The parent element of this element.
	 *
	 * @since 2.0.0
	 */
	public function __construct (\DOMNode $source, $parent = null);
	
	/**
	 * Renders this element recursively and returns the output string.
	 *
	 * @return string
	 * Returns the output of the render process of this element.
	 *
	 * @since 2.0.0
	 *
	 * @see https://github.com/Odepax/pasap/wiki/Namespaces#how-elements-are-resolved-and-rendered
	 */
	public function __toString (): string;

	/**
	 * Returns the tag name of the element, without namespace.
	 *
	 * This method returns the end name of the element, which does not include
	 * the namespace.
	 *
	 * For instance, `$e->tag()` will return `'markdown'`.
	 *
	 * @return string
	 * Returns the "_basic_" name of this element, without namespace.
	 *
	 * @since 2.0.0
	 *
	 * @see IElement
	 */
	public function endTag (): string;

	/**
	 * Returns the namespace of this element as written in the input source.
	 *
	 * This method returns the namespace of the element, as it was written in
	 * the `DOMNode` source.
	 *
	 * For instance, `$e->rawNs()` will return `'util'`.
	 *
	 * @return string|null
	 * Returns the namespace of this element as written in the input source.
	 *
	 * @since 2.0.0
	 *
	 * @see IElement
	 * @see https://github.com/Odepax/pasap/wiki/Namespaces
	 */
	public function rawNs ();

	/**
	 * Returns the root namespace of this element as written in the input source.
	 *
	 * This method returns the namespace of the element, as it was written in
	 * the `DOMNode` source.
	 *
	 * For instance, `$e->rawRootNs()` will return `'util'`.
	 *
	 * @return string|null
	 * Returns the namespace of this element as written in the input source.
	 *
	 * @since 2.0.0
	 *
	 * @see IElement
	 * @see https://github.com/Odepax/pasap/wiki/Namespaces
	 */
	public function rawRootNs ();

	/**
	 * Returns the tag of the element as written in the input source, with the
	 * namespace.
	 *
	 * For instance, `$e->rawFullTag()` will return `'util:markdown'`.
	 *
	 * @return string
	 * Returns the full name of this element, as written in the input.
	 *
	 * @since 2.0.0
	 *
	 * @see IElement
	 */
	public function rawFullTag (): string;

	/**
	 * Returns the full absolute namespace of this element.
	 *
	 * This method returns the full absolute namespace of the element, starting
	 * from the configured root namespace.
	 *
	 * For instance, `$e->ns()` will return `'writing:util'`.
	 *
	 * @return string|null
	 * Returns the namespace of this element from the configured root namespace.
	 *
	 * @since 2.0.0
	 *
	 * @see IElement
	 * @see https://github.com/Odepax/pasap/wiki/Namespaces
	 */
	public function ns ();

	/**
	 * Returns the root namespace of this element.
	 *
	 * This method returns the root namespace of the element, which is the top
	 * namespace of this element, the one that has been configured with
	 * `Configure::namespaceSource()`.
	 *
	 * For instance, `$e->rootNs()` will return `'writing'`.
	 *
	 * @return string|null
	 * Returns the configured root namespace of this element.
	 *
	 * @since 2.0.0
	 *
	 * @see IElement
	 * @see Configure::namespaceSource()
	 * @see https://github.com/Odepax/pasap/wiki/Namespaces
	 */
	public function rootNs ();

	/**
	 * Returns the fully-root-namespaced tag name of the element.
	 *
	 * This method returns the namespaced tag of the element, as computed.
	 *
	 * For instance, `$e->fullTag()` will return `'writing:util:markdown'`.
	 *
	 * @return string
	 * Returns the fully-namespaced tag name of this element.
	 *
	 * @since 2.0.0
	 *
	 * @see IElement
	 */
	public function fullTag (): string;

	/**
	 * Returns the resolved tag name of the element.
	 *
	 * The resolved tag name takes the resolution of the element into account,
	 * which means Pasap will check for a corresponding definition file in
	 * order to validate its result.
	 *
	 * For instance, `$e->resolvedTag()` can return:
	 *
	 * - `'writing:util:markdown'`.
	 * - `':writing:util:markdown'`.
	 * - `'util:markdown'`.
	 * - `':util:markdown'`.
	 *
	 * @return string|null
	 * Returns the resolved tag name of this element.
	 * Returns `null` if the element cannot be resolved (i.e. there is no
	 * definition file for it).
	 *
	 * @since 2.0.0
	 *
	 * @see IElement
	 * @see https://github.com/Odepax/pasap/wiki/Namespaces#how-elements-are-resolved-and-rendered
	 */
	public function resolvedTag ();

	/**
	 * Returns the resolved root namespace of the element.
	 *
	 * The resolved root namespace takes the resolution of the element into account,
	 * which means Pasap will check for a corresponding definition file in
	 * order to validate its result.
	 *
	 * For instance, `$e->resolvedRootNs()` can return:
	 *
	 * - `'writing'`.
	 * - `'util'`.
	 * - `''`.
	 *
	 * @return string|null
	 * Returns the resolved root namespace of this element.
	 * Returns `null` if the element cannot be resolved (i.e. there is no
	 * definition file for it).
	 *
	 * @since 2.0.0
	 *
	 * @see IElement
	 * @see IElement::resolvedTag()
	 * @see https://github.com/Odepax/pasap/wiki/Namespaces
	 */
	public function resolvedRootNs ();

	/**
	 * Returns the resolved tag or the full raw tag name of this element.
	 *
	 * @return string
	 * Returns the value of `$this->resolvedTag()` or `$this->rawFullTag()`
	 * if the element is not resolved.
	 *
	 * @since 2.0.0
	 */
	public function tag (): string;

	/**
	 * Indicates if this element "_is_" a `$tag`.
	 *
	 * @param string $tag
	 * The tag this element's tag will be compared with. Tags are compared with
	 * `$e->tag()`.
	 *
	 * This method accepts tag names in parameter, but also some special values:
	 *
	 * - `'#text'`: indicates if this element is a text node.
	 *
	 * - `'#comment'`: indicates if this element is a comment (`<!-- -->`).
	 *
	 * - `'#native'`: indicates if this element is considered as a native tag.
	 *   A native tag belongs to a configured native namespace
	 *   (`Configure::nativeNamespaces()`).
	 *
	 * - `'#self-closing'`: indicates if a native element is self-closing
	 *   (performs `$e->is('#native')` automatically).
	 *
	 * - `'#process'`: indicates if this element is a processing instruction
	 *   tag.
	 *
	 * - `'#resolved'`: indicates if this element is resolved, i.e. its
	 *   definition file is fixed and found.
	 *
	 * - `'#pasap'`: indicates if this element is a `<pasap>` element or belongs
	 *   to the "_pasap_" namespace.
	 *
	 * @return bool
	 * Returns `true` or `false` (Thanks, Captain Konstadt!).
	 *
	 * @since 2.0.0
	 *
	 * @see Configure::nativeNamespaces()
	 * @see IElement::resolvedTag()
	 */
	public function is (string $tag): bool;

	/**
	 * Returns the parent element of this element.
	 *
	 * @return IElement|null
	 * Returns the parent of this element.
	 * Returns `null` if this element is the root element of its document or
	 * simply if no parent was given to the constructor.
	 *
	 * @since 2.0.0
	 */
	public function parent ();

	/**
	 * Returns the children of this element.
	 *
	 * @param string|null $tag
	 * If specified, this method becomes a shortcut for
	 * `$e->children()->only($tag)`.
	 *
	 * @return IElementCollection|null
	 * Returns the collection of children (`IElement`s) of this element.
	 * Returns `null` when the element has no child, it is the case of text and
	 * comment nodes for instance.
	 *
	 * @since 2.0.0
	 *
	 * @see IElementCollection::only()
	 * @see https://github.com/Odepax/pasap/wiki/Namespaces
	 * @see https://github.com/Odepax/pasap/wiki/Definition-Files#access-attributes-and-children-of-an-element
	 */
	public function children ($tag = null);

	/**
	 * Gets the value of an attribute or the whole list of attributes of this
	 * element.
	 *
	 * @param string|null $name
	 * The full name of the attribute to look for.
	 *
	 * @param mixed $fallback
	 * A fallback value. If the specified attribute does not exist, then this
	 * value will be returned.
	 *
	 * @return mixed
	 * Returns the value of the specified attribute (`string`).
	 * Returns the specified fallback value (`mixed`) when the attribute does
	 * not exist.
	 * Returns the whole attribute collection (`IAttrCollection`) if called with
	 * no parameter.
	 *
	 * @since 2.0.0
	 *
	 * @see IAttrCollection
	 * @see https://github.com/Odepax/pasap/wiki/Definition-Files#access-attributes-and-children-of-an-element
	 */
	public function attr ($name = null, $fallback = null);

	/**
	 * Gets the value of a boolean attribute of this element.
	 *
	 * `'false'`, `'off'`, `'no'`, `'n'`, `'f'`, `'0'` and `''` are considered
	 * as `false`, ; other values are considered as `true`.
	 * 
	 * PS: It's case-insensitive.
	 *
	 * @param string $name
	 * The full name of the attribute to look for.
	 * 
	 * @param bool $fallback
	 * A fallback value. If the specified attribute does not exist, then this
	 * value will be returned.
	 *
	 * @return bool
	 * Returns the bool-converted value of the specified attribute.
	 * Returns the specified fallback value (`bool`) when the attribute does
	 * not exist.
	 *
	 * @since 2.0.0
	 *
	 * @see IElement::attr()
	 */
	public function attrAsBool (string $name, bool $fallback = false): bool;

	/**
	 * Gets the value of a data attached to this element.
	 *
	 * @param string $key
	 * The key of the data to look for. This key is the unique identifier of the
	 * data.
	 *
	 * @param mixed $fallback
	 * A fallback value. If the specified data does not exist in the data set of
	 * this element, then this value will be returned.
	 *
	 * @return mixed
	 * Returns the value of the specified data, or the fallback value if the
	 * data is not found.
	 *
	 * @since 2.0.0
	 *
	 * @see Pasap::data()
	 * @see https://github.com/Odepax/pasap/wiki/Data-Set-and-Scope#data-set
	 */
	public function data (string $key, $fallback = null);

	/**
	 * Gets the value of a data attached to the scope of this element.
	 *
	 * If the values is not found in this element's data scope, then the
	 * parent's data scope is browsed and so on util the recursion reached the
	 * top of the DOM tree. If the data is still not found, the method will
	 * return the specified fallback values.
	 *
	 * @param string $key
	 * The key of the data to look for. This key is the unique identifier of the
	 * data.
	 *
	 * @param mixed $fallback
	 * A fallback value. If the specified data does not exist in the data scope,
	 * then this value will be returned.
	 *
	 * @return mixed
	 * Returns the value of the specified data, or the fallback value if the
	 * data is not found.
	 *
	 * @since 2.0.0
	 *
	 * @see Pasap::scope()
	 * @see https://github.com/Odepax/pasap/wiki/Data-Set-and-Scope#data-scope
	 */
	public function scope (string $key, $fallback = null);
}
