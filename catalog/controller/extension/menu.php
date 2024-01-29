<?php

class ControllerExtensionMenu extends Controller
{

    public function index($data)
    {
        $lang_id = (int)$this->config->get('config_language_id');
        $menu_id = isset($data['id']) ? (int)$data['id'] : 0;
        if (isset($data['tpl'])) {
            $tpl = __DIR__ . "/menu/{$data['tpl']}.php";
            if (!file_exists($tpl)) {
                $tpl = __DIR__ . "/menu/base.php";
            }
        } else {
            $tpl = __DIR__ . "/menu/base.php";
        }

        $this->load->model("extension/menu");
        $tpl_md5 = md5($tpl);
        $data_menu = $this->cache->get("menu_{$menu_id}_{$lang_id}_{$tpl_md5}");

        if (!$data_menu) {
            $menu_data = $this->model_extension_menu->getTreeItems($menu_id);
            if (!$menu_data) {
                return null;
            }
            $menu_tree = $this->model_extension_menu->getMapTree($menu_data);
            $data_menu = $this->model_extension_menu->treeToHtml($menu_tree, $tpl);
            $this->cache->set("menu_{$menu_id}_{$lang_id}_{$tpl_md5}", $data_menu);
        }
        return $data_menu;
    }

    private function dump($data)
    {
        echo "<pre>" . print_r($data, 1) . "</pre>";
    }

}