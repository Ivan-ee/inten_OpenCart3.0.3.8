<?php

class ControllerExtensionModuleShowcase extends Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('extension/module/showcase');

        $this->load->model('localisation/language');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/module/showcase');

        $this->load->model('tool/image');

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

        $data['user_token'] = $this->session->data['user_token'];

        $data['action'] = $this->url->link('extension/module/showcase/edit', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['status'] = $this->model_extension_module_showcase->getStatus();

        $data['languages'] = $this->model_localisation_language->getLanguages();


        $banner_info = $this->model_extension_module_showcase->getBanners();

        $data['banners'] = array();

        $baseUrl = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $baseUrl .= $_SERVER['HTTP_HOST'];
        $baseUrl .= str_replace('/admin', '', dirname($_SERVER['SCRIPT_NAME']));

        foreach ($banner_info as $banner) {
            $data['banners'][] = array(
                'showcase_images_id' => $banner['showcase_images_id'],
                'image' => $baseUrl . '/image/' . $banner['image'],
                'title' => $banner['title'],
                'link' => $banner['link'],
            );

        }

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/showcase', $data));

    }

    public function edit()
    {
        $this->load->language('extension/module/showcase');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('extension/module/showcase');
        $this->load->model('tool/image');

        if (!$this->user->hasPermission('modify', 'extension/module/showcase')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $status = $this->request->post['status'];
            $this->model_extension_module_showcase->saveStatus($status);

            $bannerImages = $this->request->post['banner_image'];

            foreach ($bannerImages as $key => $data) {
                $showcaseImagesId = $key;
                $title = $data['title'];
                $link = $data['link'];
                $image = $data['image'];

                $path_parts = explode('catalog', $image);
                if (isset($path_parts[1])) {
                    $image = 'catalog' . $path_parts[1];
                } else {
                    $image = 'catalog/' . basename($image);
                }

                $this->model_extension_module_showcase->updateShowcaseImage($showcaseImagesId, $title, $link, $image);

                $this->session->data['success'] = $this->language->get('text_success');
            }

            $url = '';
            $this->response->redirect($this->url->link('extension/module/showcase', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->index();
    }

    protected function validateForm()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/showcase')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}
