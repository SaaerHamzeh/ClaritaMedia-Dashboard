<?php
class DBObject
{
	public $id;
	public $tableName;
	public $idColumnName;
	protected $columns = array();
	protected $extra = array();
	protected $className;

	// Constructor used to instantiate records objects
	public function __construct($table_name,$id_column_name, $columns, $id = null)
	{
		$this->className    = get_class($this);
		$this->tableName    = $table_name;
		$this->idColumnName = $id_column_name;
		foreach($columns as $col)
			$this->columns[$col] = null;
		if(!is_null($id))
			$this->select($id);
	}

	// Magical get method: allow dynamic property call
	public function __get($key)
	{
		if(array_key_exists($key, $this->columns))
			return $this->columns[$key];
		if((substr($key, 0, 2) == '__') && array_key_exists(substr($key, 2), $this->columns))
			return htmlspecialchars($this->columns[substr($key, 2)]);
		$trace = debug_backtrace();
		echo "key is $key<br/>";
		trigger_error("Undefined property DBObject::__get(): $key in {$trace[0]['file']} on line {$trace[0]['line']}", E_USER_NOTICE);
		return null;
	}

	// Magical set method: allow dynamic property set
	public function __set($key, $value)
	{
		if(array_key_exists($key, $this->columns))
			$this->columns[$key] = $value;
		else
			$this->extra[$key] = $value;
		return $value; // Seriously.
	}

	// Select one row from database table based on $column=$id to load it
	public function select($id, $column = null)
	{
		$db = Database::getDatabase();
		if(is_null($column)) $column = $this->idColumnName;
		$column = $db->escape($column);
		$db->query("SELECT * FROM `{$this->tableName}` WHERE `$column` = :id LIMIT 1", array('id' => $id));
		if($db->hasRows())
		{
			$row = $db->getRow();
			$this->load($row);
			return true;
		}
		return false;
	}

	// Load the selected row into columns array
	public function load($row)
	{
		foreach($row as $k => $v)
		{
			if($k == $this->idColumnName)
				$this->id = $v;
			elseif(array_key_exists($k, $this->columns))
				$this->columns[$k] = $v;
		}
	}

	// Save the object into DB (insert or update)
	public function save()
	{
		if(is_null($this->id))
		{
			$this->insert();
			$this->after_insert($this->id);
		}
		else
		{
			$this->update();
			$this->after_update($this->id);
		}
		return $this->id;
	}

	// Before insert the object into DB
	public function before_insert()
	{
	}

	// After insert the object into DB
	public function after_insert($id)
	{
	}

	// After update the object in DB
	public function after_update($id)
	{
	}

	// Before update the object in DB
	public function before_update($id)
	{
	}

	// Insert the object as a new record
	public function insert()
	{
		$db = Database::getDatabase();
		if(count($this->columns) == 0) return false;
		$data = array();
		foreach($this->columns as $k => $v)
			if(!is_null($v))
				$data[$k] = $db->quote($v);
		$columns = '`' . implode('`, `', array_keys($data)) . '`';
		$values = implode(',', $data);
		$db->query("INSERT INTO `{$this->tableName}` ($columns) VALUES ($values)");
		$this->id = $db->insertId();
		return $this->id;
	}

	// Replace the object in DB
	public function replace()
	{
		return $this->delete() && $this->insert();
	}

	// Update the object record in DB
	public function update()
	{
		if(is_null($this->id)) return false;
		$db = Database::getDatabase();
		if(count($this->columns) == 0) return;
		$sql = "UPDATE {$this->tableName} SET ";
		foreach($this->columns as $k => $v)
			$sql .= "`$k`=" . $db->quote($v) . ',';
		$sql[strlen($sql) - 1] = ' ';
		$sql .= "WHERE `{$this->idColumnName}` = " . $db->quote($this->id);
		$db->query($sql);
		return $db->affectedRows();
	}

	// Delete the object record from DB
	public function delete()
	{
		if(is_null($this->id)) return false;
		$this->before_delete($this->id);
		$db = Database::getDatabase();
		$db->query("DELETE FROM `{$this->tableName}` WHERE `{$this->idColumnName}` = :id LIMIT 1", array('id' => $this->id));
		$this->after_delete($this->id);
		return $db->affectedRows();
	}

