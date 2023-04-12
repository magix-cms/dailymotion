<?php
/*
 # -- BEGIN LICENSE BLOCK ----------------------------------
 #
 # This file is part of MAGIX CMS.
 # MAGIX CMS, The content management system optimized for users
 # Copyright (C) 2008 - 2019 magix-cms.com <support@magix-cms.com>
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

 # You should have received a copy of the GNU General Public License
 # along with this program.  If not, see <http://www.gnu.org/licenses/>.
 #
 # -- END LICENSE BLOCK -----------------------------------

 # DISCLAIMER

 # Do not edit or add to this file if you wish to upgrade MAGIX CMS to newer
 # versions in the future. If you wish to customize MAGIX CMS for your
 # needs please refer to http://www.magix-cms.com for more information.
 */
//include_once ('db.php');
include_once 'dailymotion-sdk-php-master/Dailymotion.php';

class plugins_dailymotion_core extends plugins_dailymotion_db
{

    protected $template, $modelPlugins, $message, $arrayTools, $data,
        $modelLanguage, $collectionLanguage, $progress;
    public $controller, $plugins, $plugin, $edit, $id_pdn, $file, $subaction,$order, $offset;
    public $allowedExts = [
        "mov",
        "mp4",
        "qt",
        "avi",
        "mpeg"
    ];

