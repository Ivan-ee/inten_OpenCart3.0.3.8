<?php
class ControllerInformationAction extends Controller {
    private $error = array();

    public function index() {

        $this->load->language('information/actions');

        $this->load->model('catalog/actions');

        $row_data = $this->model_catalog_actions->getActionById($this->request->get['action_id']);

        $action_info = json_decode($row_data['setting'], true);

        $data['name'] = $action_info['name'];

        $data['page_image'] = $action_info['page_image'];

        $data['description'] = $action_info['description'];

        $data['special_desk'] = $action_info['special_desk'];

        $this->document->setTitle($action_info['name']);

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('information/actions')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get($action_info['name']),
        );

        $dateTime = new DateTime($action_info['end_date']);

        $formattedDate = $dateTime->format('Y-m-d H:i:s');

        $data['date_countdown'] = $formattedDate;

        $start_datetime = new DateTime($action_info['start_date']);
        $end_datetime = new DateTime($action_info['end_date']);

        $start_formatted = $start_datetime->format('d ');

        $monthTranslations = [
            'January' => 'января',
            'February' => 'февраля',
            'March' => 'марта',
            'April' => 'апреля',
            'May' => 'мая',
            'June' => 'июня',
            'July' => 'июля',
            'August' => 'августа',
            'September' => 'сентября',
            'October' => 'октября',
            'November' => 'ноября',
            'December' => 'декабря',
        ];

        if ($start_datetime->format('n') !== $end_datetime->format('n')) {
            $start_formatted .= $monthTranslations[$start_datetime->format('F')];
        } elseif ($start_datetime->format('Y') !== $end_datetime->format('Y')){
            $start_formatted .= $monthTranslations[$start_datetime->format('F')];
        }

        if ($start_datetime->format('Y') !== $end_datetime->format('Y')) {
            $start_formatted .= $start_datetime->format(' Y');
        }

        $end_formatted = $end_datetime->format('d ');

        $end_formatted .= $monthTranslations[$end_datetime->format('F')];
        $end_formatted .= $end_datetime->format(' Y');

        $year_diff = $start_datetime->format('Y') !== $end_datetime->format('Y');
        $month_diff = $start_datetime->format('n') !== $end_datetime->format('n');
        $day_diff = $start_datetime->format('j') !== $end_datetime->format('j');

        if ($year_diff || $month_diff || $day_diff) {
            $date_range = "с {$start_formatted} по {$end_formatted}";
        } else {
            $date_range = "в {$start_formatted}";
        }

        $data['date'] = $date_range;

        $module_id = $this->request->get['action_id'];

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
                        'image' => $this->model_tool_image->resize($product_info['image'], 160, 160),
                        'price' => $product_info['price'],
                        'action_price' => $action_price,
                    ];
                }
            }
        }else{
            $products[]= [];
        }



        $data['product_count'] = count($products);

        $data['products'] = $products;

//        tt($data['products']);

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('information/action', $data));
    }
}
