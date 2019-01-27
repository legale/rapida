<?PHP

require_once('api/Simpla.php');

########################################
class PagesAdmin extends Simpla
{

    public function fetch()
    {

        // Меню
        $menus = $this->pages->get_menus();
        $this->design->assign('menus', $menus);

        // Текущее меню
        $menu_id = $this->request->get('menu_id', 'integer');
        $menu = $this->pages->get_menu($menu_id);
        if (!$menu && is_array($menus)) {
            $menu = reset($menus);
        }
        $this->design->assign('menu', $menu);


        // Обработка действий
        if ($this->request->method('post')) {
            // Сортировка
            $poss = $this->request->post('poss');
            $ids = array_keys($poss);
            sort($poss);
            foreach ($poss as $i => $pos) {
                $this->pages->update_page($ids[$i], array('pos' => $pos));
            }


            // Действия с выбранными
            $ids = $this->request->post('check');
            if (is_array($ids))
                switch ($this->request->post('action')) {
                    case 'disable' :
                        {
                            $this->pages->update_page($ids, array('visible' => 0));
                            break;
                        }
                    case 'enable' :
                        {
                            $this->pages->update_page($ids, array('visible' => 1));
                            break;
                        }
                    case 'delete' :
                        {
                            foreach ($ids as $id)
                                $this->pages->delete_page($id);
                            break;
                        }
                }

        }


        // Отображение
        if (isset($menu['id'])) {
            $pages = $this->pages->get_pages(['menu_id' => $menu['id'], 'force_no_cache' => true]);
        } else {
            $pages = array();
        }

        $this->design->assign('pages', $pages);
        return $this->design->fetch('pages.tpl');
    }
}


?>
