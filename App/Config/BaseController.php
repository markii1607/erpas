<?php 
    namespace App\Config;

    session_start();

    require __DIR__.'/../../vendor/autoload.php';
    require __DIR__."/../Model/Base/BaseQueryHandler.php";
    require __DIR__.'/../Helper/Helper.php';

    use App\Model\Base\BaseQueryHandler as BaseQueryHandler;

    class BaseController { 
        
        /**
         * `__construct` Constructor.
         */
        public function __construct()
        {
            $this->baseQueryHandler = new BaseQueryHandler();
        }

        /**
         * `formatDate` format date in mysql format date
         * @param  string | nullable  $date
         * @param  boolean $day
         * @param  boolean $month
         * @param  boolean $year
         * @return string | date
         */
        public function formatDate($date = null, $day = false, $month = false, $year = false)
        {
            $covertedDate = date('Y-m-d', strtotime($date));
            $explodeDate  = explode('-', $covertedDate);

            if ($day) {
                return $explodeDate[2];
            } else if ($month) {
                return '0000-'.$explodeDate[1].'-00';
            } else if ($year) {
                return $explodeDate[0];
            } else {
                return $covertedDate;
            }
        }

        /**
         * `formatMoney` Formating of money or reversing to save in database.
         * @param  string  $money
         * @param  boolean $reverse
         * @return string
         */
        public function formatMoney($money = '', $reverse = false)
        {
            $output = '';

            if (!$reverse) {
                $output = preg_replace('/[\₱,]/', '', $money);
                $output = floatval($output);
            } else {
                $output = '₱ '.number_format($money, 2, '.', ',');
            }

            return $output;
        }

        /**
         * `searchArray` Searching of value in an array or multidimentional array.
         * @param  string $needle
         * @param  array $haystack
         * @return boolean
         */
        public function searchArray($needle, $haystack) {
            if(in_array($needle, $haystack)) {
                 return true;
            }

            foreach($haystack as $element) {
                 if(is_array($element) && $this->searchArray($needle, $element))
                     return true;
            }

            return false;
        }

        /**
         * `arrayToObject` Convert multidimentional array to object.
         * @param  array  $array
         * @return object
         */
        public function arrayToObject($array = [])
        {
            return json_decode(json_encode($array));
        }

        /**
         * `objectToArray` Converting multidimentional object to array.
         * @param  array  $arrayOfObject
         * @return array
         */
        public function objectToArray($arrayOfObject = [])
        {
            $json  = json_encode($arrayOfObject);
            $array = json_decode($json, true);

            return $array;
        }

        /**
         * `multiArrayUnique` Return unique values from nested or multi-dimentional array.
         * @param  array  $nestedArray
         * @return array
         */
        public function multiArrayUnique($nestedArray = [])
        {
            return array_map("unserialize", array_unique(array_map("serialize", $nestedArray)));
        }

        /**
         * `customArrayUnique` Customized array unique of PHP.
         * @param  array  $array
         * @param  array  $condition
         * @return array
         */
        public function customArrayUnique($array = array(), $condition = array())
        {
            $output = array();

            foreach ($array as $key => $value) {
                $unique = true;

                foreach ($output as $oKey => $oVal) {
                    // TODO:
                    //  - dynamic construction of condition
                    // foreach ($condition as $cKey => $cValue) {
                    //     if ($cKey != count($condition) - 1) {
                    //         $sample = $sample . ($value[$cValue] == $oVal[$cValue]).' && ';
                    //     } else {
                    //         $sample = $sample . ($value[$cValue] == $oVal[$cValue]);
                    //     }
                    // }

                    if (count($condition) == 1) {
                        if (($value[$condition[0]] == $oVal[$condition[0]])) {
                            $unique = false;
                        }
                    } else if (count($condition) == 2) {
                        if (($value[$condition[0]] == $oVal[$condition[0]]) && ($value[$condition[1]] == $oVal[$condition[1]])) {
                            $unique = false;
                        }
                    } else if (count($condition) == 3) {
                        if (($value[$condition[0]] == $oVal[$condition[0]]) && ($value[$condition[1]] == $oVal[$condition[1]]) && ($value[$condition[2]] == $oVal[$condition[2]])) {
                            $unique = false;
                        }
                    }
                }

                if ($unique) {
                    array_push($output, $value);
                }
            }

            return $output;
        }

        /**
         * `systemLogs` Logs the action taken of every user.
         * @param  string $action
         * @param  string $tableId
         * @param  string $tableName
         * @param  string $configModule
         * @return boolean
         */
        public function systemLogs($tableId = '', $tableName = '',  $configModule = '', $action = '', $id = '')
        {
            $data = [
                'user_id'              => empty($id) ? $_SESSION['user_id'] : $id,
                'menu_id'              => $this->menu_id,
                'table_id'             => $tableId,
                'table_name'           => $tableName,
                'configuration_module' => $configModule,
                'action'               => $action,
                'date'                 => date('Y-m-d H:i:s'),
                'computer_name'        => '',
                'computer_ip'          => ''
                // 'computer_name'        => $_SESSION['computer_name'],
                // 'computer_ip'          => $_SESSION['computer_ip']
            ];

            $systemLogs = $this->dbCon->prepare($this->queryHandler->systemLogs($data));

            $systemLogs->execute($data);
        }

        /**
         * `loginSystemLogs` Logs the action taken of every user.
         * @param  string $action
         * @param  string $tableId
         * @param  string $tableName
         * @param  string $configModule
         * @return boolean
         */
        public function loginSystemLogs($tableId = '', $tableName = '',  $configModule = '', $action = '', $id = '')
        {
            $data = [
                'user_id'              => empty($id) ? $_SESSION['user_id'] : $id,
                'menu_id'              => $this->menu_id,
                'table_id'             => $tableId,
                'table_name'           => $tableName,
                'configuration_module' => $configModule,
                'action'               => $action,
                'date'                 => date('Y-m-d H:i:s'),
                'computer_name'        => gethostbyaddr($_SERVER['REMOTE_ADDR']),
                'computer_ip'          => $_SERVER['REMOTE_ADDR']
            ];

            $systemLogs = $this->dbCon->prepare($this->queryHandler->systemLogs($data));
            $systemLogs->execute($data);

            // $_SESSION['computer_name'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            // $_SESSION['computer_ip']   = $_SERVER['REMOTE_ADDR'];
        }

        /**
         * `checkSession` Check if there is session user exist.
         * @return mixed
         */
        public function checkSession()
        {
            $session = true;

            if (!isset($_SESSION['user_id'])) {
                $session = false;
            }

            return $session;
        }

        /**
         * `getUserLevel` Getting of user access level.
         * @return array
         */
        public function getUserLevel()
        {
            $sql = "
                SELECT 
                    UA.level 
                FROM 
                    `user_accesses` UA 
                WHERE 
                        user_id = :user_id 
                    AND 
                        menu_id = :menu_id
            ";

            $userAccesses = $this->dbCon->prepare($sql);
            $userAccesses->execute([':user_id' => $_SESSION['user_id'], ':menu_id' => $this->menu_id]);
            
            $output = $userAccesses->fetchAll(\PDO::FETCH_ASSOC)[0];
            
            $output = (empty($output))? ['level' => '0,0,0'] : $output;

            $_SESSION['user_level'] = $output;

            $output = explode(',', $output['level']);
            
            $result = [
                'addBtn'    => ($output[0] == 1)? true : false,
                'editBtn'   => ($output[1] == 1)? true : false,
                'delBtn'    => ($output[2] == 1)? true : false
            ];

            return $result;
        }

        // SESSION HANDLING
        public function getClientIP(){
            // if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)){
            //        return  $_SERVER["HTTP_X_FORWARDED_FOR"];  
            // }else if (array_key_exists('REMOTE_ADDR', $_SERVER)) { 
            //        return $_SERVER["REMOTE_ADDR"]; 
            // }else if (array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
            //        return $_SERVER["HTTP_CLIENT_IP"]; 
            // } 
            return $_SERVER["REMOTE_ADDR"]; 
       }


        public function insertSessionLogs($ip, $credentials)
        {
            $entryData = array(
                'ip_address'    => $ip,
                'user_id'       => $_SESSION['user_id'],
                'session_data'  => json_encode($credentials),
                'status'        => "logged_in"
            );

            $hasEntryExistingAlready = $this->checkSessionLogs($ip);
            // print_r($hasEntryExistingAlready);
            if(COUNT($hasEntryExistingAlready) == 0){

                $logSession = $this->dbCon->prepare($this->queryHandler->insertSessionLogs($entryData));
                $logSession->execute($entryData);

            }else{
                
                foreach ($hasEntryExistingAlready as $key => $value) {
                    $logSession = $this->dbCon->prepare($this->queryHandler->updateSessionLogs($value['id'], $entryData));
                    $logSession->execute($entryData);
                }
            }     
        }

        public function checkSessionLogs($ip)
        {

            $hasIp = ($ip) ? true : false;


            ($hasIp) ? $entryData['ip_address'] = $ip : '';
            $selectlogSession = $this->dbCon->prepare($this->queryHandler->selectSessionLogs($hasIp)->end());
            $selectlogSession->execute($entryData);

            $returnselectlogSession = $selectlogSession->fetchAll(\PDO::FETCH_ASSOC);
            
    
            return $returnselectlogSession;
       }

       public function getUserName($user)
       {   
            $hasUserId = ($user) ? true : false;
        
            ($hasUserId) ? $entryData['user_id'] = $user : '';
            // print_r($entryData);
            // date("Y-m-d H:i:s",  $this->formatDate($data->w_date_from).' 00:00:00' ))
            $selectUsername = $this->dbCon->prepare($this->queryHandler->selectUserName($hasUserId)->end());
            $selectUsername->execute($entryData);

            $returnUserName = $selectUsername->fetchAll(\PDO::FETCH_ASSOC);
            
            return $returnUserName;
       }

        /**
         * `getTotalHours` Fetching of total hours.
         * @param  string $from
         * @param  string $to
         * @return float
         */
        public function getTotalHours($from, $to)
        {
            $amTotalHours = 0;
            $pmTotalHours = 0;

            $explodeFrom  = explode(' ', $from);
            $explodeTo    = explode(' ', $to);

            // get morning and afternoon total hours with from: AM and to: PM validation.
            if ($explodeFrom[1] == 'AM' && $explodeTo[1] == 'PM') {
                // morning
                $explodeFtime           = explode(':', $explodeFrom[0]);
                $formatExplodeFtimeHour = ($explodeFrom[0] == '12') ? '0' : $explodeFrom[0];
                $convertFtimeToSeconds  = (((int)$formatExplodeFtimeHour * 60) * 60) + (((int)$explodeFtime[1] * 60));
                $amTotalHours           = (($amTotalHours + (43200 - $convertFtimeToSeconds)) / 60) / 60;

                // afternoon
                $explodeTtime                  = explode(':', $explodeTo[0]);
                $convertTtimeToSeconds         = (((int)$explodeTo[0] * 60) * 60) + (((int)$explodeFtime[1] * 60));
                // $formatConvertedTtimeToSeconds = ($convertTtimeToSeconds >= 36000) ? $convertTtimeToSeconds - 3600 : $convertTtimeToSeconds;
                // $pmTotalHours                  = (($pmTotalHours + ($formatConvertedTtimeToSeconds - 3600)) / 60) / 60;
                $pmTotalHours                  = (($pmTotalHours + ($convertTtimeToSeconds - 3600)) / 60) / 60;
            }

            // get morning and afternoon total hours with from: AM and to: AM validation.
            if ($explodeFrom[1] == 'AM' && $explodeTo[1] == 'AM') {
                $explodeFtime           = explode(':', $explodeFrom[0]);
                $explodeTtime           = explode(':', $explodeTo[0]);

                // to != 12
                if ((int) $explodeTtime[0] != 12) {
                    // from
                    $formatExplodeFtimeHour = ($explodeFrom[0] == '12') ? '0' : $explodeFrom[0];
                    $convertFtimeToSeconds  = (((int)$formatExplodeFtimeHour * 60) * 60) + (((int)$explodeFtime[1] * 60));

                    // to
                    $formatExplodeTtimeHour = ($explodeTo[0] == '12') ? '0' : $explodeTo[0];
                    $convertTtimeToSeconds  = (((int)$formatExplodeTtimeHour * 60) * 60) + (((int)$explodeTtime[1] * 60));

                    $amTotalHours           = (($amTotalHours + ($convertTtimeToSeconds - $convertFtimeToSeconds)) / 60) / 60;
                }

                // to == 12
                if ((int) $explodeTtime[0] == 12) {
                    // morning
                    $explodeFtime           = explode(':', $explodeFrom[0]);
                    $formatExplodeFtimeHour = ($explodeFrom[0] == '12') ? '0' : $explodeFrom[0];
                    $convertFtimeToSeconds  = (((int)$formatExplodeFtimeHour * 60) * 60) + (((int)$explodeFtime[1] * 60));
                    $amTotalHours           = (($amTotalHours + (43200 - $convertFtimeToSeconds)) / 60) / 60;

                    // afternoon
                    $convertTtimeToSeconds         = (((int)$explodeTo[0] * 60) * 60);
                    $formatConvertedTtimeToSeconds = ($convertTtimeToSeconds >= 36000) ? $convertTtimeToSeconds - 3600 : $convertTtimeToSeconds;
                    $pmTotalHours                  = (($pmTotalHours + ($formatConvertedTtimeToSeconds - 3600)) / 60) / 60;
                }
            }

            // get morning and afternoon total hours with from: PM and to: PM validation.
            if ($explodeFrom[1] == 'PM' && $explodeTo[1] == 'PM') {
                $explodeFtime           = explode(':', $explodeFrom[0]);
                $explodeTtime           = explode(':', $explodeTo[0]);

                $convertFtimeToSeconds  = (((int)$explodeFrom[0] * 60) * 60) + (((int)$explodeFtime[1] * 60));
                $convertTtimeToSeconds  = (((int)$explodeTo[0] * 60) * 60) + (((int)$explodeTtime[1] * 60));
                $pmTotalHours           = (($pmTotalHours + ($convertTtimeToSeconds - $convertFtimeToSeconds)) / 60) / 60;
            }

            $output = $amTotalHours + $pmTotalHours;

            return $output;
        }
    }