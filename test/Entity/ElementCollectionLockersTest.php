<?php

namespace Pasap\Test\Entity;

use Pasap\IElementCollection;
use Pasap\ElementCollection;
use Pasap\Element;

class ElementCollectionLockersTest extends ElementCollectionTestCase
{
	/** @var IElementCollection */
	protected static $c;

	public function setUp ()
	{
		static::$c = new ElementCollection(static::$_->childNodes, new Element(static::$_));
	}

	public function testEcho ()
	{
		$this->expectOutputString('<one>1</one><two>2</two><three>3</three><four>4</four><five>5</five><six>6</six><seven>7</seven><eight>8</eight><nine>9</nine>');

		echo static::$c;
	}

	public function testForeach ()
	{
		$this->expectOutputString('<one>1</one><two>2</two><three>3</three><four>4</four><five>5</five><six>6</six><seven>7</seven><eight>8</eight><nine>9</nine>');

		foreach (static::$c as $child) { echo $child; }
	}

	public function testLockEcho ()
	{
		$this->expectOutputString(
			  '<two>2</two><three>3</three><four>4</four><five>5</five><six>6</six><eight>8</eight><nine>9</nine>'
			. '<two>2</two><three>3</three><five>5</five><six>6</six><eight>8</eight><nine>9</nine>'
		);

		static::$c->lock('one');
		static::$c->lock('seven');
		static::$c->lock('foo');

		echo static::$c;

		static::$c->lock('four'); // Lockers are stacked, not reset.

		echo static::$c;

		$this->assertTrue(static::$c->isLocked('one'));
		$this->assertTrue(static::$c->isLocked('seven'));
		$this->assertTrue(static::$c->isLocked('foo'));
		$this->assertTrue(static::$c->isLocked('four'));

		$this->assertFalse(static::$c->isLocked('nine'));
		$this->assertFalse(static::$c->isLocked('six'));
		$this->assertFalse(static::$c->isLocked('bar'));
		$this->assertFalse(static::$c->isLocked('five'));
	}

	public function testLockForeach ()
	{
		$this->expectOutputString(
			  '<two>2</two><three>3</three><four>4</four><five>5</five><six>6</six><eight>8</eight><nine>9</nine>'
			. '<two>2</two><three>3</three><five>5</five><six>6</six><eight>8</eight><nine>9</nine>'
		);

		static::$c->lock('one');
		static::$c->lock('seven');
		static::$c->lock('bar');

		foreach (static::$c as $child) { echo $child; }

		static::$c->lock('four'); // Lockers are stacked, not reset.

		foreach (static::$c as $child) { echo $child; }

		$this->assertTrue(static::$c->isLocked('one'));
		$this->assertTrue(static::$c->isLocked('seven'));
		$this->assertTrue(static::$c->isLocked('bar'));
		$this->assertTrue(static::$c->isLocked('four'));

		$this->assertFalse(static::$c->isLocked('nine'));
		$this->assertFalse(static::$c->isLocked('six'));
		$this->assertFalse(static::$c->isLocked('foo'));
		$this->assertFalse(static::$c->isLocked('five'));
	}

	public function testLockUnlockEcho ()
	{
		$this->expectOutputString(
			  '<two>2</two><three>3</three><four>4</four><five>5</five><six>6</six><eight>8</eight><nine>9</nine>'
			. '<one>1</one><two>2</two><three>3</three><five>5</five><six>6</six><eight>8</eight><nine>9</nine>'
		);

		static::$c->lock('one');
		static::$c->lock('seven');

		echo static::$c;

		$this->assertTrue(static::$c->isLocked('one'));

		static::$c->lock('four'); // Lockers are stacked, not reset.

		static::$c->unlock('one'); // Lockers are popped, not reset.
		static::$c->unlock('nine');

		echo static::$c;

		$this->assertTrue(static::$c->isLocked('seven'));
		$this->assertTrue(static::$c->isLocked('four'));

		$this->assertFalse(static::$c->isLocked('one'));
		$this->assertFalse(static::$c->isLocked('nine'));
	}

	public function testLockUnlockForeach ()
	{
		$this->expectOutputString(
			  '<two>2</two><three>3</three><four>4</four><five>5</five><six>6</six><eight>8</eight><nine>9</nine>'
			. '<one>1</one><two>2</two><three>3</three><five>5</five><six>6</six><eight>8</eight><nine>9</nine>'
		);

		static::$c->lock('one');
		static::$c->lock('seven');

		foreach (static::$c as $child) { echo $child; }

		$this->assertTrue(static::$c->isLocked('one'));

		static::$c->lock('four'); // Lockers are stacked, not reset.

		static::$c->unlock('one'); // Lockers are popped, not reset.
		static::$c->unlock('nine');

		foreach (static::$c as $child) { echo $child; }

		$this->assertTrue(static::$c->isLocked('seven'));
		$this->assertTrue(static::$c->isLocked('four'));

		$this->assertFalse(static::$c->isLocked('one'));
		$this->assertFalse(static::$c->isLocked('nine'));
	}

