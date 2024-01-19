<?php
class ControllerExtensionbundle extends Controller {
	private $error = array();
	
	public function index() {
		$this->load->language('extension/bundle');

		$this->document->setTitle($this->language->get('heading_title_menu'));

		$this->load->model('extension/bundle');

		$this->getList();
	}

	public function add() {
		$this->load->language('extension/bundle');

		$this->document->setTitle($this->language->get('heading_title_menu'));

		$this->load->model('extension/bundle');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_bundle->addbundle($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/bundle', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('extension/bundle');

		$this->document->setTitle($this->language->get('heading_title_menu'));

		$this->load->model('extension/bundle');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_bundle->editbundle($this->request->get['bundle_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/bundle', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('extension/bundle');

		$this->document->setTitle($this->language->get('heading_title_menu'));

		$this->load->model('extension/bundle');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $bundle_id) {
				$this->model_extension_bundle->deletebundle($bundle_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/bundle', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	public function repair() {
		$this->load->language('extension/bundle');

		$this->document->setTitle($this->language->get('heading_title_menu'));

		$this->load->model('extension/bundle');

		if ($this->validateRepair()) {
			$this->model_extension_bundle->repairCategories();

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/bundle', 'user_token=' . $this->session->data['user_token'], true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title_menu'),
			'href' => $this->url->link('extension/bundle', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);
		
		$data['add'] = $this->url->link('extension/bundle/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('extension/bundle/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['repair'] = $this->url->link('extension/bundle/repair', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['bundles'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$bundle_total = $this->model_extension_bundle->getTotalBundles();

		$results = $this->model_extension_bundle->getBundles($filter_data);
		foreach ($results as $result){
			$data['bundles'][] = array(
				'bundle_id' => $result['bundle_id'],
				'name'        => $result['name'],
				'sort_order'  => $result['sort_order'],
				'edit'        => $this->url->link('extension/bundle/edit', 'user_token=' . $this->session->data['user_token'] . '&bundle_id=' . $result['bundle_id'] . $url, true),
				'delete'      => $this->url->link('extension/bundle/delete', 'user_token=' . $this->session->data['user_token'] . '&bundle_id=' . $result['bundle_id'] . $url, true)
			);
		}

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

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('extension/bundle', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_sort_order'] = $this->url->link('extension/bundle', 'user_token=' . $this->session->data['user_token'] . '&sort=sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $bundle_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/bundle', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($bundle_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($bundle_total - $this->config->get('config_limit_admin'))) ? $bundle_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $bundle_total, ceil($bundle_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/bundle_list', $data));
	}

	protected function getForm(){
		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_form'] = !isset($this->request->get['bundle_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/bundle', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);
		
		if (!isset($this->request->get['bundle_id'])) {
			$data['action'] = $this->url->link('extension/bundle/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/bundle/edit', 'user_token=' . $this->session->data['user_token'] . '&bundle_id=' . $this->request->get['bundle_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('extension/bundle', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['bundle_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$bundle_info = $this->model_extension_bundle->getbundle($this->request->get['bundle_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['bundle_description'])) {
			$data['bundle_description'] = $this->request->post['bundle_description'];
		} elseif (isset($this->request->get['bundle_id'])) {
			$data['bundle_description'] = $this->model_extension_bundle->getbundleDescriptions($this->request->get['bundle_id']);
		} else {
			$data['bundle_description'] = array();
		}
		if (isset($this->request->post['date_start'])) {
			$data['date_start'] = $this->request->post['date_start'];
		} elseif (!empty($bundle_info)) {
			$data['date_start'] = ($bundle_info['date_start'] != '0000-00-00') ? $bundle_info['date_start'] : '';
		} else {
			$data['date_start'] = '0000-00-00';
		}	

		if (isset($this->request->post['date_end'])) {
			$data['date_end'] = $this->request->post['date_end'];	
		} elseif (!empty($bundle_info)) {	
			$data['date_end'] = ($bundle_info['date_end'] != '0000-00-00') ? $bundle_info['date_end'] : '';	
		} else {
			$data['date_end'] = '0000-00-00';	
		}		
		
		if (isset($this->request->post['top'])) {
			$data['top'] = $this->request->post['top'];
		} elseif (!empty($bundle_info)) {
			$data['top'] = $bundle_info['top'];
		} else {
			$data['top'] = 0;
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($bundle_info)) {
			$data['sort_order'] = $bundle_info['sort_order'];
		} else {
			$data['sort_order'] = 0;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($bundle_info)) {
			$data['status'] = $bundle_info['status'];
		} else {
			$data['status'] = true;
		}
		
		$this->load->model('catalog/product');
		
		if (isset($this->request->post['product_bundle'])) {
			$products = $this->request->post['product_bundle'];
		} elseif (isset($this->request->get['bundle_id'])) {
			$products = $this->model_extension_bundle->getProductBundled($this->request->get['bundle_id']);
		} else {
			$products = array();
		}

		$data['product_bundles'] = array();

		foreach ($products as $product_id){
			$bundle_infos = $this->model_catalog_product->getProduct($product_id);

			if ($bundle_infos) {
				$data['product_bundles'][] = array(
					'product_id' => $bundle_infos['product_id'],
					'name'       => $bundle_infos['name']
				);
			}
		}
		
		if (isset($this->request->post['bundle_discount'])) {
			$data['bundle_discount'] = $this->request->post['bundle_discount'];
		} elseif (isset($this->request->get['bundle_id'])) {
			$data['bundle_discount'] = $this->model_extension_bundle->getBundleddiscount($this->request->get['bundle_id']);
		} else {
			$data['bundle_discount'] = array();
		}
		
		$this->load->model('catalog/category');
		if (isset($this->request->post['category'])) {
			$categories = $this->request->post['category'];
		} elseif (isset($this->request->get['bundle_id'])) {
			$categories = $this->model_extension_bundle->getbundlecategory($this->request->get['bundle_id']);
		} else {
			$categories = array();
		}
		
		$data['product_categories']=array();
		
		foreach ($categories as $category_id) {
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$data['product_categories'][] = array(
					'category_id' => $category_info['category_id'],
					'name' => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
				);
			}
		}
		
		
		if (isset($this->request->post['product'])) {
			$products = $this->request->post['product'];
		} elseif (isset($this->request->get['bundle_id'])) {
			$products = $this->model_extension_bundle->getbundleassignproduct($this->request->get['bundle_id']);
		} else {
			$products = array();
		}
		
		$data['product_relateds'] = array();

		foreach ($products as $product_id){
			$related_info = $this->model_catalog_product->getProduct($product_id);

			if ($related_info) {
				$data['product_relateds'][] = array(
					'product_id' => $related_info['product_id'],
					'name'       => $related_info['name']
				);
			}
		}

		$this->load->model('customer/customer_group');
		$data['customergroups'] = $this->model_customer_customer_group->getCustomerGroups(array());
		
		$this->load->model('setting/store');
		$data['stores'] = $this->model_setting_store->getStores();
		
		if (isset($this->request->post['bundle_store'])) {
			$data['bundle_store'] = $this->request->post['bundle_store'];
		} elseif (isset($this->request->get['bundle_id'])) {
			$data['bundle_store'] = $this->model_extension_bundle->getBundleStores($this->request->get['bundle_id']);
		} else {
			$data['bundle_store'] = array(0);
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/bundle_form', $data));
	}
	
	public function autocomplete(){
		$json = array();

		if (isset($this->request->get['filter_name'])){
			$this->load->model('extension/bundle');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_extension_bundle->getBundles($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'bundle_id'   => $result['bundle_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/bundle')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}
	
	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/bundle')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}
}