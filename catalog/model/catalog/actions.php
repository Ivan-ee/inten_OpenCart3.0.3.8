<?php

class ModelCatalogActions extends Model {

    public function getActions()
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "module` WHERE `code` = 'special'");
        return $query->rows;
    }

}