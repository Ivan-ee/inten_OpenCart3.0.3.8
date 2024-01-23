<?php

class ControllerExtensionModuleSpecial extends Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('extension/module/special');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/module');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            if (!isset($this->request->get['module_id'])) {
                $this->model_setting_module->addModule('special', $this->request->post);
            } else {
                $this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
            }

            $productSpecial = !empty($this->request->post['product_special']) ? $this->request->post['product_special'] : array();
//            $productName = !empty($this->request->post['product_name']) ? $this->request->post['product_name'] : array();

            foreach ($productSpecial as $productId => $specialPrice) {
                $specialName = $this->request->post['name'];

                // Проверяем, существует ли товар в таблице по product_id и имени акции
                $checkProductQuery = $this->db->query("SELECT * FROM oc_product_special WHERE product_id = '" . (int)$productId . "' AND special_name = '" . $specialName . "'");

                if ($checkProductQuery->num_rows) {
                    // Если товар существует, обновляем цену акции
                    $this->db->query("UPDATE oc_product_special SET price = '" . (float)$specialPrice . "' WHERE product_id = '" . (int)$productId . "' AND special_name = '" . $specialName . "'");
                } else {
                    // Если товар не существует, добавляем новую запись
                    $this->db->query("INSERT INTO oc_product_special (product_id, price, special_name) VALUES ('" . (int)$productId . "', '" . (float)$specialPrice . "', '" . $specialName . "')");
                }
            }

//            $this->tte( $productSpecial);
//            $this->tte( $productName);

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
            $product_info_special = $this->model_catalog_product->getProductSpecialsWithName($product_id, $data['name']);

//            $this->tt($product_info_special);

            $rr = $product_info_special == false ? false : $product_info_special;

//            var_dump($rr);


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

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

//        $this->tt($data);

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

        return !$this->error;
    }

    private function tt($data)
    {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
    }

    function tte($str)
    {
        echo "<pre>";
        print_r($str);
        echo "</pre>";
        exit();
    }

}