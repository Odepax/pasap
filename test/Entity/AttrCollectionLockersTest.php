<?php

namespace Pasap\Test\Entity;

use Pasap\IAttrCollection;
use Pasap\AttrCollection;

class AttrCollectionLockersTest extends AttrCollectionTestCase
{
	/** @var IAttrCollection */
	protected static $a;

	public function setUp ()
	{
		static::$a = new AttrCollection(static::$_->attributes);
	}

	public function testEcho ()
	{
		$this->expectOutputString('one="1" two="2" three="3" four="4" five="5" six="6" seven="7" eight="8" nine="9"');

		echo static::$a;
	}

	public function testForeach ()
	{
		$this->expectOutputString(' one="1" two="2" three="3" four="4" five="5" six="6" seven="7" eight="8" nine="9"');

		foreach (static::$a as $k => $v) { echo " $k=\"$v\""; }
	}

	public function testLockEcho ()
	{
		$this->expectOutputString(
			  'two="2" three="3" four="4" five="5" six="6" eight="8" nine="9"'
			. 'two="2" three="3" five="5" six="6" eight="8" nine="9"'
		);

		static::$a->lock('one');
		static::$a->lock('seven');
		static::$a->lock('foo');

		echo static::$a;

		static::$a->lock('four'); // Lockers are stacked, not reset.

		echo static::$a;

		$this->assertTrue(static::$a->isLocked('one'));
		$this->assertTrue(static::$a->isLocked('seven'));
		$this->assertTrue(static::$a->isLocked('foo'));
		$this->assertTrue(static::$a->isLocked('four'));

		$this->assertFalse(static::$a->isLocked('nine'));
		$this->assertFalse(static::$a->isLocked('six'));
		$this->assertFalse(static::$a->isLocked('bar'));
		$this->assertFalse(static::$a->isLocked('five'));
	}

	public function testLockForeach ()
	{
		$this->expectOutputString(
			  ' two="2" three="3" four="4" five="5" six="6" eight="8" nine="9"'
			. ' two="2" three="3" five="5" six="6" eight="8" nine="9"'
		);

		static::$a->lock('one');
		static::$a->lock('seven');
		static::$a->lock('bar');

		foreach (static::$a as $k => $v) { echo " $k=\"$v\""; }

		static::$a->lock('four'); // Lockers are stacked, not reset.

		foreach (static::$a as $k => $v) { echo " $k=\"$v\""; }

		$this->assertTrue(static::$a->isLocked('one'));
		$this->assertTrue(static::$a->isLocked('seven'));
		$this->assertTrue(static::$a->isLocked('bar'));
		$this->assertTrue(static::$a->isLocked('four'));

		$this->assertFalse(static::$a->isLocked('nine'));
		$this->assertFalse(static::$a->isLocked('six'));
		$this->assertFalse(static::$a->isLocked('foo'));
		$this->assertFalse(static::$a->isLocked('five'));
	}

	public function testLockUnlockEcho ()
	{
		$this->expectOutputString(
			  'two="2" three="3" four="4" five="5" six="6" eight="8" nine="9"'
			. 'one="1" two="2" three="3" five="5" six="6" eight="8" nine="9"'
		);

		static::$a->lock('one');
		static::$a->lock('seven');

		echo static::$a;

		$this->assertTrue(static::$a->isLocked('one'));

		static::$a->lock('four'); // Lockers are stacked, not reset.

		static::$a->unlock('one'); // Lockers are popped, not reset.
		static::$a->unlock('nine');

		echo static::$a;

		$this->assertTrue(static::$a->isLocked('seven'));
		$this->assertTrue(static::$a->isLocked('four'));

		$this->assertFalse(static::$a->isLocked('one'));
		$this->assertFalse(static::$a->isLocked('nine'));
	}

	public function testLockUnlockForeach ()
	{
		$this->expectOutputString(
			  ' two="2" three="3" four="4" five="5" six="6" eight="8" nine="9"'
			. ' one="1" two="2" three="3" five="5" six="6" eight="8" nine="9"'
		);

		static::$a->lock('one');
		static::$a->lock('seven');

		foreach (static::$a as $k => $v) { echo " $k=\"$v\""; }

		$this->assertTrue(static::$a->isLocked('one'));

		static::$a->lock('four'); // Lockers are stacked, not reset.

		static::$a->unlock('one'); // Lockers are popped, not reset.
		static::$a->unlock('nine');

		foreach (static::$a as $k => $v) { echo " $k=\"$v\""; }

		$this->assertTrue(static::$a->isLocked('seven'));
		$this->assertTrue(static::$a->isLocked('four'));

		$this->assertFalse(static::$a->isLocked('one'));
		$this->assertFalse(static::$a->isLocked('nine'));
	}

