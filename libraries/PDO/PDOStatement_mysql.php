<?php
/** File PDOStatement_mysql.class.php	*
 *(C) Andrea Giammarchi [2005/10/13]	*/

/**
 * Class PDOStatement_mysql
 * 	This class is used from class PDO_mysql to manage a MySQL database.
 *      Look at PDO.clas.php file comments to know more about MySQL connection.
 * ---------------------------------------------
 * @Compatibility	>= PHP 4
 * @Dependencies	PDO.class.php
 * 			PDO_mysql.class.php
 * @Author		Andrea Giammarchi
 * @Site		http://www.devpro.it/
 * @Mail		andrea [ at ] 3site [ dot ] it
 * @Date		2005/10/13
 * @LastModified	2006/01/29 09:30 [fixed execute bug]
 * @Version		0.1b - tested
 */

class PDOStatement_mysql extends PDOStatement{
	
	/**
	 * 'protected' variables:
	 *      __persistent:Boolean		Connection mode, is true on persistent, false on normal (deafult) connection
	 *      __fetchmode:Integer		constant PDO_FETCH_* result mode
	 *      __errorCode:String		Last error string code
	 *      __errorInfo:Array		Last error informations, code, number, details
	 *      __boundParams:Array		Stored bindParam
	 */	
	protected $__persistent = false;
	protected $__fetchmode = PDO::FETCH_BOTH;
	protected $__errorCode = '';
	protected $__errorInfo = Array('');

	/**
	 * Public method:
	 * Checks if query was valid and returns how may fields returns
	 * this->columnCount( void ):Void
	 */
	function columnCount() {
		$result = 0;
		if(!is_null($this->__result))
			$result = mysql_num_fields($this->__result);
		return $result; 
	}
	
	/**
	 * Public method:
	 * Returns a code rappresentation of an error
	 * this->errorCode( void ):String
	 * @Return	String		String rappresentation of the error
	 */
	function errorCode() {
		return $this->__errorCode;
	}
	
	/**
	 * Public method:
	 * Returns an array with error informations
	 * this->errorInfo( void ):Array
	 * @Return	Array		Array with 3 keys:
	 * 				0 => error code
	 *                              1 => error number
	 *                              2 => error string
	 */
	function errorInfo() {
		return $this->__errorInfo;
	}
	
	function query(){
		if(is_null($this->__result = &$this->__uquery($this->__query)))
			return false;
		else
			return true;
	}
	
	/**
	 * Public method:
	 * Excecutes a query and returns true on success or false.
	 * this->exec( $array:Array ):Boolean
	 * @Param	Array		If present, it should contain all replacements for prepared query
	 * @Return	Boolean		true if query has been done without errors, false otherwise
	 */
	function execute($array = Array()) {
		if(count($this->__boundParams) > 0)
			$array = &$this->__boundParams;
		$__query = $this->__query;
		if(count($array) > 0) {
			foreach($array as $k => $v) {
				if(!is_int($k) || substr($k, 0, 1) === ':') {
					if(!isset($tempf))
						$tempf = $tempr = array();
					array_push($tempf, $k);
					array_push($tempr, '"'.mysql_escape_string($v).'"');
				}
				else {
					$parse = create_function('$v', 'return \'"\'.mysql_escape_string($v).\'"\';');
					$__query = preg_replace("/(\?)/e", '$parse($array[$k++]);', $__query);
					break;
				}
			}
			if(isset($tempf))
				$__query = str_replace($tempf, $tempr, $__query);
		}
		if(is_null($this->__result = &$this->__uquery($__query)))
			$keyvars = false;
		else
			$keyvars = true;
		$this->__boundParams = array();
		return $keyvars;
	}
	
	/**
	 * Public method:
	 * Returns, if present, next row of executed query or false.
	 * this->fetch( $mode:Integer, $cursor:Integer, $offset:Integer ):Mixed
	 * @Param	Integer		PDO_FETCH_* constant to know how to read next row, default PDO_FETCH_BOTH
	 * 				NOTE: if $mode is omitted is used default setted mode, PDO_FETCH_BOTH
	 * @Param	Integer		this variable is not used but respects PDO original accepted parameters
	 * @Param	Integer		this variable is not used but respects PDO original accepted parameters
	 * @Return	Mixed		Next row of executed query or false if there is nomore.
	 */
	function fetch($mode = PDO::FETCH_BOTH, $cursor = null, $offset = null) {
		if(func_num_args() == 0)
			$mode = &$this->__fetchmode;
		$result = false;
		if(!is_null($this->__result)) {
			switch($mode) {
				case PDO::FETCH_NUM:
					$result = mysql_fetch_row($this->__result);
					break;
				case PDO::FETCH_ASSOC:
					$result = mysql_fetch_assoc($this->__result);
					break;
				case PDO::FETCH_OBJ:
					$result = mysql_fetch_object($this->__result);	
					break;
				case PDO::FETCH_BOTH:
				default:
					$result = mysql_fetch_array($this->__result);
					break;
			}
		}
		if(!$result)
			$this->__result = null;
		return $result;
	}
	
