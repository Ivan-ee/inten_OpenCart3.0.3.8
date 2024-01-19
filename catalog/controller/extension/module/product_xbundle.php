<?php
class ControllerExtensionModuleProductXbundle extends Controller {
	public function index($setting) {
		$this->load->language('module/product_xbundle');
		$this->load->language('extension/xbundle');
		
		$data['heading_title'] = $setting['name'];

		$data['text_tax'] = $this->language->get('text_tax');

		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');
		$data['text_option'] = $this->language->get('text_option');
		$data['text_regular_price'] = $this->language->get('text_regular_price');
		$data['text_bundle_price'] = $this->language->get('text_bundle_price');
		$data['text_you_save'] = $this->language->get('text_you_save');
		$data['text_buy_now'] = $this->language->get('text_buy_now');
		$data['text_select'] = $this->language->get('text_select');
		$data['button_upload'] = $this->language->get('button_upload');
		$data['text_loading'] = $this->language->get('text_loading');
		
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');
		$this->document->addStyle('catalog/view/javascript/xbundle/xbundle.css');
		$this->document->addScript('catalog/view/javascript/xbundle/countdown/jquery.downCount.js');
		$this->document->addStyle('catalog/view/javascript/xbundle/countdown/styles.css');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$data['xbundles'] = array();

		if (!$setting['limit']) {
			$setting['limit'] = 4;
		}
		$this->load->model('catalog/product');
		$this->load->model('extension/xbundle');
		$this->load->model('tool/image');
		$product_bundles = array_slice($setting['bundle'], 0, (int)$setting['limit']);
		foreach($product_bundles as $bundle_id){
			$xbundle = $this->model_extension_xbundle->getbundle($bundle_id);
			$total = 0;
			if($xbundle){
				$wproducts=array();
				foreach($xbundle['product'] as $product_id){
				  $product_info = $this->model_catalog_product->getProduct($product_id);
				  if($product_info){
					  if($product_info['image']){
						$image = $this->model_tool_image->resize($product_info['image'],$setting['width'], $setting['height']);
					  }else{
						$image = $this->model_tool_image->resize('placeholder.png',$setting['width'], $setting['height']);
					 }
					 
					 if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
						$price1 = $this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'));
					 } else {
						$price = false;
						$price1 = false;
					 }

					if ((float)$product_info['special']) {
						$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
						$special1 = $this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax'));
					} else {
						$special = false;
						$special1 = false;
					}

					if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
					} else {
						$tax = false;
					}
					
					$total += ($special1 ? $special1 : $price1) * (($product_info['minimum']>1) ? $product_info['minimum'] : 1);
					
					
					$options = array();

					foreach ($this->model_catalog_product->getProductOptions($product_id) as $option) {
						$product_option_value_data = array();

						foreach ($option['product_option_value'] as $option_value) {
							if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
								if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
								$oprice = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
								} else {
									$oprice = false;
								}

								$product_option_value_data[] = array(
									'product_option_value_id' => $option_value['product_option_value_id'],
									'option_value_id'         => $option_value['option_value_id'],
									'name'                    => $option_value['name'],
									'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),
									'price'                   => $oprice,
									'price_prefix'            => $option_value['price_prefix']
								);
							}
						}

						$options[] = array(
							'product_option_id'    => $option['product_option_id'],
							'product_option_value' => $product_option_value_data,
							'option_id'            => $option['option_id'],
							'name'                 => $option['name'],
							'type'                 => $option['type'],
							'value'                => $option['value'],
							'required'             => $option['required']
						);
					}
					
					if((float)$special1){
					  $percentange = ((float)$special1 && (float)$price1) ? (100 - ($special1 * 100 / $price1)) :'';
					}else{
					  $percentange = '';
					}
					  
					$wproducts[]=array(
						'product_id'  => $product_info['product_id'],
						'name'		  => $product_info['name'],
						'minimum'	  => $product_info['minimum'],
						'thumb'       => $image,
						'description' => utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('config_product_description_length')) . '..',
						'model'		  => $product_info['model'],
						'price'       => $price,
						'special'     => $special,
						'tax'		  => $tax,	
						'options'	  => $options,
						'percentange' => $percentange,
						'href'        => $this->url->link('product/product','&product_id=' . $product_info['product_id'],true)
					);
				  }
				}
				
				$bundleprice = '';
				$save = '';
				if($xbundle['discount']){
					$bundleprice = $total-$xbundle['discount'];
					$save = $total-$bundleprice;
				}else{
					$bundleprice = $total;
					$save = false;
				}
				
				$countdown_end_date = false;
				$special_date_start =  $xbundle['date_start'];
				$today = date('Y-m-d') ;
				if((strtotime($today)  >= strtotime($xbundle['date_start'])) && (strtotime($xbundle['date_start']) <= strtotime($xbundle['date_end'])) && ($xbundle['date_start']!='0000-00-00') && ($xbundle['date_end']!='0000-00-00')) {
					$countdown_end_date = date('m/d/Y h:i:s', strtotime($xbundle['date_end']));
				}
				if($wproducts){
					$data['xbundles'][]=array(
						'bundle_id'				=> $xbundle['bundle_id'],
						'discount'				=> $xbundle['discount'],
						'countdown_end_date'	=> $countdown_end_date,
						'total'					=> $this->currency->format($total, $this->session->data['currency']),
						'bundleprice'			=> $this->currency->format($bundleprice, $this->session->data['currency']),
						'save'					=> $this->currency->format($save, $this->session->data['currency']),
						'wproducts'				=> $wproducts,
						'name'					=> $xbundle['name'],
					);
				}
			}
		}

		if (count($data['xbundles']) > 1) {
			$this->document->addStyle('catalog/view/javascript/jquery/swiper/css/swiper.min.css');
			$this->document->addStyle('catalog/view/javascript/jquery/swiper/css/opencart.css');
			$this->document->addScript('catalog/view/javascript/jquery/swiper/js/swiper.jquery.js');
		}
		
		if($data['xbundles']){
			return $this->load->view('extension/module/product_xbundle', $data);
		}
	}
}