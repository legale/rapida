<?php
require_once ('Simpla.php');

class Queue extends Simpla
{

	public function addtask($keyhash, $method, $task)
	{

		if (empty($task || $keyhash)) {
			return false;
		}
		
		
		//$task = json_encode($task);
		$task = $this->db->escape($task);
		$method = $this->db->escape($method);

		$keyhash = $this->db->escape($keyhash);

		if ($this->check_task_key($keyhash)) {
			return 2;
		}

		$query = $this->db->placehold("INSERT IGNORE __queue SET `keyhash` = 0x$keyhash , `method` = '$method', `task` = '$task'");
		$query2 = "INSERT IGNORE __queue_full SET `keyhash` = 0x$keyhash , `method` = '$method', `task` = '$task'";
		dtimer::log(__METHOD__ . ' addtask query: ' . $query);
		$this->db->query($query);
		if ($this->db->affected_rows() < 0) {
			dtimer::log('addtask error insert entry code: ' . var_export($this->db->affected_rows(), true));
			return false;
		}
		elseif ($this->db->affected_rows() === 0) {
			dtimer::log('addtask insert duplicate entry code: ' . var_export($this->db->affected_rows(), true));
			return false;
		}
		else {
			$this->db->query($query2);
			if ($this->db->affected_rows() < 0) {
				dtimer::log('addtask error insert query2');
				return false;
			}
			elseif ($this->db->affected_rows() === 0) {
				dtimer::log('addtask insert duplicate query2');
				return false;
			}
		}

		return true;
	}

	public function count_tasks()
	{


		$query = "
		SELECT COUNT(*) as count
		from __queue
		;";
		$this->db->query($query);
		$return = $this->db->results();
		return $return[0]->count;
	}

	public function count_tasks_full()
	{


		$query = "
		SELECT COUNT(*) as count
		from __queue_full
		;";
		$this->db->query($query);
		$return = $this->db->results();
		//print_r($return);
		return $return[0]->count;
	}

	public function getlasttask()
	{
		//dtimer::log(__METHOD__);

		$this->db->query("
		SELECT *
		FROM __queue
		ORDER BY id
		LIMIT 1
		;");
		$return = $this->db->results()[0];
		//dtimer::log(__METHOD__.' results:'.print_r($return,true));
		return $return;
	}

	public function gettask($id)
	{
		//dtimer::log(__METHOD__.' id '.$id);
		if (empty($id)) return FALSE;

		$this->db->query("
		SELECT *
		FROM __queue
		WHERE id = $id
		;");
		$return = $this->db->results()[0];
		return $return;
	}

	public function check_task_key($keyhash)
	{
		if (empty($keyhash)) {
			return false;
		}

		$query = "
		SELECT `keyhash`
		FROM __queue
		WHERE `keyhash` = 0x$keyhash
		;";

		$this->db->query($query);

		$res = $this->db->result();

		if (isset($res->keyhash) && $res == $keyhash) {
			return true;
		}
		else {
			return false;
		}
	}

	public function exec_task($id)
	{
		if (empty($id)) return FALSE;

		$this->db->query("
		SELECT *
		FROM __queue
		WHERE id = $id
		;");
		$task = $this->db->results()[0]->task;
		//~ $task = json_decode($task);
		return eval($task);
	}

	public function exec_task_by_key($keyhash)
	{
		if (empty($keyhash)) {
			return false;
		}


		$query = "
		SELECT *
		FROM __queue
		WHERE `keyhash` = 0x$keyhash
		LIMIT 1
		;";
		//dtimer::log(__METHOD__.' query '.$query);

		$this->db->query($query);
		$res = $this->db->results()[0];
		$task = $res->task;
		//~ $task = json_decode($task);
		$id = $res->id;


		eval($task);
		return $res;
	}

	public function exec_task_by_method($method)
	{
		if (empty($method)) {
			return false;
		}


		$query = "
		SELECT *
		FROM __queue
		WHERE `method` LIKE '%$method%'
		LIMIT 1
		;";
		//dtimer::log(__METHOD__.' query '.$query);

		$this->db->query($query);
		$res = $this->db->results()[0];
		$task = $res->task;
		//~ $task = json_decode($task);
		$id = $res->id;


		eval($task);
		return $res;
	}

	public function task_delete($id)
	{
		if (empty($id)) {
			return false;
		}

		$query = "
		DELETE t
		FROM __queue t
		WHERE id = '$id'
		;";

		$this->db->query($query);
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		else {
			return false;
		}
	}

	public function execlasttask()
	{
		$query_select = "
		SELECT *
		FROM __queue
		ORDER BY id ASC
		LIMIT 1
		;";

		$this->db->query($query_select);
		$result = $this->db->result();
		if ($this->db->affected_rows() <= 0) {
			return false;
		}

		$id = $result->id;
		if ($this->task_delete($id) === false) {
			return 666;
		}

		$task = $result->task;
		//~ $task = json_decode($task);

		dtimer::log("task string before eval: " . $task);
		eval($task);

		dtimer::log(__METHOD__ . ' end ');
		return (int)$id;
	}
}
