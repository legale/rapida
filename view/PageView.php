<?PHP

/**
 * Simpla CMS
 *
 * @copyright 	2011 Denis Pikusov
 * @link 		http://simplacms.ru
 * @author 		Denis Pikusov
 *
 * Этот класс использует шаблон page.tpl
 *
 */
require_once('View.php');

class PageView extends View
{
	function fetch($url = null)
	{
		if(!isset($url)){
			$url = $this->coMaster->uri_arr['path_arr']['url'];
		}
		$page = $this->pages->get_page($url);
		
		// Отображать скрытые страницы только админу, иначе 404
		if(empty($page) || (!$page->visible && empty($_SESSION['admin']))){
			header("http/1.0 404 not found");
			$page = $this->pages->get_page('404');
		}
		
		$this->design->assign('page', $page);
		$this->design->assign('meta_title', $page->meta_title);
		$this->design->assign('meta_keywords', $page->meta_keywords);
		$this->design->assign('meta_description', $page->meta_description);
		
		return $this->design->fetch('page.tpl');
	}
}
