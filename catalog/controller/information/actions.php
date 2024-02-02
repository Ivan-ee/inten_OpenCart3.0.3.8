<?php

class ControllerInformationActions extends Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('information/actions');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['name'] = $this->language->get('heading_title');

        $this->load->model('catalog/actions');

        $actions = $this->model_catalog_actions->getActions();

        $newArray = [];

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

        foreach ($actions as $action) {
            $action_info = json_decode($action['setting'], true);

            $product_count = count($action_info['product']) - 1;
            $product_label = '';

            if ($product_count === 0) {
                $product_label = '';
            }

            if ($product_count > 0) {
                $product_label = 'Ещё ' . $product_count . ' товар';

                if ($product_count % 10 == 1 && $product_count % 100 != 11) {
                    $product_label .= '';
                } elseif ($product_count % 10 >= 2 && $product_count % 10 <= 4 && ($product_count % 100 < 10 || $product_count % 100 >= 20)) {
                    $product_label .= 'а';
                } else {
                    $product_label .= 'ов';
                }

                $product_label .= ' →';
            }

            $action_info['product_label'] = $product_label;

            $start_datetime = new DateTime($action_info['start_date']);
            $end_datetime = new DateTime($action_info['end_date']);

            $start_formatted = $start_datetime->format('d ');

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

            $action_info['date'] = $date_range;

            $action_info['id'] = $action['module_id'];

            $action_info['href'] = $this->url->link('information/action', 'action_id=' . $action_info['id']);

            $this->load->model('catalog/product');

            $product = $this->model_catalog_product->getProduct($action_info['product'][0]);

            $this->load->model('tool/image');

            $product['thumb'] = $this->model_tool_image->resize($product['image'], 80, 60);

            $action_info['product_thumb'] = $product['thumb'];
            $action_info['product_name'] = $product['name'];

            $newArray[] = $action_info;
        }

        $data['actions'] = $newArray;

        $data['top_menu_items'] = $this->load->controller('extension/menu', ['id' => 1]);
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('information/actions', $data));
    }
}