	public function testButEcho ()
	{
		$this->expectOutputString(
			  '<three>3</three><four>4</four><five>5</five><six>6</six><seven>7</seven><eight>8</eight><nine>9</nine>'
			. '<one>1</one><two>2</two><three>3</three><four>4</four><five>5</five><seven>7</seven><eight>8</eight><nine>9</nine>'
		);

		static::$c->but('one', 'two');

		echo static::$c;

		static::$c->but('six'); // Lockers are reset.

		echo static::$c;

		$this->assertTrue(static::$c->isLocked('six'));

		$this->assertFalse(static::$c->isLocked('one'));
		$this->assertFalse(static::$c->isLocked('two'));
	}

	public function testButForeach ()
	{
		$this->expectOutputString(
			  '<three>3</three><four>4</four><five>5</five><six>6</six><seven>7</seven><eight>8</eight><nine>9</nine>'
			. '<one>1</one><two>2</two><three>3</three><four>4</four><five>5</five><seven>7</seven><eight>8</eight><nine>9</nine>'
		);

		static::$c->but('one', 'two');

		foreach (static::$c as $child) { echo $child; }

		static::$c->but('six'); // Lockers are reset.

		foreach (static::$c as $child) { echo $child; }

		$this->assertTrue(static::$c->isLocked('six'));

		$this->assertFalse(static::$c->isLocked('one'));
		$this->assertFalse(static::$c->isLocked('two'));
	}

	public function testOnlyEcho ()
	{
		$this->expectOutputString(
			  '<one>1</one><two>2</two><three>3</three><nine>9</nine>'
			. '<two>2</two><six>6</six><seven>7</seven>'
			. ''
		);

		static::$c->only('one', 'two', 'three', 'nine');

		echo static::$c;

		static::$c->only('two', 'six', 'seven'); // Lockers are reset.

		echo static::$c;

		$this->assertTrue(static::$c->isLocked('one'));
		$this->assertTrue(static::$c->isLocked('nine'));

		$this->assertFalse(static::$c->isLocked('two'));
		$this->assertFalse(static::$c->isLocked('six'));

		static::$c->only(); // Lockers are reset.

		echo static::$c;

		$this->assertTrue(static::$c->isLocked('one'));
		$this->assertTrue(static::$c->isLocked('two'));
		$this->assertTrue(static::$c->isLocked('six'));
		$this->assertTrue(static::$c->isLocked('foo'));
		$this->assertTrue(static::$c->isLocked('bar'));
	}

	public function testOnlyForeach ()
	{
		$this->expectOutputString(
			  '<one>1</one><two>2</two><three>3</three><nine>9</nine>'
			. '<two>2</two><six>6</six><seven>7</seven>'
			. ''
		);

		static::$c->only('one', 'two', 'three', 'nine');

		foreach (static::$c as $child) { echo $child; }

		$this->assertTrue(static::$c->isLocked('six'));
		$this->assertTrue(static::$c->isLocked('eight'));

		$this->assertFalse(static::$c->isLocked('nine'));
		$this->assertFalse(static::$c->isLocked('three'));

		static::$c->only('two', 'six', 'seven'); // Lockers are reset.

		foreach (static::$c as $child) { echo $child; }

		static::$c->only(); // Lockers are reset.

		foreach (static::$c as $child) { echo $child; }

		$this->assertTrue(static::$c->isLocked('one'));
		$this->assertTrue(static::$c->isLocked('two'));
		$this->assertTrue(static::$c->isLocked('six'));
		$this->assertTrue(static::$c->isLocked('foo'));
		$this->assertTrue(static::$c->isLocked('bar'));
	}

