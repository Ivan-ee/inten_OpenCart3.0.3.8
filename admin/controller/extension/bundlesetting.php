<?php
class ControllerExtensionBundlesetting extends Controller {
	private $error = array();

	public function index(){
		$this->load->language('extension/bundlesetting');

		$this->document->setTitle($this->language->get('heading_title_menu'));

		$this->load->model('setting/setting');
		$this->load->model('extension/bundle');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('xbundle', $this->request->post);
			if(isset($this->request->post['xbundle_seo_url'])){
				foreach ($this->request->post['xbundle_seo_url'] as $store_id => $language) {
					foreach ($language as $language_id => $keyword) {
						$query = 'extension/xbundle';
						if(!empty($keyword)){
							$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = '" . $query . "'");
							$this->db->query("INSERT INTO `" . DB_PREFIX . "seo_url` SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = '" . $this->db->escape($query) . "', keyword = '" . $this->db->escape($keyword) . "'");
						}
					}
				}
			}
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/bundlesetting', 'user_token=' . $this->session->data['user_token'], true));
		}

		$data['heading_title'] = $this->language->get('heading_title1');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}
		
		if (isset($this->error['product'])) {
			$data['error_product'] = $this->error['product'];
		} else {
			$data['error_product'] = array();
		}

		if (isset($this->error['meta_title'])) {
			$data['error_meta_title'] = $this->error['meta_title'];
		} else {
			$data['error_meta_title'] = array();
		}
		
		if (isset($this->error['keyword'])) {
			$data['error_keyword'] = $this->error['keyword'];
		} else {
			$data['error_keyword'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title_menu'),
			'href' => $this->url->link('extension/bundlesetting', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/bundlesetting', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('extension/module', 'user_token=' . $this->session->data['user_token'], true);

		if (isset($this->request->post['xbundle_status'])) {
			$data['xbundle_status'] = $this->request->post['xbundle_status'];
		} else {
			$data['xbundle_status'] = $this->config->get('xbundle_status');
		}
		
		if (isset($this->request->post['xbundle_height'])) {
			$data['xbundle_height'] = $this->request->post['xbundle_height'];
		} else {
			$data['xbundle_height'] = $this->config->get('xbundle_height');
		}
		
		if (isset($this->request->post['xbundle_width'])) {
			$data['xbundle_width'] = $this->request->post['xbundle_width'];
		} else {
			$data['xbundle_width'] = $this->config->get('xbundle_width');
		}
		
		if (isset($this->request->post['xbundle_imit'])) {
			$data['xbundle_imit'] = $this->request->post['xbundle_imit'];
		} else {
			$data['xbundle_imit'] = $this->config->get('xbundle_imit');
		}
		
		if (isset($this->request->post['xbundle_column'])) {
			$data['xbundle_column'] = $this->request->post['xbundle_column'];
		} else {
			$data['xbundle_column'] = 1;
		}
		
		if (isset($this->request->post['xbundle_description'])){
			$data['xbundle_description'] = $this->request->post['xbundle_description'];
		} elseif($this->config->get('xbundle_description')){
			$data['xbundle_description'] = $this->config->get('xbundle_description');
		}else{
			$data['xbundle_description'] = array();
		}
		
		if (isset($this->request->post['xbundle_seo_url'])){
			$data['xbundle_seo_url'] = $this->request->post['xbundle_seo_url'];
		} elseif($this->config->get('xbundle_seo_url')){
			$data['xbundle_seo_url'] = $this->config->get('xbundle_seo_url');
		}else{
			$data['xbundle_seo_url'] = array();
		}
		
		$data['load'] = $this->load;
		
		$this->load->model('setting/store');

		$data['stores'] = array();
		
		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		);
		
		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			);
		}
			
		$data['user_token'] = $this->session->data['user_token'];
		
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/bundlesetting', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/bundlesetting')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		foreach ($this->request->post['xbundle_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 2) || (utf8_strlen($value['name']) > 255)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}

			if ((utf8_strlen($value['meta_title']) < 3) || (utf8_strlen($value['meta_title']) > 255)) {
				$this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
			}
		}
		
		return !$this->error;
	}
}