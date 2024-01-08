<?php


class ControllerExtensionModuleShowcase extends Controller
{
    public function index()
    {
        $this->load->language('extension/module/showcase');

        $this->load->model('extension/module/showcase');



        $data['popular_categories'] = $this->model_extension_module_showcase->getLatestCategories(4);
        $data['new_categories'] = $this->model_extension_module_showcase->getBestSellerCategories(4);
        $data['sale_categories'] = $this->model_extension_module_showcase->getCategoriesWithSpecials(4);

        var_dump($data['popular_categories']);

        return $this->load->view('extension/module/showcase', $data);
    }
}
