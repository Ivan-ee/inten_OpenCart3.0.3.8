<?php

class ControllerExtensionModuleShowcase extends Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('extension/module/showcase');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/module/showcase');

        $this->getForm();
    }

    public function edit()
    {
        $this->load->language('extension/module/showcase');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/module/showcase');

        // Добавим проверку на наличие разрешения на модификацию
        if (!$this->user->hasPermission('modify', 'extension/module/showcase')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $status = $this->request->post['status'];
            $bannerImages = $this->request->post['banner_image'];



            foreach ($bannerImages as $key => $data) {
                $showcaseImagesId = $key;
                $title = $data['title'];
                $link = $data['link'];
                $image = $data['image'];

                //  catalog/showcase/div.actual-offers__section-img_container.png

                $image = strstr($image, "catalog/");

//                $this->tte($image);

                $this->model_extension_module_showcase->updateShowcaseImage($showcaseImagesId, $title, $link, $image);


                $this->session->data['success'] = $this->language->get('text_success');
            }

            $url = '';

            $this->response->redirect($this->url->link('extension/module/showcase', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    protected function getForm()
    {
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $url = '';

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/showcase', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['action'] = $this->url->link('extension/module/showcase/edit', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['user_token'] = $this->session->data['user_token'];



//        if (isset($this->request->post['status'])) {
//            $data['status'] = $this->request->post['status'];
//        } elseif (!empty($banner_info)) {
//            $data['status'] = $banner_info['status'];
//        } else {
//            $data['status'] = true;
//        }

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();


        $banner_info = $this->model_extension_module_showcase->getBanner();


        $data['banners'] = array();

        $this->load->model('tool/image');

        foreach ($banner_info as $banner) {
            $data['banners'][] = array(
                'showcase_images_id' => $banner['showcase_images_id'],
                'image' => $this->model_tool_image->resize($banner['image'], 100, 100),
                'title' => $banner['title'],
                'link' => $banner['link'],
            );

            $this->tt( $data['banners']);
        }




        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/showcase', $data));
    }

    protected function validateForm()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/showcase')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    protected function validateDelete()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/showcase')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    function tt($str){
        echo "<pre>";
        print_r($str);
        echo "</pre>";
    }
    function tte($str){
        echo "<pre>";
        print_r($str);
        echo "</pre>";
        exit();
    }
}
