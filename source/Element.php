<?php

namespace Pasap;

class Element implements IElement
{
	/**
	 * The native PHP DOM node.
	 *
	 * @var \DOMNode
	 *
	 * @since 2.0.0
	 *
	 * @see IElement::__construct()
	 */
	protected $source = null;

	/**
	 * The parent of this element.
	 *
	 * @var IElement
	 *
	 * @since 2.0.0
	 *
	 * @see IElement::__construct()
	 */
	protected $parent = null;

	/**
	 * This array contains cached information that correspond to the return
	 * values of certain methods:
	 *
	 * - `scope`: `array`.
	 * - `ns`: `string` or `null`.
	 * - `endTag`: `string`.
	 * - `rawNs`: `string` or `null`.
	 * - `rawFullTag`: `string`.
	 * - `rootNs`: `string` or `null`.
	 * - `fullTag`: `string`.
	 * - `resolvedTag`: `string` or `null`.
	 *
	 * @var array
	 *
	 * @since 2.0.0
	 *
	 * @see IElement::scope()
	 * @see IElement::ns()
	 * @see IElement::endTag()
	 * @see IElement::rawNs()
	 * @see IElement::rawFullTag()
	 * @see IElement::rootNs()
	 * @see IElement::fullTag()
	 * @see IElement::resolvedTag()
	 */
	protected $cache = [
		'scope' => [],
		'data'  => []
	];

	// Pasap\IElement Methods
	// ----------------------------------------------------------------

	/** @inheritDoc */
	public function __construct (\DOMNode $source, $parent = null)
	{
		if (
			   $source instanceof \DOMElement
			|| $source instanceof \DOMText
			|| $source instanceof \DOMComment
			|| $source instanceof \DOMProcessingInstruction
		) {
			$this->source = $source;
		} else {
			throw new \TypeError('Invalid ' . get_class($source) . ' given as element source, expected \DOMElement or \DOMText or \DOMComment or \DOMProcessingInstruction.');
		}

		if ($parent instanceof IElement || is_null($parent)) {
			$this->parent = $parent;
		} else {
			throw new \TypeError('Invalid ' . get_class($parent) . ' given as parent element, expected Pasap\IElement or NULL.');
		}

		// Bring Pasap namespace to the scope.
		if (!is_null($this->attr('pasap:ns'))) {
			if ($this->attr('pasap:ns') === '') {
				// This is a pasap:ns reset.
				$this->cache['scope']['pasap:ns'] = null;
			} else {
				$this->cache['scope']['pasap:ns'] = $this->attr('pasap:ns');
			}
		}
	}

	/** @inheritDoc */
	public function __toString (): string
	{
		if ($this->is('#text'))    return $this->source->nodeValue;
		if ($this->is('#comment')) return "<!-- {$this->source->nodeValue} -->";
		if ($this->is('#cdata'))   return "<![CDATA[{$this->source->nodeValue}]]>";
		if ($this->is('#process')) return "<?{$this->source->nodeName} {$this->source->nodeValue} ?>";

		if ($this->is('#pasap')) return $this->children()->__toString();

		if ($this->is('#native')) {
			$attributes = empty($attributes = $this->attr()->__toString()) ? '' : ' ' . $attributes;

			if ($this->is('#self-closing')) {
				return "<{$this->endTag()}{$attributes}/>";
			} else {
				return "<{$this->endTag()}{$attributes}>{$this->children()}</{$this->endTag()}>";
			}
		}

		if ($this->is('#resolved')) {
			ob_start();

			include $this->cache['definitionFile'];

			return ob_get_clean();
		} else {
			$attributes = empty($attributes = $this->attr()->__toString()) ? '' : ' ' . $attributes;

			if (is_null($this->rawNs())) {
				// Ultimate check for self-closing element.
				foreach (Configure::get('nativeNamespace') as $selfClosingTags) {
					if (in_array($this->rawFullTag(), $selfClosingTags)) {
						return "<{$this->endTag()}{$attributes}/>";
					}
				}
			}

			return "<{$this->rawFullTag()}{$attributes}>{$this->children()}</{$this->rawFullTag()}>";
		}
	}

	/** @inheritDoc */
	public function endTag (): string
	{
		if (!array_key_exists('rawTagParts', $this->cache)) {
			$this->cache['rawTagParts'] = explode(':', $this->rawFullTag());
		}

		return end($this->cache['rawTagParts']);
	}

