<?php
    namespace App\Controller\UserAccessConfiguration;

    require_once("../../Config/BaseController.php");
    require_once("../../Model/UserAccessConfiguration/UserAccessConfigurationQueryHandler.php");

    use App\Config\BaseController as BaseController;
    use App\Model\UserAccessConfiguration\UserAccessConfigurationQueryHandler as QueryHandler;
use Exception;

class UserAccessConfigurationController extends BaseController {
        /**
         * `$menu_id` Set the menu id
         * @var integer
         */
        protected $menu_id = 47;

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

            $this->dbCon        = $dbCon;
            $this->queryHandler = new QueryHandler();
        }

        /**
         * `getDetails` Get details needed in menu configuration
         * @param  string $id
         * @return array    
         */
        public function getDetails($data = [])
        {
            $output = [
                'users' => $this->getUsers()
            ];

            return $output;
        }

        public function saveNewUser($input)
        {
            // print_r($input);
            // die();
            try {
                $this->dbCon->beginTransaction();

                $salt     = '+^7*_<>/?absdia7has723n7as123';
                $lname    = str_replace(' ', '', $input->lname);

                if ($input->chk_super) {
                    $access_type = 1;
                } else if ($input->chk_admin) {
                    $access_type = 2;
                } else if ($input->chk_treas) {
                    $access_type = 3;
                } else if ($input->chk_acctg) {
                    $access_type = 4;
                }
                
                $entryData = [
                    'username'    => strtolower(str_split($input->fname)[0].''.$lname),
                    'password'    => crypt(12345, $salt),
                    'fname'       => $input->fname,
                    'mname'       => isset($input->mname) ? $input->mname : null,
                    'lname'       => $input->lname,
                    'department'  => $input->department,
                    'position'    => $input->position,
                    'access_type' => $access_type,
                    'created_by'  => $_SESSION['user_id'],
                    'created_at'  => date('Y-m-d H:i:s'),
                    'updated_by'  => $_SESSION['user_id'],
                    'updated_at'  => date('Y-m-d H:i:s'),
                ];

                $insertUserData = $this->dbCon->prepare($this->queryHandler->insertUser($entryData));
                $status = $insertUserData->execute($entryData);
                $newUserId = $this->dbCon->lastInsertId();
                $this->systemLogs($newUserId, 'users', 'User Access Configuration', 'add');
            
                $this->dbCon->commit();

                $output = [
                    'status'    => $status,
                    'rowData'   => $this->getUsers($newUserId)[0]
                ];

                return $output;
            
            } catch (Exception $exc) {
                echo $exc->getMessage();
                $this->dbCon->rollBack();
            }
            
        }

        public function saveEditUser($input)
        {
            // print_r($input);
            // die();
            try {
                $this->dbCon->beginTransaction();

                if ($input->chk_super) {
                    $access_type = 1;
                } else if ($input->chk_admin) {
                    $access_type = 2;
                } else if ($input->chk_treas) {
                    $access_type = 3;
                } else if ($input->chk_acctg) {
                    $access_type = 4;
                }
                
                $entryData = [
                    'username'    => $input->username,
                    'fname'       => $input->fname,
                    'mname'       => isset($input->mname) ? $input->mname : null,
                    'lname'       => $input->lname,
                    'department'  => $input->department,
                    'position'    => $input->position,
                    'access_type' => $access_type,
                    'updated_by'  => $_SESSION['user_id'],
                    'updated_at'  => date('Y-m-d H:i:s'),
                ];

                $updateUserData = $this->dbCon->prepare($this->queryHandler->updateUser($input->id, $entryData));
                $status = $updateUserData->execute($entryData);
                $this->systemLogs($input->id, 'users', 'User Access Configuration', 'update');
            
                $this->dbCon->commit();

                $output = [
                    'status'    => $status,
                    'rowData'   => $this->getUsers($input->id)[0]
                ];

                return $output;
            
            } catch (Exception $exc) {
                echo $exc->getMessage();
                $this->dbCon->rollBack();
            }
            
        }

        public function resetUserPassword($input)
        {
            try {
                $this->dbCon->beginTransaction();

                $salt = '+^7*_<>/?absdia7has723n7as123';
                
                $entryData = [
                    'password'    => crypt(12345, $salt),
                    'updated_by'  => $_SESSION['user_id'],
                    'updated_at'  => date('Y-m-d H:i:s'),
                ];

                $updateUserData = $this->dbCon->prepare($this->queryHandler->updateUser($input->id, $entryData));
                $status = $updateUserData->execute($entryData);
                $this->systemLogs($input->id, 'users', 'User Access Configuration', 'reset password');
            
                $this->dbCon->commit();

                $output = [
                    'status' => $status,
                ];

                return $output;
            
            } catch (Exception $exc) {
                echo $exc->getMessage();
                $this->dbCon->rollBack();
            }
        }

        public function archiveUser($input)
        {
            try {
                $this->dbCon->beginTransaction();

                $entryData = [
                    'is_active'   => 0,
                    'updated_by'  => $_SESSION['user_id'],
                    'updated_at'  => date('Y-m-d H:i:s'),
                ];

                $archiveUserData = $this->dbCon->prepare($this->queryHandler->updateUser($input->id, $entryData));
                $status = $archiveUserData->execute($entryData);
                $this->systemLogs($input->id, 'users', 'User Access Configuration', 'archive');
            
                $this->dbCon->commit();

                $output = [
                    'status' => $status,
                ];

                return $output;
            
            } catch (Exception $exc) {
                echo $exc->getMessage();
                $this->dbCon->rollBack();
            }
        }

        public function getUsers($id = '')
        {
            $idCondition = ($id == '') ? false : true;

            $data = [
                'is_active' => 1
            ];

            $users = $this->dbCon->prepare($this->queryHandler->selectUsers($idCondition)->end());

            ($id != '') ? $data['id'] = $id : '';

            $users->execute($data);

            return $users->fetchAll(\PDO::FETCH_ASSOC);
        }
    }