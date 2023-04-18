<?php
include_once 'dailymotion-sdk-php-master/Dailymotion.php';
/*
 # -- BEGIN LICENSE BLOCK ----------------------------------
 #
 # This file is part of MAGIX CMS.
 # MAGIX CMS, The content management system optimized for users
 # Copyright (C) 2008 - 2013 magix-cms.com <support@magix-cms.com>
 #
 # OFFICIAL TEAM :
 #
 #   * Gerits Aurelien (Author - Developer) <aurelien@magix-cms.com> <contact@aurelien-gerits.be>
 #
 # Redistributions of files must retain the above copyright notice.
 # This program is free software: you can redistribute it and/or modify
 # it under the terms of the GNU General Public License as published by
 # the Free Software Foundation, either version 3 of the License, or
 # (at your option) any later version.
 #
 # This program is distributed in the hope that it will be useful,
 # but WITHOUT ANY WARRANTY; without even the implied warranty of
 # MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 # GNU General Public License for more details.
 #
 # You should have received a copy of the GNU General Public License
 # along with this program.  If not, see <http://www.gnu.org/licenses/>.
 #
 # -- END LICENSE BLOCK -----------------------------------
 #
 # DISCLAIMER
 #
 # Do not edit or add to this file if you wish to upgrade MAGIX CMS to newer
 # versions in the future. If you wish to customize MAGIX CMS for your
 # needs please refer to http://www.magix-cms.com for more information.
 */
class plugins_dailymotion_admin extends plugins_dailymotion_db
{
    protected backend_model_template $template;
    protected backend_model_data $data;

    protected $message;
    public string $controller;
    /**
     * GET
     * @var $getlang ,
     * @var $edit
     */
    public $getlang, $action, $edit, $tab;

    /**
     * POST
     * @var $slide
     * @var $sliderorder
     */
    public $dailyData, $id;

    /**
     * Constructor
     */
    public function __construct(backend_model_template $t = null){
        $this->template = $t instanceof backend_model_template ? $t : new backend_model_template;
        $this->message = new component_core_message($this->template);
        $this->data = new backend_model_data($this);
        $formClean = new form_inputEscape();

        // --- Get
        if (http_request::isGet('controller')) {
            $this->controller = $formClean->simpleClean($_GET['controller']);
        }
        if (http_request::isGet('edit')) {
            $this->edit = $formClean->numeric($_GET['edit']);
        }
        if (http_request::isGet('action')) {
            $this->action = $formClean->simpleClean($_GET['action']);
        } elseif (http_request::isPost('action')) {
            $this->action = $formClean->simpleClean($_POST['action']);
        }
        if (http_request::isGet('tabs')) {
            $this->tab = $formClean->simpleClean($_GET['tabs']);
        }
        if (http_request::isPost('dailyData')) {
            $this->dailyData = $formClean->arrayClean($_POST['dailyData']);
        }
    }
    /**
     * Method to override the name of the plugin in the admin menu
     * @return string
     */
    public function getExtensionName()
    {
        return $this->template->getConfigVars('dailymotion_plugin');
    }
    /**
     * Assign data to the defined variable or return the data
     * @param string $type
     * @param string|int|null $id
     * @param string $context
     * @param boolean $assign
     * @param boolean $pagination
     * @return mixed
     */
    private function getItems($type, $id = null, $context = null, $assign = true, $pagination = false) {
        return $this->data->getItems($type, $id, $context, $assign, $pagination);
    }
    /**
     * @return array
     */
    private function getAuthentication() : array{
        $data = $this->getItems('root',NULL,'one',false);
        return [
            'apikey'        => $data['apikey_dm'],
            'apisecret'     => $data['apisecret_dm'],
            'username'      => $data['username_dm'],
            'password'      => $data['password_dm'],
            'visibility'    => $data['visibility_dm']
        ];

    }
    /**
     * @param $id
     * @return array
     * @throws DailymotionAuthRequiredException
     */
    private function getImagesUrl($id) : array{
        $results = [];
        $aut = $this->getAuthentication();
        // Scopes you need to run your tests
        $scopes = array(
            'read'
        );
        // Dailymotion object instanciation
        $api = new Dailymotion();
        $access = $api->setGrantType(
            Dailymotion::GRANT_TYPE_PASSWORD,
            $aut['apikey'],
            $aut['apisecret'],
            $scopes,
            array(
                'username' => $aut['username'],
                'password' => $aut['password'],
            )
        );
        if($access){
            $results = $api->get(
                '/video/'.$id,
                array('fields' => array('private_id','thumbnail_360_url', 'thumbnail_720_url'))
            );
            return $results;
        }
    }
    /**
     * @param $data
     * @throws Exception
     */
    private function upd($data)
    {
        switch ($data['type']) {
            case 'dailymotion':
            case 'thumbVideo':
                parent::update(
                    array(
                        //'context' => $data['context'],
                        'type' => $data['type']
                    ),
                    $data['data']
                );
                break;
        }
    }

