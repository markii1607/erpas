<?php
    namespace App\Controller\PropertiesDec;

    require_once("../../Config/BaseController.php");
    require_once("../../Model/PropertiesDec/PropertiesDecQueryHandler.php");

    use App\Config\BaseController as BaseController;
    use App\Model\PropertiesDec\PropertiesDecQueryHandler as QueryHandler;
    use Exception;

    class PropertiesDecController extends BaseController {
        /**
         * `$menu_id` Set the menu id
         * @var integer
         */
        protected $menu_id = 214;

        /**
         * `$dbCon` Concern in database connection.
         * @var private class
         */
        protected $dbCon;

        /**
         * `$queryHandler` Handles query.
         * @var private class
         */
        protected $queryHandler;

        /**
         * `__construct` Constructor
         * @param object $dbCon        Database connetor
         * @param string $queryHandler Query String
         */
        public function __construct(
            $dbCon
        ) {
            parent::__construct();

            $this->dbCon        = $dbCon;
            $this->queryHandler = new QueryHandler();
            
        }

        public function getDetails()
        {
            $output = [
                'certifications' => $this->getReleasedCertifications()
            ];

            return $output;
        }

        public function getLotOwners()
        {
            $data = [
                'is_active' => 1,
                'status'    => 1,
            ];

            $owners = $this->dbCon->prepare($this->queryHandler->selectLotOwners()->groupBy('TD.owner')->end());
            $owners->execute($data);

            return $owners->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function archivePropertiesDec($input)
        {
            try {
                $this->dbCon->beginTransaction();

                $entryData = [
                    'is_active'     => 0,
                    'updated_by'    => $_SESSION['user_id'],
                    'updated_at'    => date('Y-m-d H:i:s')
                ];

                $archiveCertification = $this->dbCon->prepare($this->queryHandler->updateTable('released_certifications', $input->id, $entryData));
                $status = $archiveCertification->execute($entryData);
                $this->systemLogs($input->id, 'released_certifications', 'CERTIFICATION - PROP W/ IMPROVEMENTS', 'archive');
            
                $this->dbCon->commit();

                $output = [
                    'status' => $status
                ];

                return $output;
            
            } catch (Exception $exc) {
                echo $exc->getMessage();
                $this->dbCon->rollBack();
            }
            
        }

        public function getReleasedCertifications($id = '')
        {
            $hasId = empty($id) ? false : true;

            $data = [
                'is_active' => 1,
                'type'      => 'C'
            ];

            ($hasId) ? $data['id'] = $id : '';

            $certifications = $this->dbCon->prepare($this->queryHandler->selectReleasedCertifications($hasId)->orderBy('RC.request_date', 'DESC')->end());
            $certifications->execute($data);

            $result = $certifications->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($result as $key => $value) {
                $result[$key]['prepared_by'] = $this->getUsers($value['prepared_by'])[0];
                $result[$key]['verified_by'] = $this->getUsers($value['verified_by'])[0];
                $result[$key]['details']     = $this->getReleasedCertificationDetails($value['id']);
                $result[$key]['date'] = [
                    'month' => date('F', strtotime($value['request_date'])),
                    'day'   => date('j', strtotime($value['request_date'])),
                    'year'  => date('Y', strtotime($value['request_date'])),
                ];
            }

            return $result;
        }

        public function getReleasedCertificationDetails($cert_id)
        {
            $data = [
                'is_active' => 1,
                'cert_id'   => $cert_id
            ];

            $details = $this->dbCon->prepare($this->queryHandler->selectReleasedCertificationDetails()->end());
            $details->execute($data);

            $result = $details->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($result as $key => $value) {
                if (!empty($value['tax_declaration_classification_id'])) {
                    $tdcData = $this->getTaxDeclarationClassification($value['tax_declaration_classification_id']);
                    $result[$key]['td_no']          = !empty($tdcData) ? $tdcData[0]['td_no']           : '';
                    $result[$key]['declarant']      = !empty($tdcData) ? $tdcData[0]['declarant']       : '';
                    $result[$key]['lot_no']         = !empty($tdcData) ? $tdcData[0]['lot_no']          : '';
                    $result[$key]['area']           = !empty($tdcData) ? $tdcData[0]['area']            : '';
                    $result[$key]['market_value']   = !empty($tdcData) ? $tdcData[0]['market_value']    : '';
                    $result[$key]['assessed_value'] = !empty($tdcData) ? $tdcData[0]['assessed_value']  : '';
                    $result[$key]['property_kind']      = !empty($tdcData) ? $tdcData[0]['property_kind']  : '';
                    $result[$key]['property_location']  = !empty($tdcData) ? $tdcData[0]['property_location']  : '';
                }
            }

            return $result;
        }

        public function getTaxDeclarationClassification($tdc_id)
        {
            $data = [
                'tdc_id' => $tdc_id
            ];

            $tax_dec = $this->dbCon->prepare($this->queryHandler->selectTaxDeclarationClassification()->end());
            $tax_dec->execute($data);

            return $tax_dec->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function getDeclarantPropertyRecords($owners)
        {
            $data = [
                'is_active' => 1,
                'status'    => 1,
            ];

            $records = $this->dbCon->prepare($this->queryHandler->selectDeclarantPropertyRecords($owners)->end());
            $records->execute($data);

            $result = $records->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($result as $key => $value) {
                $result[$key]['property_location'] = (!empty($value['prop_location_street']) ? $value['prop_location_street'].' ' : '').$value['brgy_name'];
            }

            return $result;
        }

        public function getUsers($id = '')
        {
            $hasId = empty($id) ? false : true;

            $data = [
                'is_active' => 1
            ];

            ($hasId) ? $data['id'] = $id : '';

            $users = $this->dbCon->prepare($this->queryHandler->selectUsers($hasId)->orderBy('U.fname', 'ASC')->end());
            $users->execute($data);

            return $users->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function likeConditionStr($strData)
        {
            $strOutput = '';
            $strCount  = count($strData);
            foreach ($strData as $key => $value) {
                $strLikeCondition = $this->strLike($value);
                $strOutput .= 'TD.owner LIKE "'.$strLikeCondition.'"';
                if($key != ($strCount-1)) $strOutput .= ' OR ';
            }

            return $strOutput;
        }

        public function strLike($str)
        {
            $strExplode = explode(" ", $str);
            $outputStr = '%';
            foreach ($strExplode as $value) {
                $outputStr .= $value.'%';
            }
            
            return $outputStr;
        }
    }