<?php
class ModelExtensionXbundle extends Model {
	public function getbundlebyproductid($product_id){
		$query = $this->db->query("SELECT *,(SELECT discount FROM ".DB_PREFIX."bundle_discount WHERE bundle_id = w.bundle_id AND customer_group_id = '".(int)$this->config->get('config_customer_group_id')."') AS discount FROM ".DB_PREFIX."bundle w LEFT JOIN ".DB_PREFIX."bundle_product wp ON(w.bundle_id = wp.bundle_id) WHERE wp.product_id='".(int)$product_id."' AND ((w.date_start = '0000-00-00' OR w.date_start < NOW()) AND (w.date_end = '0000-00-00' OR w.date_end > NOW()))");
		
		$bundles=array();
		
		foreach($query->rows as $row){
			$product=array();
			$wquery = $this->db->query("SELECT * FROM ".DB_PREFIX."bundle_product WHERE bundle_id = '".(int)$row['bundle_id']."'");
			foreach($wquery->rows as $prow){
				$product[]= $prow['product_id'];
			}
			
			$bundles[]=array(
			  'xbundle_id'	  => $row['xbundle_id'],
			  'discount' 	  => $row['discount'],
			  'name' 	  	  => $row['name'],
			  'date_start' 	  => $row['date_start'],
			  'date_end' 	  => $row['date_end'],
			  'top' 	  	  => $row['top'],
			  'product'		  => $product,	
			);
		}
		return $bundles;
	}
	
