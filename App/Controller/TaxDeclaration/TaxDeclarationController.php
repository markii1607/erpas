<?php
    namespace App\Controller\TaxDeclaration;

    require_once("../../Config/BaseController.php");
    require_once("../../Model/TaxDeclaration/TaxDeclarationQueryHandler.php");

    use App\Config\BaseController as BaseController;
    use App\Model\TaxDeclaration\TaxDeclarationQueryHandler as QueryHandler;
    use Exception;

    class TaxDeclarationController extends BaseController {
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

        /**
         * Paging
         *
         * Construct the LIMIT clause for server-side processing SQL query
         *
         *  @param  array $request Data sent to server by DataTables
         *  @param  array $columns Column information array
         *  @return string SQL limit clause
         */
        public function limit ($request)
        {
            $limit = '';

            if ( isset($request['start']) && $request['length'] != -1 ) {
                $limit = "LIMIT ".intval($request['start']).", ".intval($request['length']);
            }

            return $limit;
        }

        public function getDetails($input)
        {
            // print_r($input);
            // die();
            $rows       = $this->getTaxDeclarations('', $input['advanced_search'], $input['search']['value'], '', true);
            $rowData    = $this->getTaxDeclarations('', $input['advanced_search'], $input['search']['value'], $this->limit($input));

            $output = array(
                'draw'            => isset ($input['draw']) ? intval($input['draw']) : 0,
                'recordsTotal'    => !empty($rows) ? intval($rows[0]['td_count']) : 0,
                'recordsFiltered' => !empty($rows) ? intval($rows[0]['td_count']) : 0,
                'data'            => $this->arrayToObject($rowData),
            );

            return $output;
        }

        public function retireTaxDeclaration($input)
        {
            try {
                $this->dbCon->beginTransaction();

                $entryData = [
                    'status'     => 2,
                    'updated_by' => $_SESSION['user_id'],
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $retireTD = $this->dbCon->prepare($this->queryHandler->updateTblData('tax_declarations', $input->id, $entryData));
                $status = $retireTD->execute($entryData);
                $this->systemLogs($input->id, 'tax_declarations', 'Tax Declarations Monitoring - DT', 'retire');
            
                $this->dbCon->commit();

                $output = [
                    'status'    => $status,
                    'rowData'   => $this->getTaxDeclarations($input->id)[0]
                ];

                return $output;
            
            } catch (Exception $exc) {
                echo $exc->getMessage();
                $this->dbCon->rollBack();
            }
            
        }

        public function archiveTaxDeclaration($input)
        {
            try {
                $this->dbCon->beginTransaction();

                $entryData = [
                    'is_active'  => 0,
                    'updated_by' => $_SESSION['user_id'],
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $archiveTD = $this->dbCon->prepare($this->queryHandler->updateTblData('tax_declarations', $input->id, $entryData));
                $status = $archiveTD->execute($entryData);
                $this->systemLogs($input->id, 'tax_declarations', 'Tax Declarations Monitoring - DT', 'archive');
            
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

        public function getTaxDeclarations($id = '', $advancedSearch = [], $filterVal = '', $limit = '', $total = '')
        {
            $hasId      = empty($id) ? false : true;
            $hasTotal   = empty($total) ? false : true;

            $data = [
                'is_active'     => 1,
                'filter_val'    => ($filterVal != '' ) ? '%'.$filterVal.'%' : '%%'
            ];

            ($hasId) ? $data['id'] = $id : '';

            $query = $this->queryHandler->selectTaxDeclarations($hasId, $hasTotal)->orderBy('RY.year', 'DESC')->end();
            $tax_declarations = $this->dbCon->prepare($query.' '.$limit);
            $tax_declarations->execute($data);

            $result = $tax_declarations->fetchAll(\PDO::FETCH_ASSOC);

            if (!$hasTotal) {
                foreach ($result as $key => $value) {
                    $result[$key]['approvers']      = json_decode($value['approvers']);
                    $result[$key]['barangay']       = $this->getBarangays($value['barangay_id'])[0];
                    $result[$key]['revision_year']  = $this->getRevisionYears($value['revision_year_id'])[0];
                    $result[$key]['canceled_td']    = !empty($value['canceled_td_id']) ? $this->getTDNumbers('', $value['canceled_td_id'])[0] : [];
                    $result[$key]['td_number']      = explode('-', $value['td_no']);
                    $result[$key]['pi_number']      = explode('-', $value['pin']);
                }
            }

            return $result;
        }

        public function getTaxDeclarationClassifications($td_id = '')
        {
            $hasTdId = empty($td_id) ? false : true;

            $data = [
                'is_active' => 1
            ];

            ($hasTdId) ? $data['td_id'] = $td_id : '';

            $td_classifications = $this->dbCon->prepare($this->queryHandler->selectTaxDeclarationClassifications($hasTdId)->end());
            $td_classifications->execute($data);

            $result = $td_classifications->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($result as $key => $value) {
                $result[$key]['classification']         = $this->getClassifications($value['classification_id'])[0];
                $result[$key]['sub_classification']     = $this->getMarketValues('', '', $value['market_value_id'])[0];
                $result[$key]['area']                   = floatval($value['area']);
                $result[$key]['market_value']           = floatval($value['market_value']);
                $result[$key]['assessed_value']         = floatval($value['assessed_value']);
                $result[$key]['assessment_level']       = floatval($value['assessment_level']);
            }

            return $result;
        }

        public function getClassifications($id = '')
        {
            $hasId = empty($id) ? false : true;

            $data = [
                'is_active' => 1
            ];

            ($hasId) ? $data['id'] = $id : '';

            $classifications = $this->dbCon->prepare($this->queryHandler->selectClassifications($hasId)->orderBy('C.name', 'ASC')->end());
            $classifications->execute($data);

            return $classifications->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function getSubClassifications($class_id = '')
        {
            $hasClassId = empty($class_id) ? false : true;

            $data = [
                'is_active' => 1
            ];

            ($hasClassId) ? $data['class_id'] = $class_id : '';

            $sub_classifications = $this->dbCon->prepare($this->queryHandler->selectSubClassifications($hasClassId)->orderBy('SC.name', 'ASC')->end());
            $sub_classifications->execute($data);

            return $sub_classifications->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function getBarangays($id = '')
        {
            $hasId = empty($id) ? false : true;

            $data = [
                'is_active' => 1
            ];

            ($hasId) ? $data['id'] = $id : '';

            $barangays = $this->dbCon->prepare($this->queryHandler->selectBarangays($hasId)->orderBy('B.name', 'ASC')->end());
            $barangays->execute($data);

            return $barangays->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function getRevisionYears($id = '')
        {
            $hasId = empty($id) ? false : true;

            $data = [
                'is_active' => 1
            ];

            ($hasId) ? $data['id'] = $id : '';

            $revYears = $this->dbCon->prepare($this->queryHandler->selectRevisionYears($hasId)->orderBy('RY.year', 'DESC')->end());
            $revYears->execute($data);

            return $revYears->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function getTDNumbers($rev_id = '', $id = '')
        {
            $hasRevId = empty($rev_id) ? false : true;
            $hasId    = empty($id)     ? false : true;

            $data = [
                'is_active' => 1
            ];

            ($hasRevId) ? $data['rev_id'] = $rev_id : '';
            ($hasId)    ? $data['id'] = $id         : '';
            
            $tdNumbers = $this->dbCon->prepare($this->queryHandler->selectTDNumbers($hasRevId, $hasId)->orderBy('TD.td_no, RY.year', 'DESC')->end());
            $tdNumbers->execute($data);

            return $tdNumbers->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function getApproverSets()
        {
            $approvers = $this->dbCon->prepare($this->queryHandler->selectApproverSets()->end());
            $approvers->execute(['is_active' => 1]);
            $result = $approvers->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($result as $key => $value) {
                $result[$key]['approvers'] = json_decode($value['approvers']);
            }

            return $result;
        }

        public function getMarketValues($class_id = '', $rev_id = '', $id = '')
        {
            $hasClassId = empty($class_id)  ? false : true;
            $hasRevId   = empty($rev_id)    ? false : true;
            $hasId      = empty($id)        ? false : true;

            $data = [
                'is_active' => 1
            ];

            ($hasClassId)   ? $data['class_id'] = $class_id    : '';
            ($hasRevId)     ? $data['rev_id'] = $rev_id        : '';
            ($hasId)        ? $data['id'] = $id                 : '';

            $market_values = $this->dbCon->prepare($this->queryHandler->selectMarketValues($hasClassId, $hasRevId, $hasId)->end());
            $market_values->execute($data);

            return $market_values->fetchAll(\PDO::FETCH_ASSOC);
        }

    }