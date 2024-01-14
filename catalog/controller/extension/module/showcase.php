<?php


class ControllerExtensionModuleShowcase extends Controller
{
    public function index()
    {
        $this->load->language('extension/module/showcase');

        $this->load->model('extension/module/showcase');

        $data['status'] = $this->model_extension_module_showcase->getStatus();

        $data['banner_new_categories'] = $this->model_extension_module_showcase->getBannerById(1);
        $data['banner_bestseller_categories'] = $this->model_extension_module_showcase->getBannerById(2);
        $data['banner_sale_categories'] = $this->model_extension_module_showcase->getBannerById(3);

        $data['baseUrl'] = $this->config->get('config_url');

        $data['new_categories'] = $this->model_extension_module_showcase->getLatestCategories(4);
        $data['bestseller_categories'] = $this->model_extension_module_showcase->getBestSellerCategories(4);
        $data['sale_categories'] = $this->model_extension_module_showcase->getCategoriesWithSpecials(4);

        foreach ($data['new_categories'] as &$category) {
            $category['href'] = $this->url->link('product/category', 'path=' . $category['category_id']);
        }

        foreach ($data['bestseller_categories'] as &$category) {
            $category['href'] = $this->url->link('product/category', 'path=' . $category['category_id']);
        }

        foreach ($data['sale_categories'] as &$category) {
            $category['href'] = $this->url->link('product/category', 'path=' . $category['category_id']);
        }

        return $this->load->view('extension/module/showcase', $data);
    }
}
