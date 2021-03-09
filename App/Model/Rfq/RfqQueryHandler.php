<?php 
    namespace App\Model\Rfq;

    require_once('../../AbstractClass/QueryHandler.php');

    use App\AbstractClass\QueryHandler;

    class RfqQueryHandler extends QueryHandler { 

        /**
         * `selectSuppliers` Query string that will select from table `suppliers`.
         * @param  boolean $id
         * @param  boolean $userId
         * @return string
         */
        public function selectSuppliers($id = false, $userId = false)
        {
            $fields = array(
                'S.id',
                'S.name',
                'S.address',
                'S.email_add',
                'S.contact_no',
                'S.tin_no',
                'S.status'
            );
            
            $initQuery = $this->select($fields)
                              ->from('suppliers S')
                              ->where(array('S.is_active' => ':is_active'));

            $initQuery = ($id) ? $initQuery->andWhere(array('S.id' => ':id')) : $initQuery;
            
            return $initQuery;
        }

        /**
         * `selectUsers` Query string that will select from table `users`.
         * @param  boolean $id
         * @param  boolean $userId
         * @return string
         */
        public function selectUsers($id = false, $department = false)
        {
            $fields = array(
                'U.id',
                'U.personal_information_id',
                'CONCAT(PI.fname, " ", PI.lname) as full_name',
                'PI.signature',
                'P.id as position_id',
                'P.name as position_name',
                'DATE_FORMAT(NOW(), "%b %d, %Y") as cur_date',
                'DATE_FORMAT(NOW(), "%h:%i %p") as cur_time'
            );
           
            $joins = array(
                'personal_informations PI'   => 'U.personal_information_id = PI.id',
                'employment_informations EI' => 'PI.id = EI.personal_information_id',
                'positions P'                      => 'EI.position_id = P.id',
            );

            $initQuery = $this->select($fields)
                              ->from('users U')
                              ->join($joins)
                              ->where(array('U.is_active' => ':is_active'));

            $initQuery = ($id)         ? $initQuery->andWhere(array('U.id' => ':id'))                       : $initQuery;
            $initQuery = ($department) ? $initQuery->andWhere(array('P.department_id' => ':department_id')) : $initQuery;
            
            return $initQuery;
        }

        /**
         * `selectRequestQuotations` Query string that will select from table `request_quotations`.
         * @param  boolean $id
         * @param  boolean $rfqMaterialId
         * @return string
         */
        public function selectRequestQuotations($id = false, $rfqMaterialId = false, $supplierId = false, $status = false, $rfqNo = false, $hasQuote = false)
        {            
            $fields = array(
                'RQ.id',
                'RQ.rfq_material_id',
                'RQ.rfq_no',
                'RQ.quoted_quantity as quantity',
                'RQ.has_quote',
                // 'RQ.quoted_quantity as quantity',

                'RQ.c_unit_price',
                'RQ.c_delivery_charge',
                'RQ.c_delivery_price',
                'RQ.p_unit_price',
                'RQ.p_delivery_charge',
                'RQ.p_delivery_price',

                'RQ.item_status',
                // 'IF(RQ.item_status = "1", "1", "0") as available',
                // 'IF(RQ.item_status = "0", "1", "0") as order_basis',

                'RQ.recommended_specification',
                'IF(RQ.rfq_type = "0", IF(RQ.c_unit_price = "0.00", RQ.c_delivery_price, RQ.c_unit_price), IF(RQ.p_unit_price = "0.00", RQ.p_delivery_price, RQ.p_unit_price)) as temp_unit_price',
                'IF(RQ.rfq_type = "0", RQ.c_unit_price, RQ.p_unit_price) as unit_price',
                'IF(RQ.rfq_type = "0", RQ.c_delivery_charge, RQ.p_delivery_charge) as delivery_charge',
                'IF(RQ.rfq_type = "0", RQ.c_delivery_price, RQ.p_delivery_price) as delivery_price',
                'RQ.rfq_type',
                'DATE_FORMAT(RQ.delivery_date, "%b %d- %Y") as delivery_date',
                'DATE_FORMAT(RQ.validity_of_offer, "%b %d- %Y") as validity_of_offer',
                'S.name as supplier_name',
                'IF(RM.material_specification_id IS NOT NULL, RM.material_specification_id, IF(RM.equipment_type_id IS NOT NULL, RM.equipment_type_id, IF(RM.power_tool_id IS NOT NULL, RM.power_tool_id, IF(RM.hand_tool_id IS NOT NULL, RM.hand_tool_id, "")))) as item_spec_id',
                'IF(RM.material_specification_id IS NOT NULL, MS.specs, IF(RM.equipment_type_id IS NOT NULL, ET.name, IF(RM.power_tool_id IS NOT NULL, PT.specification, IF(RM.hand_tool_id IS NOT NULL, HT.specification, "")))) as specs',
                'IF(RM.material_specification_id IS NOT NULL, MS.code, IF(RM.equipment_type_id IS NOT NULL, ET.cost_code, IF(RM.power_tool_id IS NOT NULL, PT.code, IF(RM.hand_tool_id IS NOT NULL, HT.code, "")))) as code',
                'IF(RM.material_specification_id IS NOT NULL, M.name, IF(RM.equipment_type_id IS NOT NULL, EC.name, IF(RM.power_tool_id IS NOT NULL, PTC.name, IF(RM.hand_tool_id IS NOT NULL, HTC.name, "")))) as material_name',
                // 'MS.specs',
                // 'MS.code',
                // '(SELECT MSBS.code FROM material_specification_brands MSB JOIN msb_suppliers MSBS ON MSB.id = MSBS.material_specification_brand_id WHERE MSB.material_specification_id = MS.id AND RM.unit = MSBS.unit LIMIT 1) as msbs_code',
                // // 'CONCAT(MS.code, MSBS.code) as material_code',
                // 'MS.id as material_specification_id',
                // 'MS.id as item_spec_id',
                // 'MS.material_id',
                // 'M.name as material_name',
                'RM.quantity as quoted_quantity',
                'RM.unit',
                'RM.status',
                'RM.request_type_id',
                'DATE_FORMAT(RM.date_assigned, "%b %d- %Y %h:%i %p") as date_assigned',
                'CONCAT(PI.lname, ", ", PI.fname) as full_name',
            );

            $joins = array(
                'request_quotation_materials RM'    => 'RQ.rfq_material_id = RM.id',
                // 'material_specifications MS'        => 'MS.id = RM.material_specification_id',
                // 'materials M'                       => 'M.id = MS.material_id',
            );

            $leftJoins = array(
                'suppliers S'              => 'S.id = RQ.supplier_id',
                'users U'                  => 'RM.assigned_to = U.id',
                'personal_informations PI' => 'U.personal_information_id = PI.id',
                'currencies C'             => 'C.id = RQ.currency_id'
            );
      
            $leftJoins['material_specifications MS'] = 'MS.id = RM.material_specification_id';
            $leftJoins['materials M']                = 'M.id = MS.material_id';
            $leftJoins['equipment_types ET']         = 'ET.id = RM.equipment_type_id';
            $leftJoins['equipment_categories EC']    = 'EC.id = ET.equipment_category_id';
            $leftJoins['power_tools PT']             = 'PT.id = RM.power_tool_id';
            $leftJoins['power_tool_categories PTC']  = 'PTC.id = PT.category';
            $leftJoins['hand_tools HT']              = 'HT.id = RM.hand_tool_id';
            $leftJoins['hand_tool_categories HTC']   = 'HTC.id = HT.category';

            $initQuery = $this->select($fields)
                              ->from('request_quotations RQ')
                              ->join($joins)
                              ->leftJoin($leftJoins)
                              ->where(array('RQ.is_active' => ':is_active', 'RM.is_active' => ':is_active'));

            $initQuery = ($id)            ? $initQuery->andWhere(array('RQ.id' => ':id'))                           : $initQuery;
            $initQuery = ($rfqMaterialId) ? $initQuery->andWhere(array('RQ.rfq_material_id' => ':rfq_material_id')) : $initQuery;
            $initQuery = ($supplierId)    ? $initQuery->andWhere(array('RQ.supplier_id' => ':supplier_id'))         : $initQuery;
            $initQuery = ($status)        ? $initQuery->andWhere(array('RQ.status' => ':status'))                   : $initQuery;
            $initQuery = ($rfqNo)         ? $initQuery->andWhere(array('RQ.rfq_no' => ':rfq_no'))                   : $initQuery;
            $initQuery = ($hasQuote)      ? $initQuery->andWhere(array('RQ.has_quote' => ':has_quote'))             : $initQuery;

            return $initQuery;
        }

        /**
         * `selectPurchaseRequisitionDescriptions` Query string that will select from table `purchase_requisition_descriptions`.
         * @param  boolean $id
         * @param  boolean $userId
         * @return string
         */
        public function selectPurchaseRequisitionDescriptions($id = false, $userId = false)
        {
            $fields = array(
                'PRD.id',
                'PRD.purchase_requisition_id',
                'PR.request_type_id',
                'PR.prs_no',
                'PRD.item_spec_id',
                'MS.code',
                'MS.specs',
                // 'M.name',
                'PRD.quantity',
                'PRD.unit_measurement',
                'IF(PR.project_id IS NULL, D.charging, IF(P.project_code IS NULL, PCR.temporary_project_code, P.project_code)) AS charging',
                'WI.item_no',
                'PRD.remarks',
                // 'PRD.signatories'
                // 'P.project_code', 
                // 'PHO.project_code', 
                // 'D.charging',
            );

            $leftJoins = array(
                'purchase_requisitions PR'   => 'PR.id = PRD.purchase_requisition_id',
                'material_specifications MS' => 'PRD.item_spec_id = MS.id',
                'materials M'                => 'MS.material_id = M.id',
                'departments D'              => 'D.id = PR.department_id',
                'projects P'                 => 'PR.project_id = P.id',
                'projects PHO'               => 'PHO.project_id = PR.ho_project_id',
                'work_items WI'              => 'WI.id = PRD.work_item_id'
            );

            $whereNotInCondition = [
                '25', // 16026
                '26', // 18029
                '27', // 18080
                '24', // 18SG-013
                '9',  // 19001
                '10', // 19002
                '11', // 19003
                '12', // 19004
                '28', // 19005
                '29', // 19006
                '30', // 19007
                '31', // 19008
                '32', // 19009
                '33', // 19010
                '34', // 19011
                '35', // 19012
                '38', // 19013
                '39', // 19015
                '1',  // 19SCDC001
                '7',  // Y03-001
                '4',  // TC-01126
                '2',  // TC-01147
                '3',  // TC01089
                '36', // 19SG-001
                '37', // 19SG-002
                '23', // 19SG-003
                '69', // 19SG-004
            ];
            
            $initQuery = $this->select($fields)
                              ->from('purchase_requisition_descriptions PRD')
                              ->leftJoin($leftJoins)
                              ->where(array('PRD.status' => ':status', 'PRD.is_active' => 1))
                              ->andWhereNotIn('(PR.project_id', $whereNotInCondition)
                              ->logicEx(' OR PR.project_id IS NULL)');

            $initQuery = ($id) ? $initQuery->andWhere(array('PRD.id' => ':id')) : $initQuery;

            $initQuery = $initQuery->orderBy('MS.specs', 'ASC');
            
            return $initQuery;
        }

        /**
         * `selectPrHeavyEquipments` Query string that will select from table `pr_heavy_equipments`.
         * @param  boolean $id
         * @return string
         */
        public function selectPrHeavyEquipments($id = false)
        {
            $fields = [
                'PHE.id',
                'PHE.pr_id as purchase_requisition_id',
                'PR.request_type_id',
                'PR.prs_no',
                'PHE.equipment_type_id as item_spec_id',
                'ET.cost_code as code',
                'ET.name as specs',
                'EC.name',
                'PHE.total_no_of_equipment as quantity',
                '"unit" as unit_measurement',
                'P.project_code', 
                'D.charging',
                '"-" as item_no',
                'PHE.remarks',
                'PHE.signatories',
            ];

            $leftJoins = [
                'purchase_requisitions PR' => 'PR.id = PHE.pr_id',
                'equipment_types ET'       => 'PHE.equipment_type_id = ET.id',
                'equipment_categories EC'  => 'ET.equipment_category_id = EC.id',
                'departments D'            => 'D.id = PR.department_id',
                'projects P'               => 'PR.project_id = P.id',
            ];

            $whereNotInCondition = [
                '25', // 16026
                '26', // 18029
                '27', // 18080
                '24', // 18SG-013
                '9',  // 19001
                '10', // 19002
                '11', // 19003
                '12', // 19004
                '28', // 19005
                '29', // 19006
                '30', // 19007
                '31', // 19008
                '32', // 19009
                '33', // 19010
                '34', // 19011
                '35', // 19012
                '38', // 19013
                '39', // 19015
                '1',  // 19SCDC001
                '7',  // Y03-001
                '4',  // TC-01126
                '2',  // TC-01147
                '3',  // TC01089
                '36', // 19SG-001
                '37', // 19SG-002
                '23', // 19SG-003
                '69', // 19SG-004
            ];

            $initQuery = $this->select($fields)
                              ->from('pr_heavy_equipments PHE')
                              ->leftJoin($leftJoins)
                              ->where(['PHE.status' => ':status'])
                              ->andWhereNotIn('P.id', $whereNotInCondition);

            $initQuery = ($id) ? $initQuery->andWhere(['PHE.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectPrEquipments` Query string that will select from table `pr_equipments`.
         * @param  boolean $id
         * @return string
         */
        public function selectPrEquipments($id = false)
        {
            $fields = [
                'PE.id',
                'PE.pr_id as purchase_requisition_id',
                'PR.request_type_id',
                'PR.prs_no',
                'PE.equipment_type_id as item_spec_id',
                'ET.cost_code as code',
                'ET.name as specs',
                'EC.name',
                'PE.total_no_of_equipment as quantity',
                '"unit/s" as unit_measurement',
                'P.project_code', 
                'D.charging',
                '"-" as item_no',
                'PE.remarks',
                'PE.signatories',
            ];

            $leftJoins = [
                'purchase_requisitions PR' => 'PR.id = PE.pr_id',
                'equipment_types ET'       => 'PE.equipment_type_id = ET.id',
                'equipment_categories EC'  => 'ET.equipment_category_id = EC.id',
                'departments D'            => 'D.id = PR.department_id',
                'projects P'               => 'PR.project_id = P.id',
            ];

            $initQuery = $this->select($fields)
                              ->from('pr_equipments PE')
                              ->leftJoin($leftJoins)
                              ->where(['PE.status' => ':status']);
                              // ->andWhereNull(['PR.project_id']);

            $initQuery = ($id) ? $initQuery->andWhere(['PE.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectPrTools` Query string that will select from table `pr_tools`.
         * @param  boolean $id
         * @return string
         */
        public function selectPrTools($id = false)
        {
            $fields = [
                'PRT.id',
                'PRT.pr_id as purchase_requisition_id',
                'PR.request_type_id',
                'PR.prs_no',
                'IF(PRT.power_tool_id IS NULL, PRT.hand_tool_id, PRT.power_tool_id) as item_spec_id',
                'IF(PRT.power_tool_id IS NULL, HT.code, PT.code) as code',
                'IF(PRT.power_tool_id IS NULL, HT.specification, PT.specification) as specs',
                'IF(PRT.power_tool_id IS NULL, HTC.name, PTC.name) as name',
                'IF(PRT.power_tool_id IS NULL, "HT", "PT") as tool_type',
                'PRT.requested_units as quantity',
                'PRT.unit_of_measurement as unit_measurement',
                'P.project_code', 
                'D.charging',
                '"-" as item_no',
                'PRT.remarks',
            ];

            $leftJoins = [
                'purchase_requisitions PR'  => 'PR.id = PRT.pr_id',
                'power_tools PT'            => 'PRT.power_tool_id = PT.id',
                'power_tool_categories PTC' => 'PT.category = PTC.id',
                'hand_tools HT'             => 'HT.id = PRT.hand_tool_id',
                'hand_tool_categories HTC'  => 'HTC.id = HT.category',
                'departments D'             => 'D.id = PR.department_id',
                'projects P'                => 'PR.project_id = P.id',
            ];

            $initQuery = $this->select($fields)
                              ->from('pr_tools PRT')
                              ->leftJoin($leftJoins)
                              ->where(['PRT.status' => ':status']);
                              // ->andWhereNull(['PR.project_id']);

            $initQuery = ($id) ? $initQuery->andWhere(['PRT.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectPrMedicalMaterials` Query string that will select from table `pr_medical_materials`.
         * @param  boolean $id
         * @return string
         */
        public function selectPrMedicalMaterials($id = false)
        {
            $fields = [
                'PMM.id',
                'PMM.pr_id as purchase_requisition_id',
                'PR.request_type_id',
                'PR.prs_no',
                'MSB.material_specification_id as item_spec_id',
                'MS.code',
                'MS.specs',
                'M.name',
                'PMM.quantity',
                'PMM.unit_of_measurement as unit_measurement',
                'P.project_code', 
                'D.charging',
                '"-" as item_no',
                'PMM.remarks',
            ];

            $leftJoins = [
                'purchase_requisitions PR'          => 'PR.id = PMM.pr_id',
                'material_specification_brands MSB' => 'PMM.material_specification_brand_id = MSB.id',
                'material_specifications MS'        => 'MSB.material_specification_id = MS.id',
                'materials M'                       => 'MS.material_id = M.id',
                'departments D'                     => 'D.id = PR.department_id',
                'projects P'                        => 'PR.project_id = P.id',
            ];

            $initQuery = $this->select($fields)
                              ->from('pr_medical_materials PMM')
                              ->leftJoin($leftJoins)
                              ->where(['PMM.status' => ':status']);
                              // ->andWhereNull(['PR.project_id']);

            $initQuery = ($id) ? $initQuery->andWhere(['PMM.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectPrsPpeDescriptions` Query string that will select from table `prs_ppe_descriptions`.
         * @param  boolean $id
         * @return string
         */
        public function selectPrsPpeDescriptions($id = false)
        {
            $fields = [
                'PPD.id',
                'PPD.purchase_requisition_id',
                'PR.request_type_id',
                'PR.prs_no',
                'PPD.material_specification_id as item_spec_id',
                'MS.code',
                'MS.specs',
                'M.name',
                'PPD.quantity',
                'PPD.unit_measurement',
                'D.charging',
                'P.project_code', 
                '"-" as item_no',
                'PPD.remarks'
            ];

            $leftJoins = [
                'purchase_requisitions PR'   => 'PR.id = PPD.purchase_requisition_id',
                'material_specifications MS' => 'PPD.material_specification_id = MS.id',
                'materials M'                => 'MS.material_id = M.id',
                'departments D'              => 'D.id = PR.department_id',
                'projects P'                 => 'PR.project_id = P.id',
            ];

            $whereNotInCondition = [
                '25', // 16026
                '26', // 18029
                '27', // 18080
                '24', // 18SG-013
                '9',  // 19001
                '10', // 19002
                '11', // 19003
                '12', // 19004
                '28', // 19005
                '29', // 19006
                '30', // 19007
                '31', // 19008
                '32', // 19009
                '33', // 19010
                '34', // 19011
                '35', // 19012
                '38', // 19013
                '39', // 19015
                '1',  // 19SCDC001
                '7',  // Y03-001
                '4',  // TC-01126
                '2',  // TC-01147
                '3',  // TC01089
                '36', // 19SG-001
                '37', // 19SG-002
                '23', // 19SG-003
                '69', // 19SG-004
            ];

            $initQuery = $this->select($fields)
                              ->from('prs_ppe_descriptions PPD')
                              ->leftJoin($leftJoins)
                              ->where(['PPD.status' => ':status'])
                              ->andWhereNotIn('P.id', $whereNotInCondition);
                              // ->andWhereNull(['PR.project_id']);

            $initQuery = ($id) ? $initQuery->andWhere(['PPD.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectPrdManpowerServices` Query string that will select from table `prs_ppe_descriptions`.
         * @param  boolean $id
         * @return string
         */
        public function selectPrdManpowerServices($id = false)
        {
            $fields = [
                'PMS.id',
                'PMS.purchase_requisition_id',
                'PR.request_type_id',
                'PR.prs_no',
                'PMS.material_specification_id as item_spec_id',
                'MS.code',
                'MS.specs',
                'M.name',
                'PMS.quantity',
                'PMS.unit_measurement',
                'D.charging',
                'P.project_code', 
                '"-" as item_no',
                'PMS.remarks'
            ];

            $leftJoins = [
                'purchase_requisitions PR'   => 'PR.id = PMS.purchase_requisition_id',
                'material_specifications MS' => 'PMS.material_specification_id = MS.id',
                'materials M'                => 'MS.material_id = M.id',
                'departments D'              => 'D.id = PR.department_id',
                'projects P'                 => 'PR.project_id = P.id',
            ];

            $whereNotInCondition = [
                '25', // 16026
                '26', // 18029
                '27', // 18080
                '24', // 18SG-013
                '9',  // 19001
                '10', // 19002
                '11', // 19003
                '12', // 19004
                '28', // 19005
                '29', // 19006
                '30', // 19007
                '31', // 19008
                '32', // 19009
                '33', // 19010
                '34', // 19011
                '35', // 19012
                '38', // 19013
                '39', // 19015
                '1',  // 19SCDC001
                '7',  // Y03-001
                '4',  // TC-01126
                '2',  // TC-01147
                '3',  // TC01089
                '36', // 19SG-001
                '37', // 19SG-002
                '23', // 19SG-003
                '69', // 19SG-004
            ];

            $initQuery = $this->select($fields)
                              ->from('prd_manpower_services PMS')
                              ->leftJoin($leftJoins)
                              ->where(array('PMS.status' => ':status', 'PMS.is_active' => 1))
                              ->andWhereNotIn('(P.id', $whereNotInCondition)
                              ->logicEx(' OR PR.project_id IS NULL)');

            $initQuery = ($id) ? $initQuery->andWhere(['PMS.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectRfqMaterials` Query string that will select from table `request_quotation_materials`.
         * @param  boolean $id
         * @param  boolean $materialId
         * @param  boolean $unit
         * @return string
         */
        public function selectRfqMaterials($id = false, $materialId = false, $unit = false, $requestTypeId = '', $toolType = false)
        {
            $fields = array(
                'RM.id',
                'RM.material_specification_id',
                'RM.equipment_type_id',
                'RM.power_tool_id',
                'RM.hand_tool_id',
                'RM.quantity',
                'IF(RM.material_specification_id IS NOT NULL, MS.specs, IF(RM.equipment_type_id IS NOT NULL, ET.name, IF(RM.power_tool_id IS NOT NULL, PT.specification, IF(RM.hand_tool_id IS NOT NULL, HT.specification, "")))) as specs',
                'IF(RM.material_specification_id IS NOT NULL, MS.code, IF(RM.equipment_type_id IS NOT NULL, ET.cost_code, IF(RM.power_tool_id IS NOT NULL, PT.code, IF(RM.hand_tool_id IS NOT NULL, HT.code, "")))) as code',
                'IF(RM.material_specification_id IS NOT NULL, M.name, IF(RM.equipment_type_id IS NOT NULL, EC.name, IF(RM.power_tool_id IS NOT NULL, PTC.name, IF(RM.hand_tool_id IS NOT NULL, HTC.name, "")))) as name',
                'RM.unit',
                // 'MS.material_id',
                // 'MS.specs',
                // 'MS.code',
                // 'M.name',
                // '(SELECT MSBS.code FROM material_specification_brands MSB JOIN msb_suppliers MSBS ON MSB.id = MSBS.material_specification_brand_id WHERE MSB.material_specification_id = MS.id AND RM.unit = MSBS.unit LIMIT 1) as msbs_code'
            );

            $leftJoins                               = [];
            $leftJoins['material_specifications MS'] = 'MS.id = RM.material_specification_id';
            $leftJoins['materials M']                = 'M.id = MS.material_id';
            $leftJoins['equipment_types ET']         = 'ET.id = RM.equipment_type_id';
            $leftJoins['equipment_categories EC']    = 'EC.id = ET.equipment_category_id';
            $leftJoins['power_tools PT']             = 'PT.id = RM.power_tool_id';
            $leftJoins['power_tool_categories PTC']  = 'PTC.id = PT.category';
            $leftJoins['hand_tools HT']              = 'HT.id = RM.hand_tool_id';
            $leftJoins['hand_tool_categories HTC']   = 'HTC.id = HT.category';

            if (($requestTypeId == '1' || $requestTypeId == 1) || ($requestTypeId == '7' || $requestTypeId == 7) || ($requestTypeId == '8' || $requestTypeId == 8)) {

                $fields[] = 'MS.specs';
                $fields[] = 'MS.code';
                $fields[] = 'M.name';
            } else if (($requestTypeId == '2' || $requestTypeId == 2) || ($requestTypeId == '5' || $requestTypeId == 5)) {

                $fields[] = 'ET.name as specs';
                $fields[] = 'ET.code';
                $fields[] = 'EC.name';
            } else if ($requestTypeId == '4' || $requestTypeId == 4) {
                if ($toolType == 'PT') {

                    $fields[] = 'PT.specification as specs';
                    $fields[] = 'PT.code';
                    $fields[] = 'PTC.name';
                } else {

                    $fields[] = 'HT.specification as specs';
                    $fields[] = 'HT.code';
                    $fields[] = 'HTC.name';
                }
            } else {
                // nothing
            }

            $initQuery = $this->select($fields)
                              ->from('request_quotation_materials RM')
                              ->leftJoin($leftJoins)
                              ->where(array('RM.status' => ':status', 'RM.is_active' => ':is_active'));

            $initQuery = ($id)   ? $initQuery->andWhere(array('RM.id' => ':id'))     : $initQuery;
            $initQuery = ($unit) ? $initQuery->andWhere(array('RM.unit' => ':unit')) : $initQuery;

            if ($materialId) {
                if (($requestTypeId == '1' || $requestTypeId == 1) || ($requestTypeId == '6' || $requestTypeId == 6) || ($requestTypeId == '7' || $requestTypeId == 7) || ($requestTypeId == '8' || $requestTypeId == 8)) {
                    $initQuery = ($materialId) ? $initQuery->andWhere(array('RM.material_specification_id' => ':material_specification_id')) : $initQuery;
                } else if (($requestTypeId == '2' || $requestTypeId == 2) || ($requestTypeId == '5' || $requestTypeId == 5)) {
                    $initQuery = ($materialId) ? $initQuery->andWhere(array('RM.equipment_type_id' => ':equipment_type_id')) : $initQuery;
                } else if ($requestTypeId == '4' || $requestTypeId == 4) {
                    if ($toolType == 'PT') {
                        $initQuery = ($materialId) ? $initQuery->andWhere(array('RM.power_tool_id' => ':power_tool_id')) : $initQuery;
                    } else {
                        $initQuery = ($materialId) ? $initQuery->andWhere(array('RM.hand_tool_id' => ':hand_tool_id')) : $initQuery;
                    }
                } else {
                    // nothing
                }
            }

            return $initQuery;
        }

        /**
         * `selectRequestQuotationDescriptions` Query string that will select from table `request_quotation_descriptions`.
         * @param  boolean $i
         * @param  boolean $rfqmId
         * @return string 
         */
        public function selectRequestQuotationDescriptions($id = false, $rfqmId = false)
        {
            $fields = array(
                'IF(RQD.purchase_requisition_description_id IS NOT NULL, PRD.id, IF(RQD.pr_heavy_equipment_id IS NOT NULL, PRH.id, IF(RQD.pr_tool_id IS NOT NULL, PRT.id, IF(RQD.pr_equipment_id IS NOT NULL, PRE.id, IF(RQD.pr_medical_material_id IS NOT NULL, PMM.id, IF(RQD.prs_ppe_description_id IS NOT NULL, PPD.id, IF(RQD.prd_manpower_service_id IS NOT NULL, PMS.id,""))))))) as id',
                'IF(RQD.purchase_requisition_description_id IS NOT NULL, PRD.unit_measurement, IF(RQD.pr_heavy_equipment_id IS NOT NULL, "unit", IF(RQD.pr_tool_id IS NOT NULL, PRT.unit_of_measurement, IF(RQD.pr_equipment_id IS NOT NULL, "unit", IF(RQD.pr_medical_material_id IS NOT NULL, PMM.unit_of_measurement, IF(RQD.prs_ppe_description_id IS NOT NULL, PPD.unit_measurement, IF(RQD.prd_manpower_service_id IS NOT NULL, PMS.unit_measurement, ""))))))) as unit_measurement',
                'IF(RQD.purchase_requisition_description_id IS NOT NULL, PRD.quantity, IF(RQD.pr_heavy_equipment_id IS NOT NULL, PRH.total_no_of_equipment, IF(RQD.pr_tool_id IS NOT NULL, PRT.requested_units, IF(RQD.pr_equipment_id IS NOT NULL, PRE.total_no_of_equipment, IF(RQD.pr_medical_material_id IS NOT NULL, PMM.quantity, IF(RQD.prs_ppe_description_id IS NOT NULL, PPD.quantity, IF(RQD.prd_manpower_service_id IS NOT NULL, PMS.quantity, ""))))))) as quantity',
                'IF(RQD.purchase_requisition_description_id IS NOT NULL, PRD.remarks, IF(RQD.pr_heavy_equipment_id IS NOT NULL, PRH.remarks, IF(RQD.pr_tool_id IS NOT NULL, PRT.remarks, IF(RQD.pr_equipment_id IS NOT NULL, PRE.remarks, IF(RQD.pr_medical_material_id IS NOT NULL, PMM.remarks, IF(RQD.prs_ppe_description_id IS NOT NULL, PPD.remarks,  IF(RQD.prd_manpower_service_id IS NOT NULL, PMS.remarks, ""))))))) as remarks',

                'IF(RQD.purchase_requisition_description_id IS NOT NULL, PRMA.id, IF(RQD.pr_heavy_equipment_id IS NOT NULL, PRHE.id, IF(RQD.pr_tool_id IS NOT NULL, PRTO.id, IF(RQD.pr_equipment_id IS NOT NULL, PRLI.id, IF(RQD.pr_medical_material_id IS NOT NULL, PRME.id, IF(RQD.prs_ppe_description_id IS NOT NULL, PRPP.id, IF(RQD.prd_manpower_service_id IS NOT NULL, PRMS.id, ""))))))) as prs_id',
                'IF(RQD.purchase_requisition_description_id IS NOT NULL, PRMA.prs_no, IF(RQD.pr_heavy_equipment_id IS NOT NULL, PRHE.prs_no, IF(RQD.pr_tool_id IS NOT NULL, PRTO.prs_no, IF(RQD.pr_equipment_id IS NOT NULL, PRLI.prs_no, IF(RQD.pr_medical_material_id IS NOT NULL, PRME.prs_no, IF(RQD.prs_ppe_description_id IS NOT NULL, PRPP.prs_no, IF(RQD.prd_manpower_service_id IS NOT NULL, PRMS.prs_no, ""))))))) as prs_no',
                'IF(RQD.purchase_requisition_description_id IS NOT NULL, PMA.project_code, IF(RQD.pr_heavy_equipment_id IS NOT NULL, PHE.project_code, "")) as project_code',
                'IF(RQD.pr_tool_id IS NOT NULL, DTO.charging, IF(RQD.pr_equipment_id IS NOT NULL, DLI.charging, IF(RQD.pr_medical_material_id IS NOT NULL, DME.charging, IF(RQD.prs_ppe_description_id IS NOT NULL, DPP.charging, "")))) as charging',
                // 'PRD.id',
                // 'PRD.unit_measurement',
                // 'PRD.quantity',

                // 'PR.id as prs_id',
                // 'PR.prs_no',
                // 'P.project_code',
                // 'D.charging',
                'RQD.converted_quantity'
            );

            $leftJoins = array(
                'purchase_requisition_descriptions PRD' => 'PRD.id = RQD.purchase_requisition_description_id',
                'pr_heavy_equipments PRH'               => 'RQD.pr_heavy_equipment_id = PRH.id',
                'pr_tools PRT'                          => 'PRT.id = RQD.pr_tool_id',
                'pr_equipments PRE'                     => 'RQD.pr_equipment_id = PRE.id',
                'pr_medical_materials PMM'              => 'PMM.id = RQD.pr_medical_material_id',
                'prs_ppe_descriptions PPD'              => 'RQD.prs_ppe_description_id = PPD.id',
                'prd_manpower_services PMS'             => 'PMS.id = RQD.prd_manpower_service_id',

                // materials
                'purchase_requisitions PRMA'            => 'PRMA.id = PRD.purchase_requisition_id',
                'projects PMA'                          => 'PMA.id = PRMA.project_id',
                'departments DMA'                       => 'PRMA.department_id = DMA.id',

                // manpower_services
                'purchase_requisitions PRMS'            => 'PRMS.id = PMS.purchase_requisition_id',
                'projects PJMS'                         => 'PJMS.id = PRMS.project_id',
                'departments DMS'                       => 'PRMS.department_id = DMS.id',

                // heavy_equipments
                'purchase_requisitions PRHE'            => 'PRHE.id = PRH.pr_id',
                'projects PHE'                          => 'PHE.id = PRHE.project_id',
                'departments DHE'                       => 'PRHE.department_id = DHE.id',

                // tools
                'purchase_requisitions PRTO'            => 'PRTO.id = PRT.pr_id',
                'departments DTO'                       => 'DTO.id = PRTO.department_id',

                // light_equipments
                'purchase_requisitions PRLI'            => 'PRLI.id = PRE.pr_id',
                'departments DLI'                       => 'DLI.id = PRLI.department_id',

                // medicals
                'purchase_requisitions PRME'            => 'PRME.id = PMM.pr_id',
                'departments DME'                       => 'DME.id = PRME.department_id',

                // ppe
                'purchase_requisitions PRPP'            => 'PRPP.id = PPD.purchase_requisition_id',
                'departments DPP'                       => 'DPP.id = PRPP.department_id',
            );

            $initQuery = $this->select($fields)
                              ->from('request_quotation_descriptions RQD')
                              ->leftJoin($leftJoins)
                              ->where(array('RQD.is_active' => ':is_active'));

            $initQuery = ($id)     ? $initQuery->andWhere(array('RQD.id' => ':id'))                           : $initQuery;
            $initQuery = ($rfqmId) ? $initQuery->andWhere(array('RQD.rfq_material_id' => ':rfq_material_id')) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectUniqueRequestQuotations` Query string that will select from table `request_quotations`.
         * @param  boolean $supplierId
         * @return string
         */
        public function selectUniqueRequestQuotations($supplierId = false, $status = false)
        {            
            $fields = array(
                // 'DISTINCT(RQ.supplier_id) as supplier_id',
                'DISTINCT(RQ.rfq_no) as rfq_no',
                'RQ.supplier_id',
                'RQ.is_vat',
                'RQ.is_sales_invoice',
                'RQ.is_official_receipt',
                'RQ.rfq_type',
                'S.name as supplier_name',
                'S.email_add',
                'S.address',
                'S.contact_no',
                'S.tin_no',
                // 'DATE_FORMAT(RQ.created_at, "%b %d, %Y %h:%i %p") as rfq_date',
                'DATE_FORMAT(RQ.rfq_date, "%b %d, %Y") as rfq_date',
                // 'CONCAT(PI.fname, " ", PI.lname) as canvasser',
                '"" as canvasser',
            );

            $joins = array(
                'users U'                         => 'RQ.representative = U.id',
                'personal_informations PI'        => 'U.personal_information_id = PI.id',
                'employment_informations EI'      => 'PI.id = EI.personal_information_id',
                'request_quotation_materials RQM' => 'RQ.rfq_material_id = RQM.id'
            );

            $leftJoins = array(
                'suppliers S' => 'S.id = RQ.supplier_id',
            );

            $initQuery = $this->select($fields)
                              ->from('request_quotations RQ')
                              ->join($joins)
                              ->leftJoin($leftJoins)
                              ->where(array('RQ.is_active' => ':is_active', 'RQM.is_active' => ':is_active'));

            $initQuery = ($status) ? $initQuery->andWhere(array('RQ.status' => ':status')) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectRfqAttachments` Query string that will select from table `rfq_attachments`.
         * @param  boolean $id
         * @param  boolean $rfqNo
         * @return string
         */
        public function selectRfqAttachments($id = false, $rfqNo = false)
        {
            $fields = array(
                'RA.id',
                'RA.attachment',
                'RA.type'
            );

            $initQuery = $this->select($fields)
                              ->from('rfq_attachments RA')
                              ->where(array('RA.is_active' => ':is_active'));

            $initQuery = ($id)    ? $initQuery->andWhere(array('RA.id' => ':id'))         : $initQuery;
            $initQuery = ($rfqNo) ? $initQuery->andWhere(array('RA.rfq_no' => ':rfq_no')) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectPersonalInformations` Query string that will select from table `personal_informations`.
         * @param  boolean $id
         * @param  boolean $departmentId
         * @return string
         */
        public function selectPersonalInformations($id = false, $departmentId = false, $isSignatory = false, $userId = false)
        {
            $fields = array(
                'PI.id',
                'CONCAT(PI.lname, ", ", PI.fname) as full_name',
                'P.name as position_name',
                'P.id as position_id',
                'U.id as user_id'
            );

            $joins = array(
                'employment_informations EI' => 'PI.id = EI.personal_information_id',
                'positions P'                => 'EI.position_id = P.id',
                'users U'                    => 'PI.id = U.personal_information_id'
            );

            $initQuery = $this->select($fields)
                              ->from('personal_informations PI')
                              ->join($joins)
                              ->where(array('PI.is_active' => ':is_active'));

            $initQuery = ($id)           ? $initQuery->andWhere(array('PI.id' => ':id'))                      : $initQuery;
            $initQuery = ($departmentId) ? $initQuery->andWhere(array('P.department_id' => ':department_id')) : $initQuery;
            $initQuery = ($userId)       ? $initQuery->andWhere(array('U.id' => ':user_id'))                  : $initQuery;

            return $initQuery;
        }

        /**
         * `selectMaterialUnits` Query string that will select from table `material_units`.
         * @param  boolean $id
         * @return string
         */
        public function selectMaterialUnits($id = false)
        {
            $fields = array(
                'MU.id',
                'MU.code',
                'MU.unit'
            );

            $initQuery = $this->select($fields)
                              ->from('material_units MU')
                              ->where(array('MU.is_active' => ':is_active'));

            $initQuery = ($id) ? $initQuery->andWhere(array('MU.id' => ':id')) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectCustomRequestQuotations` Query string that will select from table `request_quotations`.
         * @param  boolean $itemSpecId
         * @param  boolean $unit
         * @param  string  $requestType
         * @return string
         */
        public function selectCustomRequestQuotations($itemSpecId = false, $unit = false, $requestTypeId = '')
        {
            $fields = [
                'RQ.id',
                'RQ.rfq_no'
            ];

            $joins = [
                'request_quotation_materials RQM' => 'RQM.id = RQ.rfq_material_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('request_quotations RQ')
                              ->join($joins)
                              ->where(['RQ.is_active' => ':is_active', 'RQM.is_active' => ':is_active', 'RQM.status' => 0]);

            $initQuery = ($itemSpecId && ($requestTypeId == '1' || $requestTypeId == '6' || $requestTypeId == '7' || $requestTypeId == '8')) ? $initQuery->andWhere(array('RQM.material_specification_id' => ':item_spec_id')) : $initQuery;
            $initQuery = ($itemSpecId && ($requestTypeId == '2' || $requestTypeId == '5'))                                                   ? $initQuery->andWhere(array('RQM.equipment_type_id' => ':item_spec_id'))         : $initQuery;
            $initQuery = ($itemSpecId && $requestTypeId == '4')                                                                              ? $initQuery->andWhere(array('RQM.hand_tool_id' => ':item_spec_id'))              : $initQuery;
            $initQuery = ($itemSpecId && $requestTypeId == '4')                                                                              ? $initQuery->andWhere(array('RQM.power_tool_id' => ':item_spec_id'))             : $initQuery;
            $initQuery = ($unit)                                                                                                             ? $initQuery->andWhere(array('RQM.unit' => ':unit'))                              : $initQuery;

            return $initQuery;
        }

        /**
         * `selectRelatedAobs` Fetching of related aob from `abstract_of_bids`.
         * @param  boolean $rfqNo
         * @return string
         */
        public function selectRelatedAobs($rfqNo = false)
        {
            $fields = [
                'DISTINCT(AOB.aob_no) as aob_no'
            ];

            $joins = [
                'aob_descriptions AOBD'           => 'AOB.id = AOBD.aob_id',
                'request_quotation_materials RQM' => 'AOBD.rfq_material_id = RQM.id',
                'request_quotations RQ'           => 'RQM.id = RQ.rfq_material_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('abstract_of_bids AOB')
                              ->join($joins)
                              ->where(['RQ.is_active' => ':is_active', 'RQM.is_active' => ':is_active', 'AOB.is_active' => ':is_active']);

            $initQuery = ($rfqNo) ? $initQuery->andWhere(['RQ.rfq_no' => ':rfq_no']) : $initQuery;

            return $initQuery;
        }

        // public function insertNewSupplier($data = array())
        // {
        //     $initQuery = $this->insert('suppliers', $data);
        //     return $initQuery;
        // }

        // public function updateSupplier($id = '', $data = array())
        // {
        //     $initQuery = $this->update('suppliers', $id, $data);
        //     return $initQuery;
        // }


        // public function selectRequestQoutationMaterials($id = false, $quantity = false)
        // {
        //     $fields = array(
        //         'RM.id',
        //         'RM.material_id',
        //         'RM.quantity',
        //         'RM.created_by',
        //         'RM.updated_by',
        //         'RM.created_at',
        //         'RM.updated_at'
        //     );
            
        //     $initQuery = $this->select($fields)
        //                  ->from('request_quotation_materials RM')
        //                  ->where();

        //     $initQuery = ($quantity) ? $initQuery->andWhereNot(array("RM.quantity" => ":quantity")) : $initQuery;
        //     $initQuery = ($id) ? $initQuery->andWhere(array('RM.id' => ':id')) : $initQuery;
            
        //     return $initQuery;
        // }

        /**
         * `insertRfqMaterial` Insert data from table `request_quotation_materials`
         * @param  array  $data [description]
         * @return [type]       [description]
         */
        public function insertRfqMaterial($data = array())
        {
            $initQuery = $this->insert('request_quotation_materials', $data);

            return $initQuery;
        }

        /**
         * `insertRequestQuotationDescription` Insert details from table `request_quotation_descriptions`
         * @param  array  $data
         * @return string
         */
        public function insertRequestQuotationDescription($data = array())
        {
            $initQuery = $this->insert('request_quotation_descriptions', $data);

            return $initQuery;
        }

        /**
         * `insertRequestQuotation` Insert details from table `request_quotations`.
         * @param  array  $data [description]
         * @return [type]       [description]
         */
        public function insertRequestQuotation($data = array())
        {
            $initQuery = $this->insert('request_quotations', $data);

            return $initQuery;
        }

        /**
         * `insertRfqAttachment` Insert details from table `rfq_attachments`.
         * @param  array  $data [description]
         * @return [type]       [description]
         */
        public function insertRfqAttachment($data = array())
        {
            $initQuery = $this->insert('rfq_attachments', $data);

            return $initQuery;
        }

        /**
         * `updateRfqMaterial` Update data in `request_quotation_materials` table.
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function updateRfqMaterial($id = '', $data = array())
        {
            $initQuery = $this->update('request_quotation_materials', $id, $data);

            return $initQuery;
        }

        /**
         * `updateRequestQuotation` Update data in `request_quotations` table.
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function updateRequestQuotation($id = '', $data = array(), $fk = '', $fkValue = '')
        {
            $initQuery = $this->update('request_quotations', $id, $data, $fk, $fkValue);

            return $initQuery;
        }

        /**
         * `updatePurchaseRequisitionDescription` Update data in `purchase_requisition_descriptions` table.
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function updatePurchaseRequisitionDescription($id = '', $data = array())
        {
            $initQuery = $this->update('purchase_requisition_descriptions', $id, $data);

            return $initQuery;
        }

        /**
         * `updatePurchaseRequisition` Update data in `purchase_requisitions` table.
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function updatePurchaseRequisition($id = '', $data = array())
        {
            $initQuery = $this->update('purchase_requisitions', $id, $data);

            return $initQuery;
        }

        /**
         * `updateRfqAttachment` Update data in `rfq_attachments` table.
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function updateRfqAttachment($id = '', $data = array())
        {
            $initQuery = $this->update('rfq_attachments', $id, $data);

            return $initQuery;
        }

        /**
         * `updateRequestQuotationDescription` Query string that will update to table `request_quotation_descriptions`
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function updateRequestQuotationDescription($id = '', $data = array(), $fk = '', $fkValue = '')
        {
            $initQuery = $this->update('request_quotation_descriptions', $id, $data, $fk, $fkValue);

            return $initQuery;
        }

        /**
         * `updateRequestQuotationMaterial` Query string that will update to table `request_quotation_materials`
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function updateRequestQuotationMaterial($id = '', $data = array(), $fk = '', $fkValue = '')
        {
            $initQuery = $this->update('request_quotation_materials', $id, $data, $fk, $fkValue);

            return $initQuery;
        }

        /**
         * `updatePrdManpowerService` Update data in `prd_manpower_services` table.
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function updatePrdManpowerService($id = '', $data = array())
        {
            $initQuery = $this->update('prd_manpower_services', $id, $data);

            return $initQuery;
        }

        /**
         * `updatePrsPpeDescription` Update data in `prs_ppe_descriptions` table.
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function updatePrsPpeDescription($id = '', $data = array())
        {
            $initQuery = $this->update('prs_ppe_descriptions', $id, $data);

            return $initQuery;
        }

        /**
         * `updatePrMedicalMaterial` Update data in `pr_medical_materials` table.
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function updatePrMedicalMaterial($id = '', $data = array())
        {
            $initQuery = $this->update('pr_medical_materials', $id, $data);

            return $initQuery;
        }

        /**
         * `updatePrTool` Update data in `pr_tools` table.
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function updatePrTool($id = '', $data = array())
        {
            $initQuery = $this->update('pr_tools', $id, $data);

            return $initQuery;
        }

        /**
         * `updatePrEquipment` Update data in `pr_equipments` table.
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function updatePrEquipment($id = '', $data = array())
        {
            $initQuery = $this->update('pr_equipments', $id, $data);

            return $initQuery;
        }

        /**
         * `updatePrHeavyEquipment` Update data in `pr_heavy_equipments` table.
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function updatePrHeavyEquipment($id = '', $data = array())
        {
            $initQuery = $this->update('pr_heavy_equipments', $id, $data);

            return $initQuery;
        }

        /**
         * `deleteRequestQuotationDescription` Hard delete details from table `request_quotation_descriptions`.
         * @param  string $id
         * @return string
         */
        public function deleteRequestQuotationDescription($id = false, $rfqMaterialId = false)
        {
            $initQuery = $this->delete('request_quotation_descriptions');

            $initQuery = ($id)            ? $initQuery->where(array('id' => ':id'))                           : $initQuery;
            $initQuery = ($rfqMaterialId) ? $initQuery->where(array('rfq_material_id' => ':rfq_material_id')) : $initQuery;

            return $initQuery;
        }
    }