<?php
    namespace App\Model\MakeWithdrawalSlip;

    require_once('../../AbstractClass/QueryHandler.php');

    use App\AbstractClass\QueryHandler;

    class MakeWithdrawalSlipQueryHandler extends QueryHandler {

        public function selectWithdrawalSlipNumbers()
        {
            $fields = array(
                'W.id',
                'W.ws_no',
            );

            $initQuery = $this->select($fields)
                              ->from('withdrawals W')
                            //   ->where(array('W.is_active' => ':is_active'))
                              ->orderBy('W.id', 'DESC')
                              ->limit(1);

            return $initQuery;
        }

        public function selectAllWSNumbers()
        {
            $fields = array(
                'W.id',
                'W.ws_no',
            );

            $initQuery = $this->select($fields)
                              ->from('withdrawals W')
                              ->where(array('W.ws_no' => ':ws_no'));

            return $initQuery;
        }

        public function selectPRS($project_id = false, $department_id = false)
        {
            $fields = array(
                'PR.id',
                'PR.project_id',
                'PR.department_id',
                'PR.prs_no',
                'PR.created_by as prs_requestor',
                'P.id as pId',
                'P.name as project_name',
                'P.project_code',
                'D.id as dId',
                'D.name as department_name',
                'D.charging',
            );

            $joins = array(
                'projects P'      => 'P.id = PR.project_id',
                'departments D'   => 'D.id = PR.department_id',
            );

            $initQuery = $this->select($fields)
                              ->from('purchase_requisitions PR')
                              ->leftJoin($joins)
                              ->where(array('PR.is_active' => ':is_active'));

            $initQuery = ($project_id)    ? $initQuery->andWhere(array('PR.project_id'    => ':project_id'))    : $initQuery;
            $initQuery = ($department_id) ? $initQuery->andWhere(array('PR.department_id' => ':department_id')) : $initQuery;

            return $initQuery;
        }

        public function selectRequestedItems($purchase_requisition_id = false)
        {
            $fields = array(
                'PRD.id',
                'PRD.purchase_requisition_id',
                'PRD.pm_id',
                'PRD.status',
                'PRD.unit_measurement as unit',
                'PRD.quantity as total_qty',
                'PRD.category',
                'PRD.wi_category',
                'PRD.work_item_id',
                'PRD.work_volume',
                'PRD.work_volume_unit',
                'PRD.wbs',
                'PRD.account_id',
                'PRD.created_by',
                'WIC.id as wicId',
                'WIC.part',
                'WIC.name as part_name',
                'WI.id as wiId',
                'WI.item_no',
                'WI.name as item_name',
                'A.id as aId',
                'A.account_id as account_code',
                'A.name as account_name',
                'MS.specs',
                'MSB.id as msb_id',
                // 'CONCAT(MS.code, MSBS.code) as code',
                'MS.id as material_spec_id',
                'MS.material_id',
                'MS.code',
                'M.name as material_name',
                'M.material_category_id',
                'PR.created_by as prs_requestor'
            );

            $joins = array(
                //join to prd_delivery_sequences
                'work_item_categories WIC'      => 'WIC.id = PRD.wi_category',
                'work_items WI'                 => 'WI.id = PRD.work_item_id',
                'accounts A'                    => 'A.id = PRD.account_id',
                'material_specifications MS'    => 'MS.id = PRD.item_spec_id',
                'material_specification_brands MSB' => 'MSB.material_specification_id = MS.id',
                // 'msb_suppliers MSBS'            => 'MSBS.material_specification_brand_id = MSB.id',
                'materials M'                   => 'M.id = MS.material_id',
                'purchase_requisitions PR'      => 'PR.id = PRD.purchase_requisition_id'
            );

            $initQuery = $this->select($fields)
                              ->from('purchase_requisition_descriptions PRD')
                              ->leftJoin($joins)
                              ->where(array('PRD.is_active' => ':is_active', 'PRD.status' => ':status'));

            $initQuery = ($purchase_requisition_id) ? $initQuery->andWhere(array('PRD.purchase_requisition_id' => ':purchase_requisition_id')) : $initQuery;

            return $initQuery;
        }

        public function selectMsbSuppliers($msb_id = false, $unit = false)
        {
            $fields = array(
                'MSBS.id',
                'MSBS.material_specification_brand_id',
                'MSBS.code',
                'MSBS.unit'
            );

            $initQuery = $this->select($fields)
                                ->from('msb_suppliers MSBS')
                                ->where(array('MSBS.is_active' => ':is_active'));

                $initQuery = ($msb_id) ? $initQuery->andWhere(array('MSBS.material_specification_brand_id' => ':msb_id')) : $initQuery;
                $initQuery = ($unit)   ? $initQuery->andWhere(array('MSBS.unit' => ':unit')) : $initQuery;

            return $initQuery;
        }

        public function selectProjects($project_manager = false)
        {
            $fields = array(
                'P.id',
                'P.name',
                // 'P.project_code',
                'IF(P.project_code IS NOT NULL, P.project_code, (SELECT temporary_project_code FROM project_code_requests WHERE project_id = P.id)) as project_code',
                'P.project_manager'
            );

            $whereNotInCondition = [
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
                              ->andWhereNotIn('P.id', $whereNotInCondition);

            $initQuery = ($project_manager) ? $initQuery->andWhere(array('P.project_manager' => ':head_id')) : $initQuery;

            return $initQuery;
        }

        public function selectDepartments($charging = false)
        {
            $fields = array(
                'D.id',
                'D.name',
                'D.charging',
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

        public function selectPRDTotalWithdrawn()
        {
            $fields = [
                'SUM(WI.withdrawn_quantity) as total'
            ];

            $initQuery = $this->select($fields)
                              ->from('withdrawal_items WI')
                              ->join(['withdrawals W' => 'W.id = WI.withdrawal_id'])
                              ->where(['WI.is_active' => ':is_active', 'W.is_active' => ':is_active', 'WI.purchase_requisition_description_id' => ':prd_id'])
                              ->andWhereNotIn('W.status', ['5','6']);

            return $initQuery;
        }

        public function insertWithdrawal($data = array())
        {
            $initQuery = $this->insert('withdrawals', $data);

            return $initQuery;
        }

        public function insertWithdrawalItems($data = array())
        {
            $initQuery = $this->insert('withdrawal_items', $data);

            return $initQuery;
        }

        public function updateWithdrawal( $id = '', $data = array())
        {
            $initQuery = $this->update('withdrawals', $id, $data);

            return $initQuery;
        }

    }
?>