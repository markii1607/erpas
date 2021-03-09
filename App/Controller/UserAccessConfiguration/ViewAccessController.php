<?php
    namespace App\Controller\UserAccessConfiguration;

    require_once("UserAccessConfigurationController.php");

    use App\Controller\UserAccessConfiguration\UserAccessConfigurationController as ModuleController;

    class ViewAccessController extends ModuleController {

        /**
         * `getDetails` Fetching of first needed data.
         * @param  array $data
         * @return multi-dimensional array
         */
        public function getDetails($data = [])
        {
            $output = [
                'access' => $this->getUserAccess('', $data['id'])
            ];

            return $output;
        }

        /**
         * `getUserAccess` Fetching of user access.
         * @param  string $id
         * @return array
         */
        public function getUserAccess($id = '', $userId = '')
        {
            $idCondition     = ($id == '')     ? false : true;
            $userIdCondition = ($userId == '') ? false : true;

            $data = [
                'is_active' => 1
            ];

            $userAccesses = $this->dbCon->prepare($this->queryHandler->selectUserAccesses($idCondition, $userIdCondition)->end());

            ($id != '')     ? $data['id'] = $id          : '';
            ($userId != '') ? $data['user_id'] = $userId : '';

            $userAccesses->execute($data);

            return $userAccesses->fetchAll(\PDO::FETCH_ASSOC);
        }

        /**
         * `archiveAccess` archiving of specific user access from `user_accesses` table.
         * @param  string $id
         * @return boolean
         */
        public function archiveAccess($input)
        {
            $data = [
                'is_active'  => 0,
                'updated_by' => $_SESSION['user_id'],
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $updateUserAccess = $this->dbCon->prepare($this->queryHandler->updateUserAccess($input->id, $data));

            $this->systemLogs($input->id, 'user_accesses', 'delete_access', 'soft_delete');

            return $updateUserAccess->execute($data);
        }
    }