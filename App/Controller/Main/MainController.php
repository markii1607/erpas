<?php
    namespace App\Controller\Main;

    require_once("../../Config/BaseController.php");
    require_once("../../Model/Main/MainQueryHandler.php");

    use App\Config\BaseController as BaseController;
    use App\Model\Main\MainQueryHandler as QueryHandler;

    class MainController extends BaseController {

        /**
         * `$menu_id` Set the menu id
         * @var integer
         */
        public $menu_id = NULL;

    	/**
    	 * `$dbCon` Concern in database connection.
    	 * @var private class
    	 */
        protected $dbCon;

        /**
         * `$queryHandler` Handles query.
         * @var private class
         */
        protected $queryHandler;
        
        /**
         * `__construct` Constructor
         * @param object $dbCon        Database connetor
         * @param string $queryHandler Query String
         */
        public function __construct(
            $dbCon
        ) {
            parent::__construct();

            $this->dbCon 		= $dbCon;
            $this->queryHandler = new QueryHandler();
        } 

        /**
         * `getDetails` Get details needed in main page.
         * @param  string $id
         * @return array
         */
        public function getDetails($data = [])
        {
            $output = [
                'check_session'         => $this->checkSession(),
                'employee_informations' => $this->getInformations($_SESSION['user_id']),
                'accessed_sub_menus'    => $this->getSubMenus($_SESSION['user_id']),
                'employees'             => $this->getInformation('',$_SESSION['user_id']),
                'head_employees'        => $this->getHeadEmployees('',$_SESSION['user_id']),
            ];

            return $output;
        }

        /**
         * `getNotifications` Fetching Notification information.
         * @return array
         */
        public function getNotifications()
        {
            $notifications = array(
                'notifications'          => $this->getNotificationsInfo(true),
                'notifications_inactive' => $this->getNotificationsInfo(),
                'tasks'                  => $this->getTasksInfo(true),
                'tasks_inactive'         => $this->getTasksInfo(),
            );

            
            return $notifications;
        }

        /**
         * `getNotifications` Fetching Notification information.
         * @return array
         */
        public function getNotificationsInfo($active = false)
        {
            $user = $_SESSION['user_id'];

            $get_notif = $this->dbCon->prepare($this->queryHandler->selectNotifications("notif", $active)->end());
            $get_notif->execute(['user_id' => $user]);

            $notifications = $get_notif->fetchAll(\PDO::FETCH_ASSOC);

            foreach($notifications as $key => $value){
                $notifications[$key]['prs_info'] = $this->getprsInfo($value['prs_id']);

                $notifications[$key]['requestor_info'] =  $this->getInformations($_SESSION['user_id'])[0];
                $notifications[$key]['assignee_info'] =  $this->getInformations($value['assignee_id'])[0];
        
                $notifications[$key]['project_info'] =  $this->get_N_Projects($notifications[$key]['prs_info']['project_id'])[0];
                $notifications[$key]['department_info'] =  $this->get_N_Departments($notifications[$key]['prs_info']['department_id'])[0];


                switch($value['notif_status']){
                    case '1':
                        $notifications[$key]['status_message'] = "PENDING";
                        break;
                    case '2':
                        $notifications[$key]['status_message'] = "APPROVED";
                        break;
                    case '3':
                        $notifications[$key]['status_message'] = "DISAPPROVED";
                        break;
                    case '4':
                        $notifications[$key]['status_message'] = "CANCELLED";
                        break;
                    default:
                }
            }
            
            return $notifications;
        }
        

        
        /**
         * `getTasks` Fetching Tasks information.
         * @return array
         */
        public function getTasksInfo($active = false)
        {
            $user = $_SESSION['user_id'];

            $get_notif = $this->dbCon->prepare($this->queryHandler->selectNotifications("task", $active)->end());
            $get_notif->execute(['user_id' => $user]);

            $tasks = $get_notif->fetchAll(\PDO::FETCH_ASSOC);

            foreach($tasks as $key => $value){
                $tasks[$key]['prs_info'] = $this->getprsInfo($value['prs_id']);

                $tasks[$key]['requestor_info'] =  $this->getInformations($_SESSION['user_id'])[0];
                $tasks[$key]['assignee_info'] =  $this->getInformations($value['assignee_id'])[0];

                $tasks[$key]['project_info'] =  $this->get_N_Projects($tasks[$key]['prs_info']['project_id'])[0];
                $tasks[$key]['department_info'] =  $this->get_N_Departments($tasks[$key]['prs_info']['department_id'])[0];

                switch($value['notif_status']){
                    case '1':
                        $tasks[$key]['message'] = "CREATED";
                        break;
                    case '2':
                        $tasks[$key]['message'] = "APPROVED";
                        break;
                    case '3':
                        $tasks[$key]['message'] = "DISAPPROVED";
                        break;
                    case '4':
                        $tasks[$key]['message'] = "CANCELLED";
                        break;
                    case '5':
                        $tasks[$key]['message'] = "PENDING";
                        break;
                    default:
                }
                

            }
            
            return $tasks;
        }



        /**
         * `getProjects` Fetching Project Informations.
         * @param  string $projectId
         * @return array
         */
        public function get_N_Projects($Id = '')
        {
        $hasId = empty($Id) ? false : true;
        $entryData = array(
            'is_active' => 1,
        );
    
        if($hasId){
            ($hasId) ? $entryData['id'] = $Id : '';
            $projects = $this->dbCon->prepare($this->queryHandler->select_N_Projects($hasId)->end());
            $projects->execute($entryData);
            $projects_res = $projects->fetchAll(\PDO::FETCH_ASSOC);
        }else{
            $projects_res = false;
        }
     
     
        return  $projects_res;
        }

        /**
         * `getDepartments` Fetching Department Informations.
         * @param  string $departmentId
         * @return array
         */
        public function get_N_Departments($Id = '')
        {
        $hasId = empty($Id) ? false : true;
        $entryData = array(
            'is_active' => 1
        );
    
       
        if($hasId){
            ($hasId) ? $entryData['id'] = $Id : '';
            $departments = $this->dbCon->prepare($this->queryHandler->select_N_Departments($hasId)->end());
            $departments->execute($entryData);
            $departments_res = $departments->fetchAll(\PDO::FETCH_ASSOC);
        }else{
            $departments_res = false;
        }

        return $departments_res;
        }

                
        /**
         * `prsInfo` Fetching employee informations.
         * @param  string $userId
         * @return array
         */
        public function getprsInfo($prsId = '')
        {
            $gettprsInfo = $this->dbCon->prepare($this->queryHandler->selectPrsInfo()->end());
            $gettprsInfo->execute(['prs_id' => $prsId]);
            $prsInfo = $gettprsInfo->fetchAll(\PDO::FETCH_ASSOC)[0];
            $prsInfo['signatories'] = json_decode($prsInfo['signatories']);

            return $prsInfo;
        }
        
        
        /**
         * `getInformations` Fetching employee informations.
         * @param  string $userId
         * @return array
         */
        public function getInformations($userId = '')
        {
            $informations = $this->dbCon->prepare($this->queryHandler->selectUsers(true)->end());
            $informations->execute(['is_active' => 1, 'id' => $userId]);

            return $informations->fetchAll(\PDO::FETCH_ASSOC);
        }

        /**
         * `getSubMenus` Fetching of sub menus.
         * @param  array $parentMenus
         * @return array
         */
        public function getSubMenus($userId = '')
        {
            $userIdCondition = ($userId == '') ? false : true;

            $data = [
                'is_active' => 1,
            ];

            ($userId != '') ? $data['user_id'] = $userId : '';

            $childMenu = $this->dbCon->prepare($this->queryHandler->selectMenus(false, $userIdCondition)->end());
            $childMenu->execute($data);

            return $childMenu->fetchAll(\PDO::FETCH_ASSOC);
        }

        /**
         * `signOut` destroying session.
         * @return destroy session
         */
        public function signOut()
        {    

            $ip = $this->getClientIP();
            $hasEntryExistingAlready = $this->checkSessionLogs($ip);

            $entryData = array(
                'ip_address'    => $ip,
                'user_id'       => $_SESSION['user_id'],
                'session_data'  => json_encode($_SESSION),
                'status'        => "logged_off"
              );

            if(COUNT($hasEntryExistingAlready) == 0){
            }else{
                foreach ($hasEntryExistingAlready as $key => $value) {
                    $logSession = $this->dbCon->prepare($this->queryHandler->updateSessionLogs($value['id'], $entryData));
                    $logSession->execute($entryData);
                }
            }

            
            session_destroy();
        }

        /////////////////////////////////////////For Employee Records/////////////////////////////////////////////

         /**
         * `getInformation` Fetching all department and information.
         * @return array
         */
        public function getInformation($id = '', $user_id= '')
        {
            $idCondition = ($id == '') ? false : true;
            $user_idCondition = ($user_id == '') ? false : true;

            $data = [
                'is_active' => 1
            ];

            // $employees = $this->dbCon->prepare($this->queryHandler->selectEmployees($idCondition)->end());
            
            ($id != '') ? $data['id'] = $id : '';
            ($user_id != '') ? $data['user_id'] = $user_id : '';
            
            $employees = $this->dbCon->prepare($this->queryHandler->selectPersonalInformations($idCondition, $user_idCondition)->end());
            $employees->execute($data);

            return $employees->fetchAll(\PDO::FETCH_ASSOC);
        }

          /**
         * `getInformation` Fetching all department and information.
         * @return array
         */
        public function getHeadEmployees($id = '')
        {
            $idCondition = ($id == '') ? false : true;

            $data = [
                'is_active' => 1
            ];

            $employees = $this->dbCon->prepare($this->queryHandler->selectPersonalInformations($idCondition)->end());
            // $employees = $this->dbCon->prepare($this->queryHandler->selectEmployees($idCondition)->end());

            ($id != '') ? $data['id'] = $id : '';

            $employees->execute($data);

            return $employees->fetchAll(\PDO::FETCH_ASSOC);
        }

        /**
         * `getRegions` Fetching all regions.
         * @return array
         */
        public function getRegions($data = [])
        {
            $regions = $this->dbCon->prepare($this->queryHandler->selectRegions()->end());
            $regions->execute(['search' => '%'.$data['search'].'%']);

            return $regions->fetchAll(\PDO::FETCH_ASSOC);
        }

        /**
         * `getProvinces` Fetching all provinces.
         * @return array
         */
        public function getProvinces($data = [])
        {
            $provinces = $this->dbCon->prepare($this->queryHandler->selectProvinces()->end());
            $provinces->execute(['search' => '%'.$data['search'].'%']);

            return $provinces->fetchAll(\PDO::FETCH_ASSOC);
        }

        /**
         * `getCities` Fetching all cities/municipalities.
         * @return array
         */
        public function getCities($data = [])
        {
            $cities = $this->dbCon->prepare($this->queryHandler->selectCities()->end());
            $cities->execute(['search' => '%'.$data['search'].'%']);

            return $cities->fetchAll(\PDO::FETCH_ASSOC);
        }

        /**
         * `getBarangays` Fetching all barangays.
         * @return array
         */
        public function getBarangays($data = [])
        {
            $barangays = $this->dbCon->prepare($this->queryHandler->selectBarangays()->end());
            $barangays->execute(['search' => '%'.$data['search'].'%']);

            return $barangays->fetchAll(\PDO::FETCH_ASSOC);
        }

        /**
         * `getPositions` Fetching all positions.
         * @return array
         */
        public function getPositions($data = [])
        {
            $positions = $this->dbCon->prepare($this->queryHandler->selectPositions(false, true)->end());
            $positions->execute(['search' => '%'.$data['search'].'%']);

            return $positions->fetchAll(\PDO::FETCH_ASSOC);
        }

        /**
         * `getDepartments` Fetching all departments.
         * @return array
         */
        public function getDepartments($data = [])
        {
            $departments = $this->dbCon->prepare($this->queryHandler->selectDepartments(false, true)->end());
            $departments->execute(['search' => '%'.$data['search'].'%']);

            return $departments->fetchAll(\PDO::FETCH_ASSOC);
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
    }