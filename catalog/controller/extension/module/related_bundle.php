<?php
class ControllerExtensionModuleRelatedbundle extends Controller {
	public function index() {
		$this->load->language('extension/xbundle');
		$this->load->language('product/product');

		$data['heading_title'] = $this->language->get('heading_title');
		
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');
		$this->document->addStyle('catalog/view/javascript/xbundle/xbundle.css');
		$this->document->addScript('catalog/view/javascript/xbundle/countdown/jquery.downCount.js');
		$this->document->addStyle('catalog/view/javascript/xbundle/countdown/styles.css');
		
		$data['xbundles']=array();
		$this->load->model('catalog/product');
		$this->load->model('extension/xbundle');
		$this->load->model('tool/image');
		
		
		$data['text_tax'] = $this->language->get('text_tax');
		$data['text_loading'] = $this->language->get('text_loading');
		$data['text_option'] = $this->language->get('text_option');
		$data['text_select'] = $this->language->get('text_select');
		$data['button_upload'] = $this->language->get('button_upload');
		$data['text_regular_price'] = $this->language->get('text_regular_price');
		$data['text_bundle_price'] = $this->language->get('text_bundle_price');
		$data['text_you_save'] = $this->language->get('text_you_save');
		$data['text_buy_now'] = $this->language->get('text_buy_now');
		
		$this->load->model('setting/setting');
		
		$module_info = $this->model_setting_setting->getSetting('xbundle');
		
		if(!empty($module_info['xbundle_height'])){
			$xbundle_height = $module_info['xbundle_height'];
		}else{
			$xbundle_height = 150;
		}
		
		if(!empty($module_info['xbundle_width'])){
			$xbundle_width = $module_info['xbundle_width'];
		}else{
			$xbundle_width = 150;
		}
		
		if(!empty($module_info['xbundle_imit'])){
			$xbundle_imit = $module_info['xbundle_imit'];
		}else{
			$xbundle_imit = 5;
		}
		
		$filterdata=array(
		  'limit' => $xbundle_imit,
		  'start' => 0
		);
		
		
		$category_id = 0;
		
		if(isset($this->request->get['path'])){
			$path = '';

			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);
		}
		$product_id = '';
		if(isset($this->request->get['product_id'])){
		 $product_id = $this->request->get['product_id'];
		}
	
		$filter_data=array(
			'filter_category' => $category_id,
			'filter_product'  => $product_id,
			'start'			  => 0, 	
			'limit'			  => $this->config->get('module_related_bundle_limit'),
			'random'		  => $this->config->get('module_related_bundle_random'),
		);		
		
		$xbundle_product = $this->model_extension_xbundle->getbundles($filter_data);
		if(isset($xbundle_product)){
			foreach($xbundle_product as $xbundle){
				$total = 0;
				$wproducts=array();
				if($xbundle){
				foreach($xbundle['product'] as $product_id){
				  $product_info = $this->model_catalog_product->getProduct($product_id);
				  if($product_info){
					  if($product_info['image']){
						$image = $this->model_tool_image->resize($product_info['image'],$xbundle_width, $xbundle_height);
					  }else{
						$image = $this->model_tool_image->resize('placeholder.png', $xbundle_width, $xbundle_height);
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
								$oprice = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
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
					  $percentange = ((float)$special1 && (float)$price1) ? (100 - ($special1 * 100 / $price1)) : '';
					}else{
					  $percentange = '';
					}
					  
					$wproducts[]=array(
						'product_id'  => $product_info['product_id'],
						'name'		  => $product_info['name'],
						'thumb'       => $image,
						'minimum'	  => $product_info['minimum'],
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
						'bundle_id'		=> $xbundle['bundle_id'],
						'discount'		=> $xbundle['discount'],
						'countdown_end_date'	=> $countdown_end_date,
						'total'			=> $this->currency->format($total, $this->session->data['currency']),
						'bundleprice'	=> $this->currency->format($bundleprice, $this->session->data['currency']),
						'save'			=> $this->currency->format($save, $this->session->data['currency']),
						'wproducts'		=> $wproducts,
						'name'			=> $xbundle['name'],
					);
				}
			}
			}
		}

		if (count($data['xbundles']) > 1) {
			$this->document->addStyle('catalog/view/javascript/jquery/swiper/css/swiper.min.css');
			$this->document->addStyle('catalog/view/javascript/jquery/swiper/css/opencart.css');
			$this->document->addScript('catalog/view/javascript/jquery/swiper/js/swiper.jquery.js');
		}
		
		return $this->load->view('extension/module/related_bundle', $data);
	}
}