<?php

namespace Drlenux\test;

use Drlenux\LargeArray\LargeArray;
use PHPUnit\Framework\TestCase;

class LargeArrayTest extends TestCase
{
	public function testSetAndGetNamingArray()
	{
		$arr = new LargeArray();
		$max = 1000000;

		for ($i = 0; $i < $max; $i++) $arr['k_' . $i] = $i;
		$keys = [];
		for ($i = 0; $i < intval($max / 1000); $i++) $keys[] = $i;

		foreach ($keys as $key) {
			$this->assertEquals($key, $arr['k_' . $key]);
		}
	}

	public function testSetAndGet()
	{
		$arr = new LargeArray();
		$max = 1000000;

		for ($i = 0; $i < $max; $i++) $arr[] = $i;
		$keys = [];
		for ($i = 0; $i < intval($max / 1000); $i++) $keys[] = $i;

		foreach ($keys as $key) {
			$this->assertEquals($key, $arr[$key]);
		}
	}
}