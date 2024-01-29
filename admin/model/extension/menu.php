<?php

class ModelExtensionMenu extends Model
{

    public function install()
    {
        $this->db->query("
                CREATE TABLE IF NOT EXISTS `oc_menu` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	PRIMARY KEY (`id`) USING BTREE
) COLLATE='utf8mb4_unicode_ci'
ENGINE=InnoDB;
            ");

        $this->db->query("
                CREATE TABLE IF NOT EXISTS `oc_menu_link` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`menu_id` INT(10) UNSIGNED NOT NULL,
	`parent_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`) USING BTREE
) COLLATE='utf8mb4_unicode_ci'
ENGINE=InnoDB;
            ");

        $this->db->query("
                CREATE TABLE IF NOT EXISTS `oc_menu_link_description` (
  `menu_link_id` INT(10) UNSIGNED NOT NULL,
	`language_id` INT(10) UNSIGNED NOT NULL,
	`title` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`link` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	PRIMARY KEY (`menu_link_id`, `language_id`) USING BTREE
) COLLATE='utf8mb4_unicode_ci'
ENGINE=InnoDB;
            ");
    }

    public function getMenuList()
    {
        $query = $this->db->query("SELECT * FROM `oc_menu`");
        return $query->rows;
    }

    public function getMenu($menu_id)
    {
        $query = $this->db->query("SELECT * FROM oc_menu WHERE id = " . (int)$menu_id);
        return $query->row;
    }

    public function addMenu($data)
    {
        $this->db->query("INSERT INTO `oc_menu` SET `title` = '" . $this->db->escape($data['title']) . "' ");
        return $this->db->getLastId();
    }

    public function editMenu($menu_id, $data)
    {
        $this->db->query("UPDATE `oc_menu` SET title = '" . $this->db->escape($data['title']) . "' WHERE id = " . (int)$menu_id);
    }

    public function deleteMenu($menu_id)
    {
        $query = $this->db->query("SELECT COUNT(*) AS cnt FROM `oc_menu_link` WHERE menu_id = " . (int)$menu_id);
        if (!$query->row['cnt']) {
            $query = $this->db->query("DELETE FROM `oc_menu` WHERE id = " . (int)$menu_id);
            return true;
        }
        return false;
    }

    public function getTreeItems($menu_id)
    {
        $menu_id = (int)$menu_id;
        $language_id = (int)$this->config->get('config_language_id');
        $query = $this->db->query("SELECT m.id, m.parent_id, md.title, md.link FROM oc_menu_link m
LEFT JOIN oc_menu_link_description md ON m.id = md.menu_link_id
WHERE m.menu_id = {$menu_id} and md.language_id = {$language_id}");
        $menu = [];
        foreach ($query->rows as $row) {
            $menu[$row['id']] = $row;
        }

        return $menu;
    }

    public function getMapTree($dataset)
    {
        $tree = [];

        foreach ($dataset as $id => &$node) {
            if (!$node['parent_id']) {
                $tree[$id] = &$node;
            } else {
                $dataset[$node['parent_id']]['children'][$id] = &$node;
            }
        }

        return $tree;
    }

    public function addMenuLink($menu_id, $data)
    {
        $this->db->query("INSERT INTO oc_menu_link SET menu_id = " . (int)$menu_id . ", parent_id = " . (int)$data['menu_description_parent']);
        $menu_link_id = $this->db->getLastId();
        foreach ($data['menu_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO oc_menu_link_description SET menu_link_id = " . (int)$menu_link_id . ", language_id = " . (int)$language_id . ", title = '" . $this->db->escape($value['title']) . "', link = '" . $this->db->escape($value['link']) . "'");
        }
    }

    public function getMenuLink($menu_link_id)
    {
        $query = $this->db->query("SELECT mld.*, ml.parent_id FROM oc_menu_link_description mld
LEFT JOIN oc_menu_link ml
ON mld.menu_link_id = ml.id
WHERE ml.id = " . (int)$menu_link_id);

        foreach ($query->rows as $row) {
            $menu_description_data[$row['language_id']] = [
                'id' => $row['menu_link_id'],
                'title' => $row['title'],
                'link' => $row['link'],
                'parent_id' => $row['parent_id'],
            ];
        }
        return $menu_description_data;
    }

    public function getMenuLinkByLinkId($link_id)
    {
        $query = $this->db->query("SELECT * FROM oc_menu_link WHERE id = " . (int)$link_id);
        return $query->row;
    }

    public function deleteMenuLink($menu_link_id)
    {
        $menu_link_id = (int)$menu_link_id;
        $query = $this->db->query("SELECT COUNT(*) AS cnt FROM `oc_menu_link` WHERE parent_id = {$menu_link_id}");
        if (!$query->row['cnt']) {
            $this->db->query("DELETE FROM `oc_menu_link` WHERE id = {$menu_link_id}");
            $this->db->query("DELETE FROM `oc_menu_link_description` WHERE menu_link_id = {$menu_link_id}");
            return true;
        }
        return false;
    }

    public function editMenuLink($menu_link_id, $data)
    {
        $menu_link_id = (int)$menu_link_id;
        $this->db->query("UPDATE oc_menu_link SET parent_id = " . (int)$data['menu_description_parent'] . " WHERE id = {$menu_link_id}");

        foreach ($data['menu_description'] as $language_id => $value) {
            $this->db->query("UPDATE oc_menu_link_description SET title = '" . $this->db->escape($value['title']) . "', link = '" . $this->db->escape($value['link']) . "' WHERE menu_link_id = {$menu_link_id} AND language_id = " . (int)$language_id);
        }
    }

}
