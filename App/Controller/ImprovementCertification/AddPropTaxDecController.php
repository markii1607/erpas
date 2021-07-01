<?php
    namespace App\Controller\ImprovementCertification;

    require_once("ImprovementCertificationController.php");

    use App\Controller\ImprovementCertification\ImprovementCertificationController as ModuleController;
    use Exception;

    class AddPropTaxDecController extends ModuleController {

        public function getSelectionData($data)
        {
            $output = [
                'owners'    => $this->getLotOwners($data->lot_no)
            ];

            return $output;
        }
        
        public function getImprovementRecords($data)
        {
            $output = [
                'records'   => $this->getDBImprovementRecords($data->lot_no),
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
                    'type'                  => 'B',
                    'tax_declaration_id'    => !empty($input->chk_auto)   ? $input->owner->id       : null,
                    'td_no'                 => !empty($input->chk_manual) ? $input->td_no           : null,
                    'td_lot_no'             => !empty($input->chk_manual) ? $input->lot_no       : null,
                    'td_effectivity'        => !empty($input->chk_manual) ? $input->td_effectivity  : null,
                    'td_prop_location'      => !empty($input->chk_manual) ? $input->prop_location   : null,
                    'declaree'              => !empty($input->chk_auto)   ? $input->owner->owner    : $input->owner_name,
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

                foreach ($input->improvements as $key => $value) {
                    $subEntryData = [
                        'released_certification_id' => $newCertID,
                        'created_by'                => $_SESSION['user_id'],
                        'created_at'                => date('Y-m-d H:i:s'),
                        'updated_by'                => $_SESSION['user_id'],
                        'updated_at'                => date('Y-m-d H:i:s'),
                    ];

                    if (!empty($input->chk_auto)) {
                        if ($value->data_type == 'saved') {
                            $subEntryData['tax_declaration_classification_id'] = $value->id;
                        } else if ($value->data_type == 'new') {
                            $subEntryData['td_no']          = $value->td_no_new;
                            $subEntryData['declarant']      = $value->owner_new;
                            $subEntryData['lot_no']         = $value->lot_no_new;
                            $subEntryData['area']           = $value->area_new;
                            $subEntryData['market_value']   = $value->market_value_new;
                            $subEntryData['assessed_value'] = $value->assessed_value_new;
                        }
                    } else if (!empty($input->chk_manual)) {
                        $subEntryData['td_no']          = $value->td_no_new;
                        $subEntryData['declarant']      = $value->owner_new;
                        $subEntryData['lot_no']         = $value->lot_no_new;
                        $subEntryData['area']           = $value->area_new;
                        $subEntryData['market_value']   = $value->market_value_new;
                        $subEntryData['assessed_value'] = $value->assessed_value_new;
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
    }