	public function getbundles($data=array()){
		$sql = "SELECT *,(SELECT discount FROM ".DB_PREFIX."bundle_discount WHERE bundle_id = b.bundle_id AND customer_group_id = '".(int)$this->config->get('config_customer_group_id')."') AS discount FROM ".DB_PREFIX."bundle b LEFT JOIN ".DB_PREFIX."bundle_description bd ON(b.bundle_id = bd.bundle_id) LEFT JOIN ".DB_PREFIX."bundle_store bs ON(b.bundle_id = bs.bundle_id)";
		
		
		if(!empty($data['filter_category'])&&$data['filter_category']!=0){
			$sql .= " LEFT JOIN ".DB_PREFIX."bundle_category bc ON(b.bundle_id = bc.bundle_id)";
		}
		
		if(!empty($data['filter_product'])){
			$sql .= " LEFT JOIN ".DB_PREFIX."bundle_assignproduct bp ON(b.bundle_id = bp.bundle_id)";
		}
		
		$sql .= " WHERE bd.language_id = '".$this->config->get('config_language_id')."' AND bs.store_id = '".$this->config->get('config_store_id')."'";
		
		if(!empty($data['filter_category'])&&$data['filter_category']!=0&&!empty($data['filter_product'])){
			$sql .=" AND (bc.category_id = '".(int)$data['filter_category']."' OR bp.product_id = '".(int)$data['filter_product']."')";
		} else {
			if(!empty($data['filter_category'])&&$data['filter_category']!=0){
			  $sql .=" AND bc.category_id = '".(int)$data['filter_category']."'";
			 } 
			
			if(!empty($data['filter_product'])){
			  $sql .=" AND bp.product_id = '".(int)$data['filter_product']."'";
			}
		}
		$sql .= " AND ((b.date_start = '0000-00-00' OR b.date_start < NOW()) AND (b.date_end = '0000-00-00' OR b.date_end > NOW()))";

		if(!empty($data['filter_category']) || !empty($data['filter_product'])){
			$sql .= " GROUP BY b.bundle_id";
		}
		
		if(!empty($data['random'])){
			$sql .=" ORDER BY rand()";	
		} else {
			$sql .=" ORDER BY b.sort_order";
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
		
		$bundles=array();
		if($query->rows){
			foreach($query->rows as $row){
				$product=array();
				$wquery = $this->db->query("SELECT * FROM ".DB_PREFIX."bundle_product WHERE bundle_id = '".(int)$row['bundle_id']."'");
				foreach($wquery->rows as $prow){
					$product[]= $prow['product_id'];
				}
				
				$bundles[]=array(
				  'bundle_id'	  => $row['bundle_id'],
				  'product'		  => $product,	
				  'discount'	  => $row['discount'],
				  'name' 	  	  => $row['name'],
				  'top' 	  	  => $row['top'],
				  'date_start' 	  => $row['date_start'],
				  'date_end' 	  => $row['date_end'],
				);
			}
		}
		
		return $bundles;
	}
	
	public function getTotalbundles($data=array()){
		$sql = "SELECT COUNT(*) AS total FROM ".DB_PREFIX."bundle b LEFT JOIN ".DB_PREFIX."bundle_description bd ON(b.bundle_id = bd.bundle_id) LEFT JOIN ".DB_PREFIX."bundle_store bs ON(b.bundle_id = bs.bundle_id) WHERE bd.language_id = '".$this->config->get('config_language_id')."' AND bs.store_id = '".$this->config->get('config_store_id')."'";
		
		$query = $this->db->query($sql);
		
		return $query->row['total'];
	}
	
	public function getbundle($bundle_id){
		$sql = "SELECT *,(SELECT discount FROM ".DB_PREFIX."bundle_discount WHERE bundle_id = b.bundle_id AND customer_group_id = '".(int)$this->config->get('config_customer_group_id')."') AS discount FROM ".DB_PREFIX."bundle b LEFT JOIN ".DB_PREFIX."bundle_description bd ON(b.bundle_id = bd.bundle_id) LEFT JOIN ".DB_PREFIX."bundle_store bs ON(b.bundle_id = bs.bundle_id) WHERE bd.language_id = '".$this->config->get('config_language_id')."' AND b.bundle_id = '".(int)$bundle_id."' AND ((b.date_start = '0000-00-00' OR b.date_start < NOW()) AND (b.date_end = '0000-00-00' OR b.date_end > NOW())) AND bs.store_id = '".$this->config->get('config_store_id')."'";
		$query = $this->db->query($sql);
		$bundles=array();
		if($query->row){
			$product=array();
			$wquery = $this->db->query("SELECT * FROM ".DB_PREFIX."bundle_product WHERE bundle_id = '".(int)$query->row['bundle_id']."'");
			foreach($wquery->rows as $prow){
				$product[]= $prow['product_id'];
			}
			
			$bundles=array(
			  'bundle_id'	  => $query->row['bundle_id'],
			  'discount' 	  => $query->row['discount'],
			  'name' 	  	  => $query->row['name'],
			  'description'   => $query->row['description'],
			  'top' 	  	  => $query->row['top'],
			  'product'		  => $product,	
			  'date_start' 	  => $query->row['date_start'],
			  'date_end' 	  => $query->row['date_end'],
			);
		}
		
		return $bundles;
	}
	
	public function getcurrentproducts($arranagebundles){
		$allbundles = array();
		if (isset($arranagebundles)) {
			foreach ($arranagebundles as $k => $result) {
				$allbundles[$k] = $result;
			}
		}
		return $allbundles;
	}
	
	public function getcartproductqty(){
		$cartProducts = $this->cart->getProducts();
		$qty = array();
		
		foreach($cartProducts as $product){
			if (!empty($qty[$product['product_id']])){
				$qty[$product['product_id']] += $product['quantity'];
			} else {
				$qty[$product['product_id']] = $product['quantity'];
			}
		}
		
		return  $qty;
	}
	
	public function getselectedproducts($bundles){
		$cartQtesX = $this->getcartproductqty();
		$selectedbundles = array();
		foreach($bundles as $bundle){
			$bundleprd = array();
			foreach($bundle['product'] as $product_id) {
				$minimum = $this->db->query("SELECT minimum FROM ".DB_PREFIX."product WHERE product_id =".$product_id);
				if ($minimum->num_rows) $min = $minimum->row['minimum'];
				if(!empty($bundleprd[$product_id])) {
					$bundleprd[$product_id]++;
				}  else {
					$bundleprd[$product_id] = $min;
				}
			}
			while(true){
				foreach($bundleprd as $pid => $qty){
					if (!array_key_exists($pid, $cartQtesX)){
						continue 3;
					}elseif($qty > $cartQtesX[$pid]){
						continue 3;
					}
				}
				foreach($bundleprd as $pid => $qty){
					$cartQtesX[$pid] -= $qty;
				}
				$selectedbundles[][] = $bundle;
			}
		}
		return $selectedbundles;
	}

	public function getallbundlebycart($cartProducts){
		$pid_array = array();
		foreach($cartProducts as $pid){
			$pid_array[] = $pid['product_id'];
		}
		$pid_list = implode(',',$pid_array);

		$query = $this->db->query("SELECT *,(SELECT discount FROM ".DB_PREFIX."bundle_discount bds WHERE bds.bundle_id = w.bundle_id AND bds.customer_group_id = '".(int)$this->config->get('config_customer_group_id')."') AS discount FROM ".DB_PREFIX."bundle w LEFT JOIN ".DB_PREFIX."bundle_product wp ON(w.bundle_id = wp.bundle_id) WHERE wp.product_id IN (".$pid_list.") AND ((w.date_start = '0000-00-00' OR w.date_start < NOW()) AND (w.date_end = '0000-00-00' OR w.date_end > NOW())) GROUP BY w.bundle_id");
		
		$bundles = array();
		
		foreach($query->rows as $row){
			$product = array();
			$bad = 0;
			$wquery = $this->db->query("SELECT * FROM ".DB_PREFIX."bundle_product WHERE bundle_id = '".(int)$row['bundle_id']."'");
			foreach($wquery->rows as $prow){
				if (!in_array($prow['product_id'], $pid_array)) { 
					$bad = 1; 
					break;
				}
				$product[] = $prow['product_id'];
			}
			if (!$bad)
			$bundles[] = array(
			  'bundle_id'	  => $row['bundle_id'],
			  'discount' 	  => $row['discount'],
			  'product'		  => $product,	
			);
		}
		return $bundles;
	}
}