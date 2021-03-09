<?php
    namespace App\Controller\UserAccessConfiguration;

    require_once("ViewAccessController.php");

    use App\Controller\UserAccessConfiguration\ViewAccessController as SubModuleController;

    class ProjectAccessController extends SubModuleController {

        /**
         * `getDetails` Fetching of first needed details.
         * @return multi-dimesional array
         */
        public function getDetails($data = [])
        {
            $output = [
                'projects' => $this->getProjects()
            ];

            return $output;
        }

        public function getProjects()
        {
            $projects = $this->dbCon->prepare($this->queryHandler->selectProjects()->end());
            $projects->execute(array('is_active' => 1));

            return $projects->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function saveAccess($input = [])
        {
            try {
                $this->dbCon->beginTransaction();

                foreach ($input->accesses as $key => $value) {
                    $entryData = array(
                        'user_id'       =>  $input->user_id,
                        'project_id'    =>  $value->project->id,
                        'level'         =>  $value->level,
                        'created_by'    =>  $_SESSION['user_id'],
                        'updated_by'    =>  $_SESSION['user_id'],
                        'created_at'    =>  date('Y-m-d H:i:s'),
                        'updated_at'    =>  date('Y-m-d H:i:s'),
                    );

                    $insertAccess = $this->dbCon->prepare($this->queryHandler->insertProjectAccess($entryData));
                    $status = $insertAccess->execute($entryData);
                    $newId = $this->dbCon->lastInsertId();
                    $this->systemLogs($newId, 'project_accesses', 'user_access_configuration', 'add');

                    $input->accesses[$key]->id = $newId;
                    $input->accesses[$key]->project_id   = $value->project->id;
                    $input->accesses[$key]->project_code = $value->project->project_code;
                    $input->accesses[$key]->project_name = $value->project->project_name;
                }
            
                $this->dbCon->commit();

                $returnData = array(
                    'status'  => $status,
                    'accesses'=> $input->accesses
                );

                return $returnData;
            
            } catch (Exception $exc) {
                echo $exc->getMessage();
                $this->dbCon->rollBack();
            }
        }

        public function updateAccess($input = [])
        {
            try {
                $this->dbCon->beginTransaction();

                $entryData = array(
                    'level'         =>  $input->level,
                    'updated_by'    =>  $_SESSION['user_id'],
                    'updated_at'    =>  date('Y-m-d H:i:s'),
                );

                $updateAccess = $this->dbCon->prepare($this->queryHandler->updateProjectAccess($input->id, $entryData));
                $status = $updateAccess->execute($entryData);
                $this->systemLogs($input->id, 'project_accesses', 'user_access_configuration', 'update');
            
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