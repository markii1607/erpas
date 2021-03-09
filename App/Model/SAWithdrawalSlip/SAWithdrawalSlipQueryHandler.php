<?php
    namespace App\Model\SAWithdrawalSlip;

    require_once('../../AbstractClass/QueryHandler.php');

    use App\AbstractClass\QueryHandler;

    class SAWithdrawalSlipQueryHandler extends QueryHandler {

        public function selectSaWSNos()
        {
            $fields = [
                'SW.id',
                'SW.ws_no',
            ];

            $initQuery = $this->select($fields)
                              ->from('sa_withdrawals SW')
                              ->orderBy('SW.ws_no', 'DESC');

            return $initQuery;
        }
        public function selectSaWithdrawals()
        {
            $fields = [
                'SW.id',
                'SW.ws_no',
                'DATE_FORMAT(SW.ws_date, "%M %d, %Y") as ws_date',
                'SW.status',
                'SW.project_id',
                'SW.department_id',
                'SW.approved_by',
                'SW.approver_status',
                'DATE_FORMAT(SW.approved_at, "%M %d, %Y") as approved_at',
                'SW.approver_remarks',
            ];

            $initQuery = $this->select($fields)
                              ->from('sa_withdrawals SW')
                              ->where(['SW.is_active' => ':is_active']);

            return $initQuery;
        }

        public function selectProjects()
        {
            $fields = array(
                'P.id',
                'P.name',
                // 'P.project_code',
                'IF(P.project_code IS NOT NULL, P.project_code, (SELECT temporary_project_code FROM project_code_requests WHERE project_id = P.id)) as project_code',
                'P.project_manager',
                '"P" as type'
            );

            $whereInCondition = [
                '7',  // Y03-001
                '4',  // TC-01126
                '2',  // TC-01147
                '3',  // TC01089
                '1',   //19SCDC001
                '161',	//	15002
                '25',	//	16026
                '162',	//	16027
                '163',	//	16028
                '76',	//	18002
                '77',	//	18003
                '78',	//	18004
                '79',	//	18005
                '80',	//	18006
                '81',	//	18007
                '82',	//	18008
                '83',	//	18009
                '84',	//	18010
                '85',	//	18011
                '86',	//	18012
                '164',	//	18013
                '165',	//	18014
                '87',	//	18015
                '88',	//	18016
                '89',	//	18017
                '166',	//	18018
                '90',	//	18019
                '91',	//	18020
                '92',	//	18021
                '93',	//	18022
                '94',	//	18023
                '95',	//	18024
                '96',	//	18025
                '97',	//	18026
                '98',	//	18027
                '99',	//	18028
                '26',	//	18029
                '100',	//	18029
                '101',	//	18030
                '102',	//	18031
                '103',	//	18032
                '104',	//	18033
                '167',	//	18034
                '168',	//	18035
                '105',	//	18036
                '106',	//	18037
                '107',	//	18038
                '169',	//	18039
                '108',	//	18040
                '109',	//	18041
                '110',	//	18042
                '111',	//	18043
                '112',	//	18044
                '170',	//	18045
                '113',	//	18046
                '171',	//	18047
                '172',	//	18048
                '114',	//	18049
                '115',	//	18050
                '116',	//	18051
                '117',	//	18052
                '118',	//	18053
                '173',	//	18054
                '119',	//	18055
                '174',	//	18056
                '120',	//	18057
                '121',	//	18058
                '175',	//	18059
                '122',	//	18060
                '123',	//	18061
                '124',	//	18062
                '125',	//	18063
                '126',	//	18063
                '127',	//	18064
                '128',	//	18065
                '129',	//	18066
                '130',	//	18067
                '176',	//	18068
                '131',	//	18069
                '177',	//	18070
                '178',	//	18071
                '179',	//	18072
                '132',	//	18073
                '180',	//	18074
                '133',	//	18075
                '134',	//	18076
                '181',	//	18077
                '135',	//	18078
                '136',	//	18079
                '27',	//	18080
                '137',	//	18080
                '138',	//	18081
                '139',	//	18082
                '182',	//	18083
                '140',	//	18084
                '183',	//	18085
                '184',	//	18086
                '185',	//	18087
                '9',	//	19001
                '10',	//	19002
                '11',	//	19003
                '12',	//	19004
                '28',	//	19005
                '29',	//	19006
                '30',	//	19007
                '31',	//	19008
                '32',	//	19009
                '33',	//	19010
                '34',	//	19011
                '35',	//	19012
                '38',	//	19013
                '39',	//	19015
                '141',	//	 18SG001 
                '142',	//	 18SG002 
                '186',	//	17SG013
                '143',	//	18SG003
                '144',	//	18SG004
                '145',	//	18SG005
                '146',	//	18SG006
                '147',	//	18SG008
                '148',	//	18SG009
                '149',	//	18SG010
                '150',	//	18SG011
                '151',	//	18SG012
                '187',	//	18SG013
                '24',	//	18SG-013
                '152',	//	18SG014
                '153',	//	18SG015
                '154',	//	18SG016
                '155',	//	18SG017
                '156',	//	18SG018
                '157',	//	18SG019
                '158',	//	18SG021
                '159',	//	18SG022
                '160',	//	18SG023
                '188',	//	18SG026
                '189',	//	19SCDC003
                '36',	//	19SG-001
                '37',	//	19SG-002
                '23',	//-003
                '69',	//	19SG-004
                '201',	//	19SG-005
                '202'	//	19SG-006
            ];

            $initQuery = $this->select($fields)
                              ->from('projects P')
                              ->where(array('P.is_active' => ':is_active'))
                              ->andWhereIn('P.id', $whereInCondition);

            return $initQuery;
        }

        public function selectDepartments($charging = false)
        {
            $fields = array(
                'D.id',
                'D.name',
                'D.charging',
                '"D" as type'
            );

            $initQuery = $this->select($fields)
                              ->from('departments D')
                              ->where(array('D.is_active' => ':is_active'));

            $initQuery = ($charging) ? $initQuery->andWhereLike(['D.charging' => ':charging']) : $initQuery;

            return $initQuery;
        }

        public function selectRequestorInfo()
        {
            $fields = array(
                'U.id',
                'CONCAT(PI.fname, " ", LEFT(PI.mname, 1), ". ", PI.lname) as fullname',
                'P.name as position',
                'D.name as department',
            );

            $joins = array(
                'personal_informations PI'      =>      'PI.id = U.personal_information_id',
                'employment_informations EI'    =>      'EI.personal_information_id = PI.id',
                'positions P'                   =>      'P.id = EI.position_id',
                'departments D'                 =>      'D.id = P.department_id'
            );

            $initQuery = $this->select($fields)
                              ->from('users U')
                              ->join($joins)
                              ->where(array('U.is_active' => ':is_active', 'U.id' => ':user_id'));
            
            // $initQuery = ($user_id) ? $initQuery->andWhere(array('U.id' => ':user_id')) : $initQuery;

            return $initQuery;
        }

        public function selectMaterialSpecifications($material_id = false, $material_spec_id = false, $category_id = false)
        {
            $fields = array(
                'MS.id',
                'MS.material_id',
                'MS.code',
                'MS.specs',
                'M.name as material_name',
            );

            $join = array(
                'materials M'   =>  'M.id = MS.material_id'
            );

            $initQuery = $this->select($fields)
                              ->from('material_specifications MS')
                              ->join($join)
                              ->where(array('MS.is_active' => ':is_active'));

            $initQuery = ($material_id)      ? $initQuery->andWhere(array('MS.material_id' => ':material_id')) : $initQuery;
            $initQuery = ($material_spec_id) ? $initQuery->andWhereNot(array('MS.id' => ':material_spec_id'))  : $initQuery;
            $initQuery = ($category_id)      ? $initQuery->andWhere(array('M.material_category_id' => ':mc_id'))  : $initQuery;

            return $initQuery;
        }

        public function selectMsbInventories($msb_id = false, $project_id = false, $department_id = false, $unit = false, $ms_id = false)
        {
            $fields = [
                'MSBI.id',
                'MSBI.material_specification_brand_id as msb_id',
                'MSBI.project_id',
                'MSBI.department_id',
                'MSBI.warehouse_id',
                'MSBI.quantity',
                'MSBI.unit',
                'MSB.material_specification_id',
                'IF(MSBI.project_id IS NOT NULL, P.project_code, D.charging) as charging_code'
            ];

            $leftJoins = [
                'projects P'        =>  'P.id = MSBI.project_id',
                'departments D'     =>  'D.id = MSBI.department_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('msb_inventories MSBI')
                              ->join(['material_specification_brands MSB' => 'MSB.id = MSBI.material_specification_brand_id'])
                              ->leftJoin($leftJoins)
                              ->where(['MSBI.is_active' => ':is_active']);

            $initQuery = ($msb_id)          ? $initQuery->andWhere(['MSBI.material_specification_brand_id' => ':msb_id']) : $initQuery;
            $initQuery = ($project_id)      ? $initQuery->andWhere(['MSBI.project_id' => ':project_id']) : $initQuery;
            $initQuery = ($department_id)   ? $initQuery->andWhere(['MSBI.department_id' => ':department_id']) : $initQuery;
            $initQuery = ($unit)            ? $initQuery->andWhere(['MSBI.unit' => ':unit']) : $initQuery;
            $initQuery = ($ms_id)           ? $initQuery->andWhere(['MSB.material_specification_id' => ':ms_id']) : $initQuery;

            return $initQuery;
        }

        public function selectWarehouses($id = false)
        {
            $fields = [
                'W.id',
                'W.name',
                'W.address',
            ];

            $initQuery  = $this->select($fields)
                               ->from('warehouses W')
                               ->where(['W.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['W.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        public function selectUnits()
        {
            $fields = [
                'MU.id',
                'MU.unit',
            ];

            $initQuery = $this->select($fields)
                              ->from('material_units MU')
                              ->where(['MU.is_active' => ':is_active']);

            return $initQuery;
        }

        public function insertSAWithdrawals($data = [])
        {
            $initQuery = $this->insert('sa_withdrawals', $data);

            return $initQuery;
        }

        public function insertSAWithdrawalItems($data = [])
        {
            $initQuery = $this->insert('sa_withdrawal_items', $data);

            return $initQuery;
        }

        public function updateSAWithdrawals($id = '', $data = [])
        {
            $initQuery = $this->update('sa_withdrawals', $id, $data);

            return $initQuery;
        }
    }
?>