<?php

class ModelExtensionModuleSpecial extends Model {
    public function getDescription($specialDescId) {
        $query = $this->db->query("SELECT * FROM oc_special_description WHERE id = '" . (int)$specialDescId . "'");
        return $query->row;
    }
}
