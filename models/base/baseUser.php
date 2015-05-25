<?php
/**
 *		Created by Dan Blaisdell's DABL
 *		Do not alter base files, as they will be overwritten.
 *		To alter the objects, alter the extended classes in
 *		the 'models' folder.
 *
 */
abstract class baseUser extends ApplicationModel {

	const ID = 'user.id';
	const USERNAME = 'user.username';
	const PASSWORD_HASH = 'user.passwordHash';
	const CREATED = 'user.created';
	const UPDATED = 'user.updated';

	/**
	 * Name of the table
	 * @var string
	 */
	protected static $_tableName = 'user';

	/**
	 * Cache of objects retrieved from the database
	 * @var User[]
	 */
	protected static $_instancePool = array();

	protected static $_instancePoolCount = 0;

	protected static $_poolEnabled = true;

	/**
	 * Array of objects to batch insert
	 */
	protected static $_insertBatch = array();

	/**
	 * Maximum size of the insert batch
	 */
	protected static $_insertBatchSize = 500;

	/**
	 * Array of all primary keys
	 * @var string[]
	 */
	protected static $_primaryKeys = array(
		'id',
	);

	/**
	 * string name of the primary key column
	 * @var string
	 */
	protected static $_primaryKey = 'id';

	/**
	 * true if primary key is an auto-increment column
	 * @var bool
	 */
	protected static $_isAutoIncrement = true;

	/**
	 * array of all fully-qualified(table.column) columns
	 * @var string[]
	 */
	protected static $_columns = array(
		User::ID,
		User::USERNAME,
		User::PASSWORD_HASH,
		User::CREATED,
		User::UPDATED,
	);

	/**
	 * array of all column names
	 * @var string[]
	 */
	protected static $_columnNames = array(
		'id',
		'username',
		'passwordHash',
		'created',
		'updated',
	);

	/**
	 * array of all column types
	 * @var string[]
	 */
	protected static $_columnTypes = array(
		'id' => Model::COLUMN_TYPE_INTEGER,
		'username' => Model::COLUMN_TYPE_VARCHAR,
		'passwordHash' => Model::COLUMN_TYPE_VARCHAR,
		'created' => Model::COLUMN_TYPE_TIMESTAMP,
		'updated' => Model::COLUMN_TYPE_TIMESTAMP,
	);

	/**
	 * `id` INTEGER NOT NULL DEFAULT ''
	 * @var int
	 */
	protected $id;

	/**
	 * `username` VARCHAR NOT NULL
	 * @var string
	 */
	protected $username;

	/**
	 * `passwordHash` VARCHAR NOT NULL
	 * @var string
	 */
	protected $passwordHash;

	/**
	 * `created` TIMESTAMP NOT NULL
	 * @var string
	 */
	protected $created;

	/**
	 * `updated` TIMESTAMP NOT NULL
	 * @var string
	 */
	protected $updated;

	/**
	 * Gets the value of the id field
	 */
	function getId() {
		return $this->id;
	}

	/**
	 * Sets the value of the id field
	 * @return User
	 */
	function setId($value) {
		return $this->setColumnValue('id', $value, Model::COLUMN_TYPE_INTEGER);
	}

	/**
	 * Gets the value of the username field
	 */
	function getUsername() {
		return $this->username;
	}

	/**
	 * Sets the value of the username field
	 * @return User
	 */
	function setUsername($value) {
		return $this->setColumnValue('username', $value, Model::COLUMN_TYPE_VARCHAR);
	}

	/**
	 * Gets the value of the passwordHash field
	 */
	function getPasswordHash() {
		return $this->passwordHash;
	}

	/**
	 * Sets the value of the passwordHash field
	 * @return User
	 */
	function setPasswordHash($value) {
		return $this->setColumnValue('passwordHash', $value, Model::COLUMN_TYPE_VARCHAR);
	}

