<?php

class ModelExtensionModuleShowcase extends Model
{
    public function updateShowcaseImage($showcaseImagesId, $title, $link, $image)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "showcase_images SET title = '" . $this->db->escape($title) . "', link = '" . $this->db->escape($link) . "', image = '" . $this->db->escape($image) . "' WHERE showcase_images_id = '" . (int)$showcaseImagesId . "'");
    }


    public function getBanner()
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "showcase_images");

        return $query->rows;
    }
}