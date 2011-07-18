<?php
if (!defined("FUNC_FILE")) die("Illegal File Access");

class sql_db {
	var $db_connect_id;
	var $query_result;
	var $row = array();
	var $rowset = array();
	var $num_queries = 0;
	var $total_time_db = 0;
	var $time_query = "";

#��������� ���������� � �������� MySQL
	function sql_db($sqlserver, $sqluser, $sqlpassword, $database, $transaction = false, $charset=false) {
		$this->db_connect_id = new mysqli($sqlserver, $sqluser, $sqlpassword);
		if ($charset) $this->db_connect_id->set_charset($charset);
		if ($this->db_connect_id) {
			if ($database != "" && !$this->db_connect_id->select_db($database)) {
				$this->db_connect_id->close();
				$this->db_connect_id = false;
			}
			return $this->db_connect_id;
		} else {
			return false;
		}
	}
#��������� ���������� � ��
	function sql_close() {
		if ($this->db_connect_id) {
			if ($this->query_result && is_object($this->query_result)) $this->query_result->close();
			$result = $this->db_connect_id->close();
			return $result;
		} else {
			return false;
		}
	}
#��������� SQL ������
	function sql_query($query = "", $transaction = false) {
		if ($this->query_result) unset($this->query_result);
		if ($query != "") {
      $this->id_query=md5($query.microtime());
			$st = array_sum(explode(" ", microtime()));
			$this->query_result = $this->db_connect_id->query($query);
			$total_tdb = round(array_sum(explode(" ", microtime())) - $st, 5);
			$this->total_time_db += $total_tdb;
			$this->time_query .= "<font color='".(($total_tdb > 0.01)?'red':'green')."'><b>$total_tdb</b></font> "._SEC.". - [$query]<br><br>";
		}
		if ($this->query_result) {
			$this->num_queries += 1;
			unset($this->row[$this->id_query]);
			unset($this->rowset[$this->id_query]);
			return $this->query_result;
		} else {
			return ($transaction == END_TRANSACTION) ? true : false;
		}
	}
#���������� ���������� ����� � ����������
	function sql_numrows($query_id = 0) {
		if (!$query_id) $query_id = $this->query_result;
		return ($query_id) ? $query_id->num_rows : false;
	}
#�������� ���������� �����, ��������������� � ���������� �������� MySQL
	function sql_affectedrows() {
		return ($this->db_connect_id) ? $this->db_connect_id->affected_rows : false;
	}
#���������� ���������� �������� ��� ���������� �������
	function sql_numfields($query_id = 0) {
		if (!$query_id) $query_id = $this->query_result;
		return ($query_id) ? $query_id->field_count : false;
	}
#���������� �������� ��������� ������� ���������� �������
	function sql_fieldname($offset, $query_id = 0) {
		if (!$query_id) $query_id = $this->query_result;
		$query_id->field_seek($offset);
		$field = $query_id->fetch_field();
		return ($query_id) ? $field->name : false;
	}

#���������� ��� ���������� ���� ���������� �������
	function sql_fieldtype($offset, $query_id = 0) {
	$types=array(
  MYSQLI_TYPE_DECIMAL=>'decimal',
  MYSQLI_TYPE_NEWDECIMAL=>'numeric',
  MYSQLI_TYPE_BIT=>'bit',
  MYSQLI_TYPE_TINY=>'tinyint',
  MYSQLI_TYPE_SHORT=>'int',
  MYSQLI_TYPE_LONG=>'int',
  MYSQLI_TYPE_FLOAT=>'float',
  MYSQLI_TYPE_DOUBLE=>'double',
  MYSQLI_TYPE_NULL=>'default null',
  MYSQLI_TYPE_TIMESTAMP=>'timestamp',
  MYSQLI_TYPE_LONGLONG=>'bigint',
  MYSQLI_TYPE_INT24=>'mediumint',
  MYSQLI_TYPE_DATE=>'date',
  MYSQLI_TYPE_TIME=>'time',
  MYSQLI_TYPE_DATETIME=>'datetime',
  MYSQLI_TYPE_YEAR=>'year',
  MYSQLI_TYPE_NEWDATE=>'date',
  MYSQLI_TYPE_ENUM=>'enum',
  MYSQLI_TYPE_SET=>'set',
  MYSQLI_TYPE_TINY_BLOB=>'tinyblob',
  MYSQLI_TYPE_MEDIUM_BLOB=>'mediumblob',
  MYSQLI_TYPE_LONG_BLOB=>'longblob',
  MYSQLI_TYPE_BLOB=>'blob',
  MYSQLI_TYPE_VAR_STRING=>'varchar',
  MYSQLI_TYPE_STRING=>'char',
  MYSQLI_TYPE_GEOMETRY=>'geometry'
  );
		if (!$query_id) $query_id = $this->query_result;
		$query_id->field_seek($offset);
		$field = $query_id->fetch_field();
		return ($query_id) ? $types[$field->type] : false;
	}

#���������� ��������� ��� ��������� ������
	function sql_fetchrow($query_id = 0) {
		if (!$query_id) $query_id = $this->query_result;
		if ($query_id) {
      $this->row[$this->id_query] = $query_id->fetch_array(MYSQLI_BOTH);
			return $this->row[$this->id_query];
		} else {
			return false;
		}
	}
# ���������� ��������� � ������������� �������	
	function sql_fetchrowset($query_id = 0) {
		if (!$query_id) $query_id = $this->query_result;
		if ($query_id) {
			unset($this->rowset[$this->id_query]);
			unset($this->row[$this->id_query]);
			while ($this->rowset[$this->id_query] = $query_id->fetch_array(MYSQLI_BOTH)) {
				$result[] = $this->rowset[$this->id_query];
			}
			return $result;
		} else {
			return false;
		}
	}
# ���������� ������ �� ��������� ������ � ����
	function sql_fetchfield($field, $rownum = -1, $query_id = 0) {
		if (!$query_id) $query_id = $this->query_result;
		if ($query_id) {
			if ($rownum > -1) {
				$query_id->data_seek($rownum);
				$fetch = $query_id->fetch_array();
				$result = $fetch[$field];
			} else {
				if (empty($this->row[$this->id_query]) && empty($this->rowset[$this->id_query])) {
					if ($this->sql_fetchrow()) {
						$result = $this->row[$this->id_query][$field];
					}
				} else {
					if ($this->rowset[$this->id_query]) {
						$result = $this->rowset[$this->id_query][0][$field];
					} else if ($this->row[$this->id_query]) {
						$result = $this->row[$this->id_query][$field];
					}
				}
			}
			return $result;
		} else {
			return false;
		}
	}

#����������� ��������� �� ������������ ������ � ���������� �������
	function sql_rowseek($rownum, $query_id = 0) {
		if (!$query_id) $query_id = $this->query_result;
		return ($query_id) ? $query_id->data_seek($rownum) : false;
	}
#���������� ID, ��������������� ��� ��������� INSERT-�������
	function sql_nextid() {
		return ($this->db_connect_id) ? $this->db_connect_id->insert_id : false;
	}
	
#����������� ������ �� ���������� �������
	function sql_freeresult($query_id = 0){
		if (!$query_id) $query_id = $this->query_result;
		if ($query_id) {
			unset($this->row[$this->id_query]);
			unset($this->rowset[$this->id_query]);
			$query_id->free_result();
			return true;
		} else {
			return false;
		}
	}

#���������� ��� � ������ �������� ������ ���������� ����������
	function sql_error($query_id = 0) {
		$result["message"] = $this->db_connect_id->error;
		$result["code"] = $this->db_connect_id->errno;
		return $result;
	}
}
?>