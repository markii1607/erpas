<?php
    namespace App\Controller\PropertiesDec;

    require_once("PropertiesDecController.php");

    use App\Controller\PropertiesDec\PropertiesDecController as ModuleController;
    use Exception;

    class AddPropertiesDecController extends ModuleController {

        public function getSelectionData()
        {
            $output = [
                'owners'    => $this->getLotOwners()
            ];

            return $output;
        }
        
        public function verifyRecords($data)
        {
            $output = [
                'records'   => $this->getDeclarantPropertyRecords($this->formatStrArray($data->owner)),
                'users'     => $this->getUsers(),
                'user_id'   => $_SESSION['user_id']
            ];

            return $output;
        }

        public function saveCertificationData($input)
        {
            // print_r($input);
            // die();
            try {
                $this->dbCon->beginTransaction();

                $entryData = [
                    'type'                  => 'C',
                    'requestor'             => $input->requestor,
                    'purpose'               => $input->purpose,
                    'request_date'          => date('Y-m-d'),
                    'amount_paid'           => $input->amount_paid,
                    'or_no'                 => $input->or_no,
                    'prepared_by'           => $input->prepared_by->id,
                    'verified_by'           => $input->verified_by->id,
                    'created_by'            => $_SESSION['user_id'],
                    'created_at'            => date('Y-m-d H:i:s'),
                    'updated_by'            => $_SESSION['user_id'],
                    'updated_at'            => date('Y-m-d H:i:s'),
                ];

                $insertCert = $this->dbCon->prepare($this->queryHandler->insertToTable('released_certifications', $entryData));
                $status = $insertCert->execute($entryData);
                $newCertID = $this->dbCon->lastInsertId();
                $this->systemLogs($newCertID, 'released_certifications', 'CERTIFICATION - PROP W/ IMPROVEMENTS', 'insert');

                foreach ($input->property_records as $key => $value) {
                    $subEntryData = [
                        'released_certification_id' => $newCertID,
                        'created_by'                => $_SESSION['user_id'],
                        'created_at'                => date('Y-m-d H:i:s'),
                        'updated_by'                => $_SESSION['user_id'],
                        'updated_at'                => date('Y-m-d H:i:s'),
                    ];

                    if ($value->data_type == 'saved') {
                        $subEntryData['tax_declaration_classification_id'] = $value->id;
                    } else if ($value->data_type == 'new') {
                        $subEntryData['td_no']              = $value->td_no_new;
                        $subEntryData['declarant']          = $value->owner_new;
                        $subEntryData['lot_no']             = $value->lot_no_new;
                        $subEntryData['area']               = $value->area_new;
                        $subEntryData['market_value']       = $value->market_value_new;
                        $subEntryData['assessed_value']     = $value->assessed_value_new;
                        $subEntryData['property_kind']      = $value->property_kind_new;
                        $subEntryData['property_location']  = $value->property_location_new;
                    }
                    
                    $insertCertDetails = $this->dbCon->prepare($this->queryHandler->insertToTable('released_certification_details', $subEntryData));
                    $status = $insertCertDetails->execute($subEntryData);
                    $newCertDetailId = $this->dbCon->lastInsertId();
                    $this->systemLogs($newCertDetailId, 'released_certification_details', 'CERTIFICATION - PROP W/ IMPROVEMENTS', 'insert');
                }
            
                $this->dbCon->commit();

                $output = [
                    'status'    => $status,
                    'rowData'   => $this->getReleasedCertifications($newCertID)[0]
                ];

                return $output;
            
            } catch (Exception $exc) {
                echo $exc->getMessage();
                $this->dbCon->rollBack();
            }
            
        }

        public function formatStr($strData)
        {
            $strArr = explode('&', $strData);
            $outputStr = [];
            foreach ($strArr as $value) {
                array_push($outputStr, trim($value));
            }

            return $outputStr;
        }

        public function formatStrArray($data)
        {
            $output = [];
            foreach ($data as $key => $value) {
                array_push($output, "'".$value->owner."'");
            }

            return $output;
        }
    }