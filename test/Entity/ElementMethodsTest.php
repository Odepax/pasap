<?php

namespace Pasap\Test\Entity;

use Pasap\IAttrCollection;
use Pasap\IElementCollection;
use Pasap\IElement;
use Pasap\Element;

class ElementMethodsTest extends ElementTestCase
{
	/** @var IElement */
	protected static $e;

	/** @var IElement */
	protected static $pasap;

	/** @var IElement */
	protected static $pasapNs;

	public static function setUpBeforeClass ()
	{
		parent::setUpBeforeClass();

		// Pasap element without `pasap:ns` attribute.
		$pasap = static::$document->createElement('pasap');

		$pasap->appendChild(static::$document->createElement('util:md'));
		$pasap->appendChild(static::$document->createElement(':util:md'));
		$pasap->appendChild(static::$document->createElement('md'));
		$pasap->appendChild(static::$document->createElement(':md'));

		static::$pasap = new Element($pasap);

		// Pasap element with `pasap:ns` attribute.
		$pasapNs = static::$document->createElement('pasap');

		$pasapNs->appendChild(new \DOMAttr('pasap:ns', 'writing'));

		$pasapNs->appendChild(static::$document->createElement('util:md'));
		$pasapNs->appendChild(static::$document->createElement(':util:md'));
		$pasapNs->appendChild(static::$document->createElement('md'));
		$pasapNs->appendChild(static::$document->createElement(':md'));

		static::$pasapNs = new Element($pasapNs);
	}

	public function setUp ()
	{
		static::$e = new Element(static::$_, new Element(static::$_));
	}

	public function testEcho ()
	{
		$this->expectOutputString('<e one="1" two="2" three="3" four="4" five="5" six="6"><one>1</one><two>2</two><three>3</three><four>4</four><five>5</five><six>6</six></e>');

		echo static::$e;
	}

	public function testParent ()
	{
		$this->assertInstanceOf(IElement::class, static::$e->parent());
	}

	public function testChildren ()
	{
		$this->assertInstanceOf(IElementCollection::class, static::$e->children());
	}

	public function testAttrWithoutParameter ()
	{
		$this->assertInstanceOf(IAttrCollection::class, static::$e->attr());
	}

	public function testAttr ()
	{
		$this->assertEquals('1', static::$e->attr('one'));
		$this->assertEquals('2', static::$e->attr('two'));
		$this->assertEquals('6', static::$e->attr('six'));
		$this->assertEquals(null, static::$e->attr('bar'));
		$this->assertEquals('|', static::$e->attr('bar', '|'));
	}

	public function testEndTag ()
	{
		$this->expectOutputString(
			  'md' . "\n" // Without `pasap:ns`.
			. 'md' . "\n"
			. 'md' . "\n"
			. 'md' . "\n"
			. 'md' . "\n" // With `pasap:ns`.
			. 'md' . "\n"
			. 'md' . "\n"
			. 'md' . "\n"
		);

		foreach (static::$pasap->children()   as $child) { echo $child->endTag() . "\n"; }
		foreach (static::$pasapNs->children() as $child) { echo $child->endTag() . "\n"; }
	}

	public function testRawNs ()
	{
		$this->expectOutputString(
			  'util' . "\n" // Without `pasap:ns`.
			. ':util' . "\n"
			. "\n" // null
			. '' . "\n"
			. 'util' . "\n" // With `pasap:ns`.
			. ':util' . "\n"
			. "\n" // null
			. '' . "\n"
		);

		foreach (static::$pasap->children()   as $child) { echo $child->rawNs() . "\n"; }
		foreach (static::$pasapNs->children() as $child) { echo $child->rawNs() . "\n"; }
	}

	public function testRawRootNs ()
	{
		$this->expectOutputString(
			  'util' . "\n" // Without `pasap:ns`.
			. '' . "\n"
			. "\n" // null
			. '' . "\n"
			. 'util' . "\n" // With `pasap:ns`.
			. '' . "\n"
			. "\n" // null
			. '' . "\n"
		);

		foreach (static::$pasap->children()   as $child) { echo $child->rawRootNs() . "\n"; }
		foreach (static::$pasapNs->children() as $child) { echo $child->rawRootNs() . "\n"; }
	}

	public function testRawFullTag ()
	{
		$this->expectOutputString(
			  'util:md' . "\n" // Without `pasap:ns`.
			. ':util:md' . "\n"
			. 'md' . "\n"
			. ':md' . "\n"
			. 'util:md' . "\n" // With `pasap:ns`.
			. ':util:md' . "\n"
			. 'md' . "\n"
			. ':md' . "\n"
		);

		foreach (static::$pasap->children()   as $child) { echo $child->rawFullTag() . "\n"; }
		foreach (static::$pasapNs->children() as $child) { echo $child->rawFullTag() . "\n"; }
	}

	public function testNs ()
	{
		$this->expectOutputString(
			'util' . "\n" // Without `pasap:ns`.
			. ':util' . "\n"
			. "\n" // null
			. '' . "\n"
			. 'writing:util' . "\n" // With `pasap:ns`.
			. ':util' . "\n"
			. 'writing' . "\n"
			. '' . "\n"
		);

		foreach (static::$pasap->children()   as $child) { echo $child->ns() . "\n"; }
		foreach (static::$pasapNs->children() as $child) { echo $child->ns() . "\n"; }
	}

	public function testRootNs ()
	{
		$this->expectOutputString(
			  'util' . "\n" // Without `pasap:ns`.
			. '' . "\n"
			. "\n" // null
			. '' . "\n"
			. 'writing' . "\n" // With `pasap:ns`.
			. '' . "\n"
			. 'writing' . "\n"
			. '' . "\n"
		);

		foreach (static::$pasap->children()   as $child) { echo $child->rootNs() . "\n"; }
		foreach (static::$pasapNs->children() as $child) { echo $child->rootNs() . "\n"; }
	}

	public function testFullTag ()
	{
		$this->expectOutputString(
			  'util:md' . "\n" // Without `pasap:ns`.
			. ':util:md' . "\n"
			. 'md' . "\n"
			. ':md' . "\n"
			. 'writing:util:md' . "\n" // With `pasap:ns`.
			. ':util:md' . "\n"
			. 'writing:md' . "\n"
			. ':md' . "\n"
		);

		foreach (static::$pasap->children()   as $child) { echo $child->fullTag() . "\n"; }
		foreach (static::$pasapNs->children() as $child) { echo $child->fullTag() . "\n"; }
	}

	public function testResolvedTag ()
	{
		$this->expectOutputString(
			  "\n" // Without `pasap:ns`.
			. "\n"
			. "\n"
			. "\n"
			. "\n" // With `pasap:ns`.
			. "\n"
			. "\n"
			. "\n"
		); // All null...

		foreach (static::$pasap->children()   as $child) { echo $child->resolvedTag() . "\n"; }
		foreach (static::$pasapNs->children() as $child) { echo $child->resolvedTag() . "\n"; }
	}
}
