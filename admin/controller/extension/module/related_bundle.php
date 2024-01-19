<?php
class ControllerExtensionModuleRelatedBundle extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/related_bundle');

		$this->document->setTitle($this->language->get('heading_title1'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_related_bundle', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		$data['heading_title'] = $this->language->get('heading_title1');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/related_bundle', 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/related_bundle', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/related_bundle', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/module/related_bundle', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
		}

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_related_bundle_status'])) {
			$data['module_related_bundle_status'] = $this->request->post['module_related_bundle_status'];
		} else {
			$data['module_related_bundle_status'] = $this->config->get('module_related_bundle_status');
		}
		
		if (isset($this->request->post['module_related_bundle_limit'])) {
			$data['module_related_bundle_limit'] = $this->request->post['module_related_bundle_limit'];
		} else {
			$data['module_related_bundle_limit'] = $this->config->get('module_related_bundle_limit');
		}
		
		if (isset($this->request->post['module_related_bundle_random'])) {
			$data['module_related_bundle_random'] = $this->request->post['module_related_bundle_random'];
		} else {
			$data['module_related_bundle_random'] = $this->config->get('module_related_bundle_random');
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		
		$this->response->setOutput($this->load->view('extension/module/related_bundle', $data));
	}

	protected function validate(){
		if (!$this->user->hasPermission('modify', 'extension/module/related_bundle')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}
}