	public function testButEcho ()
	{
		$this->expectOutputString(
			  'three="3" four="4" five="5" six="6" seven="7" eight="8" nine="9"'
			. 'one="1" two="2" three="3" four="4" five="5" seven="7" eight="8" nine="9"'
		);

		static::$a->but('one', 'two');

		echo static::$a;

		static::$a->but('six'); // Lockers are reset.

		echo static::$a;

		$this->assertTrue(static::$a->isLocked('six'));

		$this->assertFalse(static::$a->isLocked('one'));
		$this->assertFalse(static::$a->isLocked('two'));
	}

	public function testButForeach ()
	{
		$this->expectOutputString(
			  ' three="3" four="4" five="5" six="6" seven="7" eight="8" nine="9"'
			. ' one="1" two="2" three="3" four="4" five="5" seven="7" eight="8" nine="9"'
		);

		static::$a->but('one', 'two');

		foreach (static::$a as $k => $v) { echo " $k=\"$v\""; }

		static::$a->but('six'); // Lockers are reset.

		foreach (static::$a as $k => $v) { echo " $k=\"$v\""; }

		$this->assertTrue(static::$a->isLocked('six'));

		$this->assertFalse(static::$a->isLocked('one'));
		$this->assertFalse(static::$a->isLocked('two'));
	}

	public function testOnlyEcho ()
	{
		$this->expectOutputString(
			  'one="1" two="2" three="3" nine="9"'
			. 'two="2" six="6" seven="7"'
			. ''
		);

		static::$a->only('one', 'two', 'three', 'nine');

		echo static::$a;

		static::$a->only('two', 'six', 'seven'); // Lockers are reset.

		echo static::$a;

		$this->assertTrue(static::$a->isLocked('one'));
		$this->assertTrue(static::$a->isLocked('nine'));

		$this->assertFalse(static::$a->isLocked('two'));
		$this->assertFalse(static::$a->isLocked('six'));

		static::$a->only(); // Lockers are reset.

		echo static::$a;

		$this->assertTrue(static::$a->isLocked('one'));
		$this->assertTrue(static::$a->isLocked('two'));
		$this->assertTrue(static::$a->isLocked('six'));
		$this->assertTrue(static::$a->isLocked('foo'));
		$this->assertTrue(static::$a->isLocked('bar'));
	}

	public function testOnlyForeach ()
	{
		$this->expectOutputString(
			  ' one="1" two="2" three="3" nine="9"'
			. ' two="2" six="6" seven="7"'
			. ''
		);

		static::$a->only('one', 'two', 'three', 'nine');

		foreach (static::$a as $k => $v) { echo " $k=\"$v\""; }

		$this->assertTrue(static::$a->isLocked('six'));
		$this->assertTrue(static::$a->isLocked('eight'));

		$this->assertFalse(static::$a->isLocked('nine'));
		$this->assertFalse(static::$a->isLocked('three'));

		static::$a->only('two', 'six', 'seven'); // Lockers are reset.

		foreach (static::$a as $k => $v) { echo " $k=\"$v\""; }

		static::$a->only(); // Lockers are reset.

		foreach (static::$a as $k => $v) { echo " $k=\"$v\""; }

		$this->assertTrue(static::$a->isLocked('one'));
		$this->assertTrue(static::$a->isLocked('two'));
		$this->assertTrue(static::$a->isLocked('six'));
		$this->assertTrue(static::$a->isLocked('foo'));
		$this->assertTrue(static::$a->isLocked('bar'));
	}

