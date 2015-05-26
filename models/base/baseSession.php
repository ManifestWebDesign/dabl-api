<?php
/**
 *		Created by Dan Blaisdell's DABL
 *		Do not alter base files, as they will be overwritten.
 *		To alter the objects, alter the extended classes in
 *		the 'models' folder.
 *
 */
abstract class baseSession extends ApplicationModel {

	const USER_ID = 'session.userId';
	const AUTH_TOKEN = 'session.authToken';
	const PHP_SESSION_TOKEN = 'session.phpSessionToken';
	const CREATED = 'session.created';
	const UPDATED = 'session.updated';

	/**
	 * Name of the table
	 * @var string
	 */
	protected static $_tableName = 'session';

	/**
	 * Cache of objects retrieved from the database
	 * @var Session[]
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
		'userId',
	);

	/**
	 * string name of the primary key column
	 * @var string
	 */
	protected static $_primaryKey = 'userId';

	/**
	 * true if primary key is an auto-increment column
	 * @var bool
	 */
	protected static $_isAutoIncrement = false;

	/**
	 * array of all fully-qualified(table.column) columns
	 * @var string[]
	 */
	protected static $_columns = array(
		Session::USER_ID,
		Session::AUTH_TOKEN,
		Session::PHP_SESSION_TOKEN,
		Session::CREATED,
		Session::UPDATED,
	);

	/**
	 * array of all column names
	 * @var string[]
	 */
	protected static $_columnNames = array(
		'userId',
		'authToken',
		'phpSessionToken',
		'created',
		'updated',
	);

	/**
	 * array of all column types
	 * @var string[]
	 */
	protected static $_columnTypes = array(
		'userId' => Model::COLUMN_TYPE_INTEGER,
		'authToken' => Model::COLUMN_TYPE_VARCHAR,
		'phpSessionToken' => Model::COLUMN_TYPE_VARCHAR,
		'created' => Model::COLUMN_TYPE_TIMESTAMP,
		'updated' => Model::COLUMN_TYPE_TIMESTAMP,
	);

	/**
	 * `userId` INTEGER NOT NULL DEFAULT ''
	 * @var int
	 */
	protected $userId;

	/**
	 * `authToken` VARCHAR NOT NULL
	 * @var string
	 */
	protected $authToken;

	/**
	 * `phpSessionToken` VARCHAR NOT NULL
	 * @var string
	 */
	protected $phpSessionToken;

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
	 * Gets the value of the userId field
	 */
	function getUserId() {
		return $this->userId;
	}

	/**
	 * Sets the value of the userId field
	 * @return Session
	 */
	function setUserId($value) {
		return $this->setColumnValue('userId', $value, Model::COLUMN_TYPE_INTEGER);
	}

	/**
	 * Gets the value of the authToken field
	 */
	function getAuthToken() {
		return $this->authToken;
	}

	/**
	 * Sets the value of the authToken field
	 * @return Session
	 */
	function setAuthToken($value) {
		return $this->setColumnValue('authToken', $value, Model::COLUMN_TYPE_VARCHAR);
	}

	/**
	 * Gets the value of the phpSessionToken field
	 */
	function getPhpSessionToken() {
		return $this->phpSessionToken;
	}

