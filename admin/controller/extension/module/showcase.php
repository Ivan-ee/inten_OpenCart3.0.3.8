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

//    public function add() {
//        $this->load->language('design/banner');
//
//        $this->document->setTitle($this->language->get('heading_title'));
//
//        $this->load->model('design/banner');
//
//        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
//            $this->model_extension_module_showcase->addBanner($this->request->post);
//
//            $this->session->data['success'] = $this->language->get('text_success');
//
//            $url = '';
//
//            if (isset($this->request->get['sort'])) {
//                $url .= '&sort=' . $this->request->get['sort'];
//            }
//
//            if (isset($this->request->get['order'])) {
//                $url .= '&order=' . $this->request->get['order'];
//            }
//
//            if (isset($this->request->get['page'])) {
//                $url .= '&page=' . $this->request->get['page'];
//            }
//
//            $this->response->redirect($this->url->link('design/banner', 'user_token=' . $this->session->data['user_token'] . $url, true));
//        }
//
//        $this->getForm();
//    }

    public function edit()
    {
        $this->load->language('extension/module/showcase');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/module/showcase');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_extension_module_showcase->editBanner($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

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

        if (isset($this->error['banner_image'])) {
            $data['error_banner_image'] = $this->error['banner_image'];
        } else {
            $data['error_banner_image'] = array();
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

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($banner_info)) {
            $data['status'] = $banner_info['status'];
        } else {
            $data['status'] = true;
        }

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        $banner_info = $this->model_extension_module_showcase->getBanner();

        $data['banners'] = array();

        foreach ($banner_info as $banner) {
            $data['banners'][] = array(
                'showcase_images_id' => $banner['showcase_images_id'],
                'image' => $banner['image'],
                'title' => $banner['title'],
                'link' => $banner['link'],
            );
        }
        $this->load->model('tool/image');

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
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

//        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
//            $this->error['name'] = $this->language->get('error_name');
//        }

        if (isset($this->request->post['banner_image'])) {
            foreach ($this->request->post['banner_image'] as $language_id => $value) {
                foreach ($value as $banner_image_id => $banner_image) {
                    if ((utf8_strlen($banner_image['title']) < 2) || (utf8_strlen($banner_image['title']) > 64)) {
                        $this->error['banner_image'][$language_id][$banner_image_id] = $this->language->get('error_title');
                    }
                }
            }
        }

        return !$this->error;
    }

    protected function validateDelete()
    {
        if (!$this->user->hasPermission('modify', 'design/banner')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}