	public function testLockUnlockButOnlyEcho ()
	{
		$this->expectOutputString(
			  'one="1" two="2" three="3" four="4" five="5" six="6" seven="7" eight="8" nine="9"' . "\n"
			. 'three="3" four="4" five="5" six="6" eight="8" nine="9"' . "\n"
			. 'three="3" four="4" five="5" eight="8" nine="9"' . "\n"
			. 'one="1" two="2" three="3" four="4" five="5" six="6" seven="7" eight="8"' . "\n"
			. 'one="1" two="2" three="3" four="4" five="5" six="6" seven="7" eight="8" nine="9"' . "\n"
			. 'one="1" three="3" five="5" seven="7" nine="9"' . "\n"
			. 'one="1" two="2" three="3" four="4" five="5" six="6" seven="7" eight="8" nine="9"' . "\n"
			. 'one="1" three="3" four="4" five="5" six="6" seven="7" nine="9"' . "\n"
			. 'one="1" two="2" four="4" five="5" six="6" seven="7" eight="8" nine="9"' . "\n"
			. 'one="1" four="4" five="5" seven="7" nine="9"' . "\n"
		);

		echo static::$a . "\n";

		static::$a->lock('one');
		static::$a->lock('two');
		static::$a->lock('seven');

		echo static::$a . "\n";

		static::$a->lock('six');

		echo static::$a . "\n";

		static::$a->but('nine'); // Lockers are reset.

		echo static::$a . "\n";

		static::$a->unlock('nine');

		echo static::$a . "\n";

		static::$a->lock('two'); // Lockers are stacked.
		static::$a->lock('four');
		static::$a->lock('six');
		static::$a->lock('eight');

		echo static::$a . "\n";

		echo static::$a->but() . "\n"; // Lockers are reset.

		static::$a->lock('two'); // Lockers are stacked.
		static::$a->lock('six');
		static::$a->lock('eight');

		static::$a->unlock('six');

		echo static::$a . "\n";

		echo static::$a->but('three') . "\n"; // Lockers are reset.

		static::$a->lock('two'); // Lockers are stacked.
		static::$a->lock('six');
		static::$a->lock('eight');

		echo static::$a . "\n";
	}

	public function testLockUnlockButOnlyForeach ()
	{
		$this->expectOutputString(
			  ' one="1" two="2" three="3" four="4" five="5" six="6" seven="7" eight="8" nine="9"' . "\n"
			. ' three="3" four="4" five="5" six="6" eight="8" nine="9"' . "\n"
			. ' three="3" four="4" five="5" eight="8" nine="9"' . "\n"
			. ' one="1" two="2" three="3" four="4" five="5" six="6" seven="7" eight="8"' . "\n"
			. ' one="1" two="2" three="3" four="4" five="5" six="6" seven="7" eight="8" nine="9"' . "\n"
			. ' one="1" three="3" five="5" seven="7" nine="9"' . "\n"
			. ' one="1" two="2" three="3" four="4" five="5" six="6" seven="7" eight="8" nine="9"' . "\n"
			. ' one="1" three="3" four="4" five="5" six="6" seven="7" nine="9"' . "\n"
			. ' one="1" two="2" four="4" five="5" six="6" seven="7" eight="8" nine="9"' . "\n"
			. ' one="1" four="4" five="5" seven="7" nine="9"' . "\n"
		);

		foreach (static::$a as $k => $v) { echo " $k=\"$v\""; }
		echo "\n";

		static::$a->lock('one');
		static::$a->lock('two');
		static::$a->lock('seven');

		foreach (static::$a as $k => $v) { echo " $k=\"$v\""; }
		echo "\n";

		static::$a->lock('six');

		foreach (static::$a as $k => $v) { echo " $k=\"$v\""; }
		echo "\n";

		static::$a->but('nine'); // Lockers are reset.

		foreach (static::$a as $k => $v) { echo " $k=\"$v\""; }
		echo "\n";

		static::$a->unlock('nine');

		foreach (static::$a as $k => $v) { echo " $k=\"$v\""; }
		echo "\n";

		static::$a->lock('two'); // Lockers are stacked.
		static::$a->lock('four');
		static::$a->lock('six');
		static::$a->lock('eight');

		foreach (static::$a as $k => $v) { echo " $k=\"$v\""; }
		echo "\n";

		foreach (static::$a->but() as $k => $v) { echo " $k=\"$v\""; } // Lockers are reset.
		echo "\n";

		static::$a->lock('two'); // Lockers are stacked.
		static::$a->lock('six');
		static::$a->lock('eight');

		static::$a->unlock('six');

		foreach (static::$a as $k => $v) { echo " $k=\"$v\""; }
		echo "\n";

		foreach (static::$a->but('three') as $k => $v) { echo " $k=\"$v\""; } // Lockers are reset.
		echo "\n";

		static::$a->lock('two'); // Lockers are stacked.
		static::$a->lock('six');
		static::$a->lock('eight');

		foreach (static::$a as $k => $v) { echo " $k=\"$v\""; }
		echo "\n";
	}
}