	public function testLockUnlockButOnlyEcho ()
	{
		$this->expectOutputString(
			  '<one>1</one><two>2</two><three>3</three><four>4</four><five>5</five><six>6</six><seven>7</seven><eight>8</eight><nine>9</nine>' . "\n"
			. '<three>3</three><four>4</four><five>5</five><six>6</six><eight>8</eight><nine>9</nine>' . "\n"
			. '<three>3</three><four>4</four><five>5</five><eight>8</eight><nine>9</nine>' . "\n"
			. '<one>1</one><two>2</two><three>3</three><four>4</four><five>5</five><six>6</six><seven>7</seven><eight>8</eight>' . "\n"
			. '<one>1</one><two>2</two><three>3</three><four>4</four><five>5</five><six>6</six><seven>7</seven><eight>8</eight><nine>9</nine>' . "\n"
			. '<one>1</one><three>3</three><five>5</five><seven>7</seven><nine>9</nine>' . "\n"
			. '<one>1</one><two>2</two><three>3</three><four>4</four><five>5</five><six>6</six><seven>7</seven><eight>8</eight><nine>9</nine>' . "\n"
			. '<one>1</one><three>3</three><four>4</four><five>5</five><six>6</six><seven>7</seven><nine>9</nine>' . "\n"
			. '<one>1</one><two>2</two><four>4</four><five>5</five><six>6</six><seven>7</seven><eight>8</eight><nine>9</nine>' . "\n"
			. '<one>1</one><four>4</four><five>5</five><seven>7</seven><nine>9</nine>' . "\n"
		);

		foreach (static::$c as $child) { echo $child; }
		echo "\n";

		static::$c->lock('one');
		static::$c->lock('two');
		static::$c->lock('seven');

		foreach (static::$c as $child) { echo $child; }
		echo "\n";

		static::$c->lock('six');

		foreach (static::$c as $child) { echo $child; }
		echo "\n";

		static::$c->but('nine'); // Locker reset.

		foreach (static::$c as $child) { echo $child; }
		echo "\n";

		static::$c->unlock('nine');

		foreach (static::$c as $child) { echo $child; }
		echo "\n";

		static::$c->lock('two');
		static::$c->lock('four');
		static::$c->lock('six');
		static::$c->lock('eight');

		foreach (static::$c as $child) { echo $child; }
		echo "\n";

		echo static::$c->but() . "\n"; // Locker reset.

		static::$c->lock('two');
		static::$c->lock('six');
		static::$c->lock('eight');

		static::$c->unlock('six');

		foreach (static::$c as $child) { echo $child; }
		echo "\n";

		echo static::$c->but('three') . "\n"; // Locker reset.

		static::$c->lock('two');
		static::$c->lock('six');
		static::$c->lock('eight');

		foreach (static::$c as $child) { echo $child; }
		echo "\n";
	}

	public function testLockUnlockButOnlyForeach ()
	{
		$this->expectOutputString(
			  '<one>1</one><two>2</two><three>3</three><four>4</four><five>5</five><six>6</six><seven>7</seven><eight>8</eight><nine>9</nine>' . "\n"
			. '<three>3</three><four>4</four><five>5</five><six>6</six><eight>8</eight><nine>9</nine>' . "\n"
			. '<three>3</three><four>4</four><five>5</five><eight>8</eight><nine>9</nine>' . "\n"
			. '<one>1</one><two>2</two><three>3</three><four>4</four><five>5</five><six>6</six><seven>7</seven><eight>8</eight>' . "\n"
			. '<one>1</one><two>2</two><three>3</three><four>4</four><five>5</five><six>6</six><seven>7</seven><eight>8</eight><nine>9</nine>' . "\n"
			. '<one>1</one><three>3</three><five>5</five><seven>7</seven><nine>9</nine>' . "\n"
			. '<one>1</one><two>2</two><three>3</three><four>4</four><five>5</five><six>6</six><seven>7</seven><eight>8</eight><nine>9</nine>' . "\n"
			. '<one>1</one><three>3</three><four>4</four><five>5</five><six>6</six><seven>7</seven><nine>9</nine>' . "\n"
			. '<one>1</one><two>2</two><four>4</four><five>5</five><six>6</six><seven>7</seven><eight>8</eight><nine>9</nine>' . "\n"
			. '<one>1</one><four>4</four><five>5</five><seven>7</seven><nine>9</nine>' . "\n"
		);

		foreach (static::$c as $child) { echo $child; }
		echo "\n";

		static::$c->lock('one');
		static::$c->lock('two');
		static::$c->lock('seven');

		foreach (static::$c as $child) { echo $child; }
		echo "\n";

		static::$c->lock('six');

		foreach (static::$c as $child) { echo $child; }
		echo "\n";

		static::$c->but('nine'); // Locker reset.

		foreach (static::$c as $child) { echo $child; }
		echo "\n";

		static::$c->unlock('nine');

		foreach (static::$c as $child) { echo $child; }
		echo "\n";

		static::$c->lock('two');
		static::$c->lock('four');
		static::$c->lock('six');
		static::$c->lock('eight');

		foreach (static::$c as $child) { echo $child; }
		echo "\n";

		foreach (static::$c->but() as $child) { echo $child; } // Locker reset.
		echo "\n";

		static::$c->lock('two');
		static::$c->lock('six');
		static::$c->lock('eight');

		static::$c->unlock('six');

		foreach (static::$c as $child) { echo $child; }
		echo "\n";

		foreach (static::$c->but('three') as $child) { echo $child; } // Locker reset.
		echo "\n";

		static::$c->lock('two');
		static::$c->lock('six');
		static::$c->lock('eight');

		foreach (static::$c as $child) { echo $child; }
		echo "\n";
	}
}
