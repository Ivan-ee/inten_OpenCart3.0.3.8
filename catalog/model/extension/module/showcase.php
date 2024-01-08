<?php

class ModelExtensionModuleShowcase extends Model
{
    public function getLatestCategories($limit)
    {
        $query = $this->db->query("SELECT DISTINCT c.category_id, cd.name, p.date_added
        FROM oc_product p
        LEFT JOIN oc_product_to_category p2c ON (p.product_id = p2c.product_id)
        LEFT JOIN oc_category c ON (p2c.category_id = c.category_id)
        LEFT JOIN oc_category_description cd ON (c.category_id = cd.category_id)
        WHERE p.status = '1' AND p.date_available <= NOW()
        ORDER BY p.date_added DESC
        LIMIT " . (int)$limit
        );

        $category_data = array();

        if ($query->num_rows) {
            foreach ($query->rows as $result) {
                $category_data[] = array(
                    'category_id' => $result['category_id'],
                    'name' => $result['name'],
                    'time' => $result['date_added']
                );
            }
        }

        return $category_data;
    }

    public function getBestSellerCategories($limit)
    {
        $category_data = array();

        $query = $this->db->query("
        SELECT c.category_id, COUNT(op.product_id) AS total
        FROM " . DB_PREFIX . "order_product op
        LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id)
        LEFT JOIN `" . DB_PREFIX . "product` p ON (op.product_id = p.product_id)
        LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)
        LEFT JOIN " . DB_PREFIX . "category c ON (p2c.category_id = c.category_id)
        LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id)
        WHERE o.order_status_id > '0' AND p.status = '1' AND p.date_available <= NOW() AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
        GROUP BY c.category_id
        ORDER BY total DESC
        LIMIT " . (int)$limit
        );

        foreach ($query->rows as $result) {
            $category_data[$result['category_id']] = $this->getCategory($result['category_id']);
        }

        return $category_data;
    }


    public function getCategoriesWithSpecials($limit)
    {
        $result = "
        SELECT c.category_id, cd.name
        FROM " . DB_PREFIX . "product p
        LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)
        LEFT JOIN " . DB_PREFIX . "category c ON (p2c.category_id = c.category_id)
        LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id)
        WHERE p.product_id IN (
            SELECT DISTINCT ps.product_id
            FROM " . DB_PREFIX . "product_special ps
            LEFT JOIN " . DB_PREFIX . "product p_special ON (ps.product_id = p_special.product_id)
            LEFT JOIN " . DB_PREFIX . "product_to_category p2c_special ON (p_special.product_id = p2c_special.product_id)
            WHERE p_special.status = '1'
              AND p_special.date_available <= NOW()
              AND p2c_special.category_id = c.category_id
              AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'
              AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()))
        )
        GROUP BY c.category_id, cd.name
        ORDER BY c.sort_order ASC
        LIMIT " . (int)$limit;

        $result = $this->db->query($result);

        return $result->rows;
    }


}
