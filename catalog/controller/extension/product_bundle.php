<?php
class ControllerExtensionProductBundle extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/xbundle');
		$this->load->language('product/product');
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home','',true)
		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/xbundle','',true)
		);
		
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');
		$this->document->addStyle('catalog/view/javascript/xbundle/xbundle.css');
		$this->document->addScript('catalog/view/javascript/xbundle/countdown/jquery.downCount.js');
		$this->document->addStyle('catalog/view/javascript/xbundle/countdown/styles.css');
		
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
		
		if(!empty($module_info['xbundle_status'])){
			$xbundle_status = $module_info['xbundle_status'];
		}else{
			$xbundle_status = 0;
		}
		
		if(!empty($module_info['xbundle_imit'])){
			$xbundle_imit = $module_info['xbundle_imit'];
		}else{
			$xbundle_imit = 10;
		}
		
		if(!empty($module_info['xbundle_description'][$this->config->get('config_language_id')])){
			$xbundle_description = $module_info['xbundle_description'][$this->config->get('config_language_id')];
		}else{
			$xbundle_description = array();
		}
		
		if(!empty($xbundle_description['name'])){
			$data['heading_title'] = $xbundle_description['name'];
		}else{
			$data['heading_title'] = $this->language->get('heading_title');
		}
		
		$data['text_tax'] = $this->language->get('text_tax');
		$data['text_option'] = $this->language->get('text_option');
		$data['text_select'] = $this->language->get('text_select');
		$data['button_upload'] = $this->language->get('button_upload');
		$data['text_loading'] = $this->language->get('text_loading');
		$data['text_regular_price'] = $this->language->get('text_regular_price');
		$data['text_bundle_price'] = $this->language->get('text_bundle_price');
		$data['text_you_save'] = $this->language->get('text_you_save');
		$data['text_buy_now'] = $this->language->get('text_buy_now');
		
		if(!empty($xbundle_description['meta_title'])){
			$meta_title = $xbundle_description['meta_title'];
		}else{
			$meta_title = $this->language->get('heading_title');
		}
		
		if(!empty($xbundle_description['meta_description'])){
			$meta_description = $xbundle_description['meta_description'];
		}else{
			$meta_description = '';
		}
		
		if(!empty($xbundle_description['meta_keyword'])){
			$meta_keyword = $xbundle_description['meta_keyword'];
		}else{
			$meta_keyword = '';
		}
		
		if(!empty($xbundle_description['description'])){
			$data['description'] = html_entity_decode($xbundle_description['description'], ENT_QUOTES, 'UTF-8');
		}else{
			$data['description'] = '';
		}
		
		$this->document->setTitle($meta_title);
		$this->document->setDescription($meta_description);
		$this->document->setKeywords($meta_keyword);
		
		if(isset($this->request->get['bundle_id'])){
		  $bundle_id = $this->request->get['bundle_id'];
		}else{
		  $bundle_id = 0;
		}
		
		$data['xbundles']=array();
		$this->load->model('catalog/product');
		$this->load->model('extension/xbundle');
		$this->load->model('tool/image');
		
		$xbundle = $this->model_extension_xbundle->getbundle($bundle_id);
		if(isset($xbundle)){
			$this->document->setTitle($xbundle['name']);
			$total = 0;
				$wproducts=array();
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
					  $percentange = ((float)$special1 && (float)$price1) ? 100 - ($special1 * 100 / $price1) :'';
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
						'href'        => $this->url->link('product/product','&product_id=' . $product_info['product_id'], true)
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
				
					$data['xbundles'][]=array(
						'bundle_id'	=> $xbundle['bundle_id'],
						'discount'		=> $xbundle['discount'],
						'name'			=> $xbundle['name'],
						'description'	=> html_entity_decode($xbundle['description'], ENT_QUOTES, 'UTF-8'),
						'countdown_end_date'	=> $countdown_end_date,
						'total'			=> $this->currency->format($total, $this->session->data['currency']),
						'bundleprice'	=> $this->currency->format($bundleprice, $this->session->data['currency']),
						'save'			=> $this->currency->format($save, $this->session->data['currency']),
						'wproducts'		=> $wproducts,
					);
			
		}
		
		
		
		
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		
			$this->response->setOutput($this->load->view('extension/product_bundle', $data));
	}
	
	public function addtocart(){
		$json = array();
		$this->load->model('extension/xbundle');
		$this->load->model('catalog/product');
		$this->load->language('checkout/cart');
		if(isset($this->request->get['bundle_id'])){
			$xbundles = $this->model_extension_xbundle->getbundle($this->request->get['bundle_id']);
			
			if($xbundles['product']){
				foreach($xbundles['product'] as $product_id){
						$success = false;
						$product_info = $this->model_catalog_product->getProduct($product_id);
						if ($product_info){
							if (isset($this->request->post['option'])) {
								$option = array_filter($this->request->post['option']);
							} else {
								$option = array();
							}

							$product_options = $this->model_catalog_product->getProductOptions($product_id);

							foreach ($product_options as $product_option) {
								if ($product_option['required'] && empty($option[$product_option['product_option_id']])) {
									$json['error']['option'][$product_option['product_option_id']] = sprintf($this->language->get('error_required'), $product_option['name']);
								}
							}
						}
				}
				foreach($xbundles['product'] as $product_id){
					$product_info = $this->model_catalog_product->getProduct($product_id);
					if ($product_info){
						if(!$json){
							$this->cart->add($product_id, $product_info['minimum'], $option, 0);
							$success = true;
						}
					}
				}
				
				if($success){
					$json['success'] = true;
				}
			}
		}
		print_r(json_encode($json));
	}
}