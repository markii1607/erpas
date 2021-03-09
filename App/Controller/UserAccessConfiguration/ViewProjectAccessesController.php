<?php
    namespace App\Controller\UserAccessConfiguration;

    require_once("UserAccessConfigurationController.php");

    use App\Controller\UserAccessConfiguration\UserAccessConfigurationController as ModuleController;

    class ViewProjectAccessesController extends ModuleController {

        /**
         * `getDetails` Fetching of first needed data.
         * @param  array $data
         * @return multi-dimensional array
         */
        public function getDetails($data = [])
        {
            $output = [
                'access'    => $this->getProjectAccesses($data['id']),
            ];

            return $output;
        }

        /**
         * `getUserAccess` Fetching of user access.
         * @param  string $id
         * @return array
         */
        public function getProjectAccesses($userId = '')
        {
            $userIdCondition = ($userId == '') ? false : true;

            $data = [
                'is_active' => 1
            ];

            ($userIdCondition) ? $data['user_id'] = $userId : '';
            
            $projectAccesses = $this->dbCon->prepare($this->queryHandler->selectProjectAccesses($userIdCondition)->end());
            $projectAccesses->execute($data);

            return $projectAccesses->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function archiveAccess($data = [])
        {
            try {
                $this->dbCon->beginTransaction();

                $entryData = array(
                    'is_active'     =>  0,
                    'updated_by'    =>  $_SESSION['user_id'],
                    'updated_at'    =>  date('Y-m-d H:i:s'),
                );

                $archiveAccess = $this->dbCon->prepare($this->queryHandler->updateProjectAccess($data->id, $entryData));
                $status = $archiveAccess->execute($entryData);
                $this->systemLogs($data->id, 'project_accesses', 'user_access_configuration', 'archive');
            
                $this->dbCon->commit();

                $returnData = array(
                    'status'    =>  $status
                );

                return $returnData;
            
            } catch (Exception $exc) {
                echo $exc->getMessage();
                $this->dbCon->rollBack();
            }
            
        }

    }