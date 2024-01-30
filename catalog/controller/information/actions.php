<?php
class ControllerInformationActions extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('information/actions');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/actions');

        $actions = $this->model_catalog_actions->getActions();

        $newArray = [];

        // Массив перевода месяцев
        $monthTranslations = [
            'January'   => 'января',
            'February'  => 'февраля',
            'March'     => 'марта',
            'April'     => 'апреля',
            'May'       => 'мая',
            'June'      => 'июня',
            'July'      => 'июля',
            'August'    => 'августа',
            'September' => 'сентября',
            'October'   => 'октября',
            'November'  => 'ноября',
            'December'  => 'декабря',
        ];

        foreach ($actions as $action) {
            $action_info = json_decode($action['setting'], true);

            $start_datetime = new DateTime($action_info['start_date']);
            $end_datetime = new DateTime($action_info['end_date']);

            $start_formatted = $start_datetime->format('d ');

            if ($start_datetime->format('Y') !== $end_datetime->format('Y')) {
                $start_formatted .= $start_datetime->format(' Y');
            } elseif ($start_datetime->format('n') !== $end_datetime->format('n')) {
                $start_formatted .= $monthTranslations[$start_datetime->format('F')];
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

            $action_info['formatted_date_range'] = $date_range;

            $newArray[] = $action_info;
        }

        $data['actions'] = $newArray;

        tt($data['actions']);

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('information/actions', $data));
    }

    protected function validate() {
        return !$this->error;
    }
}
