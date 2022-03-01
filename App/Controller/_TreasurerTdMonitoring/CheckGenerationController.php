<?php
    namespace App\Controller\TreasurerTdMonitoring;

    require_once("TreasurerTdMonitoringController.php");

    use App\Controller\TreasurerTdMonitoring\TreasurerTdMonitoringController as ModuleController;
    use Exception;

    class CheckGenerationController extends ModuleController {

        public function getRecords($input)
        {
            $dateExplode = explode(' - ', $input->date_range);
            $fromDate    = date('Y-m-d', strtotime($dateExplode[0]));
            $toDate      = date('Y-m-d', strtotime($dateExplode[1]));
            
            $output = [
                'records' => $this->getSpecifiedOrNumbers($fromDate, $toDate)
            ];

            return $output;
        }

        public function getOrTdList($data)
        {
            $tdList = $this->getPaidTaxDecDetails($data['ptd_id']);
            foreach ($tdList as $key => $value) {
                $tdList[$key]['td_details'] = $this->getTDNumbers('', $value['tax_declaration_id'])[0];
            }
            $output = [
                'td_list' => $tdList
            ];

            return $output;
        }

        public function saveCheckDetails($input)
        {
            // print_r($input);
            // die();
            try {
                $this->dbCon->beginTransaction();

                $checkEntryData = [
                    'user_id'           => $_SESSION['user_id'],
                    'date_generated'    => date('Y-m-d'),
                    'check_no'          => $input->check_no,
                    'total_amount'      => $input->total_amount,
                    'created_by'        => $_SESSION['user_id'],
                    'created_at'        => date('Y-m-d H:i:s'),
                    'updated_by'        => $_SESSION['user_id'],
                    'updated_at'        => date('Y-m-d H:i:s'),
                ];

                $insertORData = $this->dbCon->prepare($this->queryHandler->insertTblData('treasurer_collections', $checkEntryData));
                $insertORData->execute($checkEntryData);
                $newCheckID = $this->dbCon->lastInsertId();
                $this->systemLogs($newCheckID, 'treasurer_collections', 'TREASURER - CHECK GENERATION', 'insert');

                foreach ($input->records as $key => $value) {
                    $tdEntryData = [
                        'has_check_no'  => 1,
                        'updated_by'    => $_SESSION['user_id'],
                        'updated_at'    => date('Y-m-d H:i:s')
                    ];

                    $updateTdData = $this->dbCon->prepare($this->queryHandler->updateTblData('paid_tax_declarations', $value->id, $tdEntryData));
                    $updateTdData->execute($tdEntryData);
                    $this->systemLogs($value->id, 'paid_tax_declarations', 'TREASURER - CHECK GENERATION', 'update');

                    $checkDetailEntryData = [
                        'treasurer_collection_id'   => $newCheckID,
                        'paid_tax_declaration_id'   => $value->id,
                        'created_by'                => $_SESSION['user_id'],
                        'created_at'                => date('Y-m-d H:i:s'),
                        'updated_by'                => $_SESSION['user_id'],
                        'updated_at'                => date('Y-m-d H:i:s'),
                    ];

                    $insertORDetailData = $this->dbCon->prepare($this->queryHandler->insertTblData('treasurer_collection_details', $checkDetailEntryData));
                    $status = $insertORDetailData->execute($checkDetailEntryData);
                    $newOrDetailID = $this->dbCon->lastInsertId();
                    $this->systemLogs($newOrDetailID, 'treasurer_collection_details', 'TREASURER - CHECK GENERATION', 'insert');
                }
            
                $this->dbCon->commit();

                $output = [
                    'status'    => $status,
                    'rowData'   => $this->getGeneratedChkNumbers($newCheckID)[0]
                ];

                return $output;
            
            } catch (Exception $exc) {
                echo $exc->getMessage();
                $this->dbCon->rollBack();
            }
            
        }
    }