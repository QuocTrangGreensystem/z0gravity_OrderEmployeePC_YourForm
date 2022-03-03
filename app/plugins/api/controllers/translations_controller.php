<?php
class TranslationsController extends ApiAppController
{
    // some components inherited from AppController (app_controller.php) so do not include them here
    public $components = array('Api.ZAuth');

    public $uses = array('Translation');

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function get_contents()
    {
        if ($this->RequestHandler->isPost()) {
            $data = $_POST;
            // Debug($data);
            if (empty($data['keys']) || empty($data['page'])) $this->ZAuth->respond('fail', null, 'data_incorrect', '0');
            $list_keys = $data['keys'];
            $page = $data['page'];
            $user = $this->get_user();
            $company_id = $this->employee_info['Company']['id'];
            $this->Translation->recursive = -1;
            $this->Translation->Behaviors->attach('Containable');
            $translate = $this->Translation->find('all', array(
                'conditions' => array(
                    'page' => $page,
                    'original_text' => $list_keys,
                ),
                'fields' => array('id', 'original_text', 'page', 'field'),
                'contain' => array(
                    'TranslationEntry' => array(
                        'conditions' => array(
                            'TranslationEntry.company_id' => $company_id
                        ),
                        'fields' => array('id', 'text', 'code')
                    )
                )
            ));
            foreach ($translate as $key => $item) {
                $item = $this->z_merge_all_key($item);
                foreach (array(0, 1) as $k) {
                    if (!empty($item[$k])) {
                        if ($item[$k]['code'] == 'eng') {
                            $item[$k]['text'] != '' ? $item['en'] = $item[$k]['text'] : $item['en'] = $item['original_text'];
                        }
                        if ($item[$k]['code'] == 'fre') {
                            $item[$k]['text'] != '' ? $item['fr'] = $item[$k]['text'] : $item['fr'] = $item['original_text'];
                        }
                    }
                    unset($item[$k]);
                }

                $translate[$key] = $item;
            }
            $this->ZAuth->respond('success', $translate);
        }
        $this->ZAuth->respond('fail', null, 'data_incorrect', '0');
    }
    public function get_contents_menu()
    {
        if ($this->RequestHandler->isPost()) {
            $data = $_POST;
            // Debug($data);
            if (empty($data['keys'])) $this->ZAuth->respond('fail', null, 'data_incorrect', '0');
            $list_keys = $data['keys'];
            $company_id = $this->employee_info['Company']['id'];
            $this->loadModel('Menu');
            $results = $this->Menu->find('all', array(
                'recursive' => -1,
                'conditions' => array(
                    'Menu.widget_id' => $list_keys,
                    'Menu.company_id' => $company_id,
                    'Menu.model' => 'project',
                ),
                'fields' => array('id', 'widget_id', 'name_eng as en', 'name_fre as fr'),
            ));
            foreach ($results as $key => $value) {
                $results[$key]=$value['Menu'];
            }
            $this->ZAuth->respond('success', $results);
        }
        $this->ZAuth->respond('fail', null, 'data_incorrect', '0');
    }
}
