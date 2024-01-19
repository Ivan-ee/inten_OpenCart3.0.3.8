<?php
class ModelExtensionTotalProductBundle extends Model {
	
	public function getTotal($total){
		$cartProducts = $this->cart->getProducts();
		if ($cartProducts){	
			
			$cp = array();
			foreach($cartProducts as $product){
				$cp[$product['product_id']] = $product;
			}

			$bundles = array();
			$this->load->model('extension/xbundle');
			$bundles = $this->model_extension_xbundle->getallbundlebycart($cartProducts);

			if(!empty($bundles)){

				$this->load->language('extension/total/product_bundle');
				$totaldiscount = 0;
				
				foreach($bundles as $bundle){
					$multiple = array();
					foreach($bundle['product'] as $product_id) {
						$multiple[$product_id] = intval($cp[$product_id]['quantity'] / $cp[$product_id]['minimum']);
					}

					$totaldiscount += (float)$bundle['discount'] * min($multiple);
				}
				
				$total['totals'][] = array(
					'code'       => 'product_bundle',
					'title'      => $this->language->get('bundle_title'),
					'value'      => -$totaldiscount,
					'sort_order' => $this->config->get('total_product_bundle_sort_order')
				);
		
				$total['total'] -= (float)$totaldiscount;
			
			}
	  	}
	}
}