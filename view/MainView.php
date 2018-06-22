<?PHP
 
require_once('View.php');


class MainView extends View
{

	function fetch()
	{
		$this->design->assign('slider', $this->slider->get());
		$this->design->assign('brands', $this->brands->get_brands());

		$this->db->query("SELECT * FROM __pages WHERE trans = ''");
		$page = $this->db->result_array();
		if($page)
		{
			$this->design->assign('meta_title', $page['meta_title']);
			$this->design->assign('meta_keywords', $page['meta_keywords']);
			$this->design->assign('meta_description', $page['meta_description']);
		}

		return $this->design->fetch('main.tpl');
	}
}