    /**
     * Update data
     * @param $data
     * @throws Exception
     */
    private function add($data)
    {
        switch ($data['type']) {
            case 'dailymotion':
                parent::insert(
                    array(
                        //'context' => $data['context'],
                        'type' => $data['type']
                    ),
                    $data['data']
                );
                break;
        }
    }

    /**
     * @throws Exception
     */
    private function save(){
        $setData = $this->getItems('root',NULL,'one',false);
        $newData = [];
        
        $newData['apikey_dm'] = $this->dailyData['apikey_dm'];
        $newData['apisecret_dm'] = $this->dailyData['apisecret_dm'];
        $newData['username_dm'] = $this->dailyData['username_dm'];
        $newData['password_dm'] = $this->dailyData['password_dm'];
        $newData['visibility_dm'] = $this->dailyData['visibility_dm'];

        if($setData['id_dm']){

            $newData['id_dm'] = $setData['id_dm'];
            $this->upd(
                array(
                    'type' => 'dailymotion',
                    'data' => $newData
                )
            );
        }else{

            $this->add(
                array(
                    'type' => 'dailymotion',
                    'data' => $newData
                )
            );
        }
        //print_r($newData);
        $this->message->json_post_response(true, 'update');
    }
    /**
     * @return mixed
     */
    public function setItemData(){
        $setData = $this->getItems('root',NULL,'one',false);
        return $setData;
    }

    /**
     *
     */
    public function run(){
        if(isset($this->action)) {
            switch ($this->action) {
                case 'edit':
                    $this->save();
                    break;
                case 'list':
                    $videosAll = $this->getItems('videosAll',NULL,'all',false);
                    $newArr = [];
                    if(!empty($videosAll)) {
                        foreach ($videosAll as $key => $value) {
                            if(!empty($value['video_id_pdn'])) {
                                $thumbnails = $this->getImagesUrl($value['video_id_pdn']);
                                /*print '<pre>';
                                print_r([
                                    'id' => $value['video_id_pdn'],
                                    'thumbnail_360_url' => !empty($thumbnails['thumbnail_360_url']) ? $thumbnails['thumbnail_360_url'] : NULL,
                                    'thumbnail_720_url' => !empty($thumbnails['thumbnail_720_url']) ? $thumbnails['thumbnail_720_url'] : NULL
                                ]);
                                print '</pre>';*/
                                $this->upd(
                                    [
                                        'type' => 'thumbVideo',
                                        'data' => [
                                            'id' => $value['video_id_pdn'],
                                            'private_id' => !empty($thumbnails['private_id']) ? $thumbnails['private_id'] : NULL,
                                            'thumbnail_360_url' => !empty($thumbnails['thumbnail_360_url']) ? $thumbnails['thumbnail_360_url'] : NULL,
                                            'thumbnail_720_url' => !empty($thumbnails['thumbnail_720_url']) ? $thumbnails['thumbnail_720_url'] : NULL
                                        ]
                                    ]
                                );
                            }
                        }
                    }
                    break;
            }
        }else{
            $data = $this->getItems('root',NULL,'one',false);
            $this->template->assign('daily', $data);
            $this->template->display('index.tpl');
        }
    }
}
