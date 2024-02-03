<?php
class ControllerInformationAction extends Controller {
    private $error = array();

    public function index() {

        $this->load->language('information/actions');

        $this->load->model('catalog/actions');

        $row_data = $this->model_catalog_actions->getActionById($this->request->get['action_id']);

        $action_info = json_decode($row_data['setting'], true);

        $data['action_info'] = $action_info;

        $data['description'] = $action_info['description'];

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

        $this->load->model('catalog/product');

        $products = [];

        foreach ($action_info['product'] as $product_id) {
            // Здесь предполагается, что $this->model_catalog_product->getProduct возвращает информацию о продукте по его ID
            $product_info = $this->model_catalog_product->getProduct($product_id);

            tt($product_info);

            if ($product_info) {
                // Добавляем информацию о продукте в массив $products
                $products[] = [
                    'product_id' => $product_id,
                    'name' => $product_info['name'],
                    'image' => $product_info['image'],
                    'price' => $action_info['product_special'], // Здесь может понадобиться коррекция в зависимости от вашей логики
                    // Другие поля продукта, которые вам нужны
                ];
            }
        }



//        $this->load->model('tool/image');
//
//        $product['thumb'] = $this->model_tool_image->resize($product['image'], 80, 60);
//
//        $action_info['product_thumb'] = $product['thumb'];
//        $action_info['product_name'] = $product['name'];

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('information/action', $data));
    }
}