    public function __construct($t = null)
    {
        $this->template = $t ? $t : new backend_model_template();
        $this->modelPlugins = new backend_model_plugins();
        $this->plugins = new backend_controller_plugins();
        $formClean = new form_inputEscape();
        $this->message = new component_core_message($this->template);
        $this->arrayTools = new collections_ArrayTools();
        $this->data = new backend_model_data($this);
        $this->modelLanguage = new backend_model_language($this->template);
        $this->collectionLanguage = new component_collections_language();
        if (http_request::isGet('controller')) {
            $this->controller = $formClean->simpleClean($_GET['controller']);
        }
        if (http_request::isGet('plugin')) {
            $this->plugin = $formClean->simpleClean($_GET['plugin']);
        }
        // --- ADD or EDIT
        if (http_request::isGet('edit')) $this->edit = $formClean->numeric($_GET['edit']);
        if (http_request::isGet('id')) $this->id_pdn = $formClean->simpleClean($_GET['id']);
        elseif (http_request::isPost('id')) $this->id_pdn = $formClean->simpleClean($_POST['id']);
        if (http_request::isPost('product')) $this->order = $formClean->arrayClean($_POST['product']);
        if (http_request::isGet('offset')) $this->offset = intval(form_inputEscape::simpleClean($_GET['offset']));
        if (isset($_FILES['file']["name"])) $this->file = $_FILES['file']["name"];
        if (http_request::isGet('mod')) $this->subaction = form_inputEscape::simpleClean($_GET['mod']);

    }
    /**
     * Method to override the name of the plugin in the admin menu
     * @return string
     */
    public function getExtensionName()
    {
        return $this->template->getConfigVars('dailymotion_product_plugin');
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
    private function getItems($type, $id = null, $context = null, $assign = true, $pagination = false)
    {
        return $this->data->getItems($type, $id, $context, $assign, $pagination);
    }
    /**
     * @return string[]
     */
    public function getTabsAvailable() : array{
        return ['product'];
    }
    /**
     * Update data
     * @param $data
     * @throws Exception
     */
    private function add($data)
    {
        switch ($data['type']) {
            case 'productVideo':
                parent::insert(
                    array(
                        'context' => $data['context'],
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
    private function upd($data)
    {
        switch ($data['type']) {
            case 'order':
                $p = $this->order;
                for ($i = 0; $i < count($p); $i++) {
                    parent::update(
                        ['type'=>$data['type']],
                        [
                            'id_pdn' => $p[$i],
                            'order_pdn' => $i + (isset($this->offset) ? ($this->offset + 1) : 0)
                        ]
                    );
                }
                break;
            case 'productVideo':
                parent::update(
                    array(
                        'context' => $data['context'],
                        'type' => $data['type']
                    ),
                    $data['data']
                );
                break;
        }
    }
    /**
     * Insertion de données
     * @param $data
     * @throws Exception
     */
    private function del($data)
    {
        switch($data['type']){
            case 'delVideo':
                parent::delete(
                    array(
                        'type' => $data['type']
                    ),
                    $data['data']
                );
                $this->message->json_post_response(true,'delete',$data['data']);
                break;
        }
    }
    /**
     * @return array
     */
    private function getAuthentication() : array{
        $data = $this->getItems('root',NULL,'one',false);
        return [
            'apikey'    => $data['apikey_dm'],
            'apisecret' => $data['apisecret_dm'],
            'username'  => $data['username_dm'],
            'password'  => $data['password_dm']
        ];

    }

    /**
     * @param string $url
     * @param string $title
     * @return mixed|void
     * @throws DailymotionApiException
     * @throws DailymotionAuthRequiredException
     */
    private function getPostApi(string $url,string $title) {
        $log = new debug_logger(MP_LOG_DIR);
        //$log->tracelog('dailymotion connect');
        $aut = $this->getAuthentication();
        // Scopes you need to run your tests
        $scopes = array(
            'write',
            'manage_videos',
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
            $filePath = $url;
            $progressUrl = null;
            $url = $api->uploadFile($filePath, null, $progressUrl);
            //$log->tracelog(json_encode($progressUrl));
            //print_r($progressUrl);
            // More fields may be mandatory in order to create a video.
            // Please refer to the complete API reference for a list of all the required data.
            $videoTitle = $title;
            $channel = 'auto';
            $postvideo = $api->post(
                '/me/videos',
                array(
                    'url'       => $url,
                    'title'     => $videoTitle,
                    //'tags'      => 'dailymotion,api,sdk,test',
                    'channel'   => $channel,
                    'published' => true,
                    'is_created_for_kids' => false,
                    'private'   => true
                )
            );
            //$log->tracelog(json_encode($postvideo));
            return $postvideo['id'];
        }else{
            $log->tracelog(json_encode($access));
        }

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
        }
        return $results;
    }
    /**
     * @return void
     * @throws DailymotionApiException
     * @throws DailymotionAuthRequiredException
     */
    private function setUploadVideo(){
        if(isset($this->file)) {

            $log = new debug_logger(MP_LOG_DIR);
            //$log->tracelog('upload start');
            $this->template->configLoad();
            $this->progress = new component_core_feedback($this->template);
            $extension = pathinfo($_FILES['file']["name"], PATHINFO_EXTENSION);
            usleep(200000);
            $this->progress->sendFeedback(array('message' => $this->template->getConfigVars('control_of_data'), 'progress' => 30));
            //$log->tracelog(json_encode($_FILES));
            $nbVideoProduct = $this->getItems('nbVideoProduct', array('id' => $this->edit), 'one', false);
            $prefixName = ($nbVideoProduct['nbvideo'] + 1).'_';//$nbVideoProduct['nbvideo'] > 0 ? $nbVideoProduct['nbvideo'].'_' : '';
            $defaultLanguage = $this->collectionLanguage->fetchData(array('context' => 'one', 'type' => 'default'));

            $productData = $this->getItems('productData', array('id' => $this->edit,'default_lang'=>$defaultLanguage['id_lang']), 'one', false);
            $videoName = !empty($productData['bcb_ref_pos']) ? $productData['bcb_ref_pos'] : $productData['name_p'];
            $fileUpload = new component_files_upload();
            $resultUpload = $fileUpload->setUploadFile(
                'file',
                ['name'=>$prefixName.$videoName],
                [
                    'upload_root_dir' => 'upload/video', //string
                    'upload_dir' => $this->edit //string ou array
                ],
                ['mp4','avi','mpeg','mov','qt'],
                false
            );
            if($resultUpload){
                //$log->tracelog(json_encode($resultUpload));
                // Add video data
                $this->add([
                    'type' => 'productVideo',
                    'data' => [
                        'id_product'=>$this->edit,
                        'name_pdn'  =>$prefixName.$videoName,
                        'video_id_pdn'=> null
                    ]
                ]);

                $videoUrl = $resultUpload['path'].$resultUpload['file'];
                //$log->tracelog($videoUrl);
                $percent = $this->progress->progress;
                $preparePercent = (80 - $percent) / count($resultUpload);
                $percent = $percent + $preparePercent;
                usleep(200000);
                $this->progress->sendFeedback(['message' => $this->template->getConfigVars('upload_on_dailymotion'), 'progress' => $percent]);

                //$log->tracelog(json_encode($_FILES));
                //$log->tracelog(json_encode($resultUpload));
                if(!empty($videoUrl)){
                    $video_id = $this->getPostApi($videoUrl,$prefixName.$videoName);
                    $lastVideo = $this->getItems('lastVideo', NULL, 'one', false);
                }else{
                    $video_id = NULL;
                }
                if(!empty($video_id)){
                    $thumbnails = $this->getImagesUrl($video_id);
                    $this->upd([
                        'type' => 'productVideo',
                        'data' => [
                            'id'            =>  $lastVideo['id_pdn'],
                            'video_id_pdn'  =>  $video_id,
                            'private_id'    => !empty($thumbnails['private_id']) ? $thumbnails['private_id'] : NULL,
                            'thumbnail_360_url'=>!empty($thumbnails['thumbnail_360_url']) ? $thumbnails['thumbnail_360_url'] : NULL,
                            'thumbnail_720_url'=>!empty($thumbnails['thumbnail_720_url']) ? $thumbnails['thumbnail_720_url'] : NULL
                        ]
                    ]);
                    usleep(200000);
                    $this->progress->sendFeedback(['message' => $this->template->getConfigVars('remove_local_video'), 'progress' => 90]);
                    // Suppression de la video local
                    $makefile = new filesystem_makefile();
                    $makefile->remove($videoUrl);
                    usleep(200000);
                    $this->getVideoList();
                    $display = $this->modelPlugins->fetch('mod/video.tpl');
                    $this->progress->sendFeedback(array('message' => $this->template->getConfigVars('video_success'), 'progress' => 100, 'status' => 'success', 'result' => $display));

                }else{
                    $makefile = new filesystem_makefile();
                    $makefile->remove($videoUrl);
                    //$log->tracelog(json_encode($_FILES));
                    $this->del([
                        'type' => 'delVideo',
                        'data' => [
                            'id'    =>  $lastVideo['id_pdn']
                        ]
                    ]);
                    usleep(200000);
                    $this->progress->sendFeedback(array('message' => $this->template->getConfigVars('error_format'), 'progress' => 100, 'status' => 'error', 'error_code' => 'error_data'));

                }

            }else {
                //$log->tracelog(json_encode($_FILES));
                usleep(200000);
                $this->progress->sendFeedback(array('message' => $this->template->getConfigVars('error_format'), 'progress' => 100, 'status' => 'error', 'error_code' => 'error_data'));

            }
        }
    }

    /**
     * @return void
     */
    protected function getVideoList()
    {
        $video = $this->getItems('videos',['id' => $this->edit],'all', false);

        $newVideo = [];
        foreach($video as $key => $item){
            $newVideo[$key]['id_pdn'] = $item['id_pdn'];
            $newVideo[$key]['name_pdn'] = $item['name_pdn'];
            $newVideo[$key]['url_pdn'] = 'https://www.dailymotion.com/video/'.$item['video_id_pdn'];
            $newVideo[$key]['video_id_pdn'] = $item['video_id_pdn'];
            $newVideo[$key]['private_id'] = $item['private_id'];
            $newVideo[$key]['thumbnail_360_url'] = $item['thumbnail_360_url'];
            $newVideo[$key]['thumbnail_720_url'] = $item['thumbnail_720_url'];
        }
        $this->template->assign('videos',$newVideo);
        $assign = [
            'id_pdn',
            'name_pdn' => ['title' => 'name'],
            'video_id_pdn' => ['title' => 'name'],
            'private_id'=> ['title' => 'name'],
            'url_pdn' => ['title' => 'name'],
            'thumbnail_360_url'=> ['title' => 'name','type' => 'bin', 'input' => null, 'class' => ''],
            'thumbnail_720_url' => ['title' => 'name','type' => 'bin', 'input' => null, 'class' => '']
        ];
        $this->data->getScheme(['mc_product_dailymotion'], ['id_pdn','name_pdn','video_id_pdn','private_id','thumbnail_360_url','thumbnail_720_url'], $assign);
    }

    /**
     * @param $id
     * @return void
     * @throws DailymotionAuthRequiredException
     */
    private function getDeleteApi($id){
        $video = $this->getItems('videoId',['id' => $id],'one', false);
        if($video != null) {
            if($video['video_id_pdn'] != null) {
                $aut = $this->getAuthentication();
                // Scopes you need to run your tests
                $scopes = array(
                    'read', 'write', 'delete', 'manage_videos'
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
                if ($access) {
                    $api->delete(
                        "/video/{$video['video_id_pdn']}"
                    );
                }
            }
        }
    }
    /**
     * Execution du plugin dans un ou plusieurs modules core
     */
    public function run(){
        if(isset($this->controller)){
            switch ($this->controller) {
                case 'about':
                    $extends = $this->controller.(!isset($this->action) ? '/index.tpl' : '/pages/edit.tpl');
                    break;
                case 'category':
                case 'product':
                    $extends = 'catalog/'.$this->controller.'/edit.tpl';
                    break;
                case 'news':
                case 'catalog':
                    $extends = $this->controller.'/index.tpl';
                    break;
                case 'pages':
                    $extends = $this->controller.'/edit.tpl';
                    break;
                default:
                    $extends = 'index.tpl';
            }
            $this->template->assign('extends',$extends);
            if (isset($this->subaction)) {

                switch($this->subaction){
                    case 'add':
                        if(isset($_FILES['file']["name"])){
                            $this->setUploadVideo();
                        }
                        break;
                    case 'delete':
                        if(isset($this->id_pdn)){
                            $this->getDeleteApi($this->id_pdn);
                            $this->del([
                                'type' => 'delVideo',
                                'data' => [
                                    'id'    =>  $this->id_pdn
                                ]
                            ]);
                        }
                        break;
                    case 'order':
                        if (isset($this->order) && is_array($this->order)) {
                            $this->upd([
                                'type' => 'order'
                            ]);
                        }
                        break;
                }
            }else {
                if ($this->controller == 'product') {
                    $defaultLanguage = $this->collectionLanguage->fetchData(array('context' => 'one', 'type' => 'default'));
                    $this->getVideoList();
                    $this->modelPlugins->display('mod/index.tpl');
                }
            }
        }
    }
}