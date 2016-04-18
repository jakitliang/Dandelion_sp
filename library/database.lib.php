<?php
/**
 * DataBase
 *
 * Database request interface
 *
 * @package   Dandelion
 * @author    Fedic
 * @version   1.0.0
 * @copyright (2013) Dandelion Framework
 *****************************************
 */

class DataBaseLibrary
{
	private $db_name;
	private $db_user;
	private $db_pass;
	private $db_host;
	private $db_conn;
	
	public function __construct($name,$user,$pass,$host)
	{
		$this->db_name = $name;
		$this->db_user = $user;
		$this->db_pass = $pass;
		$this->db_host = $host;
	}

	/**
	 * @param (bool) $flag - true[link] or false[unlink]
	 * @return (bool) $db_conn | (bool) $db_close
	 */
	public function link($flag=true)
	{
		if ($flag) {
			if ($this->db_conn == NULL) {
				$this->db_conn = @mysql_connect($this->db_host,$this->db_user,$this->db_pass);
				mysql_select_db($this->db_name);
			}
			return $this->db_conn;
		} else {
			$db_close = mysql_close($this->db_conn);
			return $db_close;
		}
	}

	/**
	 * @param (string) $sql
	 * @return (bool) $result
	 */
	public function query($sql)
	{
		$result = mysql_query($sql,$this->db_conn);
		return $result;
	}

	/**
	 * @param (data) $query
	 * @return (array|bool) $result
	 */
	public function fetch_row($query)
	{
		$result = mysql_query($sql,$this->db_conn);
		$result = mysql_fetch_row($query);
		return $result;
	}

	/**
	 * @param (data) $query
	 * @return (object|bool) $result
	 */
	public function fetch_obj($query)
	{
		$result = mysql_query($sql,$this->db_conn);
		$result = mysql_fetch_object($query);
		return $result;
	}

	/**
	 * @param (string) $table
	 * @param (string) $query
	 * @param (string) $query
	 * @return (bool) $result
	 */
	public function insert($table,$value,$data)
	{
		$sql = "insert into `" . $table . "` (`" . implode("`,`", $value) . "`) values ('" . implode("','", $data)  . "')";
		$result = mysql_query($sql,$this->db_conn);
		return $result;
	}

	/**
	 * @return (string) $result
	 */
	public function error()
	{
		return mysql_error($this->db_conn);
	}

}
?>