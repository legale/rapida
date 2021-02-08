<?php
if(defined('PHP7')) {
     eval("declare(strict_types=1);");
}


require_once ('Simpla.php');

/**
 * Class Managers
 */
class Managers extends Simpla
{
    /**
     * @var array
     */
    public $permissions_list = array(
		'products', 'categories', 'brands', 'features', 'orders', 'labels',
		'users', 'groups', 'coupons', 'pages', 'blog', 'comments', 'feedback', 'import', 'export',
		'backup', 'stats', 'design', 'settings', 'currency', 'delivery', 'payment', 'managers', 'license'
	);

    /**
     * @var string
     */
    public $passwd_file = "simpla/.passwd";

    /**
     * Managers constructor.
     */
    public function __construct()
	{
		// Для совмсестимости с режимом CGI
		if (isset($_SERVER['REDIRECT_REMOTE_USER']) && empty($_SERVER['PHP_AUTH_USER']))
			{
			$_SERVER['PHP_AUTH_USER'] = $_SERVER['REDIRECT_REMOTE_USER'];
		}
		elseif (empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER["REMOTE_USER"]))
			{
			$_SERVER['PHP_AUTH_USER'] = $_SERVER["REMOTE_USER"];
		}
	}

    /**
     * @return array
     */
    public function get_managers()
	{
		$lines = explode("\n", @file_get_contents(dirname(dirname(__FILE__)) . '/' . $this->passwd_file));
		$managers = array();
		foreach ($lines as $line)
			{
			if (!empty($line))
				{
				$manager = null;
				$fields = explode(":", $line);
				$manager = array();
				$manager['login'] = trim($fields[0]);
				$manager['permissions'] = array();
				if (isset($fields[2]))
					{
					$manager['permissions'] = explode(",", $fields[2]);
					foreach ($manager['permissions'] as &$permission)
						$permission = trim($permission);
				}
				else
					$manager['permissions'] = $this->permissions_list;

				$managers[] = $manager;
			}
		}
		return $managers;
	}

    /**
     * @param array $filter
     * @return int
     */
    public function count_managers($filter = array())
	{
		return count($this->get_managers());
	}

    /**
     * @return array
     */
    public function get_manager()
	{
		$manager = array();
		$manager['login'] = $_SESSION['admin'];
		$manager['permissions'] = $this->permissions_list;
		
		return $manager;
	}

    /**
     * @param $manager
     * @return bool|mixed
     */
    public function add_manager($manager)
	{
		$manager = (array)$manager;
		if (!empty($manager['login']))
			$m[0] = $manager['login'];
		if (!empty($manager['password']))
			{
			// захешировать пароль
			$m[1] = $this->crypt_apr1_md5($manager['password']);
		}
		else
			{
			$m[1] = "";
		}
		if (is_array($manager['permissions']))
			{
			if (count(array_diff($this->permissions_list, $manager['permissions'])) > 0)
				{
				$m[2] = implode(",", $manager['permissions']);
			}
			else
				{
				unset($m[2]);
			}
		}
		$line = implode(":", $m);
		file_put_contents($this->passwd_file, @file_get_contents($this->passwd_file) . "\n" . $line);
		if ($m = $this->get_manager($manager['login']))
			return $m['login'];
		else
			return false;
	}

    /**
     * @param $login
     * @param $manager
     * @return bool|mixed
     */
    public function update_manager($login, $manager)
	{
		$manager = (array)$manager;
		// Не допускаем двоеточия в логине
		if (!empty($manager['login']))
			$manager['login'] = str_replace(":", "", $manager['login']);

		$lines = explode("\n", @file_get_contents($this->passwd_file));
		$updated_flag = false;
		foreach ($lines as &$line)
			{
			$m = explode(":", $line);
			if ($m[0] == $login)
				{
				if (!empty($manager['login']))
					$m[0] = $manager['login'];
				if (!empty($manager['password']))
					{
					// захешировать пароль
					$m[1] = $this->crypt_apr1_md5($manager['password']);
				}
				if (isset($manager['permissions']) && is_array($manager['permissions']))
					{
					if (count(array_diff($this->permissions_list, $manager['permissions'])) > 0)
						{
						$m[2] = implode(",", array_intersect($this->permissions_list, $manager['permissions']));
					}
					else
						{
						unset($m[2]);
					}
				}
				$line = implode(":", $m);
				$updated_flag = true;
			}
		}
		if ($updated_flag)
			{
			file_put_contents($this->passwd_file, implode("\n", $lines));
			if ($m = $this->get_manager($manager['login']))
				return $m['login'];
		}
		return false;
	}

    /**
     * @param $login
     * @return bool
     */
    public function delete_manager($login)
	{
		$lines = explode("\n", @file_get_contents($this->passwd_file));
		foreach ($lines as $i => $line)
			{
			$m = explode(":", $line);
			if ($m[0] == $login)
				unset($lines[$i]);
		}
		file_put_contents($this->passwd_file, implode("\n", $lines));
		return true;
	}

    /**
     * @param $plainpasswd
     * @return string
     */
    private function crypt_apr1_md5($plainpasswd)
	{
		$salt = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz0123456789"), 0, 8);
		$len = strlen($plainpasswd);
		$text = $plainpasswd . '$apr1$' . $salt;
		$bin = pack("H32", md5($plainpasswd . $salt . $plainpasswd));
		for ($i = $len; $i > 0; $i -= 16) {
			$text .= substr($bin, 0, min(16, $i));
		}
		for ($i = $len; $i > 0; $i >>= 1) {
			$text .= ($i & 1) ? chr(0) : $plainpasswd[0];
		}
		$bin = pack("H32", md5($text));
		for ($i = 0; $i < 1000; $i++) {
			$new = ($i & 1) ? $plainpasswd : $bin;
			if ($i % 3) $new .= $salt;
			if ($i % 7) $new .= $plainpasswd;
			$new .= ($i & 1) ? $bin : $plainpasswd;
			$bin = pack("H32", md5($new));
		}
		$tmp = '';
		for ($i = 0; $i < 5; $i++) {
			$k = $i + 6;
			$j = $i + 12;
			if ($j == 16) $j = 5;
			$tmp = $bin[$i] . $bin[$k] . $bin[$j] . $tmp;
		}
		$tmp = chr(0) . chr(0) . $bin[11] . $tmp;
		$tmp = strtr(
			strrev(substr(base64_encode($tmp), 2)),
			"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/",
			"./0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"
		);
		return "$" . "apr1" . "$" . $salt . "$" . $tmp;
	}

    /**
     * @param $module
     * @return bool
     */
    public function access($module)
	{	
		$manager = $this->get_manager();
		if (is_array($manager['permissions']))
			return in_array($module, $manager['permissions']);
		else
			return false;
	}
}
