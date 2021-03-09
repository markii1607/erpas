<?php
    namespace App\Model\ConstructionAdminAssistant;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class PrsHoMonitoringQueryHandler extends QueryHandler {

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
                    '"1" as charge_to',
                    'D.charging',
                    'CONCAT(PI.fname, " ", LEFT(PI.mname,1), ". ", PI.lname) as full_name',
                    'EI.employee_no',
                    'PR.status',
                    '"prs" AS identifier',
                    'RT.name as request_type_name',
                    'DATE_FORMAT(PR.date_requested, "%M %d, %Y") as date_requested',
                    'DATE_FORMAT(PR.created_at, "%b %d, %Y") as created_at',
                    'D.name AS charging_name',
                    'POS.name as position_name'
                ];
            }

            $orWhereCondition = array(
                'PR.prs_no'                                       => ':filter_val',
                'DATE_FORMAT(PR.date_requested, "%M %d, %Y")'     => ':filter_val',
                // 'P.project_code'                                  => ':filter_val',
                // 'P.name'                                          => ':filter_val',
                'D.charging'                                      => ':filter_val',
                'D.name'                                          => ':filter_val',
                'PI.fname'                                        => ':filter_val',
                'PI.lname'                                        => ':filter_val',
                'RT.name'                                         => ':filter_val',
                'EI.employee_no'                                  => ':filter_val'
            );

            $leftJoins = [
                // 'projects P'    => 'P.id = PR.project_id',
                'departments D' => 'PR.department_id = D.id',
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
                              ->andWhereNotNull(['PR.department_id'])
                              ->logicEx('AND')
                              ->orWhereLike($orWhereCondition);

            $initQuery = ($id)              ? $initQuery->andWhere(['PR.id' => ':id'])                                                  : $initQuery;
            $initQuery = ($status)          ? $initQuery->andWhere(['PR.status' => ':status'])                                          : $initQuery;
            $initQuery = ($date_requested)  ?   $initQuery->andWhere(['DATE_FORMAT(PR.created_at, "%Y-%m-%d")' => ':date_requested'])   : $initQuery;
            $initQuery = ($prs_no)          ?   $initQuery->andWhere(['PR.prs_no' => ':prs_no'])                                        : $initQuery;
            $initQuery = ($charging_id)     ? $initQuery->andWhere(['PR.department_id' => ':charging_id']) : $initQuery;
            $initQuery = ($request_type_id) ? $initQuery->andWhere(['PR.request_type_id' => ':request_type_id'])    : $initQuery;
            $initQuery = ($requestor_id)    ? $initQuery->andWhere(['PR.created_by' => ':requestor_id'])            : $initQuery;
            

            $initQuery = $initQuery->orderBy('PR.prs_no', 'DESC');

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
                'CONCAT(PI.fname, " ", LEFT(PI.mname,1), ". ", PI.lname) as full_name',
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
                'ACC.name as acc_name',
                'ACC.account_id as acc_code',
            ];

            $joins = [
                'material_specifications MS'  => 'MS.id = PPD.material_specification_id',
                'materials M'                 => 'M.id = MS.material_id',
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
                'ACC.name as acc_name',
                'ACC.account_id as acc_code',

            ];

            $joins = [
                'equipment_types ET'        => 'ET.id = PRE.equipment_type_id',
                'equipment_categories EC'   => 'EC.id = ET.equipment_category_id',
                'accounts ACC'              => 'PRE.account_id = ACC.id',

            ];

            $initQuery = $this->select($fields)
                              ->from('pr_equipments PRE')
                              ->leftJoin($joins)
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
                'PRT.requested_units as quantity',
                'PRT.unit_of_measurement as unit_measurement',
                'IF(PRT.category = 1, PRT.category, null) as category',
                'PRT.status',
                'PRT.remarks',
                'PRT.process_status',
                'ACC.name as acc_name',
                'ACC.account_id as acc_code',
            ];

            $leftJoins = [
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
                'ACC.name as acc_name',
                'ACC.account_id as acc_code',
            ];

            $joins = [
                'equipment_types ET'        => 'ET.id = PRH.equipment_type_id',
                'equipment_categories EC'   => 'EC.id = ET.equipment_category_id',
                'accounts ACC'              => 'PRH.account_id = ACC.id',
            ];

            $initQuery = $this->select($fields)
                              ->from('pr_heavy_equipments PRH')
                              ->leftJoin($joins)
                              ->where(['PRH.is_active' => ':is_active']);

            $initQuery = ($id)   ? $initQuery->andWhere(['PRH.id' => ':id'])                         : $initQuery;
            $initQuery = ($prId) ? $initQuery->andWhere(['PRH.pr_id' => ':purchase_requisition_id']) : $initQuery;

            $initQuery = ($item_category)       ? $initQuery->andWhere(['PRH.category' => ':item_category'])                : $initQuery;
            $initQuery = ($equipment_type_id)   ? $initQuery->andWhere(['PRH.equipment_type_id' => ':equipment_type_id'])   : $initQuery;

            $initQuery = $initQuery->orderBy('ET.name', 'ASC');

            return $initQuery;
        }

        /**
         * `selectPurchaseRequisitionDescriptions` Query string that will fetch prs description from table `purchase_requisition_descriptions`.
         * @param  boolean $id
         * @param  boolean $prId
         * @return string
         */
        public function selectPurchaseRequisitionDescriptions($id = false, $prId = false, $count = false, $item_category = false, $ms_id = false)
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
                    // 'PRD.expense_type',
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
                // 'work_items WI'              => 'WI.id = PRD.work_item_id',
                // 'work_item_categories WIC'   => 'WIC.id = WI.work_item_category_id',
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

            $initQuery = $initQuery->orderBy('MS.specs', 'ASC');

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
                'CONCAT(PI.fname, " ", LEFT(PI.mname,1), ". ", PI.lname) as full_name',
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

        public function selectPrLabors($pr_id = false, $item_category = false, $position_id = false)
        {
            $fields = array(
                'PRL.id',
                'PRL.pr_id',
                'PRL.category',
                '(SELECT name FROM positions WHERE id = PRL.position_id) as specs',
                '(SELECT IF(position_type IS NULL, "", IF(position_type = 1, "SKILLED LABORER", "")) FROM positions WHERE id = PRL.position_id) as description',
                'PRL.no_of_labor as quantity',
                '"" as unit_measurement',
                'PRL.remarks',
                'PRL.signatories',
                'PRL.status',
                'ACC.name as acc_name',
                'ACC.account_id as acc_code',
            );

            $leftJoins = array(
                'accounts ACC' => 'PRL.account_id = ACC.id',
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
                'ACC.name as acc_name',
                'ACC.account_id as acc_code',
            );

            $leftJoins = array(
                'material_specification_brands MSB'     =>      'MSB.id = PRM.material_specification_brand_id',
                'material_specifications MS'            =>      'MS.id = MSB.material_specification_id',
                'materials M'                           =>      'M.id = MS.material_id',
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

        public function selectPrMedicalSignatories($prmId = '')
        {
            $fields = [
                'PRMS.id',
                'PRMS.user_id',
                'PRMS.status',
                'IF(PRMS.comment IS NULL, "", PRMS.comment) as comment',
                'CONCAT(PI.fname, " ", LEFT(PI.mname,1), ". ", PI.lname) as full_name',
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
    }