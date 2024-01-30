<?php

class ControllerExtensionMenu extends Controller
{

    public function index($data)
    {
        $lang_id = (int)$this->config->get('config_language_id');
        $menu_id = isset($data['id']) ? (int)$data['id'] : 0;

        $this->load->model("extension/menu");
        $data_menu = $this->cache->get("menu_{$menu_id}_{$lang_id}");

        if (!$data_menu) {
            $menu_data = $this->model_extension_menu->getTreeItems($menu_id);
            if (!$menu_data) {
                return null;
            }
            $data_menu = $this->model_extension_menu->getMapTree($menu_data);
            $this->cache->set("menu_{$menu_id}_{$lang_id}", $data_menu);
        }

        return $data_menu;
    }

    private function dump($data)
    {
        echo "<pre>" . print_r($data, 1) . "</pre>";
    }

}