	/**
	 * Public method:
	 *	Returns an array with all rows of executed query.
	 * this->fetchAll( $mode:Integer ):Array
	 * @Param	Integer		PDO_FETCH_* constant to know how to read all rows, default PDO_FETCH_BOTH
	 * 				NOTE: this doesn't work as fetch method, then it will use always PDO_FETCH_BOTH
	 *                                    if this param is omitted
	 * @Return	Array		An array with all fetched rows
	 */
	function fetchAll($mode = PDO::FETCH_BOTH, $column_index = 0) {
		$result = array();
		if(!is_null($this->__result)) {
			switch($mode) {
				case PDO::FETCH_NUM:
					while($r = mysql_fetch_row($this->__result))
						array_push($result, $r);
					break;
				case PDO::FETCH_ASSOC:
					while($r = mysql_fetch_assoc($this->__result))
						array_push($result, $r);
					break;
				case PDO::FETCH_COLUMN:
					for($x = 0; $x < mysql_num_rows($this->__result); $x++)
						array_push($result, mysql_result($this->__result, $x, $column_index));
					break;
				case PDO::FETCH_OBJ:
					while($r = mysql_fetch_object($this->__result))
						array_push($result, $r);
					break;
				case PDO::FETCH_BOTH:
				default:
					while($r = mysql_fetch_array($this->__result))
						array_push($result, $r);
					break;
			}
		}
		$this->__result = null;
		return $result;
	}

	/**
	 * @Return	Mixed		Null or next row's first column
	 */
	function fetchColumn($column_number = 0) {
		$result = null;
		if(!is_null($this->__result)) {
			$result = @mysql_fetch_row($this->__result);
			if($result)
				$result = $result[$column_number];
			else
				$this->__result = null;
		}
		return $result;
	}

	/**
	 * Public method:
	 * Returns number of last affected database rows
	 * this->rowCount( void ):Integer
	 * number of last affected rows
	 * NOTE: works with INSERT, UPDATE and DELETE query type
	 */
	function rowCount() {
		return mysql_affected_rows($this->__connection);
	}
	
	
	// NOT TOTALLY SUPPORTED PUBLIC METHODS
	/**
	 * Public method:
	 * Quotes correctly a string for this database
	 * this->getAttribute( $attribute:Integer ):Mixed
	 * @Param	Integer		a constant [	PDO_ATTR_SERVER_INFO,
	 * 										PDO_ATTR_SERVER_VERSION,
	 * 										PDO_ATTR_CLIENT_VERSION,
	 * 										PDO_ATTR_PERSISTENT	]
	 * @Return	Mixed		correct information or false
	 */
	function getAttribute($attribute) {
		$result = false;
		switch($attribute) {
			case PDO::ATTR_SERVER_INFO:
				$result = mysql_get_host_info($this->__connection);
				break;
			case PDO::ATTR_SERVER_VERSION:
				$result = mysql_get_server_info($this->__connection);
				break;
			case PDO::ATTR_CLIENT_VERSION:
				$result = mysql_get_client_info();
				break;
			case PDO::ATTR_PERSISTENT:
				$result = $this->__persistent;
				break;
		}
		return $result;
	}
	
	/**
	 * Public method:
	 * Sets database attributes, in this version only connection mode.
	 * this->setAttribute( $attribute:Integer, $mixed:Mixed ):Boolean
	 * @Param	Integer		PDO_* constant, in this case only PDO_ATTR_PERSISTENT
	 * @Param	Mixed		value for PDO_* constant, in this case a Boolean value
	 * true for permanent connection, false for default not permament connection
	 * @Return	Boolean		true on change, false otherwise
	 */
	function setAttribute($attribute, $mixed) {
		$result = false;
		if($attribute === PDO::ATTR_PERSISTENT && $mixed != $this->__persistent) {
			$result = true;
			$this->__persistent = (boolean) $mixed;
			mysql_close($this->__connection);
			if($this->__persistent === true)
				$this->__connection = &mysql_pconnect($this->__dbinfo[0], $this->__dbinfo[1], $this->__dbinfo[2]);
			else
				$this->__connection = &mysql_connect($this->__dbinfo[0], $this->__dbinfo[1], $this->__dbinfo[2]);
			mysql_select_db($this->__dbinfo[3], $this->__connection);
		}
		return $result;
	}
	
	/**
	 * Public method:
	 * Sets default fetch mode to use with this->fetch() method.
	 * this->setFetchMode( $mode:Integer ):Boolean
	 * @Param	Integer		PDO_FETCH_* constant to use while reading an execute query with fetch() method.
	 * NOTE: PDO_FETCH_LAZY and PDO_FETCH_BOUND are not supported
	 * @Return	Boolean		true on change, false otherwise
	 */
	function setFetchMode($mode) {
		$result = false;
		switch($mode) {
			case PDO::FETCH_NUM:
			case PDO::FETCH_ASSOC:
			case PDO::FETCH_OBJ:
			case PDO::FETCH_BOTH:
				$result = true;
				$this->__fetchmode = &$mode;
				break;
		}
		return $result;
	}
	
	
	// UNSUPPORTED PUBLIC METHODS
	function bindColumn($mixewd, &$param, $type = null, $max_length = null, $driver_option = null) {
		return false;
	}
	
	function __setErrors($er) {
		if(!is_resource($this->__connection)) {
			$errno = mysql_errno();
			$errst = mysql_error();
		}
		else {
			$errno = mysql_errno($this->__connection);
			$errst = mysql_error($this->__connection);
		}
		throw new PDOException("Database error ($errno): $errst");
		$this->__errorCode = &$er;
		$this->__errorInfo = array($this->__errorCode, $errno, $errst);
		$this->__result = null;
	}
	
	function __uquery(&$query) {
		if(!$result = mysql_query($query, $this->__connection)) {
			$this->__setErrors('SQLER');
			$result = null;
		}
		$this->__position = 0;
		$this->__num_rows = mysql_num_rows($result);
		return $result;
	}
	
}