	/** @inheritDoc */
	public function rawNs ()
	{
		if (!array_key_exists('rawNs', $this->cache)) {
			if (!array_key_exists('rawTagParts', $this->cache)) {
				$this->cache['rawTagParts'] = explode(':', $this->rawFullTag());
			}

			if (count($this->cache['rawTagParts']) === 1) {
				$this->cache['rawNs'] =  null;
			} else {
				$this->cache['rawNs'] =  implode(':', array_slice($this->cache['rawTagParts'], 0, -1));
			}
		}

		return $this->cache['rawNs'];
	}

	/** @inheritDoc */
	public function rawRootNs ()
	{
		if (!array_key_exists('rawTagParts', $this->cache)) {
			$this->cache['rawTagParts'] = explode(':', $this->rawFullTag());
		}

		if (count($this->cache['rawTagParts']) === 1) {
			return null;
		} else {
			return $this->cache['rawTagParts'][0];
		}
	}

	/** @inheritDoc */
	public function rawFullTag (): string
	{
		return $this->source->nodeName;
	}

	/** @inheritDoc */
	public function ns ()
	{
		if ($this->rawRootNs() === '') {
			return $this->rawNs();
		}

		if (!array_key_exists('ns', $this->cache)) {
			if (!array_key_exists('tagParts', $this->cache)) {
				$this->cache['tagParts'] = explode(':', $this->fullTag());
			}

			if (count($this->cache['tagParts']) === 1) {
				$this->cache['ns'] =  null;
			} else {
				$this->cache['ns'] =  implode(':', array_slice($this->cache['tagParts'], 0, -1));
			}
		}

		return $this->cache['ns'];
	}

	/** @inheritDoc */
	public function rootNs ()
	{
		if ($this->rawRootNs() === '') {
			return $this->rawRootNs();
		}

		if (!array_key_exists('tagParts', $this->cache)) {
			$this->cache['tagParts'] = explode(':', $this->fullTag());
		}

		if (count($this->cache['tagParts']) === 1) {
			return null;
		} else {
			return $this->cache['tagParts'][0];
		}
	}

	/** @inheritDoc */
	public function fullTag (): string
	{
		if ($this->rawRootNs() === '') {
			return $this->rawFullTag();
		}

		if (is_null($this->parent()) || is_null($this->parent()->scope('pasap:ns'))) {
			return $this->rawFullTag();
		} else {
			return $this->parent()->scope('pasap:ns') . ':' . $this->rawFullTag();
		}
	}

	/** @inheritDoc */
	public function resolvedTag ()
	{
		if (
			  !$this->is('#element')
			|| $this->is('#native')
			|| $this->is('#pasap')
		) {
			return null;
		}

		if (!array_key_exists('resolvedTag', $this->cache)) {
			if ($this->rawRootNs() === '') {
				// Case 1. Empty namespace is used: try to find a definition file
				// for the element in the empty ns definition folder.
				if (is_null($definitionFile = Pasap::definitionFile($this->rawFullTag()))) {
					// Case 1.1. Nothing in the empty ns folder... Let's get rid of the
					// empty ns and find a definition file in another namespace folder.
					$tag = $this->cache['rawTagParts'];

					array_shift($tag);

					if (is_null($definitionFile = Pasap::definitionFile(implode(':', $tag)))) {
						// Case 1.2. Give up this shit.
						$this->cache['resolvedTag'] = null;
					} else {
						$this->cache['resolvedTag'] = $tag;
						$this->cache['definitionFile'] = $definitionFile;
					}
				} else {
					$this->cache['resolvedTag'] = $this->rawFullTag();
					$this->cache['definitionFile'] = $definitionFile;
				}
			} else if (!is_null($this->parent()) && !is_null($this->parent()->scope('pasap:ns'))) {
				// Case 2. We have a pasap:ns somewhere: let's use it.
				if (is_null($definitionFile = Pasap::definitionFile($this->fullTag()))) {
					// Case 2.1. Nothing in this namespace. Let's try with a sub-folder
					// of the empty ns maybe?
					if (is_null($definitionFile = Pasap::definitionFile(':' . $this->fullTag()))) {
						// Case 2.2. Ok... Fuck the pasap:ns, we'll find without it.
						if (is_null($definitionFile = Pasap::definitionFile($this->rawFullTag()))) {
							// Case 2.3. Empty namespace, please!
							if (is_null($definitionFile = Pasap::definitionFile(':' . $this->rawFullTag()))) {
								// Case 2.4. Give up this shit.
								$this->cache['resolvedTag'] = null;
							} else {
								$this->cache['resolvedTag'] = ':' . $this->rawFullTag();
								$this->cache['definitionFile'] = $definitionFile;
							}
						} else {
							$this->cache['resolvedTag'] = $this->rawFullTag();
							$this->cache['definitionFile'] = $definitionFile;
						}
					} else {
						$this->cache['resolvedTag'] = ':' . $this->fullTag();
						$this->cache['definitionFile'] = $definitionFile;
					}
				} else {
					$this->cache['resolvedTag'] = $this->fullTag();
					$this->cache['definitionFile'] = $definitionFile;
				}
			} else {
				// Case 3. Try to find the element in it's defined root namespace.
				if (is_null($definitionFile = Pasap::definitionFile($this->rawFullTag()))) {
					// Case 3.1. Try again in a sub-folder of the empty ns.
					if (is_null($definitionFile = Pasap::definitionFile(':' . $this->rawFullTag()))) {
						// Case 3.2. Try fucking with flies...
						$this->cache['resolvedTag'] = null;
					} else {
						$this->cache['resolvedTag'] = ':' . $this->rawFullTag();
						$this->cache['definitionFile'] = $definitionFile;
					}
				} else {
					$this->cache['resolvedTag'] = $this->rawFullTag();
					$this->cache['definitionFile'] = $definitionFile;
				}
			}
		}

		return $this->cache['resolvedTag'];
	}

