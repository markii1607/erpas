<?php
    namespace App\Controller\MarketValueRevision;

    require_once("MarketValueRevisionController.php");

    use App\Controller\MarketValueRevision\MarketValueRevisionController as ModuleController;

    class AddRevisionYearController extends ModuleController {
        
        public function getRevisionYearDetails()
        {
            $output = [
                'revision_years' => $this->getRevisionYears()
            ];

            return $output;
        }

        public function saveNewRevisionYear($input)
        {
            try {
                $this->dbCon->beginTransaction();

                $entryData = [
                    'year'              => $input->year,
                    'created_by'        => $_SESSION['user_id'],
                    'created_at'        => date('Y-m-d H:i:s'),
                    'updated_by'        => $_SESSION['user_id'],
                    'updated_at'        => date('Y-m-d H:i:s')
                ];

                $insertData = $this->dbCon->prepare($this->queryHandler->insertTblData('revision_years', $entryData));
                $status = $insertData->execute($entryData);
                $newRevYearId = $this->dbCon->lastInsertId();
                $this->systemLogs($newRevYearId, 'revision_years', 'MARKET VALUE CONFIG - REVISIONS', 'insert');
            
                $this->dbCon->commit();

                $output = [
                    'status'    => $status,
                    'rowData'   => $this->getRevisionYears($newRevYearId)[0]
                ];

                return $output;
            
            } catch (Exception $exc) {
                echo $exc->getMessage();
                $this->dbCon->rollBack();
            }
        }

        public function saveUpdatedRevisionYear($input)
        {
            try {
                $this->dbCon->beginTransaction();

                $entryData = [
                    'year'              => $input->year,
                    'updated_by'        => $_SESSION['user_id'],
                    'updated_at'        => date('Y-m-d H:i:s')
                ];

                $updateData = $this->dbCon->prepare($this->queryHandler->updateTblData('revision_years', $input->id, $entryData));
                $status = $updateData->execute($entryData);
                $this->systemLogs($input->id, 'revision_years', 'MARKET VALUE CONFIG - REVISIONS', 'update');
            
                $this->dbCon->commit();

                $output = [
                    'status'    => $status,
                    'rowData'   => $this->getRevisionYears($input->id)[0]
                ];

                return $output;
            
            } catch (Exception $exc) {
                echo $exc->getMessage();
                $this->dbCon->rollBack();
            }
        }

        public function archiveRevisionYear($input)
        {
            try {
                $this->dbCon->beginTransaction();

                $entryData = [
                    'is_active'         => 0,
                    'updated_by'        => $_SESSION['user_id'],
                    'updated_at'        => date('Y-m-d H:i:s')
                ];

                $updateData = $this->dbCon->prepare($this->queryHandler->updateTblData('revision_years', $input->id, $entryData));
                $status = $updateData->execute($entryData);
                $this->systemLogs($input->id, 'revision_years', 'MARKET VALUE CONFIG - REVISIONS', 'archive');
            
                $this->dbCon->commit();

                $output = [
                    'status'    => $status,
                ];

                return $output;
            
            } catch (Exception $exc) {
                echo $exc->getMessage();
                $this->dbCon->rollBack();
            }
        }
    }