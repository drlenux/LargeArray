<?php

namespace Drlenux\LargeArray;

use SQLite3;

class LargeArray implements \ArrayAccess, \Countable, \Iterator
{
	private $db;
	private $tableName;
	private $position = 0;
	private $count = 0;
	private $currentId = 0;

	public function __construct($salt = '')
	{
		$salt = str_replace(' ', '_', $salt);
		$salt = SQLite3::escapeString($salt);
		$this->db = new SQLite3(':memory:');
		$this->tableName = 'mem_' . uniqid(rand()) . $salt;
		$this->initializeTable();
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
			$offset = SQLite3::escapeString($offset);
			$query = "SELECT value FROM {$this->tableName} WHERE keys = '{$offset}'";
		}
		$result = $this->db->querySingle($query);
		return $result !== false ? unserialize($result) : null;
	}

	public function offsetSet($offset, $value)
	{
		if ($offset === null) $offset = $this->currentId++;
		$value = serialize($value);
		$value = SQLite3::escapeString($value);
		$query = "INSERT INTO {$this->tableName} (keys, value) VALUES ('{$offset}', '{$value}')";
		$this->db->exec($query);
		$this->count++;
	}

	public function offsetUnset($offset)
	{
		$query = "DELETE FROM {$this->tableName} WHERE keys = '{$offset}'";
		$this->db->exec($query);
		$this->count--;
	}

	public function count()
	{
		return $this->count;
	}

	public function in($offset)
	{
		$offset = serialize($offset);
		$offset = SQLite3::escapeString($offset);
		$query = "SELECT value FROM {$this->tableName} WHERE value = '{$offset}'";
		$result = $this->db->querySingle($query);
		return $result !== false;
	}
}