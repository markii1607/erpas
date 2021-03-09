<?php
    namespace App\Controller\UserAccessConfiguration;

    require_once("ViewDeputyConfigController.php");

    use App\Controller\UserAccessConfiguration\ViewDeputyConfigController as SubModuleController;

    class AddDeputyController extends SubModuleController {

        /**
         * `getDetails` Fetching of first needed details.
         * @return multi-dimesional array
         */
        public function getDetails($data = [])
        {
            $output = [
                'users' => $this->getInformation()
            ];

            return $output;
        }

        public function saveDeputies($input = [])
        {
            try {
                $this->dbCon->beginTransaction();

                foreach ($input->tblData as $key => $value) {
                    if (isset($value->status)) {
                        if (!empty($value->status) || $value->status != '' || $value->status != null) {
                            $status = 1;
                            $strStatus = 'ON';
                        } else {
                            $status = 0;
                            $strStatus = 'OFF';
                        }
                    } else {
                        $status = 0;
                        $strStatus = 'OFF';
                    }

                    $priviledges = array(
                        "approve" => 1,
                        "upload"  => 1  
                    );

                    

                    $entryData = array(
                        'user_id'   =>  $input->user_id,
                        'deputy_id' =>  $value->deputy->id,
                        'status'    =>  $status,
                        'priviledges' =>  json_encode($priviledges),
                        'created_by'=>  $_SESSION['user_id'],
                        'created_at'=>  date('Y-m-d H:i:s'),
                        'updated_by'=>  $_SESSION['user_id'],
                        'updated_at'=>  date('Y-m-d H:i:s'),
                    );

                    $insertUserDeputy = $this->dbCon->prepare($this->queryHandler->insertUserDeputies($entryData));
                    $status = $insertUserDeputy->execute($entryData);
                    $newID = $this->dbCon->lastInsertId();
                    $this->systemLogs($newID, 'user_deputies', 'user_access_configuration', 'insert');

                    $input->tblData[$key]->id               = $newID;
                    $input->tblData[$key]->full_name        = $value->deputy->full_name;
                    $input->tblData[$key]->position_name    = $value->deputy->position_name;
                    $input->tblData[$key]->department_name  = $value->deputy->department_name;
                    $input->tblData[$key]->status           = $strStatus;
                }

                $this->dbCon->commit();

                $returnData = array(
                    'status' => $status,
                    'tblData'=> $input->tblData
                );

                return $returnData;
            
            } catch (Exception $exc) {
                echo $exc->getMessage();
                $this->dbCon->rollBack();
            }
            
        }

        public function updateDeputyStatus($input = [])
        {

            // var_dump($input);

            if(isset($input->priviledges)){
                foreach($input->priviledges as $key => $value){
                    ($value == true) ? $input->priviledges->$key = 1 : $input->priviledges->$key = 0;
                }

                $priviledges = json_encode($input->priviledges);
            }else{

            }

            try {
                $this->dbCon->beginTransaction();

                $entryData = array(
                    'status'        => !empty($input->newStatus) ? 1 : 0,
                    'updated_by'    => $_SESSION['user_id'],
                    'updated_at'    => date('Y-m-d H:i:s'),
                    'priviledges'   => $priviledges
                );

                $updateData = $this->dbCon->prepare($this->queryHandler->updateUserDeputies($input->id, $entryData));
                $status = $updateData->execute($entryData);
                $this->systemLogs($input->id, 'user_deputies', 'user_access_configuration', 'update');

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