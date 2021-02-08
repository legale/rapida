<?php
require_once('Simpla.php');

class Queue extends Simpla
{

    public static $skip_queue_full = true;

    public function redis_addtask(string $keyhash, string $method, string $task): ?bool
    {
        dtimer::log(__METHOD__. "start keyhash: $keyhash");
        $redis = $this->cache->redis_init();
        if ($redis && !$redis->hexists($this->config->host . "_queue_hashtable", $keyhash)) {
            $redis->hset($this->config->host . "_queue_hashtable", $keyhash, null);
            return $redis->rPush($this->config->host . "_queue", msgpack_pack([$keyhash, $method, $task]));
        } else {
            return false;
        }
    }

    public function redis_execlast(): bool
    {
        $redis = $this->cache->redis_init();
        if(!$redis){
            return false;
        }
        $val = $redis->lPop($this->config->host . "_queue");
        if($val === false){
            //if linked list is empty we can delete hashtable
            $redis->del($this->config->host . "_queue_hashtable");
            return false;
        }
        list($keyhash, $method, $task) = msgpack_unpack($val);
        //echo $keyhash ." $task" . PHP_EOL;
        echo ".";
        eval($task);
        return $redis->hdel($this->config->host . "_queue_hashtable", $keyhash);
    }

    public function redis_count(): ?int
    {
        $redis = $this->cache->redis_init();
        return $redis ? $redis->lLen($this->config->host . "_queue") : null;
    }

    public function redis_qreset(): bool
    {
        $redis = $this->cache->redis_init();
        return $redis ? $redis->del($this->config->host . "_queue", $this->config->host . "_queue_hashtable") : false;
    }


    public function addtask($keyhash, $method, $task)
    {
        dtimer::log(__METHOD__. "start keyhash: $keyhash");
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
        if (!self::$skip_queue_full) {
            $query2 = "INSERT IGNORE __queue_full SET `keyhash` = 0x$keyhash , `method` = '$method', `task` = '$task'";
        }
        dtimer::log(__METHOD__ . ' addtask query: ' . $query);
        $this->db->query($query);
        dtimer::log('queue inserted id: ' . $this->db->insert_id());

        $switch = $this->db->affected_rows();
        if ($switch < 0) {
            dtimer::log('addtask error insert entry code: ' . var_export($this->db->affected_rows(), true));
            return false;
        } elseif ($switch === 0) {
            dtimer::log('addtask insert duplicate entry');
            return false;
        }


        return true;
    }

    public function count_tasks()
    {
        $query = "show table status like 's_queue'";
        $this->db->query($query);
        $return = $this->db->result_array('Rows');
        return $return;
    }

    public function count_tasks_full()
    {
        $query = "show table status like 's_queue_full'";
        $this->db->query($query);
        $return = $this->db->result_array('Rows');
        return $return;
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
        $return = $this->db->result_array();
        return $return;
    }

    public function gettask($id)
    {
        if (empty($id)) return FALSE;

        $this->db->query("
		SELECT *
		FROM __queue
		WHERE id = $id
		;");
        $res = $this->db->result_array();
        return $res;
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

        $res = $this->db->result_array('keyhash');

        if (isset($res) && $res == $keyhash) {
            return true;
        } else {
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
        $task = $this->db->result_array('task');
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
        $task = $this->db->result_array('task');
        return eval($task);

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
        $res = $this->db->result_array('task');
        return eval($res);
    }

    public function task_delete($id)
    {
        if (empty($id)) {
            return false;
        }

        $query = "DELETE t FROM __queue t WHERE id = '$id'";

        $this->db->query($query);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
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
        $res = $this->db->result_array();
        if ($this->db->affected_rows() <= 0) {
            return false;
        }

        $id = $res['id'];
        if ($this->task_delete($id) === false) {
            return 666;
        }

        $task = $res['task'];

        dtimer::log("task string before eval: " . $task);
        eval($task);

        dtimer::log(__METHOD__ . ' end ');
        return (int)$id;
    }
}
