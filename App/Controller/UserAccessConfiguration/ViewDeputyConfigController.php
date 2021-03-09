<?php
    namespace App\Controller\UserAccessConfiguration;

    require_once("UserAccessConfigurationController.php");

    use App\Controller\UserAccessConfiguration\UserAccessConfigurationController as ModuleController;

    class ViewDeputyConfigController extends ModuleController {

        /**
         * `getDetails` Fetching of first needed data.
         * @param  array $data
         * @return multi-dimensional array
         */
        public function getDetails($data = [])
        {
            $output = [
                'deputies' => $this->getUserDeputies($data['id']),
            ];

            return $output;
        }

        public function getUserDeputies($user_id = '')
        {
            $hasUserId = empty($user_id) ? false : true;

            $data = array(
                'is_active' => 1
            );

            ($hasUserId) ? $data['user_id'] = $user_id : '';

            $userDeputies = $this->dbCon->prepare($this->queryHandler->selectUserDeputies($hasUserId)->end());
            $userDeputies->execute($data);
            $userDeputies_result = $userDeputies->fetchAll(\PDO::FETCH_ASSOC);
            
            if(empty($userDeputies_result)){
            }else{
              
                foreach($userDeputies_result as $key => $value){
                    $userDeputies_result[$key]['priviledges'] = json_decode($value['priviledges']);
                }
            }
            return $userDeputies_result ;
        }

        public function changeAccountStatus($input = [])
        {
            try {
                $this->dbCon->beginTransaction();

                $account_status = null;
                if ($input->onleaveStatus) $account_status = 'on-leave';
                if ($input->onlineStatus)  $account_status = 'online';
                if ($input->offlineStatus) $account_status = 'offline';

                $entryData = array(
                    'account_status'    =>  $account_status,
                    'updated_at'        =>  date('Y-m-d H:i:s')
                );

                $updateUserDeputy = $this->dbCon->prepare($this->queryHandler->updateUsers($input->id, $entryData));
                $status = $updateUserDeputy->execute($entryData);
                $this->systemLogs($input->id, 'users', 'user_access_configuration', 'update');
            
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

        public function archiveDeputy($input = [])
        {
            try {
                $this->dbCon->beginTransaction();

                $entryData = array(
                    'is_active'     =>  0,
                    'updated_by'    =>  $_SESSION['user_id'],
                    'updated_at'    =>  date('Y-m-d H:i:s')
                );

                $archiveData = $this->dbCon->prepare($this->queryHandler->updateUserDeputies($input->id, $entryData));
                $status = $archiveData->execute($entryData);
                $this->systemLogs($input->id, 'user_deputies', 'user_access_configuration', 'archive');
            
                $this->dbCon->commit();

                $returnData = array(
                    'status' => $status
                );

                return $returnData;
            
            } catch (Exception $exc) {
                echo $exc->getMessage();
                $this->dbCon->rollBack();
            }
            
        }
    }