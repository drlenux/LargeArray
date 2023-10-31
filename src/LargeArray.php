<?php

namespace Drlenux\LargeArray;

use SQLite3;

class LargeArray implements \ArrayAccess, \Countable, \Iterator
{
	/** @var SQLite3  */
	private $db;

	/** @var string  */
	private $tableName;

	/** @var int  */
	private $position;

	public function __construct($salt = '')
	{
		$this->db = new SQLite3(':memory:');
		$this->tableName = 'mem_' . uniqid(rand()) . $salt;
		$this->initializeTable();
		$this->position = 0;
	}

	private function initializeTable()
	{
		$query = "CREATE TABLE IF NOT EXISTS {$this->tableName} (id INTEGER PRIMARY KEY, keys VARCHAR(255), value TEXT)";
		$this->db->exec($query);
	}

	public function current()
	{
		return $this->offsetGet($this->position, true);
	}

	public function next()
	{
		$this->position++;
	}

	public function key()
	{
		$query = "SELECT keys FROM {$this->tableName} WHERE id = {$this->position}";
		$result = $this->db->querySingle($query);
		return $result !== false ? unserialize($result) : null;
	}

	public function valid()
	{
		return $this->offsetGet($this->position) !== null;
	}

	public function rewind()
	{
		$this->position = 0;
	}

	public function offsetExists($offset)
	{
		return $this->offsetGet($offset) !== null;
	}

	public function offsetGet($offset, $isId = false)
	{
		if ($isId && is_int($offset)) {
			$query = "SELECT value FROM {$this->tableName} WHERE id = {$offset}";
		} else {
			$query = "SELECT value FROM {$this->tableName} WHERE keys = '{$offset}'";
		}
		$result = $this->db->querySingle($query);
		return $result !== false ? unserialize($result) : null;
	}

	public function offsetSet($offset, $value)
	{
		if ($offset === null) $offset = $this->count();
		$value = serialize($value);
		$value = SQLite3::escapeString($value);
		$query = "INSERT INTO {$this->tableName} (keys, value) VALUES ('{$offset}', '{$value}')";
		$this->db->exec($query);
	}

	public function offsetUnset($offset)
	{
		$query = "DELETE FROM {$this->tableName} WHERE keys = '{$offset}'";
		$this->db->exec($query);
	}

	public function count()
	{
		$query = "SELECT COUNT(*) FROM {$this->tableName}";
		return $this->db->querySingle($query);
	}
}