<?PHP

/**
 * Этот класс использует шаблон page.tpl
 *
 */
require_once('View.php');

class PageView extends View
{
    function fetch($url = null)
    {
        dtimer::log(__METHOD__. " start url: ".var_export($url,true));
        if (!isset($url)) {
            $url = $this->root->uri_arr['path']['url'];
        }
        $page = $this->pages->get_page($url);

        // Отображать скрытые страницы только админу, иначе 404
        if (empty($page) || (!$page['visible'] && empty($_SESSION['admin']))) {
            $url = '404';
            $page = $this->pages->get_page($url);
        }

        if($url === '404'){
            header("http/1.0 404 not found");
        }
        $this->design->assign('page', $page);
        $this->design->assign('meta_title', $page['meta_title']);
        $this->design->assign('meta_keywords', $page['meta_keywords']);
        $this->design->assign('meta_description', $page['meta_description']);

        return $this->design->fetch('page.tpl');
    }
}
