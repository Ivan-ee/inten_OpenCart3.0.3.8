<?php

class ControllerCatalogActions extends Controller {

    public function index()
    {
        $this->load->model('catalog/actions');

        $sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'action_price_percent';
        $order = isset($this->request->get['order']) ? $this->request->get['order'] : 'desc';

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['sorts'] = array(
            array(
                'href' => $this->url->link('catalog/actions', 'action_id=' . $this->request->get['action_id'] .  '&sort=price&order=asc'),
                'value' => 'price-asc',
                'text' => 'Сначала недорогие'
            ),
            array(
                'href' => $this->url->link('catalog/actions', 'action_id=' . $this->request->get['action_id'] . '&sort=price&order=desc'),
                'value' => 'price-desc',
                'text' => 'Сначала дорогие'
            ),
            array(
                'href' => $this->url->link('catalog/actions', 'action_id=' . $this->request->get['action_id'] . '&sort=rating&order=desc'),
                'value' => 'rating-desc',
                'text' => 'Сначала с лучшей оценкой'
            ),
            array(
                'href' => $this->url->link('catalog/actions', 'action_id=' . $this->request->get['action_id'] . '&sort=viewed&order=desc'),
                'value' => 'viewed-desc',
                'text' => 'Сначала популярные'
            ),
            array(
                'href' => $this->url->link('catalog/actions', 'action_id=' . $this->request->get['action_id'] . '&sort=action_price_percent&order=desc'),
                'value' => 'action_price_percent-desc',
                'text' => 'По скидке (%)'
            ),
        );

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
                        'action_price' => ceil($action_price),
                        'action_price_percent' => ceil($this->getPersent($action_price, $product_info['price'])),
                        'rating' => $product_info['rating'],
                        'viewed' => $product_info['viewed'],
                    ];
                }
            }
        }else{
            $products[]= [];
        }

        $data['product_count'] = count($products);

        $sorted_products = $products;

        if ($sort !== 'default') {
            switch ($order) {
                case 'asc':
                    usort($sorted_products, function($a, $b) use ($sort) {
                        return $a[$sort] - $b[$sort];
                    });
                    break;
                case 'desc':
                    usort($sorted_products, function($a, $b) use ($sort) {
                        return $b[$sort] - $a[$sort];
                    });
                    break;
                default:
                    usort($sorted_products, function($a, $b) {
                        return $a['rating'] - $b['rating'];
                    });
            }
        }

        $data['products'] = $sorted_products;

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