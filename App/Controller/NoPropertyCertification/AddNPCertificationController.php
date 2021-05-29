<?php
    namespace App\Controller\NoPropertyCertification;

    require_once("NoPropertyCertificationController.php");

    use App\Controller\NoPropertyCertification\NoPropertyCertificationController as ModuleController;
    use Exception;

    class AddNPCertificationController extends ModuleController {

        public function getSelectionDetails()
        {
            $output = [
                'users'     => $this->getUsers(),
                'user_id'   => $_SESSION['user_id']
            ];

            return $output;
        }
        
        public function verifyDeclareeRecords($data)
        {
            $output = [
                'records' => $this->getTaxDeclarationRecords($this->formatStr($data->declaree))
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
                    'type'          => 'A',
                    'declaree'      => $input->declarees,
                    'requestor'     => $input->requestor,
                    'purpose'       => $input->purpose,
                    'request_date'  => date('Y-m-d'),
                    'amount_paid'   => $input->amount_paid,
                    'or_no'         => $input->or_no,
                    'prepared_by'   => $input->prepared_by->id,
                    'verified_by'   => $input->verified_by->id,
                    'created_by'    => $_SESSION['user_id'],
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_by'    => $_SESSION['user_id'],
                    'updated_at'    => date('Y-m-d H:i:s'),
                ];

                $insertCert = $this->dbCon->prepare($this->queryHandler->insertCertification($entryData));
                $status = $insertCert->execute($entryData);
                $newCertID = $this->dbCon->lastInsertId();
                $this->systemLogs($newCertID, 'released_certifications', 'CERTIFICATION - NO PROPERTY DEC', 'insert');
            
                $this->dbCon->commit();

                $output = [
                    'status'    => $status,
                    'rowData'   => $this->getNoPropertyCertifications($newCertID)[0]
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