	/** @inheritDoc */
	public function resolvedRootNs () {
		if (!$this->is('#resolved')) {
			return null;
		}

		if (($i = strpos($this->resolvedTag(), ':')) === false) {
			return null;
		} else {
			return substr($this->resolvedTag(), 0, $i);
		}
	}

	/** @inheritDoc */
	public function tag (): string {
		return is_null($tag = $this->resolvedTag()) ? $this->rawFullTag() : $tag;
	}

	/** @inheritDoc */
	public function is (string $tag): bool
	{
		switch ($tag) {
			case '#text':
			case '#comment':
				return $tag === $this->rawFullTag();

			case '#native':
				return array_key_exists($this->rawRootNs(), Configure::get('nativeNamespace'));

			case '#self-closing':
				return $this->is('#native') && in_array($this->rawFullTag(), Configure::get('nativeNamespace')[$this->rootNs()]);

			case '#process': return $this->source instanceof \DOMProcessingInstruction;
			case '#element': return $this->source instanceof \DOMElement;

			case '#pasap':
				return $this->rawRootNs() === 'pasap' || $this->endTag() === 'pasap';

			case '#resolved':
				return !is_null($this->resolvedTag());

			case '#cdata':
				return $this->rawFullTag() === '#cdata-section';

			default:
				return $tag === $this->tag();
		}
	}

	/** @inheritDoc */
	public function parent ()
	{
		return $this->parent;
	}

	/** @inheritDoc */
	public function children ($tag = null)
	{
		if ($this->is('#element')) {
			$children = new ElementCollection($this->source->childNodes, $this);

			if (is_null($tag)) {
				return $children;
			} else {
				return $children->only($tag);
			}
		} else {
			return null;
		}
	}

	/** @inheritDoc */
	public function attr ($name = null, $fallback = null)
	{
		if ($this->is('#element')) {
			// We consider only regular DOM elements can have attributes. Text nodes,
			// comments, etc... don't have ones.
			if (is_null($name)) {
				// No argument given: return the whole collection.
				return new AttrCollection($this->source->attributes);
			} else {
				if ($this->source->hasAttribute($name)) {
					return $this->source->getAttribute($name);
				} else {
					return $fallback;
				}
			}
		} else {
			return $fallback;
		}
	}

	/** @inheritDoc */
	public function data (string $key, $fallback = null)
	{
		if (is_null($this->attr('pasap:data'))) {
			return $fallback;
		} else if (array_key_exists($key, $data = Pasap::getData($this->attr('pasap:data')))) {
			return $data[$key];
		} else {
			return $fallback;
		}
	}

	/** @inheritDoc */
	public function scope (string $key, $fallback = null)
	{
		if (!array_key_exists($key, $this->cache['scope'])) {
			if (
				   !is_null($this->attr('pasap:scope'))
				&& array_key_exists($key, $data = Pasap::getData($this->attr('pasap:scope')))
			) {
				// Case 1: it's in our scope. Great, return it.
				$this->cache['scope'][$key] = $data[$key];
			} else {
				// Case 2. it's not in our scope, but don't give up, it's maybe in the parent's one.
				if (is_null($this->parent())) {
					// Ok, give up this shit.
					$this->cache['scope'][$key] = $fallback;
				} else {
					// See? Our parents will always be here for us!
					$this->cache['scope'][$key] = $this->parent()->scope($key, $fallback);
				}
			}
		}

		return $this->cache['scope'][$key];
	}
}