	/**
	 * Sets the value of the phpSessionToken field
	 * @return Session
	 */
	function setPhpSessionToken($value) {
		return $this->setColumnValue('phpSessionToken', $value, Model::COLUMN_TYPE_VARCHAR);
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
	 * @return Session
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
	 * @return Session
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
	 * @return Session
	 */
	 static function retrieveByPK($user_id) {
		return static::retrieveByPKs($user_id);
	}

	/**
	 * Searches the database for a row with the primary keys that match
	 * the ones input.
	 * @return Session
	 */
	static function retrieveByPKs($user_id) {
		if (null === $user_id) {
			return null;
		}
		if (static::$_poolEnabled) {
			$pool_instance = static::retrieveFromPool($user_id);
			if (null !== $pool_instance) {
				return $pool_instance;
			}
		}
		$q = new Query;
		$q->add('userId', $user_id);
		return static::doSelectOne($q);
	}

	/**
	 * Searches the database for a row with a userId
	 * value that matches the one provided
	 * @return Session
	 */
	static function retrieveByUserId($value) {
		return Session::retrieveByPK($value);
	}

	/**
	 * Searches the database for a row with a authToken
	 * value that matches the one provided
	 * @return Session
	 */
	static function retrieveByAuthToken($value) {
		return static::retrieveByColumn('authToken', $value);
	}

	/**
	 * Searches the database for a row with a phpSessionToken
	 * value that matches the one provided
	 * @return Session
	 */
	static function retrieveByPhpSessionToken($value) {
		return static::retrieveByColumn('phpSessionToken', $value);
	}

	/**
	 * Searches the database for a row with a created
	 * value that matches the one provided
	 * @return Session
	 */
	static function retrieveByCreated($value) {
		return static::retrieveByColumn('created', $value);
	}

	/**
	 * Searches the database for a row with a updated
	 * value that matches the one provided
	 * @return Session
	 */
	static function retrieveByUpdated($value) {
		return static::retrieveByColumn('updated', $value);
	}


	/**
	 * Casts values of int fields to (int)
	 * @return Session
	 */
	function castInts() {
		$this->userId = (null === $this->userId) ? null : (int) $this->userId;
		return $this;
	}

	/**
	 * @return Session
	 */
	function setUser(User $user = null) {
		return $this->setUserRelatedByUserId($user);
	}

	/**
	 * @return Session
	 */
	function setUserRelatedByUserId(User $user = null) {
		if (null === $user) {
			$this->setuserId(null);
		} else {
			if (!$user->getid()) {
				throw new Exception('Cannot connect a User without a id');
			}
			$this->setuserId($user->getid());
		}
		return $this;
	}

	/**
	 * Returns a user object with a id
	 * that matches $this->userId.
	 * @return User
	 */
	function getUser() {
		return $this->getUserRelatedByUserId();
	}

	/**
	 * Returns a user object with a id
	 * that matches $this->userId.
	 * @return User
	 */
	function getUserRelatedByUserId() {
		$fk_value = $this->getuserId();
		if (null === $fk_value) {
			return null;
		}
		return User::retrieveByPK($fk_value);
	}

	static function doSelectJoinUser(Query $q = null, $join_type = Query::LEFT_JOIN) {
		return static::doSelectJoinUserRelatedByUserId($q, $join_type);
	}

	/**
	 * @return Session[]
	 */
	static function doSelectJoinUserRelatedByUserId(Query $q = null, $join_type = Query::LEFT_JOIN) {
		$q = $q ? clone $q : new Query;
		$columns = $q->getColumns();
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

		$to_table = User::getTableName();
		$q->join($to_table, $this_table . '.userId = ' . $to_table . '.id', $join_type);
		foreach (User::getColumns() as $column) {
			$columns[] = $column;
		}
		$q->setColumns($columns);

		return static::doSelect($q, array('User'));
	}

	/**
	 * @return Session[]
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

		$to_table = User::getTableName();
		$q->join($to_table, $this_table . '.userId = ' . $to_table . '.id', $join_type);
		foreach (User::getColumns() as $column) {
			$columns[] = $column;
		}
		$classes[] = 'User';
	
		$q->setColumns($columns);
		return static::doSelect($q, $classes);
	}

	/**
	 * Returns true if the column values validate.
	 * @return bool
	 */
	function validate() {
		$this->_validationErrors = array();
		if (null === $this->getauthToken()) {
			$this->_validationErrors[] = 'authToken must not be null';
		}
		if (null === $this->getphpSessionToken()) {
			$this->_validationErrors[] = 'phpSessionToken must not be null';
		}
		return 0 === count($this->_validationErrors);
	}

}