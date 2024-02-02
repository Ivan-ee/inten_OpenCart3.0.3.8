<?php

class ModelCatalogActions extends Model {

    public function getActions()
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "module` WHERE `code` = 'special'");
        return $query->rows;
    }

    public function getActionById($id)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "module` WHERE `module_id` = $id");
        return $query->row;
    }

}