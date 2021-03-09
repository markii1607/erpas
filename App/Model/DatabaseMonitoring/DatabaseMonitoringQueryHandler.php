<?php 
    namespace App\Model\DatabaseMonitoring;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class DatabaseMonitoringQueryHandler extends QueryHandler { 
        /**
         * `selectPurchaseRequisitions` Query string that will fetch prs from table `purchase_requisitions`.
         * @return string
         */
        public function selectPurchaseRequisitions($id = false)
        {
            $fields = [
                'PR.id',
                'PR.prs_no',
                'PR.status',
                'PR.for_cancelation',

                'P.name as project_name',
                'P.project_code',

                'PRD.status as prd_status',

                'MS.code as material_code',
                'MS.specs',

                'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as requestor',
            ];

            $orWhereCondition = array(
                'PR.prs_no'      => ':filter_val',
                'P.project_code' => ':filter_val',
                'MS.code'        => ':filter_val',
                'MS.specs'       => ':filter_val',
            );

            $joins = [
                'purchase_requisition_descriptions PRD' => 'PR.id = PRD.purchase_requisition_id',
                'material_specifications MS'            => 'MS.id = PRD.item_spec_id',
                'users U'                               => 'U.id = PR.created_by',
                'personal_informations PI'              => 'PI.id = U.personal_information_id'
            ];

            $leftJoins = [
                'projects P' => 'P.id = PR.project_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('purchase_requisitions PR')
                              ->join($joins)
                              ->leftJoin($leftJoins)
                              ->where(['PR.is_active' => ':is_active'])
                              ->logicEx('AND')
                              ->orWhereLike($orWhereCondition);

            $initQuery = ($id) ? $initQuery->andWhere(['PR.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        public function selectPrsViaStatus($id = false, $status = '')
        {
            $fields = [
                'PR.id',
                'PR.prs_no',
                'PR.signatories',
                'P.project_code',
            ];

            $leftJoins = [
                'projects P' => 'P.id = PR.project_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('purchase_requisitions PR')
                              ->leftJoin($leftJoins)
                              ->where(['PR.is_active' => ':is_active'])
                              ->andWhereNotNull(['PR.signatories'])
                              ->andWhereNull(['PR.for_cancelation'])
                              ->andWhereNotIN('PR.id', ['25', '26', '27', '24', '9', '10', '11', '12', '28', '29', '30', '31', '32', '33', '34', '35', '38', '39', '1', '7', '4', '2', '3', '36', '37', '23', '69']);

            $initQuery = ($id)                      ? $initQuery->andWhere(['PR.id' => ':id'])   : $initQuery;
            $initQuery = ($status == 'approved')    ? $initQuery->andWhereIn('PR.status', ['3']) : $initQuery;
            $initQuery = ($status == 'disapproved') ? $initQuery->andWhereIn('PR.status', ['1']) : $initQuery;
            $initQuery = ($status == 'pending')     ? $initQuery->andWhereIn('PR.status', ['2']) : $initQuery;

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
                'PR.date_requested',
                'PRD.item_spec_id',
                'MS.code',
                'MS.specs',
                'M.name',
                'PRD.quantity',
                'PRD.unit_measurement',
                'P.project_code',
                'D.charging',
                'WI.item_no',
                '(SELECT COUNT(RQ.id) AS rfq_count FROM `request_quotations` RQ JOIN `request_quotation_materials` RQM ON RQM.id = RQ.rfq_material_id JOIN `request_quotation_descriptions` RQD ON RQD.rfq_material_id = RQM.id WHERE RQD.purchase_requisition_description_id = PRD.id AND RQD.is_active = 1 AND RQM.is_active = 1) as rfq_count'
            );

            // $joins = [
            //     'request_quotation_descriptions RQD' => 'RQD.purchase_requisition_description_id = PRD.id'
            // ];

            $leftJoins = array(
                'purchase_requisitions PR'   => 'PR.id = PRD.purchase_requisition_id',
                'material_specifications MS' => 'PRD.item_spec_id = MS.id',
                'materials M'                => 'MS.material_id = M.id',
                'departments D'              => 'D.id = PR.department_id',
                'projects P'                 => 'PR.project_id = P.id',
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
                              // ->join($joins)
                              ->where(array('PRD.status' => ':status', 'PRD.is_active' => 1))
                              ->andWhereNotIn('(P.id', $whereNotInCondition)
                              ->logicEx(' OR PR.project_id IS NULL)');

            $initQuery = ($id) ? $initQuery->andWhere(array('PRD.id' => ':id')) : $initQuery;
            
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
                'PR.date_requested',
                'PHE.equipment_type_id as item_spec_id',
                'ET.cost_code as code',
                'ET.name as specs',
                'EC.name',
                'PHE.total_no_of_equipment as quantity',
                '"unit" as unit_measurement',
                'P.project_code', 
                'D.charging',
                '"-" as item_no',
                '(SELECT COUNT(RQ.id) AS rfq_count FROM `request_quotations` RQ JOIN `request_quotation_materials` RQM ON RQM.id = RQ.rfq_material_id JOIN `request_quotation_descriptions` RQD ON RQD.rfq_material_id = RQM.id WHERE RQD.pr_heavy_equipment_id = PHE.id AND RQD.is_active = 1 AND RQM.is_active = 1) as rfq_count'
            ];

            // $joins = [
            //     'request_quotation_descriptions RQD' => 'RQD.pr_heavy_equipment_id = PHE.id'
            // ];

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
                              // ->join($joins)
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
                'PR.date_requested',
                'PE.equipment_type_id as item_spec_id',
                'ET.cost_code as code',
                'ET.name as specs',
                'EC.name',
                'PE.total_no_of_equipment as quantity',
                '"unit" as unit_measurement',
                'P.project_code', 
                'D.charging',
                '"-" as item_no',
                '(SELECT COUNT(RQ.id) AS rfq_count FROM `request_quotations` RQ JOIN `request_quotation_materials` RQM ON RQM.id = RQ.rfq_material_id JOIN `request_quotation_descriptions` RQD ON RQD.rfq_material_id = RQM.id WHERE RQD.pr_equipment_id = PE.id AND RQD.is_active = 1 AND RQM.is_active = 1) as rfq_count'
            ];

            // $joins = [
            //     'request_quotation_descriptions RQD' => 'RQD.pr_equipment_id = PE.id'
            // ];

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
                              // ->join($joins)
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
                'PR.date_requested',
                'IF(PRT.power_tool_id IS NULL, PRT.hand_tool_id, PRT.power_tool_id) as item_spec_id',
                'IF(PRT.power_tool_id IS NULL, HT.code, PT.code) as code',
                'IF(PRT.power_tool_id IS NULL, HT.specification, PT.specification) as specs',
                'IF(PRT.power_tool_id IS NULL, HTC.name, PTC.name) as name',
                'IF(PRT.power_tool_id IS NULL, "HT", "PT") as tool_type',
                'PRT.requested_units as quantity',
                'PRT.unit_of_measurement as unit_measurement',
                'D.charging',
                'P.project_code', 
                '"-" as item_no',
                '(SELECT COUNT(RQ.id) AS rfq_count FROM `request_quotations` RQ JOIN `request_quotation_materials` RQM ON RQM.id = RQ.rfq_material_id JOIN `request_quotation_descriptions` RQD ON RQD.rfq_material_id = RQM.id WHERE RQD.pr_tool_id = PRT.id AND RQD.is_active = 1 AND RQM.is_active = 1) as rfq_count'
            ];

            // $joins = [
            //     'request_quotation_descriptions RQD' => 'RQD.pr_tool_id = PRT.id'
            // ];

            $leftJoins = [
                'purchase_requisitions PR'  => 'PR.id = PRT.pr_id',
                'power_tools PT'            => 'PRT.power_tool_id = PT.id',
                'power_tool_categories PTC' => 'PT.category = PTC.id',
                'hand_tools HT'             => 'HT.id = PRT.hand_tool_id',
                'hand_tool_categories HTC'  => 'HTC.id = HT.category',
                'departments D'            => 'D.id = PR.department_id',
                'projects P'               => 'PR.project_id = P.id',
            ];

            $initQuery = $this->select($fields)
                              ->from('pr_tools PRT')
                              ->leftJoin($leftJoins)
                              // ->join($joins)
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
                'PR.date_requested',
                'MSB.material_specification_id as item_spec_id',
                'MS.code',
                'MS.specs',
                'M.name',
                'PMM.quantity',
                'PMM.unit_of_measurement as unit_measurement',
                'P.project_code', 
                'D.charging',
                '"-" as item_no',
                '(SELECT COUNT(RQ.id) AS rfq_count FROM `request_quotations` RQ JOIN `request_quotation_materials` RQM ON RQM.id = RQ.rfq_material_id JOIN `request_quotation_descriptions` RQD ON RQD.rfq_material_id = RQM.id WHERE RQD.pr_medical_material_id = PMM.id AND RQD.is_active = 1 AND RQM.is_active = 1) as rfq_count'
            ];

            // $joins = [
            //     'request_quotation_descriptions RQD' => 'RQD.pr_medical_material_id = PMM.id'
            // ];

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
                              // ->join($joins)
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
                'PR.date_requested',
                'PPD.material_specification_id as item_spec_id',
                'MS.code',
                'MS.specs',
                'M.name',
                'PPD.quantity',
                'PPD.unit_measurement',
                'D.charging',
                'P.project_code', 
                '"-" as item_no',
                '(SELECT COUNT(RQ.id) AS rfq_count FROM `request_quotations` RQ JOIN `request_quotation_materials` RQM ON RQM.id = RQ.rfq_material_id JOIN `request_quotation_descriptions` RQD ON RQD.rfq_material_id = RQM.id WHERE RQD.prs_ppe_description_id = PPD.id AND RQD.is_active = 1 AND RQM.is_active = 1) as rfq_count'
            ];

            // $joins = [
            //     'request_quotation_descriptions RQD' => 'RQD.prs_ppe_description_id = PPD.id'
            // ];

            $leftJoins = [
                'purchase_requisitions PR'   => 'PR.id = PPD.purchase_requisition_id',
                'material_specifications MS' => 'PPD.material_specification_id = MS.id',
                'materials M'                => 'MS.material_id = M.id',
                'departments D'              => 'D.id = PR.department_id',
                'projects P'                 => 'PR.project_id = P.id'
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
                              // ->join($joins)
                              ->where(['PPD.status' => ':status'])
                              ->andWhereNotIn('P.id', $whereNotInCondition);
                              // ->andWhereNull(['PR.project_id']);

            $initQuery = ($id) ? $initQuery->andWhere(['PPD.id' => ':id']) : $initQuery;

            return $initQuery;
        }
    }