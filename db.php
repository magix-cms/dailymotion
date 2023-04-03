<?php
class plugins_dailymotion_db
{
    /**
     * @param $config
     * @param bool $params
     * @return mixed|null
     * @throws Exception
     */
    public function fetchData(array $config, array $params = [])
    {
        $sql = '';
        $dateFormat = new component_format_date();

        if ($config['context'] === 'all') {
            switch ($config['type']) {
                case 'pages':
                    $limit = '';
                    if ($config['offset']) {
                        $limit = ' LIMIT 0, ' . $config['offset'];
                        if (isset($config['page']) && $config['page'] > 1) {
                            $limit = ' LIMIT ' . (($config['page'] - 1) * $config['offset']) . ', ' . $config['offset'];
                        }
                    }

                    $sql = "SELECT mom.id_om,mom.date_start_om,
                                    DATE_FORMAT(mom.date_start_om, '%H:%i') AS hour_start_om,
                                    mom.date_end_om,
                                    DATE_FORMAT(mom.date_end_om, '%H:%i') AS hour_end_om,
                                    mom.date_register
                                    
                                    FROM mc_offers_monthly AS mom" . $limit;

                    if (isset($config['search'])) {
                        $cond = '';
                        if (is_array($config['search']) && !empty($config['search'])) {
                            $nbc = 0;
                            foreach ($config['search'] as $key => $q) {
                                if($q !== '') {
                                    $cond .= !$nbc ? ' WHERE ' : 'AND ';

                                    switch ($key) {
                                        case 'id_om':
                                            $cond .= 'mom.' . $key . ' = :' . $q . ' ';
                                            break;
                                        case 'date_register':
                                        case 'date_start_om':
                                        case 'date_end_om':
                                        $dateFormat = new component_format_date();
                                        $q = $dateFormat->date_to_db_format($q);
                                        $cond .= "mom.".$key." LIKE '%".$q."%' ";
                                            break;
                                    }
                                    $nbc++;
                                }
                            }

                            $sql = "SELECT mom.id_om,mom.date_start_om,
                                    DATE_FORMAT(mom.date_start_om, '%H:%i') AS hour_start_om,
                                    mom.date_end_om,
                                    DATE_FORMAT(mom.date_end_om, '%H:%i') AS hour_end_om,
                                    mom.date_register
                                    FROM mc_offers_monthly AS mom
									$cond " . $limit;
                        }
                    }
                    break;
                case 'videos':
                    $sql = 'SELECT * FROM mc_product_dailymotion
                            WHERE id_product = :id ORDER BY order_pdn ASC';
                    break;
                case 'videosAll':
                    $sql = 'SELECT * FROM mc_product_dailymotion ORDER BY id_pdn DESC';
                    break;
            }

            try {
                return $sql ? component_routing_db::layer()->fetchAll($sql, $params) : null;
            }
            catch (Exception $e) {
                return 'Exception reçue : '.$e->getMessage();
            }
        }
		elseif ($config['context'] === 'one') {
            switch ($config['type']) {
                case 'root':
                    $sql = 'SELECT * FROM mc_dailymotion ORDER BY id_dm DESC LIMIT 0,1';
                    break;
                case 'nbVideoProduct':
                    $sql = 'SELECT count(id_pdn) AS nbvideo FROM mc_product_dailymotion 
                                WHERE id_product = :id';
                    break;
                case 'productData':
                    $sql = "SELECT mcpc.name_p, mpo.bcb_ref_pos
						FROM mc_catalog_product AS mcp
						JOIN mc_catalog_product_content AS mcpc ON(mcp.id_product = mcpc.id_product)
						JOIN mc_lang AS lang ON(mcpc.id_lang = lang.id_lang)
						LEFT JOIN mc_product_offers mpo on (mcp.id_product = mpo.id_product)
						WHERE mcp.id_product = :id AND mcpc.id_lang = :default_lang";
                    break;
                case 'lastVideo':
                    $sql = 'SELECT * FROM mc_product_dailymotion ORDER BY id_pdn DESC LIMIT 0,1';
                    break;
                case 'videoId':
                    $sql = 'SELECT * FROM mc_product_dailymotion
                            WHERE id_pdn = :id';
                    break;
                case 'videoExist':
                    $sql = 'SELECT * FROM mc_product_dailymotion
                            WHERE video_id_pdn = :id';
                    break;
            }

            try {
                return $sql ? component_routing_db::layer()->fetch($sql, $params) : null;
            }
            catch (Exception $e) {
                return 'Exception reçue : '.$e->getMessage();
            }
        }
    }

