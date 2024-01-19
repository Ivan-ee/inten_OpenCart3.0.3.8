<?php
class ControllerExtensionModuleXbundleSide extends Controller {
	public function index() {
		$this->load->language('extension/module/xbundle_side');
		$this->load->model('extension/xbundle');
		$data['heading_title'] = $this->language->get('heading_title');
		
		if(isset($this->request->get['bundle_id'])){
			$data['bundle_id'] = $this->request->get['bundle_id'];
		}else{
			$data['bundle_id'] = 0;
		}
		
		$filter_data = array(
			'sort'  => 'p.date_added',
			'order' => 'DESC',
		);
		
		$xbundle_product = $this->model_extension_xbundle->getbundles($filter_data);
		foreach($xbundle_product as $xbundle){
			$data['xbundles'][]=array(
				'bundle_id'	=> $xbundle['bundle_id'],
				'name'			=> $xbundle['name'],
				'href'  => $this->url->link('extension/product_bundle', 'bundle_id=' . $xbundle['bundle_id'],true)
			);
		}
		
		return $this->load->view('extension/module/xbundle_side', $data);
	}
}