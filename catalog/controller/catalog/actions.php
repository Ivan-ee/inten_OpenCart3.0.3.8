<?php

class ControllerCatalogActions extends Controller {

    public function index()
    {
        $this->load->model('catalog/actions');

        $row_data = $this->model_catalog_actions->getActionById($this->request->get['action_id']);

        $action_info = json_decode($row_data['setting'], true);

        $this->document->setTitle($action_info['name']);

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


        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('catalog/actions', $data));
    }

}