	// Before delete the object from DB
	public function before_delete($id)
	{
	}

	// After insert the object into DB
	public function after_delete($id)
	{
	}

	// Calculate the total number of $class_name objects in the database.
	public static function count($class_name, $sql = null)
	{
		$db = Database::getDatabase();
		// Make sure the class exists before we instantiate it...
		if(!class_exists($class_name))
			return false;
		$tmp_obj = new $class_name;
		// Also, it needs to be a subclass of DBObject...
		if(!is_subclass_of($tmp_obj, 'DBObject'))
			return false;
		if(is_null($sql))
			$sql = "SELECT COUNT(*) FROM `{$tmp_obj->tableName}`";
		return $db->getValue($sql);
	}

	// Grabs a large block of instantiated $class_name objects from the database using only one query.
	public static function glob($class_name, $sql = null, $extra_columns = array())
	{
		$db = Database::getDatabase();
		// Make sure the class exists before we instantiate it...
		if(!class_exists($class_name))
			return false;
		$tmp_obj = new $class_name;
		// Also, it needs to be a subclass of DBObject...
		if(!is_subclass_of($tmp_obj, 'DBObject'))
			return false;
		if(is_null($sql))
			$sql = "SELECT * FROM `{$tmp_obj->tableName}`";
		$objs = array();
		$rows = $db->getRows($sql);
		foreach($rows as $row)
		{
			$o = new $class_name;
			$o->load($row);
			$objs[$o->id] = $o;
			foreach($extra_columns as $c)
			{
				$o->addColumn($c,$row[$c]);
				$o->$c = isset($row[$c]) ? $row[$c] : null;
			}
		}
		return $objs;
	}

	// Add a column to the objects
	public function addColumn($key, $val = null)
	{
		if(!in_array($key, array_keys($this->columns)))
			$this->columns[$key] = $val;
	}

	// Return true if id is not null
	public function ok()
	{
		return !is_null($this->id);
	}
}

    class TaggableDBObject extends DBObject
    {
        protected $tagColumnName;

        public function __construct($table_name, $columns, $id = null)
        {
            parent::__construct($table_name, $columns, $id);
            $this->tagColumnName = strtolower($this->className . '_id');
        }

        public function addTag($name)
        {
            $db = Database::getDatabase();

            if(is_null($this->id)) return false;

            $name = trim($name);
            if($name == '') return false;

            $t = new Tag($name);
            $db->query("INSERT IGNORE {$this->tableName}2tags ({$this->tagColumnName}, tag_id) VALUES (:obj_id, :tag_id)", array('obj_id' => $this->id, 'tag_id' => $t->id));
            return true;
        }

        public function removeTag($name)
        {
            $db = Database::getDatabase();

            if(is_null($this->id)) return false;

            $name = trim($name);
            if($name == '') return false;

            $t = new Tag($name);
            $db->query("DELETE FROM {$this->tableName}2tags WHERE {$this->tagColumnName} = :obj_id AND tag_id = :tag_id", array('obj_id' => $this->id, 'tag_id' => $t->id));
            return true;
        }

        public function clearTags()
        {
            $db = Database::getDatabase();
            if(is_null($this->id)) return false;
            $db->query("DELETE FROM {$this->tableName}2tags WHERE {$this->tagColumnName} = :obj_id", array('obj_id' => $this->id));
            return true;
        }

        public function tags()
        {
            $db = Database::getDatabase();
            if(is_null($this->id)) return false;
            $result = $db->query("SELECT t.id, t.name FROM {$this->tableName}2tags a LEFT JOIN tags t ON a.tag_id = t.id WHERE a.{$this->tagColumnName} = '{$this->id}'");
            $tags = array();
            $rows = $db->getRows($result);
            foreach($rows as $row)
                $tags[$row['name']] = $row['id'];
            return $tags;
        }

        // Return all objects tagged $tag_name
        public function tagged($tag_name, $sql = '')
        {
            $db = Database::getDatabase();

            $tag = new Tag($tag_name);
            if(is_null($tag->id)) return array();

            return DBObject::glob(get_class($this), "SELECT b.* FROM {$this->tableName}2tags a LEFT JOIN {$this->tableName} b ON a.{$this->tagColumnName} = b.{$this->idColumnName} WHERE a.tag_id = {$tag->id} $sql");
        }
    }
?>
