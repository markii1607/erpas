<?php
    namespace App\Controller\Main;

    require_once("MainController.php");

    use App\Controller\Main\MainController as ModuleController;

    class ChangePasswordController extends ModuleController {
        /**
         * `getDetails` Fetching of first needed details in change password.
         * @param  array  $data
         * @return array
         */
        public function getDetails($data = array())
        {
            $output = array(
                // 'old_pass' => $this->getOldPassword()
            );

            return $output;
        }

        /**
         * `getOldPassword` Fetching of old password.
         * @return string
         */
        public function getOldPassword()
        {
            // $oldPass = $this->dbCon->prepare($this->queryHandler->selectUsers(true)->end());
            // $oldPass->execute(array('id' => $_SESSION['user_id']));

            // return $oldPass->fetchAll(\PDO::FETCH_ASSOC);
        }

        /**
         * `saveNewPassword` Save new password.
         * @param object $input
         * 
         * @return  void
         */
        public function saveNewPassword($input)
        {
            $salt = '+^7*_<>/?absdia7has723n7as123';

            $data = array(
                'password' => crypt($input->new_pass, $salt)
            );

            $changePassword = $this->dbCon->prepare($this->queryHandler->updateUser($_SESSION['user_id'], $data));
            $changePassword->execute($data);

            $this->systemLogs($_SESSION['user_id'], 'users', 'change_password', 'edit');
        
            return array('status' => true);
        }
    }