	/**
	 * Gets the value of the created field
	 */
	function getCreated($format = null) {
		if (null === $this->created || null === $format) {
			return $this->created;
		}
		if (0 === strpos($this->created, '0000-00-00')) {
			return null;
		}
		return date($format, strtotime($this->created));
	}

	/**
	 * Sets the value of the created field
	 * @return User
	 */
	function setCreated($value) {
		return $this->setColumnValue('created', $value, Model::COLUMN_TYPE_TIMESTAMP);
	}

	/**
	 * Gets the value of the updated field
	 */
	function getUpdated($format = null) {
		if (null === $this->updated || null === $format) {
			return $this->updated;
		}
		if (0 === strpos($this->updated, '0000-00-00')) {
			return null;
		}
		return date($format, strtotime($this->updated));
	}

	/**
	 * Sets the value of the updated field
	 * @return User
	 */
	function setUpdated($value) {
		return $this->setColumnValue('updated', $value, Model::COLUMN_TYPE_TIMESTAMP);
	}

	/**
	 * @return DABLPDO
	 */
	static function getConnection() {
		return DBManager::getConnection('default_connection');
	}

	/**
	 * Searches the database for a row with the ID(primary key) that matches
	 * the one input.
	 * @return User
	 */
	 static function retrieveByPK($id) {
		return static::retrieveByPKs($id);
	}

	/**
	 * Searches the database for a row with the primary keys that match
	 * the ones input.
	 * @return User
	 */
	static function retrieveByPKs($id) {
		if (null === $id) {
			return null;
		}
		if (static::$_poolEnabled) {
			$pool_instance = static::retrieveFromPool($id);
			if (null !== $pool_instance) {
				return $pool_instance;
			}
		}
		$q = new Query;
		$q->add('id', $id);
		return static::doSelectOne($q);
	}

	/**
	 * Searches the database for a row with a id
	 * value that matches the one provided
	 * @return User
	 */
	static function retrieveById($value) {
		return User::retrieveByPK($value);
	}

	/**
	 * Searches the database for a row with a username
	 * value that matches the one provided
	 * @return User
	 */
	static function retrieveByUsername($value) {
		return static::retrieveByColumn('username', $value);
	}

	/**
	 * Searches the database for a row with a passwordHash
	 * value that matches the one provided
	 * @return User
	 */
	static function retrieveByPasswordHash($value) {
		return static::retrieveByColumn('passwordHash', $value);
	}

	/**
	 * Searches the database for a row with a created
	 * value that matches the one provided
	 * @return User
	 */
	static function retrieveByCreated($value) {
		return static::retrieveByColumn('created', $value);
	}

	/**
	 * Searches the database for a row with a updated
	 * value that matches the one provided
	 * @return User
	 */
	static function retrieveByUpdated($value) {
		return static::retrieveByColumn('updated', $value);
	}


	/**
	 * Casts values of int fields to (int)
	 * @return User
	 */
	function castInts() {
		$this->id = (null === $this->id) ? null : (int) $this->id;
		return $this;
	}

	/**
	 * @return User[]
	 */
	static function doSelectJoinAll(Query $q = null, $join_type = Query::LEFT_JOIN) {
		$q = $q ? clone $q : new Query;
		$columns = $q->getColumns();
		$classes = array();
		$alias = $q->getAlias();
		$this_table = $alias ? $alias : static::getTableName();
		if (!$columns) {
			if ($alias) {
				foreach (static::getColumns() as $column_name) {
					$columns[] = $alias . '.' . $column_name;
				}
			} else {
				$columns = static::getColumns();
			}
		}

		$q->setColumns($columns);
		return static::doSelect($q, $classes);
	}

	/**
	 * Returns true if the column values validate.
	 * @return bool
	 */
	function validate() {
		$this->_validationErrors = array();
		if (null === $this->getusername()) {
			$this->_validationErrors[] = 'username must not be null';
		}
		if (null === $this->getpasswordHash()) {
			$this->_validationErrors[] = 'passwordHash must not be null';
		}
		return 0 === count($this->_validationErrors);
	}

}