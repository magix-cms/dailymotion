<?php
/*
 # -- BEGIN LICENSE BLOCK ----------------------------------
 #
 # This file is part of MAGIX CMS.
 # MAGIX CMS, The content management system optimized for users
 # Copyright (C) 2008 - 2022 magix-cms.com <support@magix-cms.com>
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
/**
 * MAGIX CMS
 * @category plugins
 * @package brand
 * @copyright  MAGIX CMS Copyright (c) 2008 - 2023 Gerits Aurelien,
 * http://www.magix-cms.com
 * @license Dual licensed under the MIT or GPL Version 3 licenses.
 * @version 1.0
 * @author: Gerits Aurelien
 * @name plugins_dailymotion_public
 */
class plugins_dailymotion_public extends plugins_dailymotion_db
{
    /**
     * @var frontend_model_template $template
     * @var frontend_model_data $template
     * @var frontend_model_module $module
     * @var frontend_model_catalog $modelCatalog
     */
    protected frontend_model_template $template;
    protected frontend_model_data $data;
    protected frontend_model_module $module;
    protected frontend_model_catalog $modelCatalog;

    /**
     * @var array $mods
     */
    protected array $mods;

    /**
     * @var string $controller
     */
    public string
        $controller;

    /**
     * @var int $id
     */
    public int
        $id;


    /**
     * plugins_brand_public constructor.
     * @param frontend_model_template|null $t
     */
    public function __construct(frontend_model_template $t = null)
    {
        $this->template = $t instanceof frontend_model_template ? $t : new frontend_model_template();
        $this->data = new frontend_model_data($this, $this->template);

        if (http_request::isGet('controller')) $this->controller = form_inputEscape::simpleClean($_GET['controller']);
        if (http_request::isGet('id')) $this->id = form_inputEscape::numeric($_GET['id']);
    }
    // --- Database methods
    /**
     * Assign data to the defined variable or return the data
     * @param string $type
     * @param array|int|null $id
     * @param string|null $context
     * @param bool|string $assign
     * @return mixed
     */
    public function getItems(string $type, $id = null, string $context = null, $assign = true) {
        return $this->data->getItems($type, $id, $context, $assign);
    }
    /**
     * @param array $rawData
     * @return array
     */
    public function setVideoData(array $rawData): array {
        $videos = [];

        if (!empty($rawData)) {
            foreach ($rawData as $key => $value) {

                if (isset($value['id_pdn'])) {

                    $videos[$key]['id'] = $value['id_pdn'];
                    $videos[$key]['video_id'] = $value['video_id_pdn'];
                    $videos[$key]['thumbnail_360_url'] = $value['thumbnail_360_url'];
                    $videos[$key]['thumbnail_720_url'] = $value['thumbnail_720_url'];
                    $videos[$key]['name'] = $value['name_pdn'];
                    $videos[$key]['date']['register'] = $value['date_register'];
                }
            }
        }

        return $videos;
    }
    /**
     * @param array $data
     * @return array
     */
    public function extendProduct(array $data): array {
        return $this->setVideoData($data);
    }
    /**
     * @param array $data
     * @return array
     */
    public function extendProductData(array $data) : array{
        $extend['newRow'] = ['dailymotion' => 'dailymotion'];
        $extend['collection'] = 'dailymotion';
        if (http_request::isGet('id')) $this->id = form_inputEscape::numeric($_GET['id']);
        if(!empty($data)) {
            $videoCollection = $this->getitems('videos',['id' => $this->id ?? $data['id_product']],'all',false);
            $product = $this->extendProduct($videoCollection);
            $extend['data'] = empty($product) ? [] : $product;
        }
        return $extend;
    }
}