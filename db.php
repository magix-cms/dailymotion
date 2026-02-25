<?php
class plugins_dailymotion_db
{
    /**
     * @var debug_logger $logger
     */
    protected debug_logger $logger;

    /**
     * @param array $config
     * @param array $params
     * @return array|bool
     */
    public function fetchData(array $config, array $params = []){
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

                    $query = "SELECT mom.id_om,mom.date_start_om,
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

                            $query = "SELECT mom.id_om,mom.date_start_om,
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
                    $query = 'SELECT * FROM mc_product_dailymotion
                            WHERE id_product = :id ORDER BY order_pdn ASC';
                    break;
                case 'videosAll':
                    $query = 'SELECT * FROM mc_product_dailymotion ORDER BY id_pdn DESC';
                    break;
                default:
                    return false;
            }

            try {
                return component_routing_db::layer()->fetchAll($query, $params);
            }
            catch (Exception $e) {
                if(!isset($this->logger)) $this->logger = new debug_logger(MP_LOG_DIR);
                $this->logger->log('statement','db',$e->getMessage(),$this->logger::LOG_MONTH);
            }
        }
		elseif ($config['context'] === 'one') {
            switch ($config['type']) {
                case 'root':
                    $query = 'SELECT * FROM mc_dailymotion ORDER BY id_dm DESC LIMIT 0,1';
                    break;
                case 'nbVideoProduct':
                    $query = 'SELECT count(id_pdn) AS nbvideo FROM mc_product_dailymotion 
                                WHERE id_product = :id';
                    break;
                case 'productData':
                    $query = "SELECT mcpc.name_p, mpo.bcb_ref_pos
						FROM mc_catalog_product AS mcp
						JOIN mc_catalog_product_content AS mcpc ON(mcp.id_product = mcpc.id_product)
						JOIN mc_lang AS lang ON(mcpc.id_lang = lang.id_lang)
						LEFT JOIN mc_product_offers mpo on (mcp.id_product = mpo.id_product)
						WHERE mcp.id_product = :id AND mcpc.id_lang = :default_lang";
                    break;
                case 'lastVideo':
                    $query = 'SELECT * FROM mc_product_dailymotion ORDER BY id_pdn DESC LIMIT 0,1';
                    break;
                case 'videoId':
                    $query = 'SELECT * FROM mc_product_dailymotion
                            WHERE id_pdn = :id';
                    break;
                case 'videoExist':
                    $query = 'SELECT * FROM mc_product_dailymotion
                            WHERE video_id_pdn = :id';
                    break;
                default:
                    return false;
            }

            try {
                return component_routing_db::layer()->fetch($query, $params);
            }
            catch (Exception $e) {
                if(!isset($this->logger)) $this->logger = new debug_logger(MP_LOG_DIR);
                $this->logger->log('statement','db',$e->getMessage(),$this->logger::LOG_MONTH);
            }
        }
        return false;
    }

    /**
     * @param string $type
     * @param array $params
     * @return bool
     */
    public function insert(string $type, array $params = []): bool {
        switch ($type) {
            case 'dailymotion':
                $query = "INSERT INTO mc_dailymotion (apikey_dm, apisecret_dm, username_dm, password_dm, visibility_dm, date_register)
                        VALUE (:apikey_dm, :apisecret_dm, :username_dm, :password_dm, :visibility_dm, NOW())";;
                break;
            case 'productVideo':
                $query = 'INSERT INTO mc_product_dailymotion (id_product, name_pdn, video_id_pdn, visibility_pdm, order_pdn, date_register)
                        SELECT :id_product, :name_pdn, :video_id_pdn, :visibility_pdm, COUNT(id_pdn), NOW() FROM mc_product_dailymotion WHERE id_product IN ('.$params['id_product'].')';
                break;
            default:
                return false;
        }

        try {
            component_routing_db::layer()->insert($query,$params);
            return true;
        }
        catch (Exception $e) {
            if(!isset($this->logger)) $this->logger = new debug_logger(MP_LOG_DIR);
            $this->logger->log('statement','db',$e->getMessage(),$this->logger::LOG_MONTH);
            return false;
        }

    }

    /**
     * @param string $type
     * @param array $params
     * @return bool
     */
    public function update(string $type, array $params = []): bool {
        switch ($type) {
            case 'dailymotion':
                $query = 'UPDATE mc_dailymotion 
						SET 
						    apikey_dm = :apikey_dm, 
							apisecret_dm = :apisecret_dm,
							username_dm = :username_dm,
							password_dm = :password_dm

                		WHERE id_dm = :id_dm';
                break;
            case 'productVideo':
                $query = 'UPDATE mc_product_dailymotion 
						SET 
						    video_id_pdn = :video_id_pdn,
						    visibility_pdm = :visibility_pdm,
						    private_id = :private_id,
						    thumbnail_360_url = :thumbnail_360_url,
						    thumbnail_720_url = :thumbnail_720_url

                		WHERE id_pdn = :id';
                break;
            case 'thumbVideo':
                $query = 'UPDATE mc_product_dailymotion 
						SET 
						    private_id = :private_id,
						    thumbnail_360_url = :thumbnail_360_url,
						    thumbnail_720_url = :thumbnail_720_url

                		WHERE video_id_pdn = :id';
                break;
            case 'order':
                $query = 'UPDATE mc_product_dailymotion 
						SET order_pdn = :order_pdn
                		WHERE id_pdn = :id_pdn';
                break;
            default:
                return false;
        }

        try {
            component_routing_db::layer()->update($query,$params);
            return true;
        }
        catch (Exception $e) {
            if(!isset($this->logger)) $this->logger = new debug_logger(MP_LOG_DIR);
            $this->logger->log('statement','db',$e->getMessage(),$this->logger::LOG_MONTH);
            return false;
        }
    }

    /**
     * @param string $type
     * @param array $params
     * @return bool
     */
    protected function delete(string $type, array $params = []): bool {
        switch ($type) {
            case 'delVideo':
                $query = 'DELETE FROM mc_product_dailymotion 
						WHERE id_pdn IN ('.$params['id'].')';
                $params = [];
                break;
            default:
                return false;
        }

        try {
            component_routing_db::layer()->delete($query,$params);
            return true;
        }
        catch (Exception $e) {
            if(!isset($this->logger)) $this->logger = new debug_logger(MP_LOG_DIR);
            $this->logger->log('statement','db',$e->getMessage(),$this->logger::LOG_MONTH);
            return false;
        }
    }
}