<?php
class ControllerCommonMenu extends Controller {
    public function index() {
        $this->load->language('common/menu');

        // Menu
        $this->load->model('catalog/category');

        $this->load->model('catalog/product');

        $this->load->model('tool/image');

        $data['categories'] = array();

        $categories = $this->model_catalog_category->getCategories(0);

        foreach ($categories as $category) {

            if($category['image']){
                $image = $this->model_tool_image->resize($category['image'], 20, 20);
            } else {
                $image = false;
            }

            if ($category['top']) {
                // Level 2
                $children_data = array();

                $children = $this->model_catalog_category->getCategories($category['category_id']);

                foreach ($children as $child) {
                    $filter_data = array(
                        'filter_category_id'  => $child['category_id'],
                        'filter_sub_category' => true
                    );

                    $children_data[] = array(
                        'name'  => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
                        'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
                    );
                }

                // Level 1
                $data['categories'][] = array(
                    'name'     => $category['name'],
                    'image'    => $image,
                    'children' => $children_data,
                    'column'   => $category['column'] ? $category['column'] : 1,
                    'href'     => $this->url->link('product/category', 'path=' . $category['category_id'])
                );
            }
        }


				if($this->config->get('xbundle_status')){
					$xbundle_description = $this->config->get('xbundle_description');
					if (isset($xbundle_description)&&$xbundle_description[$this->config->get('config_language_id')]['menu']!='') {
						$bundles_data=array();
						$this->load->model('extension/xbundle');
						$bundles= $this->model_extension_xbundle->getbundles(array());
						foreach($bundles as $bundle){
							if($bundle['top']){
								$bundles_data[]=array(
								  'name'  => $bundle['name'],
								  'href'  => $this->url->link('extension/product_bundle', 'bundle_id=' . $bundle['bundle_id'],true)
								);
							}
						}
						
						$data['categories'][] = array(
							'name'     => $xbundle_description[$this->config->get('config_language_id')]['menu'],
							'children' => $bundles_data,
							'column'   => $this->config->get('xbundle_column'),
							'href'     => $this->url->link('extension/xbundle','',true)
						);
					}
				}
				
        return $this->load->view('common/menu', $data);
    }
}
