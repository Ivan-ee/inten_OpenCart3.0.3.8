<?php


class ControllerExtensionModuleShowcase extends Controller
{
    public function index()
    {
        $this->load->language('extension/module/showcase');

        $this->load->model('extension/module/showcase');



        $data['new_categories'] = $this->model_extension_module_showcase->getLatestCategories(4);
        $data['bestseller_categories'] = $this->model_extension_module_showcase->getBestSellerCategories(4);
        $data['sale_categories'] = $this->model_extension_module_showcase->getCategoriesWithSpecials(4);

        return $this->load->view('extension/module/showcase', $data);
    }
}
