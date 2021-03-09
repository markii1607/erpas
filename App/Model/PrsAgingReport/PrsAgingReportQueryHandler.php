<?php 
    namespace App\Model\PrsAgingReport;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class PrsAgingReportQueryHandler extends QueryHandler { 

        /**
         * `selectCustomAobSignatories` Query string that will select custom pending aob signatory from `aob_signatories` table.
         * @param  boolean $descriptionId
         * @param  string  $rt
         * @return string
         */
        public function selectCustomAobSignatories($descriptionId = false, $rt = '')
        {            
            $fields = [
                'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as pending_to',
            ];

            $joins = [
                'aob_descriptions AOBD'              => 'AOBD.aob_id = AOBS.aob_id',
                'request_quotation_descriptions RQD' => 'RQD.rfq_material_id = AOBD.rfq_material_id',
                'users U'                            => 'AOBS.signatory = U.id',
                'personal_informations PI'           => 'U.personal_information_id = PI.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('aob_signatories AOBS')
                              ->join($joins)
                              ->where(['AOBS.is_active' => ':is_active', 'AOBS.status' => ':status']);

            $initQuery = ($descriptionId && $rt == 'MAS') ? $initQuery->andWhere(['RQD.purchase_requisition_description_id' => ':description_id']) : $initQuery;
            $initQuery = ($descriptionId && $rt == 'HER') ? $initQuery->andWhere(['RQD.pr_heavy_equipment_id' => ':description_id'])               : $initQuery;
            $initQuery = ($descriptionId && $rt == 'TLS') ? $initQuery->andWhere(['RQD.pr_tool_id' => ':description_id'])                          : $initQuery;
            $initQuery = ($descriptionId && $rt == 'LER') ? $initQuery->andWhere(['RQD.pr_equipment_id' => ':description_id'])                     : $initQuery;
            $initQuery = ($descriptionId && $rt == 'MED') ? $initQuery->andWhere(['RQD.pr_medical_material_id' => ':description_id'])              : $initQuery;
            $initQuery = ($descriptionId && $rt == 'PPE') ? $initQuery->andWhere(['RQD.prs_ppe_description_id' => ':description_id'])              : $initQuery;
            $initQuery = ($descriptionId && $rt == 'MAN') ? $initQuery->andWhere(['RQD.prd_manpower_service_id' => ':description_id'])             : $initQuery;

            return $initQuery;
        }

        /**
         * `selectPurchaseRequisitionDescriptions` Query string that will fetch from `purchase_requisition_descriptions` table.
         * @return string
         */
        public function selectPurchaseRequisitionDescriptions($id = false, $projectId = false)
        {
            $fields = [
                'PRD.id',
                'RT.name as request_type',
                'PR.prs_no',
                'IF(PR.project_id IS NULL, D.charging, IF(P.project_code IS NULL, PCR.temporary_project_code, P.project_code)) as charging',
                'IF(PRD.expense_type IS NULL, "-", PRD.expense_type) as expense_type',
                'MS.specs',
                'PRD.quantity',
                'PRD.unit_measurement as unit',
                'PRD.remarks',
                'PR.date_requested',
                'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as requested_by',
                'IF(PR.for_cancelation = "0", "CANCELATION FOR APPROVAL", IF(PR.for_cancelation = "1", "CANCELED", IF(PRD.status = "2", "RFQ", IF(PRD.status = "3", "WITHDRAWAL", IF(PRD.status = "4", "AOB", IF(PRD.status = "5", "PO", IF(PRD.status = "7", "ISSUANCE", IF(PRD.status = "9", "PETTY CASH", IF(PRD.status = "10", "HARD COPY", IF((PRD.status = "1" OR PRD.status IS NULL) AND PR.status = "2", "PENDING FOR APPROVAL", IF(PRD.status = "0", "DISAPPROVED", IF(PRD.status = "1", "APPROVED", IF(PRD.status = "12", "CANCELED", "-"))))))))))))) as current_status',
                'IF(PRD.status = "2", "Canvasser", IF(PR.status = "3" AND PRD.status = "1", "Supply Receiving", "-")) as pending_to',
                'PRD.status'
            ];

            $joins = [
                'purchase_requisitions PR'   => 'PR.id = PRD.purchase_requisition_id',
                'request_types RT'           => 'RT.id = PR.request_type_id',
                'material_specifications MS' => 'PRD.item_spec_id = MS.id',
                'users U'                    => 'PR.user_id = U.id',
                'personal_informations PI'   => 'U.personal_information_id = PI.id'
            ];

            $leftJoins = [
                'projects P'                 => 'P.id = PR.project_id',
                'departments D'              => 'PR.department_id = D.id',
                'project_code_requests PCR'  => 'PCR.project_id = P.id',
            ];

            $initQuery = $this->select($fields)
                              ->from('purchase_requisition_descriptions PRD')
                              ->join($joins)
                              ->leftJoin($leftJoins)
                              ->where(['PRD.is_active' => ':is_active', 'PR.is_active' => ':is_active', 'MS.is_active' => ':is_active'])
                              ->andWhereIn('PR.status', ['1', '2', '3', '4', '5', '6', '7', '8']);
                              // ->andWhereNotIn('(PR.project_id', ['25', '26', '27', '24', '9', '10', '11', '12', '28', '29', '30', '31', '32', '33', '34', '35', '38', '39', '1', '7', '4', '2', '3', '36', '37', '23', '69'])
                              // ->logicEx(' OR PR.project_id IS NULL)');

            $initQuery = ($id)        ? $initQuery->andWhere(['PRD.id' => ':id'])       : $initQuery;
            $initQuery = ($projectId) ? $initQuery->andWhere(['P.id' => ':project_id']) : $initQuery;

            // $initQuery = $initQuery->logicEx(' LIMIT 2000, 4000');

            return $initQuery;
        }

        /**
         * `selectPrHeavyEquipments` Query string that will fetch from `pr_heavy_equipments` table.
         * @return string
         */
        public function selectPrHeavyEquipments($id = false, $projectId = false)
        {
            $fields = [
                'PHE.id',
                'RT.name as request_type',
                'PR.prs_no',
                'IF(PR.project_id IS NULL, D.charging, IF(P.project_code IS NULL, PCR.temporary_project_code, P.project_code)) as charging',
                'IF(PHE.expense_type IS NULL, "-", PHE.expense_type) as expense_type',
                'ET.name as specs',
                'PHE.total_no_of_equipment as quantity',
                'ET.unit',
                'PHE.remarks',
                'PR.date_requested',
                'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as requested_by',
                'IF(PR.for_cancelation = "0", "CANCELATION FOR APPROVAL", IF(PR.for_cancelation = "1", "CANCELED", IF(PHE.status = "2", "RFQ", IF(PHE.status = "3", "WITHDRAWAL", IF(PHE.status = "4", "AOB", IF(PHE.status = "5", "PO", IF(PHE.status = "7", "ISSUANCE", IF(PHE.status = "9", "PETTY CASH", IF(PHE.status = "10", "HARD COPY", IF((PHE.status = "1" OR PHE.status IS NULL) AND PR.status = "2", "PENDING FOR APPROVAL", IF(PHE.status = "0", "DISAPPROVED", IF(PHE.status = "1", "APPROVED", IF(PHE.status = "12", "CANCELED", "-"))))))))))))) as current_status',
                'IF(PHE.status = "2", "Canvasser", IF(PR.status = "3" AND PHE.status = "1", "Supply Receiving", "-")) as pending_to',
                'PHE.status'
            ];

            $joins = [
                'purchase_requisitions PR'   => 'PR.id = PHE.pr_id',
                'request_types RT'           => 'RT.id = PR.request_type_id',
                'equipment_types ET'         => 'PHE.equipment_type_id = ET.id',
                'users U'                    => 'PR.user_id = U.id',
                'personal_informations PI'   => 'U.personal_information_id = PI.id'
            ];

            $leftJoins = [
                'projects P'                 => 'P.id = PR.project_id',
                'departments D'              => 'PR.department_id = D.id',
                'project_code_requests PCR'  => 'PCR.project_id = P.id',
            ];

            $initQuery = $this->select($fields)
                              ->from('pr_heavy_equipments PHE')
                              ->join($joins)
                              ->leftJoin($leftJoins)
                              ->where(['PHE.is_active' => ':is_active', 'PR.is_active' => ':is_active', 'ET.is_active' => ':is_active'])
                              ->andWhereIn('PR.status', ['1', '2', '3', '4', '5', '6', '7', '8']);
                              // ->andWhereNotIn('(PR.project_id', ['25', '26', '27', '24', '9', '10', '11', '12', '28', '29', '30', '31', '32', '33', '34', '35', '38', '39', '1', '7', '4', '2', '3', '36', '37', '23', '69'])
                              // ->logicEx(' OR PR.project_id IS NULL)');

            $initQuery = ($id)        ? $initQuery->andWhere(['PHE.id' => ':id'])       : $initQuery;
            $initQuery = ($projectId) ? $initQuery->andWhere(['P.id' => ':project_id']) : $initQuery;

            // $initQuery = $initQuery->logicEx(' LIMIT 2000, 4000');

            return $initQuery;
        }

        /**
         * `selectPrTools` Query string that will fetch from `pr_tools` table.
         * @return string
         */
        public function selectPrTools($id = false, $projectId = false)
        {
            $fields = [
                'PRT.id',
                'RT.name as request_type',
                'PR.prs_no',
                'IF(PR.project_id IS NULL, D.charging, IF(P.project_code IS NULL, PCR.temporary_project_code, P.project_code)) as charging',
                'IF(PRT.expense_type IS NULL, "-", PRT.expense_type) as expense_type',
                'IF(PRT.hand_tool_id IS NULL, PT.specification, HT.specification) as specs',
                'PRT.requested_units as quantity',
                'PRT.unit_of_measurement as unit',
                'PRT.remarks',
                'PR.date_requested',
                'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as requested_by',
                'IF(PR.for_cancelation = "0", "CANCELATION FOR APPROVAL", IF(PR.for_cancelation = "1", "CANCELED", IF(PRT.process_status = "2", "RFQ", IF(PRT.process_status = "3", "WITHDRAWAL", IF(PRT.process_status = "4", "AOB", IF(PRT.process_status = "5", "PO", IF(PRT.process_status = "7", "ISSUANCE", IF(PRT.process_status = "9", "PETTY CASH", IF(PRT.process_status = "10", "HARD COPY", IF((PRT.process_status = "1" OR PRT.process_status IS NULL) AND PR.status = "2", "PENDING FOR APPROVAL", IF(PRT.process_status = "0", "DISAPPROVED", IF(PRT.process_status = "1", "APPROVED", IF(PRT.process_status = "12", "CANCELED", "-"))))))))))))) as current_status',
                'IF(PRT.process_status = "2", "Canvasser", IF(PR.status = "3" AND PRT.process_status = "1", "Supply Receiving", "-")) as pending_to',
                'PRT.process_status as status'
            ];

            $joins = [
                'purchase_requisitions PR'   => 'PR.id = PRT.pr_id',
                'request_types RT'           => 'RT.id = PR.request_type_id',
                'users U'                    => 'PR.user_id = U.id',
                'personal_informations PI'   => 'U.personal_information_id = PI.id'
            ];

            $leftJoins = [
                'projects P'                => 'P.id = PR.project_id',
                'departments D'             => 'PR.department_id = D.id',
                'project_code_requests PCR' => 'PCR.project_id = P.id',
                'power_tools PT'            => 'PT.id = PRT.power_tool_id',
                'hand_tools HT'             => 'PRT.hand_tool_id = HT.id',
            ];

            $initQuery = $this->select($fields)
                              ->from('pr_tools PRT')
                              ->join($joins)
                              ->leftJoin($leftJoins)
                              ->where(['PRT.is_active' => ':is_active', 'PR.is_active' => ':is_active'])
                              ->andWhereIn('PR.status', ['1', '2', '3', '4', '5', '6', '7', '8']);
                              // ->andWhereNotIn('(PR.project_id', ['25', '26', '27', '24', '9', '10', '11', '12', '28', '29', '30', '31', '32', '33', '34', '35', '38', '39', '1', '7', '4', '2', '3', '36', '37', '23', '69'])
                              // ->logicEx(' OR PR.project_id IS NULL)');

            $initQuery = ($id)        ? $initQuery->andWhere(['PRT.id' => ':id'])       : $initQuery;
            $initQuery = ($projectId) ? $initQuery->andWhere(['P.id' => ':project_id']) : $initQuery;

            // $initQuery = $initQuery->logicEx(' LIMIT 2000, 4000');

            return $initQuery;
        }

        /**
         * `selectPrEquipments` Query string that will fetch from `pr_equipments` table.
         * @return string
         */
        public function selectPrEquipments($id = false, $projectId = false)
        {
            $fields = [
                'PLE.id',
                'RT.name as request_type',
                'PR.prs_no',
                'IF(PR.project_id IS NULL, D.charging, IF(P.project_code IS NULL, PCR.temporary_project_code, P.project_code)) as charging',
                'IF(PLE.expense_type IS NULL, "-", PLE.expense_type) as expense_type',
                'ET.name as specs',
                'PLE.total_no_of_equipment as quantity',
                'ET.unit',
                'PLE.remarks',
                'PR.date_requested',
                'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as requested_by',
                'IF(PR.for_cancelation = "0", "CANCELATION FOR APPROVAL", IF(PR.for_cancelation = "1", "CANCELED", IF(PLE.status = "2", "RFQ", IF(PLE.status = "3", "WITHDRAWAL", IF(PLE.status = "4", "AOB", IF(PLE.status = "5", "PO", IF(PLE.status = "7", "ISSUANCE", IF(PLE.status = "9", "PETTY CASH", IF(PLE.status = "10", "HARD COPY", IF((PLE.status = "1" OR PLE.status IS NULL) AND PR.status = "2", "PENDING FOR APPROVAL", IF(PLE.status = "0", "DISAPPROVED", IF(PLE.status = "1", "APPROVED", IF(PLE.status = "12", "CANCELED", "-"))))))))))))) as current_status',
                'IF(PLE.status = "2", "Canvasser", IF(PR.status = "3" AND PLE.status = "1", "Supply Receiving", "-")) as pending_to',
                'PLE.status'
            ];

            $joins = [
                'purchase_requisitions PR'   => 'PR.id = PLE.pr_id',
                'request_types RT'           => 'RT.id = PR.request_type_id',
                'equipment_types ET'         => 'PLE.equipment_type_id = ET.id',
                'users U'                    => 'PR.user_id = U.id',
                'personal_informations PI'   => 'U.personal_information_id = PI.id'
            ];

            $leftJoins = [
                'projects P'                 => 'P.id = PR.project_id',
                'departments D'              => 'PR.department_id = D.id',
                'project_code_requests PCR'  => 'PCR.project_id = P.id',
            ];

            $initQuery = $this->select($fields)
                              ->from('pr_equipments PLE')
                              ->join($joins)
                              ->leftJoin($leftJoins)
                              ->where(['PLE.is_active' => ':is_active', 'PR.is_active' => ':is_active', 'ET.is_active' => ':is_active'])
                              ->andWhereIn('PR.status', ['1', '2', '3', '4', '5', '6', '7', '8']);
                              // ->andWhereNotIn('(PR.project_id', ['25', '26', '27', '24', '9', '10', '11', '12', '28', '29', '30', '31', '32', '33', '34', '35', '38', '39', '1', '7', '4', '2', '3', '36', '37', '23', '69'])
                              // ->logicEx(' OR PR.project_id IS NULL)');

            $initQuery = ($id)        ? $initQuery->andWhere(['PLE.id' => ':id'])       : $initQuery;
            $initQuery = ($projectId) ? $initQuery->andWhere(['P.id' => ':project_id']) : $initQuery;

            // $initQuery = $initQuery->logicEx(' LIMIT 2000, 4000');

            

            return $initQuery;
        }

        /**
         * `selectPrdManpowerServices` Query string that will fetch from `prd_manpower_services` table.
         * @return string
         */
        public function selectPrdManpowerServices($id = false, $projectId = false)
        {
            $fields = [
                'PMS.id',
                'RT.name as request_type',
                'PR.prs_no',
                'IF(PR.project_id IS NULL, D.charging, IF(P.project_code IS NULL, PCR.temporary_project_code, P.project_code)) as charging',
                'IF(PMS.expense_type IS NULL, "-", PMS.expense_type) as expense_type',
                'MS.specs',
                'PMS.quantity',
                'PMS.unit_measurement as unit',
                'PMS.remarks',
                'PR.date_requested',
                'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as requested_by',
                'IF(PR.for_cancelation = "0", "CANCELATION FOR APPROVAL", IF(PR.for_cancelation = "1", "CANCELED", IF(PMS.status = "2", "RFQ", IF(PMS.status = "3", "WITHDRAWAL", IF(PMS.status = "4", "AOB", IF(PMS.status = "5", "PO", IF(PMS.status = "7", "ISSUANCE", IF(PMS.status = "9", "PETTY CASH", IF(PMS.status = "10", "HARD COPY", IF((PMS.status = "1" OR PMS.status IS NULL) AND PR.status = "2", "PENDING FOR APPROVAL", IF(PMS.status = "0", "DISAPPROVED", IF(PMS.status = "1", "APPROVED", IF(PMS.status = "12", "CANCELED", "-"))))))))))))) as current_status',
                'IF(PMS.status = "2", "Canvasser", IF(PR.status = "3" AND PMS.status = "1", "Supply Receiving", "-")) as pending_to',
                'PMS.status'
            ];

            $joins = [
                'purchase_requisitions PR'   => 'PR.id = PMS.purchase_requisition_id',
                'material_specifications MS' => 'MS.id = PMS.material_specification_id',
                'request_types RT'           => 'RT.id = PR.request_type_id',
                'users U'                    => 'PR.user_id = U.id',
                'personal_informations PI'   => 'U.personal_information_id = PI.id'
            ];

            $leftJoins = [
                'projects P'                => 'P.id = PR.project_id',
                'departments D'             => 'PR.department_id = D.id',
                'project_code_requests PCR' => 'PCR.project_id = P.id',
            ];

            $initQuery = $this->select($fields)
                              ->from('prd_manpower_services PMS')
                              ->join($joins)
                              ->leftJoin($leftJoins)
                              ->where(['PMS.is_active' => ':is_active', 'PR.is_active' => ':is_active', 'MS.is_active' => ':is_active'])
                              ->andWhereIn('PR.status', ['1', '2', '3', '4', '5', '6', '7', '8']);
                              // ->andWhereNotIn('(PR.project_id', ['25', '26', '27', '24', '9', '10', '11', '12', '28', '29', '30', '31', '32', '33', '34', '35', '38', '39', '1', '7', '4', '2', '3', '36', '37', '23', '69'])
                              // ->logicEx(' OR PR.project_id IS NULL)');

            $initQuery = ($id)        ? $initQuery->andWhere(['PMS.id' => ':id'])       : $initQuery;
            $initQuery = ($projectId) ? $initQuery->andWhere(['P.id' => ':project_id']) : $initQuery;

            // $initQuery = $initQuery->logicEx(' LIMIT 2000, 4000');

            return $initQuery;
        }

        /**
         * `selectPrMedicalMaterials` Query string that will fetch from `pr_medical_materials` table.
         * @return string
         */
        public function selectPrMedicalMaterials($id = false, $projectId = false)
        {
            $fields = [
                'PMM.id',
                'RT.name as request_type',
                'PR.prs_no',
                'IF(PR.project_id IS NULL, D.charging, IF(P.project_code IS NULL, PCR.temporary_project_code, P.project_code)) as charging',
                'IF(PMM.expense_type IS NULL, "-", PMM.expense_type) as expense_type',
                'MS.specs',
                'PMM.quantity',
                'PMM.unit_of_measurement as unit',
                'PMM.remarks',
                'PR.date_requested',
                'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as requested_by',
                'IF(PR.for_cancelation = "0", "CANCELATION FOR APPROVAL", IF(PR.for_cancelation = "1", "CANCELED", IF(PMM.process_status = "2", "RFQ", IF(PMM.process_status = "3", "WITHDRAWAL", IF(PMM.process_status = "4", "AOB", IF(PMM.process_status = "5", "PO", IF(PMM.process_status = "7", "ISSUANCE", IF(PMM.process_status = "9", "PETTY CASH", IF(PMM.process_status = "10", "HARD COPY", IF((PMM.process_status = "1" OR PMM.process_status IS NULL) AND PR.status = "2", "PENDING FOR APPROVAL", IF(PMM.process_status = "0", "DISAPPROVED", IF(PMM.process_status = "1", "APPROVED", IF(PMM.process_status = "12", "CANCELED", "-"))))))))))))) as current_status',
                'IF(PMM.process_status = "2", "Canvasser", IF(PR.status = "3" AND PMM.process_status = "1", "Supply Receiving", "-")) as pending_to',
                'PMM.process_status as status'
            ];

            $joins = [
                'purchase_requisitions PR'          => 'PR.id = PMM.pr_id',
                'material_specification_brands MSB' => 'PMM.material_specification_brand_id = MSB.id',
                'material_specifications MS'        => 'MSB.material_specification_id = MS.id',
                'request_types RT'                  => 'RT.id = PR.request_type_id',
                'users U'                           => 'PR.user_id = U.id',
                'personal_informations PI'          => 'U.personal_information_id = PI.id'
            ];

            $leftJoins = [
                'projects P'                => 'P.id = PR.project_id',
                'departments D'             => 'PR.department_id = D.id',
                'project_code_requests PCR' => 'PCR.project_id = P.id',
            ];

            $initQuery = $this->select($fields)
                              ->from('pr_medical_materials PMM')
                              ->join($joins)
                              ->leftJoin($leftJoins)
                              ->where(['PMM.is_active' => ':is_active', 'PR.is_active' => ':is_active', 'MS.is_active' => ':is_active'])
                              ->andWhereIn('PR.status', ['1', '2', '3', '4', '5', '6', '7', '8']);
                              // ->andWhereNotIn('(PR.project_id', ['25', '26', '27', '24', '9', '10', '11', '12', '28', '29', '30', '31', '32', '33', '34', '35', '38', '39', '1', '7', '4', '2', '3', '36', '37', '23', '69'])
                              // ->logicEx(' OR PR.project_id IS NULL)');

            $initQuery = ($id)        ? $initQuery->andWhere(['PMM.id' => ':id'])       : $initQuery;
            $initQuery = ($projectId) ? $initQuery->andWhere(['P.id' => ':project_id']) : $initQuery;

            // $initQuery = $initQuery->logicEx(' LIMIT 2000, 4000');

            return $initQuery;
        }

        /**
         * `selectPrsPpeDescriptions` Query string that will fetch from `prs_ppe_descriptions` table.
         * @return string
         */
        public function selectPrsPpeDescriptions($id = false, $projectId = false)
        {
            $fields = [
                'PPD.id',
                'RT.name as request_type',
                'PR.prs_no',
                'IF(PR.project_id IS NULL, D.charging, IF(P.project_code IS NULL, PCR.temporary_project_code, P.project_code)) as charging',
                'IF(PPD.expense_type IS NULL, "-", PPD.expense_type) as expense_type',
                'MS.specs',
                'PPD.quantity',
                'PPD.unit_measurement as unit',
                'PPD.remarks',
                'PR.date_requested',
                'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as requested_by',
                'IF(PR.for_cancelation = "0", "CANCELATION FOR APPROVAL", IF(PR.for_cancelation = "1", "CANCELED", IF(PPD.status = "2", "RFQ", IF(PPD.status = "3", "WITHDRAWAL", IF(PPD.status = "4", "AOB", IF(PPD.status = "5", "PO", IF(PPD.status = "7", "ISSUANCE", IF(PPD.status = "9", "PETTY CASH", IF(PPD.status = "10", "HARD COPY", IF((PPD.status = "1" OR PPD.status IS NULL) AND PR.status = "2", "PENDING FOR APPROVAL", IF(PPD.status = "0", "DISAPPROVED", IF(PPD.status = "1", "APPROVED", IF(PPD.status = "12", "CANCELED", "-"))))))))))))) as current_status',
                'IF(PPD.status = "2", "Canvasser", IF(PR.status = "3" AND PPD.status = "1", "Supply Receiving", "-")) as pending_to',
                'PPD.status'
            ];

            $joins = [
                'purchase_requisitions PR'   => 'PR.id = PPD.purchase_requisition_id',
                'material_specifications MS' => 'MS.id = PPD.material_specification_id',
                'request_types RT'           => 'RT.id = PR.request_type_id',
                'users U'                    => 'PR.user_id = U.id',
                'personal_informations PI'   => 'U.personal_information_id = PI.id'
            ];

            $leftJoins = [
                'projects P'                => 'P.id = PR.project_id',
                'departments D'             => 'PR.department_id = D.id',
                'project_code_requests PCR' => 'PCR.project_id = P.id',
            ];

            $initQuery = $this->select($fields)
                              ->from('prs_ppe_descriptions PPD')
                              ->join($joins)
                              ->leftJoin($leftJoins)
                              ->where(['PPD.is_active' => ':is_active', 'PR.is_active' => ':is_active', 'MS.is_active' => ':is_active'])
                              ->andWhereIn('PR.status', ['1', '2', '3', '4', '5', '6', '7', '8']);
                              // ->andWhereNotIn('(PR.project_id', ['25', '26', '27', '24', '9', '10', '11', '12', '28', '29', '30', '31', '32', '33', '34', '35', '38', '39', '1', '7', '4', '2', '3', '36', '37', '23', '69'])
                              // ->logicEx(' OR PR.project_id IS NULL)');

            $initQuery = ($id)        ? $initQuery->andWhere(['PPD.id' => ':id'])       : $initQuery;
            $initQuery = ($projectId) ? $initQuery->andWhere(['P.id' => ':project_id']) : $initQuery;

            // $initQuery = $initQuery->logicEx(' LIMIT 2000, 4000');

            return $initQuery;
        }

        /**
         * `selectPersonalInformations` Query string that will select from table `personal_informations`.
         * @param  boolean $id
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
                'users U'                    => 'U.personal_information_id = PI.id'
            );

            $initQuery = $this->select($fields)
                              ->from('personal_informations PI')
                              ->join($joins)
                              ->where(array('PI.is_active' => ':is_active'));

            $initQuery = ($id)           ? $initQuery->andWhere(array('PI.id' => ':id'))                      : $initQuery;
            $initQuery = ($departmentId) ? $initQuery->andWhere(array('P.department_id' => ':department_id')) : $initQuery;
            $initQuery = ($isSignatory)  ? $initQuery->andWhere(array('P.is_signatory' => ':is_signatory'))   : $initQuery;
            $initQuery = ($userId)       ? $initQuery->andWhere(array('U.id' => ':user_id'))                  : $initQuery;

            return $initQuery;
        }

        /**
         * `selectPrToolSignatories` Query string that will select from table `pr_tool_signatories`.
         * @param  boolean $id
         * @param  boolean $pmmId
         * @return string
         */
        public function selectPrToolSignatories($id = false, $pmmId = false)
        {
            $fields = array(
                'PRTS.id',
                'PRTS.pr_tool_id',
                'PRTS.user_id',
                'PRTS.status',
                'IF(PRTS.comment IS NOT NULL, PRTS.comment, "") as remarks',
                'PRTS.updated_at',
                'CONCAT(PI.fname, " ", PI.lname, " ", PI.sname) as full_name',
                'P.name as position_name',
                'D.name as dept_name',
            );

            $joins = array(
                'users U'                    => 'U.id = PRTS.user_id',
                'personal_informations PI'   => 'PI.id = U.personal_information_id',
                'employment_informations EI' => 'EI.personal_information_id = PI.id',
                'positions P'                => 'P.id = EI.position_id',
                'departments D'              => 'D.id = P.department_id'
            );

            $initQuery = $this->select($fields)
                              ->from('pr_tool_signatories PRTS')
                              ->join($joins)
                              ->where(array('PRTS.is_active' => ':is_active'));

            $initQuery = ($id)    ? $initQuery->andWhere(array('PRTS.id' => ':id'))                 : $initQuery;
            $initQuery = ($pmmId) ? $initQuery->andWhere(array('PRTS.pr_tool_id' => ':pr_tool_id')) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectPrMedicalSignatories` Query string that will select from table `pr_medical_signatories`.
         * @param  boolean $id
         * @param  boolean $pmmId
         * @return string
         */
        public function selectPrMedicalSignatories($id = false, $pmmId = false)
        {
            $fields = array(
                'PS.id',
                'PS.pr_medical_material_id',
                'PS.user_id',
                'PS.status',
                'IF(PS.comment IS NOT NULL, PS.comment, "") as remarks',
                'PS.updated_at',
                'CONCAT(PI.fname, " ", PI.lname, " ", PI.sname) as full_name',
                'P.name as position_name',
                'D.name as dept_name',
            );

            $joins = array(
                'users U'                    => 'U.id = PS.user_id',
                'personal_informations PI'   => 'PI.id = U.personal_information_id',
                'employment_informations EI' => 'EI.personal_information_id = PI.id',
                'positions P'                => 'P.id = EI.position_id',
                'departments D'              => 'D.id = P.department_id'
            );

            $initQuery = $this->select($fields)
                              ->from('pr_medical_signatories PS')
                              ->join($joins)
                              ->where(array('PS.is_active' => ':is_active'));

            $initQuery = ($id)    ? $initQuery->andWhere(array('PS.id' => ':id'))                                         : $initQuery;
            $initQuery = ($pmmId) ? $initQuery->andWhere(array('PS.pr_medical_material_id' => ':pr_medical_material_id')) : $initQuery;

            return $initQuery;
        }
    }