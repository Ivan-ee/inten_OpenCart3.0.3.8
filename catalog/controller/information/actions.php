<?php
class ControllerInformationActions extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('information/actions');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/actions');

        $actions = $this->model_catalog_actions->getActions();

        $newArray = [];

        foreach ($actions as $action) {
            $settingArray = json_decode($action['setting'], true);

            $action['action_info'] = $settingArray;

            $newArray[] = $action;
        }

        $data['actions'] = $newArray;

//        tt($data['actions']);

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
