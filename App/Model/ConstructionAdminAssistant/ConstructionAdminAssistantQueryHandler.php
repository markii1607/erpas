<?php
    namespace App\Model\ConstructionAdminAssistant;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class ConstructionAdminAssistantQueryHandler extends QueryHandler {

        /**
         * `selectPurchaseRequisitions` Query string that will fetch prs from table `purchase_requisitions`.
         * @return string
         */
        public function selectPurchaseRequisitions($id = false, $status = false, $count = false, $date_requested = false, $prs_no = false, $charging_type = '', $charging_id = false, $request_type_id = false, $requestor_id = false)
        {
            if ($count) {
                $fields = [
                    'count(PR.id) as prs_count'
                ];
            } else {
                $fields = [
                    'PR.id',
                    'PR.request_type_id',
                    'PR.prs_no',
                    'PR.signatories',
                    '"0" as charge_to',
                    'IF(PR.project_id IS NULL, D.charging, P.project_code) AS charging',
                    'CONCAT(PI.fname, " ", PI.lname) as full_name',
                    'EI.employee_no',
                    'PR.status',
                    '"prs" AS identifier',
                    'RT.name as request_type_name',
                    'DATE_FORMAT(PR.date_requested, "%M %d, %Y") as date_requested',
                    'DATE_FORMAT(PR.created_at, "%b %d, %Y") as created_at',
                    // 'IF(PR.project_id is NULL, D.name, P.name) AS charging_name',
                    'IF(PR.project_id IS NULL, D.charging, IF(P.project_code IS NULL, PCR.temporary_project_code, P.project_code)) as charging_code',
                    'IF(PR.project_id IS NULL, D.name, P.name) as charging_name',
                    // 'IF(PR.project_id IS NULL, CONCAT(D.charging, "-",(D.name)), CONCAT(IF(P.project_code IS NULL, PCR.temporary_project_code, P.project_code),"-", P.name)) as charging',
                    'POS.name as position_name'
                ];
            }

            $orWhereCondition = array(
                'PR.prs_no'                                       => ':filter_val',
                'DATE_FORMAT(PR.date_requested, "%M %d, %Y")'     => ':filter_val',
                'P.project_code'                                  => ':filter_val',
                'P.name'                                          => ':filter_val',
                'D.charging'                                      => ':filter_val',
                'D.name'                                          => ':filter_val',
                'PI.fname'                                        => ':filter_val',
                'PI.lname'                                        => ':filter_val',
                'RT.name'                                         => ':filter_val',
                'EI.employee_no'                                  => ':filter_val'
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
                // '28',	//	19005
                // '29',	//	19006
                // '30',	//	19007
                // '31',	//	19008
                // '32',	//	19009
                // '33',	//	19010
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

            $leftJoins = [
                'projects P'    => 'P.id = PR.project_id',
                'departments D' => 'PR.department_id = D.id',
                'project_code_requests PCR' => 'P.id = PCR.project_id'
            ];

            $joins = [
                'users U'                       => 'U.id = PR.created_by',
                'personal_informations PI'      => 'PI.id = U.personal_information_id',
                'employment_informations EI'    => 'EI.personal_information_id = PI.id',
                'positions POS'                 => 'POS.id = EI.position_id',
                'request_types RT'              => 'RT.id = PR.request_type_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('purchase_requisitions PR')
                              ->leftJoin($leftJoins)
                              ->join($joins)
                              ->where(['PR.is_active' => ':is_active'])
                              ->andWhereNotNull(['PR.signatories'])
                              ->andWhereNull(['PR.for_cancelation'])
                              ->andWhereNotIn('PR.project_id', $whereNotInCondition)
                              ->logicEx('AND')
                              ->orWhereLike($orWhereCondition);

            $initQuery = ($id)              ? $initQuery->andWhere(['PR.id' => ':id'])                                                  : $initQuery;
            $initQuery = ($status)          ? $initQuery->andWhere(['PR.status' => ':status'])                                          : $initQuery;
            $initQuery = ($date_requested)  ?   $initQuery->andWhere(['DATE_FORMAT(PR.created_at, "%Y-%m-%d")' => ':date_requested'])   : $initQuery;
            $initQuery = ($prs_no)          ?   $initQuery->andWhere(['PR.prs_no' => ':prs_no'])                                        : $initQuery;

            if (!empty($charging_type)) {
                if ($charging_type == 'P') {
                    $initQuery = ($charging_id) ? $initQuery->andWhere(['PR.project_id' => ':charging_id']) : $initQuery;
                } else if ($charging_type == 'D') {
                    $initQuery = ($charging_id) ? $initQuery->andWhere(['PR.department_id' => ':charging_id']) : $initQuery;
                }
            }

            $initQuery = ($request_type_id) ? $initQuery->andWhere(['PR.request_type_id' => ':request_type_id'])    : $initQuery;
            $initQuery = ($requestor_id)    ? $initQuery->andWhere(['PR.created_by' => ':requestor_id'])            : $initQuery;
            

            $initQuery = $initQuery->orderBy('PR.prs_no', 'DESC');

            return $initQuery;
        }

        /**
         * `selectPurchaseRequisitionDescriptions` Query string that will fetch prs description from table `purchase_requisition_descriptions`.
         * @param  boolean $id
         * @param  boolean $prId
         * @return string
         */
        public function selectPurchaseRequisitionDescriptions($id = false, $prId = false, $count = false, $item_category = false, $ms_id = false, $work_item = false)
        {
            if ($count) {
                $fields = [
                    'count(PRD.id) as prd_count'
                ];
            } else {
                $fields = [
                    'PRD.id',
                    'MS.specs',
                    'MS.code as supply_code',
                    'M.name as description',
                    // 'PRD.quantity',
                    'PRD.unit_measurement',
                    'PRD.remarks',
                    'PRD.wbs',
                    'PRD.work_volume',
                    'PRD.work_volume_unit',
                    'PRD.work_item_id',
                    // 'PRD.expense_type',
                    'WI.name as wi_name',
                    'WI.item_no',
                    'WI.unit',
                    'WIC.id as wic_id',
                    'WIC.name as wic_name',
                    'WIC.part as wic_part',
                    'ACC.name as acc_name',
                    'ACC.account_id as acc_code',
                    'PRD.signatories',
                    'PRD.status',
                    'PRD.category',
                    'DATE_FORMAT(PDS.delivery_date, "%M %d, %Y") as delivery_date',
                    'PDS.quantity'
                ];
            }


            $joins = [
                'material_specifications MS' => 'MS.id = PRD.item_spec_id',
                'materials M'                => 'M.id = MS.material_id',
                'material_categories MC'     => 'MC.id = M.material_category_id',
                'work_items WI'              => 'WI.id = PRD.work_item_id',
                'work_item_categories WIC'   => 'WIC.id = WI.work_item_category_id',
                'accounts ACC'               => 'PRD.account_id = ACC.id',
                'prd_delivery_sequences PDS' => 'PDS.purchase_requisition_description_id = PRD.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('purchase_requisition_descriptions PRD')
                              ->leftJoin($joins)
                              ->where(['PRD.is_active' => ':is_active']);

            $initQuery = ($id)   ? $initQuery->andWhere(['PRD.id' => ':id'])                                           : $initQuery;
            $initQuery = ($prId) ? $initQuery->andWhere(['PRD.purchase_requisition_id' => ':purchase_requisition_id']) : $initQuery;

            $initQuery = ($item_category)   ? $initQuery->andWhere(['PRD.category' => ':item_category']) : $initQuery;
            $initQuery = ($ms_id)           ? $initQuery->andWhere(['PRD.item_spec_id' => ':ms_id']) : $initQuery;
            $initQuery = ($work_item)       ? $initQuery->andWhere(['PRD.work_item_id' => ':work_item']) : $initQuery;

            $initQuery = $initQuery->orderBy('MS.specs', 'ASC');

            return $initQuery;
        }

        /**
         * `selectUsers` Query string that will fetch user from table `users`.
         * @param  boolean $id
         * @return string
         */
        public function selectPrdDeliverySequence($id = false)
        {
            $fields = [
                'PDS.id',
                'PDS.purchase_requisition_description_id',
                'PDS.seq_no',
                'DATE_FORMAT(PDS.delivery_date, "%b %d, %Y") as delivery_sequence_date',
                'PDS.quantity as delivery_sequence_quantity',

            ];

            $initQuery = $this->select($fields)
                              ->from('prd_delivery_sequences PDS')
                              ->where(['PDS.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['PDS.purchase_requisition_description_id' => ':prd_id']) : $initQuery;

            return $initQuery;
        }


        /**
         * `selectUsers` Query string that will fetch user from table `users`.
         * @param  boolean $id
         * @return string
         */
        public function selectUsers($id = false)
        {
            $fields = [
                'U.id',
                'PI.id as personal_information_id',
                'CONCAT(PI.fname, " ", PI.lname) as full_name',
                'P.name as position_name',

            ];

            $joins = [
                'personal_informations PI'   => 'PI.id = U.personal_information_id',
                'employment_informations EI' => 'EI.personal_information_id = PI.id',
                'positions P'                => 'P.id = EI.position_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('users U')
                              ->join($joins)
                              ->where(['U.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['U.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectSubProjects` Query string that will fetch sub projects from table `sub_projects`.
         * @return string
         */
        public function selectSubProjects($id = false, $wdId = false)
        {
            $fields = [
                'SP.id',
                'SP.work_discipline_id',
                'SP.wbs',
                'SP.code',
                'SP.name',
                '"sp" AS identifier'
            ];

            $orWhereCondition = array(
                'SP.wbs'  => ':filter_val',
                'SP.name' => ':filter_val',
            );

            $initQuery = $this->select($fields)
                              ->from('sub_projects SP')
                              ->where(['SP.is_active' => ':is_active'])
                              ->logicEx('AND')
                              ->orWhereLike($orWhereCondition);

            $initQuery = ($id)   ? $initQuery->andWhere(['SP.id' => ':id'])                                 : $initQuery;
            $initQuery = ($wdId) ? $initQuery->andWhere(['SP.work_discipline_id' => ':work_discipline_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectSubProjectTypes` Query string that will fetch sub project types from table `sub_project_types`.
         * @return string
         */
        public function selectSubProjectTypes($id = false, $spId = false)
        {
            $fields = [
                'SPT.id',
                'SPT.sub_project_id',
                'SPT.wbs',
                'SPT.name'
            ];

            $initQuery = $this->select($fields)
                              ->from('sub_project_types SPT')
                              ->where(['SPT.is_active' => ':is_active']);

            $initQuery = ($id)   ? $initQuery->andWhere(['SPT.id' => ':id'])                         : $initQuery;
            $initQuery = ($spId) ? $initQuery->andWhere(['SPT.sub_project_id' => ':sub_project_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectSptWics` Query string that will fetch sub project work item category from table `spt_wics`.
         * @return string
         */
        public function selectSptWics($id = false, $spId = false, $sptId = false)
        {
            $fields = [
                'SWIC.id',
                'SWIC.sub_project_id',
                'SWIC.wbs',
                'WIC.id as wic_id',
                'WIC.name as wic_name',
                '"wic" AS identifier'
            ];

            $initQuery = $this->select($fields)
                              ->from('spt_wics SWIC')
                              ->join(['work_item_categories WIC' => 'WIC.id = SWIC.work_item_category_id'])
                              ->where(['SWIC.is_active' => ':is_active', 'WIC.is_active' => ':is_active']);

            $initQuery = ($id)   ? $initQuery->andWhere(['SWIC.id' => ':id'])                                    : $initQuery;
            $initQuery = ($spId) ? $initQuery->andWhere(['SWIC.sub_project_id' => ':sub_project_id'])            : $initQuery;
            $initQuery = ($sptId) ? $initQuery->andWhere(['SWIC.sub_project_type_id' => ':sub_project_type_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectWorkItemCategories` Query string that will fetch work items from table `work_item_categories`.
         * @return string
         */
        public function selectWorkItemCategories($id = false)
        {
            $fields = [
                'WIC.id',
                'WIC.code',
                'WIC.name'
            ];

            $orWhereCondition = array(
                'WIC.code' => ':filter_val',
                'WIC.name' => ':filter_val',
            );

            $initQuery = $this->select($fields)
                              ->from('work_item_categories WIC')
                              ->where(['WIC.is_active' => ':is_active'])
                              ->logicEx('AND')
                              ->orWhereLike($orWhereCondition);

            $initQuery = ($id) ? $initQuery->andWhere(['WIC.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectSwWis` Query string that will fetch work items from table `sw_wis`.
         * @return string
         */
        public function selectSwWis($id = false, $swicId = false)
        {
            $fields = [
                'SWI.id',
                'SWI.wbs',
                'SWI.alternative_name',
                'IF(SWI.unit = "", WI.unit, SWI.unit) as unit',
                'SWI.safety_factor',
                'SWI.output',
                'WI.id as wi_id',
                'WI.name as wi_name',
                'WI.code as wi_wbs',
                'WI.item_no',
                '"wi" AS identifier'
            ];

            $joins = [
                'work_items WI' => 'WI.id = SWI.work_item_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('sw_wis SWI')
                              ->join($joins)
                              ->where(['SWI.is_active' => ':is_active', 'WI.is_active' => ':is_active']);

            $initQuery = ($id)     ? $initQuery->andWhere(['SWI.id' => ':id'])                 : $initQuery;
            $initQuery = ($swicId) ? $initQuery->andWhere(['SWI.spt_wic_id' => ':spt_wic_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectWorkItems` Query string that will fetch work items from table `work_items`.
         * @return string
         */
        public function selectWorkItems($id = false, $categoryId = false)
        {
            $fields = [
                'WI.id',
                'WI.code',
                'WI.wbs',
                'WI.name',
                'WI.item_no',
                'WI.unit',
                'WIC.id as wic_id',
                'WIC.name as wic_name',
                'WIC.code as wic_code'
            ];

            $orWhereCondition = array(
                'WI.code'    => ':filter_val',
                'WI.item_no' => ':filter_val',
                'WI.name'    => ':filter_val',
                'WI.unit'    => ':filter_val',
                'WI.wbs'     => ':filter_val',
            );

            $initQuery = $this->select($fields)
                              ->from('work_items WI')
                              ->join(['work_item_categories WIC' => 'WIC.id = WI.work_item_category_id'])
                              ->where(['WI.is_active' => ':is_active', 'WIC.is_active' => ':is_active'])
                              ->logicEx('AND')
                              ->orWhereLike($orWhereCondition);

            $initQuery = ($id)         ? $initQuery->andWhere(['WI.id' => ':id'])                                       : $initQuery;
            $initQuery = ($categoryId) ? $initQuery->andWhere(['WI.work_item_category_id' => ':work_item_category_id']) : $initQuery;

            return $initQuery;
        }

        public function selectMaterials($filter = false, $limit = '')
        {
            $fields = array(
                'MS.id',
                'MS.specs',
                'MS.code as material_code',
                'M.name as material_name',
                'M.material_category_id',
                'MSB.id as msb_id',
            );

            $joins = array(
                'material_specifications MS'            =>      'MS.id = MSB.material_specification_id',
                'materials M'                           =>      'M.id = MS.material_id',
            );

            $initQuery = $this->select($fields)
                              ->from('material_specification_brands MSB')
                              ->join($joins)
                              ->where(array('MSB.is_active' => ':is_active'));

            $initQuery = ($filter) ? $initQuery->logicEx('AND')->orWhereLike(array('MS.code' => ':filter','M.name' => ':filter', 'MS.specs' => ':filter')) : $initQuery;
            $initQuery = ($limit != '') ? $initQuery->logicEx('LIMIT '.$limit.', 50') : $initQuery;

            return $initQuery;
        }

        public function selectMsbSuppliers($msb_id = false, $unit = false)
        {
            $fields = array(
                'MSBS.id',
                'MSBS.material_specification_brand_id',
                'MSBS.code',
                'MSBS.unit',
                'MSBS.price',
                'MSBS.created_at',
            );

            $initQuery = $this->select($fields)
                              ->from('msb_suppliers MSBS')
                              ->where(array('MSBS.is_active' => ':is_active'));

            $initQuery = ($msb_id) ? $initQuery->andWhere(array('MSBS.material_specification_brand_id' => ':msb_id')) : $initQuery;
            $initQuery = ($unit) ? $initQuery->andWhere(array('MSBS.unit' => ':unit')) : $initQuery;

            return $initQuery;
        }

        public function selectEquipmentTypes($filter = false, $limit = '')
        {
            $fields = array(
                'ET.id',
                'ET.name',
                'ET.cost_code',
                'ET.unit'
            );

            $initQuery = $this->select($fields)
                              ->from('equipment_types ET')
                              ->where(array('ET.is_active' => ':is_active'));

            $initQuery = ($filter) ? $initQuery->logicEx('AND')->orWhereLike(array('ET.name' => ':filter','ET.cost_code' => ':filter')) : $initQuery;
            $initQuery = ($limit != '') ? $initQuery->logicEx('LIMIT '.$limit.', 50') : $initQuery;

            return $initQuery;
        }

        public function selectEquipments($equipment_type_id = false, $filter = false, $limit = '')
        {
            $fields = array(
                'E.id',
                'E.equipment_type_id',
                'E.model',
                'E.cost_code',
                'E.brand'
            );

            $initQuery = $this->select($fields)
                              ->from('equipments E');

            $initQuery = ($equipment_type_id) ? $initQuery->andWhere(array('E.equipment_type_id' => ':equipment_type_id')) : $initQuery;
            $initQuery = ($filter) ? $initQuery->logicEx('AND')->orWhereLike(array('E.model' => ':filter', 'E.brand' => ':filter','E.cost_code' => ':filter')) : $initQuery;
            $initQuery = ($limit != '') ? $initQuery->logicEx('LIMIT '.$limit.', 50') : $initQuery;

            return $initQuery;
        }

        public function selectPowertools($filter = false, $limit = '')
        {
            $fields = array(
                'PT.id',
                'PT.code',
                'PT.specification',
                '"POWER" as type'
            );

            $initQuery = $this->select($fields)
                              ->from('power_tools PT')
                              ->where(array('PT.is_active' => ':is_active'));

            $initQuery = ($filter) ? $initQuery->logicEx('AND')->orWhereLike(array('PT.code' => ':filter', 'PT.specification')) : $initQuery;
            $initQuery = ($limit != '') ? $initQuery->logicEx('LIMIT '.$limit.', 50') : $initQuery;

            return $initQuery;
        }

        public function selectPositions()
        {
            $fields = array(
                'P.id',
                'P.code',
                'P.name'
            );

            $initQuery = $this->select($fields)
                              ->from('positions P')
                              ->where(array('P.is_active' => ':is_active'));

            // $initQuery = ($filter) ? $initQuery->logicEx('AND')->orWhereLike(array('P.code' => ':filter', 'P.name')) : $initQuery;
            // $initQuery = ($limit != '') ? $initQuery->logicEx('LIMIT '.$limit.', 50') : $initQuery;

            return $initQuery;
        }

        public function selectSwMaterials($sw_wi_id = false, $id = false)
        {
            $fields = array(
                'SWM.id',
                'SWM.sw_wi_id',
                'SWM.msb_supplier_id',
                'SWM.type',
                'SWM.multiplier',
                'MSBS.code as material_code',
                'MS.specs',
                'M.name as material_name',
                'MC.name as material_category',
                'SWW.wbs',
                'MSBS.price',
                'MSBS.unit',
            );

            $orWhereCondition = array(
                'MSBS.code'=> ':filter_val',
                'MS.specs' => ':filter_val',
                'M.name'   => ':filter_val',
                'MC.name'  => ':filter_val',
                'SWW.wbs'  => ':filter_val',
            );

            $joins = array(
                'msb_suppliers MSBS'                    =>      'MSBS.id = SWM.msb_supplier_id',
                'material_specification_brands MSB'     =>      'MSB.id = MSBS.material_specification_brand_id',
                'material_specifications MS'            =>      'MS.id = MSB.material_specification_id',
                'materials M'                           =>      'M.id = MS.material_id',
                'material_categories MC'                =>      'MC.id = M.material_category_id',
                'sw_wis SWW'                            =>      'SWW.id = SWM.sw_wi_id',
            );

            $initQuery = $this->select($fields)
                              ->from('sw_materials SWM')
                              ->leftJoin($joins)
                              ->where(array('SWM.is_active' => ':is_active'))
                              ->logicEx('AND')
                              ->orWhereLike($orWhereCondition);

            $initQuery = ($sw_wi_id) ? $initQuery->andWhere(array('SWM.sw_wi_id' => ':sw_wi_id')) : $initQuery;
            $initQuery = ($id) ? $initQuery->andWhere(array('SWM.id' => ':id')) : $initQuery;

            return $initQuery;
        }

        public function selectSwEquipments($sw_wi_id = false, $id = false)
        {
            $fields = array(
                'SWE.id',
                'SWE.sw_wi_id',
                'SWE.equipment_id',
                'SWE.type',
                'SWE.equipment_rate',
                'SWE.no_of_equipment',
                'SWW.wbs',
                'E.model',
                'E.brand',
            );

            $join = array(
                'sw_wis SWW'    =>  'SWW.id = SWE.sw_wi_id',
                'equipments E'  =>  'E.id = SWE.equipment_id'
            );

            $orWhereCondition = array(
                'E.model'   => ':filter_val',
                'E.brand'   => ':filter_val',
                'SWW.wbs'   => ':filter_val',
            );

            $initQuery = $this->select($fields)
                              ->from('sw_equipments SWE')
                              ->leftJoin($join)
                              ->where(array('SWE.is_active' => ':is_active'))
                              ->logicEx('AND')
                              ->orWhereLike($orWhereCondition);

            $initQuery = ($sw_wi_id) ? $initQuery->andWhere(array('SWE.sw_wi_id' => ':sw_wi_id')) : $initQuery;
            $initQuery = ($id) ? $initQuery->andWhere(array('SWE.id' => ':id')) : $initQuery;

            return $initQuery;
        }

        public function selectSwLabor($sw_wi_id = false, $id = false)
        {
            $fields = array(
                'SWL.id',
                'SWL.sw_wi_id',
                'SWL.position_id',
                'SWL.type',
                'SWL.manpower_rate',
                'SWL.no_of_manpower',
                'SWW.wbs',
                'P.name as position'
            );

            $joins = array(
                'sw_wis SWW'    =>  'SWW.id = SWL.sw_wi_id',
                'positions P'   =>  'P.id = SWL.position_id'

            );

            $orWhereCondition = array(
                'SWL.manpower_rate'  => ':filter_val',
                'P.name'             => ':filter_val',
                'SWW.wbs'            => ':filter_val',
            );

            $initQuery = $this->select($fields)
                              ->from('sw_labor SWL')
                              ->leftJoin($joins)
                              ->where(array('SWL.is_active' => ':is_active'))
                              ->logicEx('AND')
                              ->orWhereLike($orWhereCondition);

            $initQuery = ($sw_wi_id) ? $initQuery->andWhere(array('SWL.sw_wi_id' => ':sw_wi_id')) : $initQuery;
            $initQuery = ($id) ? $initQuery->andWhere(array('SWL.id' => ':id')) : $initQuery;

            return $initQuery;
        }

        public function selectSwPowertools($sw_wi_id = false, $id = false)
        {
            $fields = array(
                'SWP.id',
                'SWP.sw_wi_id',
                'SWP.power_tool_id',
                'SWP.no_of_powertool',
                'SWW.wbs',
                'PT.code',
                'PT.specification'
            );

            $joins = array(
                'sw_wis SWW'        =>  'SWW.id = SWP.sw_wi_id',
                'power_tools PT'    =>  'PT.id = SWP.power_tool_id'
            );

            $orWhereCondition = array(
                'PT.code'            => ':filter_val',
                'PT.specification'   => ':filter_val',
                'SWW.wbs'            => ':filter_val',
            );

            $initQuery = $this->select($fields)
                              ->from('sw_powertools SWP')
                              ->leftJoin($joins)
                              ->where(array('SWP.is_active' => ':is_active'))
                              ->logicEx('AND')
                              ->orWhereLike($orWhereCondition);

            $initQuery = ($sw_wi_id) ? $initQuery->andWhere(array('SWP.sw_wi_id' => ':sw_wi_id')) : $initQuery;
            $initQuery = ($id) ? $initQuery->andWhere(array('SWP.id' => ':id')) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectPrHeavyEquipments` Query string that will fetch prs description from table `pr_heavy_equipments`.
         * @param  boolean $id
         * @param  boolean $prId
         * @return string
         */
        public function selectPrHeavyEquipments($id = false, $prId = false, $item_category = false, $equipment_type_id = false)
        {
            $fields = [
                'PRH.id',
                'ET.name as specs',
                'EC.name as description',
                'PRH.total_no_of_equipment as quantity',
                '"unit" as unit_measurement',
                'PRH.signatories',
                'PRH.status',
                'PRH.category',
                'WI.name as wi_name',
                'WI.item_no',
                'WIC.id as wic_id',
                'WIC.name as wic_name',
                'WIC.part as wic_part',
                'ACC.name as acc_name',
                'ACC.account_id as acc_code',
            ];

            $joins = [
                'equipment_types ET'        => 'ET.id = PRH.equipment_type_id',
                'equipment_categories EC'   => 'EC.id = ET.equipment_category_id',
                'pr_heavy_work_items HWI'   => 'HWI.pre_id = PRH.id',
                'work_items WI'             => 'WI.id = HWI.wi_id',
                'work_item_categories WIC'  => 'WIC.id = WI.work_item_category_id',
                'accounts ACC'              => 'PRH.account_id = ACC.id',
            ];

            $initQuery = $this->select($fields)
                              ->from('pr_heavy_equipments PRH')
                              ->join($joins)
                              ->where(['PRH.is_active' => ':is_active']);

            $initQuery = ($id)   ? $initQuery->andWhere(['PRH.id' => ':id'])                         : $initQuery;
            $initQuery = ($prId) ? $initQuery->andWhere(['PRH.pr_id' => ':purchase_requisition_id']) : $initQuery;

            $initQuery = ($item_category)       ? $initQuery->andWhere(['PRH.category' => ':item_category'])                : $initQuery;
            $initQuery = ($equipment_type_id)   ? $initQuery->andWhere(['PRH.equipment_type_id' => ':equipment_type_id'])   : $initQuery;

            $initQuery = $initQuery->orderBy('ET.name', 'ASC');

            return $initQuery;
        }

        /**
         * `selectPrEquipments` Query string that will fetch prs description from table `pr_equipments`.
         * @param  boolean $id
         * @param  boolean $prId
         * @return string
         */
        public function selectPrEquipments($id = false, $prId = false, $item_category = false, $equipment_type_id = false)
        {
            $fields = [
                'PRE.id',
                'ET.name as specs',
                'EC.name as description',
                'PRE.total_no_of_equipment as quantity',
                '"unit" as unit_measurement',
                'PRE.category',
                'PRE.signatories',
                'PRE.status',
                'WI.name as wi_name',
                'WI.item_no',
                'WIC.id as wic_id',
                'WIC.name as wic_name',
                'WIC.part as wic_part',
                'ACC.name as acc_name',
                'ACC.account_id as acc_code',

            ];

            $joins = [
                'equipment_types ET'        => 'ET.id = PRE.equipment_type_id',
                'equipment_categories EC'   => 'EC.id = ET.equipment_category_id',
                'pr_work_items LWI'         => 'LWI.pre_id = PRE.id',
                'work_items WI'             => 'WI.id = LWI.wi_id',
                'work_item_categories WIC'  => 'WIC.id = WI.work_item_category_id',
                'accounts ACC'              => 'PRE.account_id = ACC.id',

            ];

            $initQuery = $this->select($fields)
                              ->from('pr_equipments PRE')
                              ->join($joins)
                              ->where(['PRE.is_active' => ':is_active']);

            $initQuery = ($id)   ? $initQuery->andWhere(['PRE.id' => ':id'])                         : $initQuery;
            $initQuery = ($prId) ? $initQuery->andWhere(['PRE.pr_id' => ':purchase_requisition_id']) : $initQuery;

            $initQuery = ($item_category)       ? $initQuery->andWhere(['PRE.category' => ':item_category'])                : $initQuery;
            $initQuery = ($equipment_type_id)   ? $initQuery->andWhere(['PRE.equipment_type_id' => ':equipment_type_id'])   : $initQuery;

            $initQuery = $initQuery->orderBy('ET.name', 'ASC');

            return $initQuery;
        }

        /**
         * `selectPrTools` Query string that will fetch prs description from table `pr_tools`.
         * @param  boolean $id
         * @param  boolean $prId
         * @return string
         */
        public function selectPrTools($id = false, $prId = false, $item_category = false, $tool_type = '', $tool_id = false)
        {
            $fields = [
                'PRT.id',
                'IF(PRT.power_tool_id IS NOT NULL, PT.specification, HT.specification) as specs',
                'IF(PRT.power_tool_id IS NOT NULL, PTC.name, HTC.name) as description',
                'PRWI.requested_units as quantity',
                'PRWI.unit_of_measurement as unit_measurement',
                'IF(PRT.category = 1, PRT.category, null) as category',
                'PRT.status',
                'PRT.process_status',
                'WI.name as wi_name',
                'WI.item_no',
                'WI.unit',
                'WIC.id as wic_id',
                'WIC.name as wic_name',
                'WIC.part as wic_part',
                'ACC.name as acc_name',
                'ACC.account_id as acc_code',
            ];

            $leftJoins = [
                'pr_work_items PRWI'        => 'PRWI.pr_tool_id = PRT.id',
                // 'ps_swi_directs PSD'        => 'PSD.id = PRWI.ps_swi_directs_id',
                // 'sw_wis SW'                 => 'SW.id = PSD.sw_wi_id',
                'work_items WI'             => 'WI.id = PRWI.wi_id',
                'work_item_categories WIC'  => 'WIC.id = WI.work_item_category_id',
                'power_tools PT'            => 'PT.id = PRT.power_tool_id',
                'power_tool_categories PTC' => 'PTC.id = PT.category',
                'hand_tools HT'             => 'HT.id = PRT.hand_tool_id',
                'hand_tool_categories HTC'  => 'HTC.id = HT.category',
                'accounts ACC'              => 'PRT.account_id = ACC.id',
            ];

            $initQuery = $this->select($fields)
                              ->from('pr_tools PRT')
                              ->leftJoin($leftJoins)
                              ->where(['PRT.is_active' => ':is_active']);

            $initQuery = ($id)   ? $initQuery->andWhere(['PRT.id' => ':id'])                         : $initQuery;
            $initQuery = ($prId) ? $initQuery->andWhere(['PRT.pr_id' => ':purchase_requisition_id']) : $initQuery;
            
            $initQuery = ($item_category) ? $initQuery->andWhere(['PRT.category' => ':item_category']) : $initQuery;

            if ($tool_type != '') {
                if ($tool_type == 'POWER') {
                    $initQuery = ($tool_id) ? $initQuery->andWhere(['PRT.power_tool_id' => ':tool_id']) : $initQuery;
                } else if ($tool_type == 'HAND') {
                    $initQuery = ($tool_id) ? $initQuery->andWhere(['PRT.hand_tool_id' => ':tool_id']) : $initQuery;
                }
                
            }

            return $initQuery;
        }

        /**
         * `selectPrToolSignatories` Query string that select from table `pr_tool_signatories`.
         * @param  string $id
         * @param  string $prToolId
         * @return string
         */
        public function selectPrToolSignatories($id = '', $prToolId = '')
        {
            $fields = [
                'PRTS.id',
                'PRTS.status',
                'PRTS.comment',
                'CONCAT(PI.fname, " ", PI.lname) as full_name',
            ];

            $joins = [
                'users U'                  => 'U.id = PRTS.user_id',
                'personal_informations PI' => 'PI.id = U.personal_information_id',
            ];

            $initQuery = $this->select($fields)
                              ->from('pr_tool_signatories PRTS')
                              ->join($joins)
                              ->where(['PRTS.is_active' => ':is_active']);

            $initQuery = ($id)       ? $initQuery->andWhere(['PRTS.id' => ':id'])                 : $initQuery;
            $initQuery = ($prToolId) ? $initQuery->andWhere(['PRTS.pr_tool_id' => ':pr_tool_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectPrsPpeDescriptions` Query string that will fetch prs description from table `prs_ppe_descriptions`.
         * @param  boolean $id
         * @param  boolean $prId
         * @return string
         */
        public function selectPrsPpeDescriptions($id = false, $prId = false, $ms_id = false)
        {
            $fields = [
                'PPD.id',
                'MS.specs',
                'M.name as description',
                'PPD.quantity',
                'PPD.unit_measurement',
                'PPD.signatories',
                'PPD.status',
                'WI.name as wi_name',
                'WI.item_no',
                'WI.unit',
                'WIC.id as wic_id',
                'WIC.name as wic_name',
                'WIC.part as wic_part',
                'ACC.name as acc_name',
                'ACC.account_id as acc_code',
            ];

            $joins = [
                'material_specifications MS'  => 'MS.id = PPD.material_specification_id',
                'materials M'                 => 'M.id = MS.material_id',
                'work_items WI'               => 'WI.id = PPD.work_item_id',
                'work_item_categories WIC'    => 'WIC.id = WI.work_item_category_id',
                'accounts ACC'                => 'ACC.id = PPD.account_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('prs_ppe_descriptions PPD')
                              ->leftJoin($joins)
                              ->where(['PPD.is_active' => ':is_active']);

            $initQuery = ($id)    ? $initQuery->andWhere(['PPD.id' => ':id'])                                           : $initQuery;
            $initQuery = ($prId)  ? $initQuery->andWhere(['PPD.purchase_requisition_id' => ':purchase_requisition_id']) : $initQuery;
            $initQuery = ($ms_id) ? $initQuery->andWhere(['PPD.material_specification_id' => ':ms_id']) : $initQuery;

            $initQuery = $initQuery->orderBy('M.name', 'ASC');

            return $initQuery;
        }

        public function selectPrLabors($pr_id = false, $item_category = false, $position_id = false)
        {
            $fields = array(
                'PRL.id',
                'PRL.pr_id',
                'PRL.category',
                '(SELECT name FROM positions WHERE id = PRL.position_id) as specs',
                '(SELECT IF(position_type IS NULL, "", IF(position_type = 1, "SKILLED LABORER", "")) FROM positions WHERE id = PRL.position_id) as description',
                'PLWI.requested_labor as quantity',
                '"" as unit_measurement',
                'PRL.remarks',
                'PRL.signatories',
                'PRL.status',
                'PLWI.id as prl_work_item_id',
                'WI.name as wi_name',
                'WI.item_no',
                'WI.unit',
                'WIC.id as wic_id',
                'WIC.name as wic_name',
                'WIC.part as wic_part',
                'ACC.name as acc_name',
                'ACC.account_id as acc_code',

            );

            $leftJoins = array(
                'prl_work_items PLWI'                   => 'PLWI.prl_id = PRL.id',
                'ps_swi_directs PSD'                    => 'PSD.id = PLWI.ps_swi_directs_id',
                'sw_wis SW'                             => 'SW.id = PSD.sw_wi_id',
                'work_items WI'                         => 'WI.id = SW.work_item_id',
                'work_item_categories WIC'              => 'WIC.id = WI.work_item_category_id',
                'accounts ACC'                          => 'PRL.account_id = ACC.id',
            );

            $initQuery = $this->select($fields)
                              ->from('pr_labors PRL')
                              ->leftJoin($leftJoins)
                              ->where(array('PRL.is_active' => ':is_active'));

            $initQuery = ($pr_id)           ? $initQuery->andWhere(array('PRL.pr_id' => ':pr_id'))              : $initQuery;
            $initQuery = ($item_category)   ? $initQuery->andWhere(array('PRL.category' => ':item_category'))   : $initQuery;
            $initQuery = ($position_id)     ? $initQuery->andWhere(array('PRL.position_id' => ':position_id'))  : $initQuery;

            return $initQuery;
        }

        public function selectPrMedicalMaterials($pr_id = false, $msb_id = false)
        {
            $fields = array(
                'PRM.id',
                'PRM.quantity',
                'PRM.unit_of_measurement as unit_measurement',
                'PRM.remarks',
                'PRM.status',
                'PRM.process_status',
                'MS.specs',
                'MS.code as supply_code',
                'M.name as description',
                'WI.name as wi_name',
                'WI.item_no',
                'WI.unit',
                'WIC.id as wic_id',
                'WIC.name as wic_name',
                'WIC.part as wic_part',
                'ACC.name as acc_name',
                'ACC.account_id as acc_code',
            );

            $leftJoins = array(
                'material_specification_brands MSB'     =>      'MSB.id = PRM.material_specification_brand_id',
                'material_specifications MS'            =>      'MS.id = MSB.material_specification_id',
                'materials M'                           =>      'M.id = MS.material_id',
                'p_wi_indirects PWI'                    =>      'PWI.id = PRM.p_wi_indirect_id',
                'work_items WI'                         =>      'WI.id = PWI.work_item_id',
                'work_item_categories WIC'              =>      'WIC.id = WI.work_item_category_id',
                'accounts ACC'                          =>      'PRM.account_id = ACC.id',
            );

            $initQuery = $this->select($fields)
                              ->from('pr_medical_materials PRM')
                              ->leftJoin($leftJoins)
                              ->where(array('PRM.is_active' => ':is_active'));

            $initQuery = ($pr_id)  ? $initQuery->andWhere(array('PRM.pr_id' => ':pr_id')) : $initQuery;
            $initQuery = ($msb_id) ? $initQuery->andWhere(array('PRM.material_specification_brand_id' => ':msb_id')) : $initQuery;

            return $initQuery;
        }

        public function selectPrdManpowerServices($pr_id = false, $item_category = false, $ms_id = false)
        {
            $fields = [
                'PMS.id',
                'MS.specs',
                'MS.code as supply_code',
                'M.name as description',
                'PMS.quantity',
                'PMS.unit_measurement',
                'PMS.remarks',
                'PMS.wbs',
                'PMS.work_volume',
                'PMS.work_volume_unit',
                'WI.name as wi_name',
                'WI.item_no',
                'WI.unit',
                'WIC.id as wic_id',
                'WIC.name as wic_name',
                'WIC.part as wic_part',
                'ACC.name as acc_name',
                'ACC.account_id as acc_code',
                'PMS.signatories',
                'PMS.status',
                'PMS.category',
                // 'DATE_FORMAT(PDS.delivery_date, "%M %d, %Y") as delivery_date'
            ];

            $joins = [
                'material_specifications MS' => 'MS.id = PMS.material_specification_id',
                'materials M'                => 'M.id = MS.material_id',
                'material_categories MC'     => 'MC.id = M.material_category_id',
                'work_items WI'              => 'WI.id = PMS.work_item_id',
                'work_item_categories WIC'   => 'WIC.id = WI.work_item_category_id',
                'accounts ACC'               => 'PMS.account_id = ACC.id',
            ];

            $initQuery = $this->select($fields)
                            ->from('prd_manpower_services PMS')
                            ->leftJoin($joins)
                            ->where(['PMS.is_active' => ':is_active']);

            $initQuery = ($pr_id) ? $initQuery->andWhere(['PMS.purchase_requisition_id' => ':pr_id']) : $initQuery;
            $initQuery = ($item_category) ? $initQuery->andWhere(['PMS.category' => ':item_category']) : $initQuery;
            $initQuery = ($ms_id) ? $initQuery->andWhere(['PMS.material_specification_id' => ':ms_id']) : $initQuery;

            $initQuery = $initQuery->orderBy('MS.specs', 'ASC');

            return $initQuery;
        }

        public function selectPrdAttachments($prd_id = false)
        {
            $fields = array(
                'A.id',
                'A.purchase_requisition_description_id',
                'A.filename',
            );

            $initQuery = $this->select($fields)
                              ->from('prd_attachments A')
                              ->where(array('A.is_active' => ':is_active'));

            $initQuery = ($prd_id) ? $initQuery->andWhere(array('A.purchase_requisition_description_id' => ':prd_id' )) : $initQuery;

            return $initQuery;
        }

        public function selectPrToolAttachments($pr_tool_id = false)
        {
            $fields = array(
                'A.id',
                'A.pr_tool_id',
                'A.filename',
            );

            $initQuery = $this->select($fields)
                              ->from('pr_tool_attachments A')
                              ->where(array('A.is_active' => ':is_active'));

            $initQuery = ($pr_tool_id) ? $initQuery->andWhere(array('A.pr_tool_id' => ':pr_tool_id' )) : $initQuery;

            return $initQuery;
        }

        public function selectPrMedicalAttachments($pr_medical_material_id = false)
        {
            $fields = array(
                'A.id',
                'A.pr_medical_material_id',
                'A.filename',
            );

            $initQuery = $this->select($fields)
                              ->from('pr_medical_attachments A')
                              ->where(array('A.is_active' => ':is_active'));

            $initQuery = ($pr_medical_material_id) ? $initQuery->andWhere(array('A.pr_medical_material_id' => ':pr_medical_material_id' )) : $initQuery;

            return $initQuery;
        }

        public function selectPrLaborAttachments($pr_labor_id = false)
        {
            $fields = array(
                'A.id',
                'A.pr_labor_id',
                'A.filename',
            );

            $initQuery = $this->select($fields)
                              ->from('pr_labor_attachments A')
                              ->where(array('A.is_active' => ':is_active'));

            $initQuery = ($pr_labor_id) ? $initQuery->andWhere(array('A.pr_labor_id' => ':pr_labor_id' )) : $initQuery;

            return $initQuery;
        }

        public function selectPrHeavyEquipmentAttachments($pr_equipment_id = false)
        {
            $fields = array(
                'A.id',
                'A.pr_equipment_id',
                'A.filename',
            );

            $initQuery = $this->select($fields)
                              ->from('pr_heavy_equipment_attachments A')
                              ->where(array('A.is_active' => ':is_active'));

            $initQuery = ($pr_equipment_id) ? $initQuery->andWhere(array('A.pr_equipment_id' => ':pr_equipment_id' )) : $initQuery;

            return $initQuery;
        }

        public function selectPrLightEquipmentAttachments($pr_equipment_id = false)
        {
            $fields = array(
                'A.id',
                'A.pr_equipment_id',
                'A.filename',
            );

            $initQuery = $this->select($fields)
                              ->from('pr_light_equipment_attachments A')
                              ->where(array('A.is_active' => ':is_active'));

            $initQuery = ($pr_equipment_id) ? $initQuery->andWhere(array('A.pr_equipment_id' => ':pr_equipment_id' )) : $initQuery;

            return $initQuery;
        }

        public function selectPrsPpeAttachments($prs_ppe_description_id = false)
        {
            $fields = array(
                'A.id',
                'A.prs_ppe_description_id',
                'A.filename',
            );

            $initQuery = $this->select($fields)
                              ->from('prs_ppe_attachments A')
                              ->where(array('A.is_active' => ':is_active'));

            $initQuery = ($prs_ppe_description_id) ? $initQuery->andWhere(array('A.prs_ppe_description_id' => ':ppd_id' )) : $initQuery;

            return $initQuery;
        }

        public function selectPrdManpowerAttachments($prd_manpower_service_id = false)
        {
            $fields = array(
                'A.id',
                'A.prd_manpower_service_id',
                'A.filename',
            );

            $initQuery = $this->select($fields)
                              ->from('prd_manpower_attachments A')
                              ->where(array('A.is_active' => ':is_active'));

            $initQuery = ($prd_manpower_service_id) ? $initQuery->andWhere(array('A.prd_manpower_service_id' => ':pms_id' )) : $initQuery;

            return $initQuery;
        }

        public function selectPrMedicalSignatories($prmId = '')
        {
            $fields = [
                'PRMS.id',
                'PRMS.user_id',
                'PRMS.status',
                'IF(PRMS.comment IS NULL, "", PRMS.comment) as comment',
                'CONCAT(PI.fname, " ", PI.lname) as full_name',
            ];

            $joins = [
                'users U'                  => 'U.id = PRMS.user_id',
                'personal_informations PI' => 'PI.id = U.personal_information_id',
            ];

            $initQuery = $this->select($fields)
                              ->from('pr_medical_signatories PRMS')
                              ->join($joins)
                              ->where(['PRMS.is_active' => ':is_active']);

            $initQuery = ($prmId) ? $initQuery->andWhere(['PRMS.pr_medical_material_id' => ':prm_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * KTM's syntax
         */

        public function selectRequestTypes()
        {
            $fields = array(
                'RT.id',
                'RT.name',
                'RT.cost_code',
            );

            $initQuery = $this->select($fields)
                              ->from('request_types RT');

            return $initQuery;
        }

        public function selectHandTools()
        {
            $fields = array(
                'HT.id',
                'HT.code',
                'HT.specification',
                '"HAND" as type'
            );

            $initQuery = $this->select($fields)
                              ->from('hand_tools HT')
                              ->where(array('HT.is_active' => ':is_active'));

            return $initQuery;
        }

        public function selectHeavyEquipments()
        {
            $fields = array(
                'HE.id',
                'HE.body_no',
                'HE.cost_code',
                'HE.description',
                'HE.capacity',
                'HE.c_unit',
            );

            $initQuery = $this->select($fields)
                              ->from('heavy_equipments HE');

            return $initQuery;
        }

        public function selectLightEquipments()
        {
            $fields = array(
                'SE.id',
                'SE.cost_code',
                'SE.model',
            );

            $initQuery = $this->select($fields)
                              ->from('small_equipments SE');

            return $initQuery;
        }

        public function selectProjects()
        {
            $fields = array(
                'P.id',
                'P.project_code as charging_code',
                'P.name as charging_name',
                '"P" as type'
            );

            $initQuery = $this->select($fields)
                              ->from('projects P')
                              ->where(array('P.is_active' => ':is_active'));

            return $initQuery;
        }

        public function selectDepartments()
        {
            $fields = array(
                'D.id',
                'D.charging as charging_code',
                'D.name as charging_name',
                '"D" as type'
            );

            $initQuery = $this->select($fields)
                              ->from('departments D')
                              ->where(array('D.is_active' => ':is_active'));

            return $initQuery;
        }

        public function selectPrsNumbers()
        {
            $fields = array(
                'PR.id',
                'PR.prs_no',
            );

            $initQuery = $this->select($fields)
                              ->from('purchase_requisitions PR')
                              ->where(array('PR.is_active' => ':is_active'));

            return $initQuery;
        }

        public function selectAllWorkItems($filter = false, $limit = '')
        {
            $fields = array(
                'WI.id',
                'WI.wbs',
                'WI.item_no',
                'WI.name as item_name',
                'IF(WIC.part IS NULL, "", CONCAT("PART ", WIC.part)) as part_code',
                'WIC.name as item_category'
            );

            $initQuery = $this->select($fields)
                              ->from('work_items WI')
                              ->join(['work_item_categories WIC' => 'WIC.id = WI.work_item_category_id'])
                              ->where(['WI.is_active' => ':is_active']);
                            //   ->groupBy('WI.name');

            $initQuery = ($filter)      ? $initQuery->logicEx('AND')->orWhereLike(array('WI.wbs' => ':filter','WI.item_no' => ':filter', 'WI.name' => ':filter')) : $initQuery;
            $initQuery = ($limit != '') ? $initQuery->logicEx('LIMIT '.$limit.', 50') : $initQuery;

            return $initQuery;
        }

        /**
         * `insertSubProjectType` Query string that will insert to table `sub_project_types`
         * @return string
         */
        public function insertSubProjectType($data = [])
        {
            $initQuery = $this->insert('sub_project_types', $data);

            return $initQuery;
        }

        /**
         * `insertSptWic` Query string that will insert to table `spt_wics`
         * @return string
         */
        public function insertSptWic($data = [])
        {
            $initQuery = $this->insert('spt_wics', $data);

            return $initQuery;
        }

        /**
         * `insertSwWi` Query string that will insert to table `sw_wis`
         * @return string
         */
        public function insertSwWi($data = [])
        {
            $initQuery = $this->insert('sw_wis', $data);

            return $initQuery;
        }

        /**
         * `insertWorkDiscipline` Query string that will insert to table `work_disciplines`
         * @return string
         */
        public function insertWorkDiscipline($data = [])
        {
            $initQuery = $this->insert('work_disciplines', $data);

            return $initQuery;
        }

        /**
         * `insertSubProject` Query string that will insert to table `sub_projects`
         * @return string
         */
        public function insertSubProject($data = [])
        {
            $initQuery = $this->insert('sub_projects', $data);

            return $initQuery;
        }

        /**
         * `insertWorkItemCategory` Query string that will insert to table `work_item_categories`
         * @return string
         */
        public function insertWorkItemCategory($data = [])
        {
            $initQuery = $this->insert('work_item_categories', $data);

            return $initQuery;
        }

        /**
         * `insertWorkItem` Query string that will insert to table `work_items`
         * @return string
         */
        public function insertWorkItem($data = [])
        {
            $initQuery = $this->insert('work_items', $data);

            return $initQuery;
        }

        public function insertSwMaterial($data = [])
        {
            $initQuery = $this->insert('sw_materials', $data);

            return $initQuery;
        }

        public function insertSwEquipment($data = [])
        {
            $initQuery = $this->insert('sw_equipments', $data);

            return $initQuery;
        }

        public function insertSwLabor($data = [])
        {
            $initQuery = $this->insert('sw_labor', $data);

            return $initQuery;
        }

        public function insertSwPowertool($data = [])
        {
            $initQuery = $this->insert('sw_powertools', $data);

            return $initQuery;
        }

        public function updateSwMaterial($id = '', $data = [])
        {
            $initQuery = $this->update('sw_materials', $id, $data);

            return $initQuery;
        }

        public function updateSwEquipment($id = '', $data = [])
        {
            $initQuery = $this->update('sw_equipments', $id, $data);

            return $initQuery;
        }

        public function updateSwLabor($id = '', $data = [])
        {
            $initQuery = $this->update('sw_labor', $id, $data);

            return $initQuery;
        }

        public function updateSwPowertools($id = '', $data = [])
        {
            $initQuery = $this->update('sw_powertools', $id, $data);

            return $initQuery;
        }

        /**
         * `updateWorkDiscipline` Query string that will update specific work discipline from table `work_disciplines`
         * @return string
         */
        public function updateWorkDiscipline($id = '', $data = [])
        {
            $initQuery = $this->update('work_disciplines', $id, $data);

            return $initQuery;
        }

        /**
         * `updateSubProject` Query string that will update specific sub project from table `sub_projects`
         * @return string
         */
        public function updateSubProject($id = '', $data = [])
        {
            $initQuery = $this->update('sub_projects', $id, $data);

            return $initQuery;
        }

        /**
         * `updateWorkItemCategory` Query string that will update specific work item category from table `work_item_categories`
         * @return string
         */
        public function updateWorkItemCategory($id = '', $data = [])
        {
            $initQuery = $this->update('work_item_categories', $id, $data);

            return $initQuery;
        }

        /**
         * `updateWorkItem` Query string that will update specific work item from table `work_items`
         * @return string
         */
        public function updateWorkItem($id = '', $data = [])
        {
            $initQuery = $this->update('work_items', $id, $data);

            return $initQuery;
        }

        /**
         * `updateSwWis` Query string that will update specific work item from table `sw_wis`
         * @return string
         */
        public function updateSwWis($id = '', $data = [])
        {
            $initQuery = $this->update('sw_wis', $id, $data);

            return $initQuery;
        }

        /*
            KTM's syntax special
        */
        public function updateRequest($id = '', $data = [])
        {
            $initQuery = $this->update('purchase_requisitions', $id, $data);

            return $initQuery;
        }
    }