<?php
    namespace App\Controller\AccountingCollectionConfig;

    require_once("../../Config/BaseController.php");
    require_once("../../Model/AccountingCollectionConfig/AccountingCollectionConfigQueryHandler.php");

    use App\Config\BaseController as BaseController;
    use App\Model\AccountingCollectionConfig\AccountingCollectionConfigQueryHandler as QueryHandler;
    use Exception;

    class AccountingCollectionConfigController extends BaseController {
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

        public function getDtChkDetails($input)
        {
            // print_r($input['advanced_search']);
            // die();
            
            $rows       = $this->getGeneratedChkNumbers('', $input['advanced_search'], $input['search']['value'], '', true);
            $rowData    = $this->getGeneratedChkNumbers('', $input['advanced_search'], $input['search']['value'], $this->limit($input));
            

            $output = array(
                'draw'            => isset ($input['draw']) ? intval($input['draw']) : 0,
                'recordsTotal'    => !empty($rows) ? intval($rows[0]['tc_count']) : 0,
                'recordsFiltered' => !empty($rows) ? intval($rows[0]['tc_count']) : 0,
                'data'            => $this->arrayToObject($rowData),
            );

            return $output;
        }

        public function getGeneratedChkNumbers($id = '', $advancedSearch = [], $filterVal = '', $limit = '', $total = '')
        {
            $hasId          = empty($id)     ? false : true;
            // $hasRevId       = empty($advancedSearch['rev_id'])      ? false : true;
            $hasTotal       = empty($total)  ? false : true;

            $data = [
                'is_active'     => 1,
                'filter_val'    => ($filterVal != '' ) ? '%'.$filterVal.'%' : '%%'
            ];

            ($hasId) ? $data['id'] = $id : '';
            // ($hasRevId)     ? $data['rev_id'] = $advancedSearch['rev_id'] : '';

            $query = $this->queryHandler->selectGeneratedChkNumbers($hasId, $hasTotal)->orderBy('TC.date_generated', 'DESC')->end();
            $orNumbers = $this->dbCon->prepare($query.' '.$limit);
            $orNumbers->execute($data);

            $result = $orNumbers->fetchAll(\PDO::FETCH_ASSOC);

            if (!$hasTotal) {
                foreach ($result as $key => $value) {
                    $result[$key]['or_numbers'] = $this->getTreasurerCollectionDetails($value['id']);
                }
            }

            return $result;
        }

        public function getTreasurerCollectionDetails($tc_id = '')
        {
            $hasTcId = empty($tc_id) ? false : true;

            $data = [
                'is_active' => 1
            ];

            ($hasTcId) ? $data['tc_id'] = $tc_id : '';

            $details = $this->dbCon->prepare($this->queryHandler->selectTreasurerCollectionDetails($hasTcId)->orderBy('PTD.or_no', 'ASC')->end());
            $details->execute($data);

            return $details->fetchAll(\PDO::FETCH_ASSOC);
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

        public function getTDNumbers($rev_id = '', $id = '', $td_no = '')
        {
            $hasRevId = empty($rev_id) ? false : true;
            $hasId    = empty($id)     ? false : true;
            $hasTdNo  = empty($td_no)  ? false : true;

            $data = [
                'is_active' => 1
            ];

            ($hasRevId) ? $data['rev_id'] = $rev_id : '';
            ($hasId)    ? $data['id'] = $id         : '';
            ($hasTdNo)  ? $data['td_no'] = $td_no   : '';
            
            $tdNumbers = $this->dbCon->prepare($this->queryHandler->selectTDNumbers($hasRevId, $hasId, $hasTdNo)->orderBy('TD.td_no, RY.year', 'DESC')->end());
            $tdNumbers->execute($data);

            $result = $tdNumbers->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($result as $key => $value) {
                $result[$key]['approvers']      = json_decode($value['approvers']);
                $result[$key]['barangay']       = $this->getBarangays($value['barangay_id'])[0];
                $result[$key]['revision_year']  = $this->getRevisionYears($value['revision_year_id'])[0];
                $result[$key]['canceled_td']    = !empty($value['canceled_td_id']) ? $this->getTDNumbers('', $value['canceled_td_id'])[0] : [];
            }

            return $result;
        }

        public function getSpecifiedOrNumbers($from_date, $to_date)
        {
            $data = [
                'is_active' => 1,
                'from_date' => $from_date,
                'to_date'   => $to_date,
            ];

            $or_numbers = $this->dbCon->prepare($this->queryHandler->selectSpecifiedOrNumbers()->end());
            $or_numbers->execute($data);

            $result = $or_numbers->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($result as $key => $value) {
                $result[$key]['collector'] = $this->getUsers($value['user_id'])[0];
            }

            return $result;
        }

        public function getPaidTaxDecDetails($ptd_id = '')
        {
            $hasPtdId = empty($ptd_id) ? false : true;

            $data = [
                'is_active' => 1
            ];

            ($hasPtdId) ? $data['ptd_id'] = $ptd_id : '';

            $details = $this->dbCon->prepare($this->queryHandler->selectPaidTaxDecDetails($hasPtdId)->end());
            $details->execute($data);

            return $details->fetchAll(\PDO::FETCH_ASSOC);
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

        public function formatStr($str)
        {
            $strExplode = explode(" ", $str);
            $outputStr = '%';
            foreach ($strExplode as $value) {
                $outputStr .= $value.'%';
            }
            
            return $outputStr;
        }

        public function filterIDs($arrayData = [])
        {
            $output = [];

            foreach ($arrayData as $key => $value) {
                if(!empty($value)) array_push($output, $value['tax_declaration_id']);
            }

            return $output;
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