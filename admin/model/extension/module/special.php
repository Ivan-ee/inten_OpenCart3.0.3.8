<?php

class ModelExtensionModuleSpecial extends Model {
    public function getDescription($specialDescId) {
        $query = $this->db->query("SELECT * FROM oc_special_description WHERE id = '" . (int)$specialDescId . "'");
        return $query->row;
    }

    public function deleteProduct($productId, $specialId)
    {
        $productId = (int)$productId;
        $specialId = (int)$specialId;

        $this->db->query("DELETE FROM oc_product_special WHERE product_id = '" . $productId . "' AND special_id = '" . $specialId . "'");
    }


}
