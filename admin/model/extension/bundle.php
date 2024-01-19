<?php
class ModelExtensionBundle extends Model {
	
	public function CreateTable(){
		$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."bundle` (`bundle_id` int(11) NOT NULL AUTO_INCREMENT,`sort_order` int(11) NOT NULL,`status` int(11) NOT NULL,`product` text NOT NULL,`category` text NOT NULL,`date_start` date NOT NULL,`date_end` date NOT NULL,`top` tinyint(4) NOT NULL, PRIMARY KEY (`bundle_id`))");
		
		$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."bundle_description` (`bundle_description_id` int(11) NOT NULL AUTO_INCREMENT,`bundle_id` int(11) NOT NULL,`name` varchar(255) NOT NULL,`description` text NOT NULL,`language_id` int(11) NOT NULL,PRIMARY KEY (`bundle_description_id`)) CHARACTER SET utf8 COLLATE utf8_general_ci");
		
		$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."bundle_discount` (`customer_group_id` int(11) NOT NULL,`bundle_id` int(11) NOT NULL,`discount` decimal(10,2)NOT NULL)");
		
		$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."bundle_product` (`bundle_product_id` int(11) NOT NULL AUTO_INCREMENT,`product_id` int(11) NOT NULL,`bundle_id` int(11) NOT NULL,PRIMARY KEY (`bundle_product_id`))");
		
		$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."bundle_category` (`bundle_category_id` int(11) NOT NULL AUTO_INCREMENT,`bundle_id` int(11) NOT NULL,`category_id` int(11) NOT NULL, PRIMARY KEY (`bundle_category_id`))");
		
		$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."bundle_assignproduct` (`bundle_assignproduct_id` int(11) NOT NULL AUTO_INCREMENT,`bundle_id` int(11) NOT NULL,`product_id` int(11) NOT NULL, PRIMARY KEY (`bundle_assignproduct_id`))");
		
		$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."bundle_store` (`store_id` int(11) NOT NULL,`bundle_id` int(11) NOT NULL)");
		
		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle` LIKE 'top'");
		if(!$query->num_rows){
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle` ADD `top` tinyint(4) NOT NULL AFTER `date_end`");
		}
		
		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "bundle_description` LIKE 'description'");
		if(!$query->num_rows){
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "bundle_description` ADD `description` text NOT NULL AFTER `name`");
		}
	}
	
	public function addbundle($data){
		
		$this->db->query("INSERT INTO ".DB_PREFIX."bundle SET status = '".(int)$data['status']."', sort_order = '".(int)$data['sort_order']."', product = '', category = '', date_start = '" . $this->db->escape($data['date_start']) . "', date_end = '" . $this->db->escape($data['date_end']) . "',`top` = '" . (isset($data['top']) ? (int)$data['top'] : 0) . "'");
		
		$bundle_id = $this->db->getLastId();
		
		foreach($data['category_description'] as $language_id => $value){
			$this->db->query("INSERT INTO " . DB_PREFIX . "bundle_description SET bundle_id = '" . (int)$bundle_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "',description = '" . $this->db->escape($value['description']) . "'");
		}
		
		if(isset($data['product_category'])){
			foreach($data['product_category'] as $key => $value){
				$this->db->query("INSERT INTO " . DB_PREFIX . "bundle_category SET bundle_id = '" . (int)$bundle_id . "', category_id = '" . (int)$value . "'");
			}
		}
		
		if(isset($data['product_related'])){
			foreach($data['product_related'] as $key => $value){
				$this->db->query("INSERT INTO " . DB_PREFIX . "bundle_assignproduct SET bundle_id = '" . (int)$bundle_id . "', product_id = '" . (int)$value . "'");
			}
		}
		
		foreach($data['product_bundle'] as $product_id){
			$this->db->query("INSERT INTO ".DB_PREFIX."bundle_product SET product_id = '".(int)$product_id."', bundle_id = '".(int)$bundle_id."'");
		}
		
		foreach($data['bundle_discount'] as $customer_group_id => $discount){
				$this->db->query("INSERT INTO ".DB_PREFIX."bundle_discount SET bundle_id = '".(int)$bundle_id."', customer_group_id = '".(int)$customer_group_id."', discount = '".(float)$discount['disount']."'");
		}
		
		if (isset($data['bundle_store'])) {
			foreach ($data['bundle_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "bundle_store SET bundle_id = '" . (int)$bundle_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
		
		return $bundle_id;
	}

	public function editbundle($bundle_id, $data) {
		
		
		$this->db->query("UPDATE ".DB_PREFIX."bundle SET status = '".(int)$data['status']."',`top` = '" . (isset($data['top']) ? (int)$data['top'] : 0) . "', sort_order = '".(int)$data['sort_order']."', product = '', category = '',date_start = '" . $this->db->escape($data['date_start']) . "', date_end = '" . $this->db->escape($data['date_end']) . "' WHERE bundle_id = '".(int)$bundle_id."'");
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "bundle_description WHERE bundle_id = '" . (int)$bundle_id . "'");
		foreach($data['category_description'] as $language_id => $value){
			$this->db->query("INSERT INTO " . DB_PREFIX . "bundle_description SET bundle_id = '" . (int)$bundle_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "',description = '" . $this->db->escape($value['description']) . "'");
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "bundle_category WHERE bundle_id = '" . (int)$bundle_id . "'");
		if(isset($data['product_category'])){
			foreach($data['product_category'] as $key => $value){
				$this->db->query("INSERT INTO " . DB_PREFIX . "bundle_category SET bundle_id = '" . (int)$bundle_id . "', category_id = '" . (int)$value . "'");
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "bundle_assignproduct WHERE bundle_id = '" . (int)$bundle_id . "'");
		if(isset($data['product_related'])){
			foreach($data['product_related'] as $key => $value){
				$this->db->query("INSERT INTO " . DB_PREFIX . "bundle_assignproduct SET bundle_id = '" . (int)$bundle_id . "', product_id = '" . (int)$value . "'");
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "bundle_product WHERE bundle_id = '" . (int)$bundle_id . "'");
		foreach($data['product_bundle'] as $product_id){
			$this->db->query("INSERT INTO ".DB_PREFIX."bundle_product SET product_id = '".(int)$product_id."', bundle_id = '".(int)$bundle_id."'");
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "bundle_discount WHERE bundle_id = '" . (int)$bundle_id . "'");
		foreach($data['bundle_discount'] as $customer_group_id => $discount){
			$this->db->query("INSERT INTO ".DB_PREFIX."bundle_discount SET bundle_id = '".(int)$bundle_id."', customer_group_id = '".(int)$customer_group_id."', discount = '".(float)$discount['disount']."'");
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "bundle_store WHERE bundle_id = '" . (int)$bundle_id . "'");
		if (isset($data['bundle_store'])) {
			foreach ($data['bundle_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "bundle_store SET bundle_id = '" . (int)$bundle_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
	
	}

	public function deletebundle($bundle_id) {

		$this->db->query("DELETE FROM " . DB_PREFIX . "bundle WHERE bundle_id = '" . (int)$bundle_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "bundle_description WHERE bundle_id = '" . (int)$bundle_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "bundle_product WHERE bundle_id = '" . (int)$bundle_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "bundle_discount WHERE bundle_id = '" . (int)$bundle_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "bundle_store WHERE bundle_id = '" . (int)$bundle_id . "'");
		
		$this->cache->delete('bundle');

	}

	public function getbundle($bundle_id){
		$query = $this->db->query("SELECT * FROM ".DB_PREFIX."bundle b LEFT JOIN ".DB_PREFIX."bundle_description bd ON(b.bundle_id = bd.bundle_id) WHERE bd.language_id = '".(int)$this->config->get('config_language_id')."' AND b.bundle_id = '".(int)$bundle_id."'");
		
		return $query->row;
	}

	public function getBundles($data = array()){
	 	$sql = "SELECT * FROM ".DB_PREFIX."bundle b LEFT JOIN ".DB_PREFIX."bundle_description bd ON(b.bundle_id = bd.bundle_id) WHERE bd.language_id = '".(int)$this->config->get('config_language_id')."'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND bd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sql .= " GROUP BY b.bundle_id";

		$sort_data = array(
			'bd.name',
			'b.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY b.sort_order";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}
		
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);
		
		return $query->rows;
	}

	public function getbundleDescriptions($bundle_id) {
		$bundle_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_description WHERE bundle_id = '" . (int)$bundle_id . "'");

		foreach ($query->rows as $result) {
			$bundle_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'description'      => $result['description'],
			);
		}

		return $bundle_description_data;
	}

	public function getProductBundled($bundle_id) {
		$product_bundled_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_product WHERE bundle_id = '" . (int)$bundle_id . "'");

		foreach ($query->rows as $result) {
			$product_bundled_data[] = $result['product_id'];
		}

		return $product_bundled_data;
	}
	
	public function getbundlecategory($bundle_id) {
		$product_category_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_category WHERE bundle_id = '" . (int)$bundle_id . "'");

		foreach ($query->rows as $result) {
			$product_category_data[] = $result['category_id'];
		}

		return $product_category_data;
	}
	
	public function getbundleassignproduct($bundle_id) {
		$product_assignproduct_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_assignproduct WHERE bundle_id = '" . (int)$bundle_id . "'");

		foreach ($query->rows as $result) {
			$product_assignproduct_data[] = $result['product_id'];
		}

		return $product_assignproduct_data;
	}
	
	public function getBundleddiscount($bundle_id) {
		$product_reward_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_discount WHERE bundle_id = '" . (int)$bundle_id . "'");

		foreach ($query->rows as $result) {
			$product_reward_data[$result['customer_group_id']] = array('discount' => $result['discount']);
		}

		return $product_reward_data;
	}
	
	public function getTotalBundles() {
		$this->CreateTable();
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "bundle");

		return $query->row['total'];
		
	}

	public function getBundleStores($bundle_id) {
		$category_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_store WHERE bundle_id = '" . (int)$bundle_id . "'");

		foreach ($query->rows as $result) {
			$category_store_data[] = $result['store_id'];
		}

		return $category_store_data;
	}

}