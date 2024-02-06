<?php

class ControllerCatalogActions extends Controller {

    public function index()
    {
        $this->load->model('catalog/actions');

        $row_data = $this->model_catalog_actions->getActionById($this->request->get['action_id']);

        $action_info = json_decode($row_data['setting'], true);

        $this->document->setTitle($action_info['name']);

        $module_id = $this->request->get['action_id'];

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => 'Акции',
            'href' => $this->url->link('information/actions')
        );

        $data['breadcrumbs'][] = array(
            'text' => $action_info['name'],
            'href' => $this->url->link('information/action', ['action_id' => $this->request->get['action_id']])
        );

        $data['breadcrumbs'][] = array(
            'text' => 'Акционный товар',
        );

        $data['name'] = 'Акционный товар ' . count($action_info['product']);

        $this->load->model('catalog/product');
        $this->load->model('tool/image');

        $products = [];

        if (isset($action_info['product'])){
            foreach ($action_info['product'] as $product_id) {
                $product_info = $this->model_catalog_product->getProduct($product_id);
                $action_price = $this->model_catalog_product->getProductSpecialPrice($product_id, $module_id);
                if ($product_info) {
                    $products[] = [
                        'product_id' => $product_id,
                        'name' => $product_info['name'],
                        'href' => $this->url->link('product/product', ['product_id' => $product_id]),
                        'image' => $this->model_tool_image->resize($product_info['image'], 160, 160),
                        'price' => $product_info['price'],
                        'action_price' => $action_price,
                        'action_price_percent' => $this->getPersent($action_price, $product_info['price']),
                        'rating' => $product_info['rating'],
                    ];
                }
            }
        }else{
            $products[]= [];
        }

        $data['product_count'] = count($products);

        $data['products'] = $products;

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('catalog/actions', $data));
    }

    function getPersent($action_price, $price)
    {
        return (1 - $action_price / $price) * 100;
    }
}