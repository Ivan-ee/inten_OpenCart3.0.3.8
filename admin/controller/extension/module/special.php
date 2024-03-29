<?php

class ControllerExtensionModuleSpecial extends Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('extension/module/special');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/module');

        $this->load->model('catalog/product');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            $productSpecial = !empty($this->request->post['product_special']) ? $this->request->post['product_special'] : array();

            $module_id = $this->request->get['module_id'] ? $this->request->get['module_id'] : 0;

            foreach ($productSpecial as $productId => $specialPrice) {
                $startDate = $this->request->post['start_date'];
                $endDate = $this->request->post['end_date'];

                $isProductSpecial = $this->model_catalog_product->isProductSpecialWithId($productId, $module_id);

                if ($isProductSpecial) {
                    $this->model_catalog_product->updateProductSpecial($productId, $module_id, $startDate,$endDate, $specialPrice);
                } else {
                    $this->model_catalog_product->setProductSpecial($productId, $module_id, $startDate,$endDate, $specialPrice);
                }
            }

            if (!isset($this->request->get['module_id'])) {
                $this->model_setting_module->addModule('special', $this->request->post);
            } else {
                $this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
            }

            $this->cache->delete('product');

            $this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = '';
        }

        if (isset($this->error['start_date'])) {
            $data['error_start_date'] = $this->error['start_date'];
        } else {
            $data['error_start_date'] = '';
        }

        if (isset($this->error['end_date'])) {
            $data['error_end_date'] = $this->error['end_date'];
        } else {
            $data['error_end_date'] = '';
        }

        if (isset($this->error['page_image'])) {
            $data['error_page_image'] = $this->error['page_image'];
        } else {
            $data['error_page_image'] = '';
        }

        if (isset($this->error['special_desk'])) {
            $data['error_special_desk'] = $this->error['special_desk'];
        } else {
            $data['error_special_desk'] = '';
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
                'href' => $this->url->link('extension/module/special', 'user_token=' . $this->session->data['user_token'], true)
            );
        } else {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/module/special', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
            );
        }

        if (!isset($this->request->get['module_id'])) {
            $data['action'] = $this->url->link('extension/module/special', 'user_token=' . $this->session->data['user_token'], true);
        } else {
            $data['action'] = $this->url->link('extension/module/special', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
        }

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
        }

        $data['user_token'] = $this->session->data['user_token'];

        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($module_info)) {
            $data['name'] = $module_info['name'];
        } else {
            $data['name'] = '';
        }

        if (isset($this->request->post['page_image'])) {
            $data['page_image'] = $this->request->post['page_image'];
        } elseif (!empty($module_info)) {
            $data['page_image'] = $module_info['page_image'];
        } else {
            $data['page_image'] = '';
        }

        if (isset($this->request->post['block_image'])) {
            $data['block_image'] = $this->request->post['block_image'];
        } elseif (!empty($module_info)) {
            $data['block_image'] = $module_info['block_image'];
        } else {
            $data['block_image'] = '';
        }

        if (isset($this->request->post['description'])) {
            $data['description'] = $this->request->post['description'];
        } elseif (!empty($module_info)) {
            $data['description'] = $module_info['description'];
        } else {
            $data['description'] = '';
        }

        if (isset($this->request->post['special_desk'])) {
            $data['special_desk'] = $this->request->post['special_desk'];
        } elseif (!empty($module_info)) {
            $data['special_desk'] = $module_info['special_desk'];
        } else {
            $data['special_desk'] = '';
        }

        if (isset($this->request->post['start_date'])) {
            $data['start_date'] = $this->request->post['start_date'];
        } elseif (!empty($module_info)) {
            $data['start_date'] = $module_info['start_date'];
        } else {
            $data['start_date'] = '';
        }

        if (isset($this->request->post['end_date'])) {
            $data['end_date'] = $this->request->post['end_date'];
        } elseif (!empty($module_info)) {
            $data['end_date'] = $module_info['end_date'];
        } else {
            $data['end_date'] = '';
        }

        $this->load->model('catalog/product');

        $data['products'] = array();

        if (!empty($this->request->post['product'])) {
            $products = $this->request->post['product'];
        } elseif (!empty($module_info['product'])) {
            $products = $module_info['product'];
        } else {
            $products = array();
        }

        $this->load->model('tool/image');

        foreach ($products as $product_id) {
            $product_info = $this->model_catalog_product->getProduct($product_id);
            $product_info_special = $this->model_catalog_product->getProductSpecialsWithId($product_id, $this->request->get['module_id']);

            if ($product_info) {
                $data['products'][] = array(
                    'product_id' => $product_info['product_id'],
                    'name' => $product_info['name'],
                    'price' => $product_info['price'],
                    'special' => !empty($product_info_special) ? $product_info_special[0]['price'] : $product_info['price'],
                    'image' => $this->model_tool_image->resize($product_info['image'], 100, 100),
                );
            }
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($module_info)) {
            $data['status'] = $module_info['status'];
        } else {
            $data['status'] = '';
        }

        $data['module_id'] = $this->request->get['module_id'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/special', $data));
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/special')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_name');
        }

        if ((utf8_strlen($this->request->post['start_date']) < 3)) {
            $this->error['start_date'] = $this->language->get('error_start_date');
        }

        if ((utf8_strlen($this->request->post['end_date']) < 3)) {
            $this->error['end_date'] = $this->language->get('error_end_date');
        }

        if ((utf8_strlen($this->request->post['page_image']) < 3)) {
            $this->error['page_image'] = $this->language->get('error_page_image');
        }

        if ((utf8_strlen($this->request->post['special_desk']) < 3)) {
            $this->error['special_desk'] = $this->language->get('error_special_desk');
        }

        return !$this->error;
    }

    public function addDescription() {
        $json = array();

        if (isset($this->request->get['user_token'])) {
            $this->load->model('extension/module/special');

            $desk = $this->model_extension_module_special->getDescription(1);

            $json['description'] = $desk['description'];

        } else {
            $json['error'] = 'Отсутствует токен пользователя';
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function deleteProduct() {
        if (isset($this->request->get['user_token'])) {
            $productId = (int)$this->request->post['product_id'];
            $moduleId = (int)$this->request->post['module_id'];

            $this->load->model('extension/module/special');

            $this->model_extension_module_special->deleteProduct($productId, $moduleId);

            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode(['success' => true]));
        } else {
            // Если данные отсутствуют, возвращаем ошибку
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode(['error' => 'Missing required data']));
        }
    }


}