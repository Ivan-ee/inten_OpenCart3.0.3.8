<?php

class ModelExtensionModuleShowcase extends Model
{
    public function updateShowcaseImage($showcaseImagesId, $title, $link, $image)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "showcase_images SET title = '" . $this->db->escape($title) . "', link = '" . $this->db->escape($link) . "', image = '" . $this->db->escape($image) . "' WHERE showcase_images_id = '" . (int)$showcaseImagesId . "'");
    }


    public function getBanners()
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "showcase_images");

        return $query->rows;
    }

    public function saveStatus($status) {
        $this->db->query("UPDATE " . DB_PREFIX . "showcase SET status = '" . (int)$status . "' WHERE id = '1'");
    }

    public function getStatus() {
        $query = $this->db->query("SELECT status FROM " . DB_PREFIX . "showcase WHERE id = '1'");

        if ($query->num_rows) {
            return $query->row['status'];
        } else {
            return 0;
        }
    }
}