    /**
     * @param array $config
     * @param array $params
     * @return string|true
     */
    public function insert(array $config, array $params = []) {
        if (!is_array($config)) return '$config must be an array';

        $sql = '';

        switch ($config['type']) {
            case 'dailymotion':
                $sql = "INSERT INTO mc_dailymotion (apikey_dm, apisecret_dm, username_dm, password_dm, date_register)
                        VALUE (:apikey_dm, :apisecret_dm, :username_dm, :password_dm, NOW())";
                break;
            case 'productVideo':
                $sql = "INSERT INTO mc_product_dailymotion (id_product, name_pdn, video_id_pdn, date_register)
                        VALUE (:id_product, :name_pdn, :video_id_pdn, NOW())";
                break;
        }

        if($sql === '') return 'Unknown request asked';

        try {
            component_routing_db::layer()->insert($sql,$params);
            return true;
        }
        catch (Exception $e) {
            return 'Exception reçue : '.$e->getMessage();
        }
    }

    /**
     * @param $config
     * @param array $params
     * @return bool|string
     */
    public function update(array $config, array $params = []) {
        if (!is_array($config)) return '$config must be an array';

        $sql = '';

        switch ($config['type']) {
            case 'dailymotion':
                $sql = 'UPDATE mc_dailymotion 
						SET 
						    apikey_dm = :apikey_dm, 
							apisecret_dm = :apisecret_dm,
							username_dm = :username_dm,
							password_dm = :password_dm

                		WHERE id_dm = :id_dm';
                break;
            case 'productVideo':
                $sql = 'UPDATE mc_product_dailymotion 
						SET 
						    video_id_pdn = :video_id_pdn,
						    thumbnail_360_url = :thumbnail_360_url,
						    thumbnail_720_url = :thumbnail_720_url

                		WHERE id_pdn = :id';
                break;
            case 'thumbVideo':
                $sql = 'UPDATE mc_product_dailymotion 
						SET 
						    thumbnail_360_url = :thumbnail_360_url,
						    thumbnail_720_url = :thumbnail_720_url

                		WHERE video_id_pdn = :id';
                break;
            case 'order':
                $sql = 'UPDATE mc_product_dailymotion 
						SET order_pdn = :order_pdn
                		WHERE id_pdn = :id_pdn';
                break;
        }

        if($sql === '') return 'Unknown request asked';

        try {
            component_routing_db::layer()->update($sql,$params);
            return true;
        }
        catch (Exception $e) {
            return 'Exception reçue : '.$e->getMessage();
        }
    }

    /**
     * @param array $config
     * @param array $params
     * @return bool|string
     */
    public function delete(array $config, array $params = []) {

        if (!is_array($config)) return '$config must be an array';
        $sql = '';

        switch ($config['type']) {
            case 'delVideo':
                $sql = 'DELETE FROM mc_product_dailymotion 
						WHERE id_pdn IN ('.$params['id'].')';
                $params = array();
                break;
        }

        if($sql === '') return 'Unknown request asked';

        try {
            component_routing_db::layer()->delete($sql,$params);
            return true;
        }
        catch (Exception $e) {
            return 'Exception reçue : '.$e->getMessage();
        }
    }
}