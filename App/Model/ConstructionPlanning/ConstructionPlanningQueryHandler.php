<?php
    namespace App\Model\ConstructionPlanning;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class ConstructionPlanningQueryHandler extends QueryHandler {
        /**
         * `selectProjects` Query string that will fetch projects from table `projects`.
         * @return string
         */
        public function selectProjects($id = false)
        {
            $fields = [
                'P.id',
                'IF(P.project_code IS NULL, PCR.temporary_project_code, P.project_code) as project_code',
                'P.name',
                'P.location',
                'P.contract_days',
                'P.date_started',
                'P.transaction_id',
                'P.project_manager',
                'P.revision_no',
                // 'WD.name as pwd_name',
                '"" AS folder_icon',
                'T.status as transaction_status'
            ];

            $leftJoins = [
                // 'p_wds AS PWD'        => 'PWD.project_id = P.id',
                // 'work_disciplines WD' => 'WD.id = PWD.work_discipline_id',
                'transactions T'            => 'T.id = P.transaction_id',
                'project_code_requests PCR' => 'P.id = PCR.project_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('projects P')
                              ->leftJoin($leftJoins)
                              ->where(['P.is_active' => ':is_active']);
                              // ->where(['P.is_active' => ':is_active', 'PWD.is_active' => ':is_active', 'WD.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['P.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectPositions` Query string that will select from table `positions`
         * @param  boolean $id
         * @return string
         */
        public function selectPositions($id = false)
        {
            $fields = [
                'P.id',
                'P.name',
                'P.code',
                'P.default_rate'
            ];

            $initQuery = $this->select($fields)
                              ->from('positions P')
                              ->where(['P.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['P.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectMaterials` Query string that will select from table `materials`.
         * @param  boolean $id
         * @return string
         */
        public function selectMaterials($id = false)
        {
            $fields = [
                'M.id',
                'M.name'
            ];

            $initQuery = $this->select($fields)
                              ->from('materials M')
                              ->where(['M.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['P.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectMaterialSpecifications` Query string that will select from table `material_specifications`
         * @param  boolean $id
         * @return string
         */
        public function selectMaterialSpecifications($id = false)
        {
            $fields = [
                'MS.id',
                'MS.material_id',
                'MS.code',
                'MS.specs'
            ];

            $joins = [
                'material_specification_brands MSB' => 'MS.id = MSB.material_specification_id',
                'msb_suppliers MSBS'                => 'MSB.id = MSBS.material_specification_brand_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('material_specifications MS')
                              ->join($joins)
                              ->where(['MS.is_active' => ':is_active', 'MSB.is_active' => ':is_active', 'MSBS.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['MS.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectPwds` Query string that will select from table `p_wds`
         * @param  boolean $id
         * @param  boolean $projectId
         * @return string
         */
        public function selectPwds($id = false, $projectId = false)
        {
            $fields = [
                'PWD.id',
                'WD.id as wd_id',
                'WD.name as wd_name',
                'WD.wbs'
            ];

            $initQuery = $this->select($fields)
                              ->from('p_wds PWD')
                              ->join(['work_disciplines WD' => 'WD.id = PWD.work_discipline_id'])
                              ->where(['PWD.is_active' => ':is_active', 'WD.is_active' => ':is_active']);

            $initQuery = ($id)        ? $initQuery->andWhere(['PWD.id' => ':id'])                 : $initQuery;
            $initQuery = ($projectId) ? $initQuery->andWhere(['PWD.project_id' => ':project_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectPwSps` Query string that will select from table `pw_sps`.
         * @param  boolean $id
         * @param  boolean  $pwdId
         * @return string
         */
        public function selectPwSps($id = false, $pwdId = false)
        {
            $fields = [
                'PWSP.id',
                'PWSP.name',
                'SP.id as sp_id',
                'SP.name as sp_name',
                'SP.wbs'
            ];

            $initQuery = $this->select($fields)
                              ->from('pw_sps PWSP')
                              ->join(['sub_projects SP' => 'SP.id = PWSP.sub_project_id'])
                              ->where(['PWSP.is_active' => ':is_active', 'SP.is_active' => ':is_active']);

            $initQuery = ($id)    ? $initQuery->andWhere(['PWSP.id' => ':id'])           : $initQuery;
            $initQuery = ($pwdId) ? $initQuery->andWhere(['PWSP.p_wd_id' => ':p_wd_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectPsSwiDirects` Query string that will select from table `ps_swi_directs`.
         * @param  boolean $id
         * @param  boolean  $pwSpId
         * @return string
         */
        public function selectPsSwiDirects($id = false, $pwSpId = false, $revNo = false)
        {
            $fields = [
                'PSWID.id',
                'PSWID.quantities',
                'PSWID.weight_factor',
                'SWI.id as swi_id',
                'SWI.alternative_name as swi_name',
                'SWI.unit as swi_unit',
                'WIC.id as wic_id',
                'WIC.name as wic_name',
                'WIC.part',
                'WI.id as wi_id',
                'WI.name as wi_name',
                'WI.item_no',
                'WI.unit',
                'WI.direct',
                'SWIC.id as swic_id',
                'SWIC.wbs as wic_wbs',
                'SWI.wbs as swi_wbs',
            ];

            $joins = [
                'sw_wis SWI'               => 'SWI.id = PSWID.sw_wi_id',
                'spt_wics SWIC'            => 'SWIC.id = SWI.spt_wic_id',
                'work_item_categories WIC' => 'WIC.id = SWIC.work_item_category_id',
                'work_items WI'            => 'WI.id = SWI.work_item_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('ps_swi_directs PSWID')
                              ->join($joins)
                              ->where(['PSWID.is_active' => ':is_active', 'SWI.is_active' => ':is_active', 'SWIC.is_active' => ':is_active', 'WIC.is_active' => ':is_active', 'WI.is_active' => ':is_active']);

            $initQuery = ($id)     ? $initQuery->andWhere(['PSWID.id' => ':id'])                   : $initQuery;
            $initQuery = ($pwSpId) ? $initQuery->andWhere(['PSWID.pw_sp_id' => ':pw_sp_id'])       : $initQuery;
            $initQuery = ($revNo)  ? $initQuery->andWhere(['PSWID.revision_no' => ':revision_no']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectPwiIndirects` Query string that will select from table `p_wi_indirects`
         * @param  boolean $id
         * @param  boolean $projectId
         * @return string
         */
        public function selectPwiIndirects($id = false, $projectId = false, $revNo = false)
        {
            $fields = [
                'PWII.id',
                'PWII.quantities',
                'PWII.weight_factor',
                'WIC.id as wic_id',
                'WIC.name as wic_name',
                'WI.id as wi_id',
                'WI.name as wi_name',
                'WI.item_no',
                'WI.unit',
                'WI.direct',
                'WIC.part',
                'WIC.code as wic_wbs',
                'WI.wbs as wi_wbs'
            ];

            $joins = [
                'work_items WI'            => 'PWII.work_item_id = WI.id',
                'work_item_categories WIC' => 'WI.work_item_category_id = WIC.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('p_wi_indirects PWII')
                              ->join($joins)
                              ->where(['PWII.is_active' => ':is_active', 'WI.is_active' => ':is_active', 'WIC.is_active' => ':is_active']);

            $initQuery = ($id)        ? $initQuery->andWhere(['PWII.id' => ':id'])                   : $initQuery;
            $initQuery = ($projectId) ? $initQuery->andWhere(['PWII.project_id' => ':project_id'])   : $initQuery;
            $initQuery = ($revNo)     ? $initQuery->andWhere(['PWII.revision_no' => ':revision_no']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectMsbSuppliers` Query string that will select from table `msb_suppliers`.
         * @return string
         */
        public function selectMsbSuppliers()
        {
            $fields = [
                'MSBS.id',
                'MSBS.code as material_code',
                'MS.specs',
                'LOWER(M.name) as material_name',
                'MC.name as material_category_name',
                'MU.unit',
                // 'MSBS.unit',
            ];

            $joins = [
                'material_specification_brands MSB' => 'MSB.id = MSBS.material_specification_brand_id',
                'material_specifications MS'        => 'MS.id = MSB.material_specification_id',
                'materials M'                       => 'M.id = MS.material_id',
                'material_categories MC'            => 'MC.id = M.material_category_id',
                'material_units MU'                 => 'MU.id = MSBS.material_unit_id',
            ];

            $orWhereCondition = array(
                'MS.specs'                   => ':search',
                'M.name'                     => ':search',
                'MC.name'                    => ':search',
                'CONCAT(MS.code, MSBS.code)' => ':search',
            );

            $initQuery = $this->select($fields)
                              ->from('msb_suppliers MSBS')
                              ->join($joins)
                              ->where(['MSBS.is_active' => ':is_active', 'MSB.is_active' => ':is_active', 'MS.is_active' => ':is_active'])
                              ->andWhereNotEqual(['M.id' => '516'])
                              ->logicEx('AND')
                              ->orWhereLike($orWhereCondition);

            return $initQuery;
        }

        /**
         * `selectPsdMaterials` Query string that will select from table `psd_materials`
         * @param  boolean $id
         * @param  boolean $psSwiDirectId
         * @return string
         */
        public function selectPsdMaterials($id = false, $psSwiDirectId = false)
        {
            $fields = [
                'PSDM.id',
                'PSDM.total_quantity as material_quantity',
                'PSDM.unit_cost',
                'PSDM.total_cost',
                'MSB.id as msb_id',
                'MSBS.code as material_code',
                'MS.id as material_specification_id',
                'MS.specs',
                'LOWER(M.name) as material_name',
                'MC.name as material_category_name',
                'MU.unit',
                // 'MSBS.unit',
                '"1" as direct',
                '"!empty" as status',
            ];

            $joins = [
                'msb_suppliers MSBS'                => 'MSBS.id = PSDM.msb_supplier_id',
                'material_specification_brands MSB' => 'MSB.id = MSBS.material_specification_brand_id',
                'material_specifications MS'        => 'MS.id = MSB.material_specification_id',
                'materials M'                       => 'M.id = MS.material_id',
                'material_categories MC'            => 'MC.id = M.material_category_id',
                'material_units MU'                 => 'MU.id = MSBS.material_unit_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('psd_materials PSDM')
                              ->join($joins)
                              ->where(['PSDM.is_active' => ':is_active', 'MS.is_active' => ':is_active']);

            $initQuery = ($id)            ? $initQuery->andWhere(['PSDM.id' => ':id'])                             : $initQuery;
            $initQuery = ($psSwiDirectId) ? $initQuery->andWhere(['PSDM.ps_swi_direct_id' => ':ps_swi_direct_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectPwiMaterials` Query string that will select from table `pwi_materials`
         * @param  boolean $id
         * @param  boolean $pwiIndirectId
         * @return string
         */
        public function selectPwiMaterials($id = false, $pwiIndirectId = false, $psSwiDirectId = false)
        {
            $fields = [
                'PWIM.id',
                'PWIM.total_quantity as material_quantity',
                'PWIM.unit_cost',
                'PWIM.total_cost',
                'MSB.id as msb_id',
                'MSBS.code as material_code',
                'MS.id as material_specification_id',
                'MS.specs',
                'LOWER(M.name) as material_name',
                'MC.name as material_category_name',
                'MU.unit',
                // 'MSBS.unit',
                '"0" as direct',
                '"!empty" as status',
            ];

            $joins = [
                'msb_suppliers MSBS'                => 'MSBS.id = PWIM.msb_supplier_id',
                'material_specification_brands MSB' => 'MSB.id = MSBS.material_specification_brand_id',
                'material_specifications MS'        => 'MS.id = MSB.material_specification_id',
                'materials M'                       => 'M.id = MS.material_id',
                'material_categories MC'            => 'MC.id = M.material_category_id',
                'material_units MU'                 => 'MU.id = MSBS.material_unit_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('pwi_materials PWIM')
                              ->join($joins)
                              ->where(['PWIM.is_active' => ':is_active', 'MS.is_active' => ':is_active']);

            $initQuery = ($id)            ? $initQuery->andWhere(['PWIM.id' => ':id'])                             : $initQuery;
            $initQuery = ($pwiIndirectId) ? $initQuery->andWhere(['PWIM.p_wi_indirect_id' => ':p_wi_indirect_id']) : $initQuery;
            $initQuery = ($psSwiDirectId) ? $initQuery->andWhere(['PWIM.ps_swi_direct_id' => ':ps_swi_direct_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectProjectMaterialDeliverySequences` Query string that will select from table `proejct_material_delivery_sequences`.
         * @param  boolean $id
         * @param  boolean $psdMaterialId
         * @param  boolean $pwiMaterialId
         * @return string
         */
        public function selectProjectMaterialDeliverySequences($id = false, $psdMaterialId = false, $pwiMaterialId = false)
        {
            $fields = [
                'PMDS.sequence_no',
                'DATE_FORMAT(PMDS.delivery_date, "%m/%d/%Y") as delivery_date',
                'PMDS.quantity'
            ];

            $initQuery = $this->select($fields)
                              ->from('project_material_delivery_sequences PMDS')
                              ->where(['PMDS.is_active' => ':is_active']);

            $initQuery = ($id)            ? $initQuery->andWhere(['PMDS.id' => ':id'])                           : $initQuery;
            $initQuery = ($psdMaterialId) ? $initQuery->andWhere(['PMDS.psd_material_id' => ':psd_material_id']) : $initQuery;
            $initQuery = ($pwiMaterialId) ? $initQuery->andWhere(['PMDS.pwi_material_id' => ':pwi_material_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectEquipment` Query string that will select all equipments.
         * @return string
         */
        public function selectEquipment()
        {
            $fields = [
                'E.id',
                'E.equipment_type_id',
                'E.make',
                'E.model',
                'E.body_no',
                'E.cost_code',
                'E.brand',
                'E.capacity',
                'E.capacity_unit',
                'E.file',
                'E.equipment_status',
                'E.total_smr',
                'E.status',
            ];

            $initQuery = $this->select($fields)
                              ->from('equipments E');

            return $initQuery;
        }

        /**
         * `selectLightEquipments` Query string that will select all light/small equipments.
         * @param  boolean $id
         * @return string
         */
        public function selectLightEquipments($id = false)
        {
            $fields = [
                'SE.id',
                'SE.equipment_type_id',
                'SE.make',
                'SE.model',
                'SE.serial_no',
                'SE.body_no',
                'SE.cost_code',
                'SE.brand',
                'SE.capacity',
                'SE.capacity_unit',
                'SE.file',
                'SE.equipment_status',
                'SE.status',
            ];

            $initQuery = $this->select($fields)
                              ->from('small_equipments SE')
                              ->where(['SE.status' => ':status']);

            $initQuery = ($id) ? $initQuery->andWhere(['SE.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectPsdEquipments` Query string that will select from table `psd_equipments`.
         * @param  boolean $id
         * @param  boolean $psSwiDirectId
         * @return string
         */
        public function selectPsdEquipments($id = false, $psSwiDirectId = false)
        {
            $fields = [
                'PE.id',
                'PE.ps_swi_direct_id',
                'PE.equipment_type_id',
                'PE.capacity',
                'PE.no_of_equipment',
                'PE.duration',
                'PE.equipment_days',
                'PE.rental_rate',
                'PE.total as total_cost',
                'PE.is_active',
                'DATE_FORMAT(PE.mob_plan_from,  "%m/%d/%Y") as mob_plan_dateFrom',
                'DATE_FORMAT(PE.mob_plan_to,  "%m/%d/%Y") as mob_plan_dateTo',
                'ET.cost_code as equipment_code',
                'ET.name as equipment_desc',
                'ET.unit',
                '"1" as direct',
            ];

            $joins = array(
                'equipment_types ET' => 'ET.id = PE.equipment_type_id'
            );

            $initQuery = $this->select($fields)
                              ->from('psd_equipments PE')
                              ->join($joins)
                              ->where(['PE.is_active' => ':is_active', 'ET.is_active' => ':is_active']);

            $initQuery = ($id)            ? $initQuery->andWhere(['PE.id' => ':id'])                             : $initQuery;
            $initQuery = ($psSwiDirectId) ? $initQuery->andWhere(['PE.ps_swi_direct_id' => ':ps_swi_direct_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectTransactionComments` Query string that will select from table `transaction_comments`.
         * @param  string $id
         * @param  string $transactionId
         * @return string
         */
        public function selectTransactionComments($id = '', $transactionId = '')
        {
            $fields = [
                'TC.id',
                'TC.reference_id',
                'TC.comment',
                'TC.comment_by',
                'TC.created_at',
                'EI.position_id',
                'P.name as position_name',
                'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as full_name',
            ];

            $joins = [
                'users U'                    => 'TC.comment_by = U.id',
                'personal_informations PI'   => 'U.personal_information_id = PI.id',
                'employment_informations EI' => 'PI.id = EI.personal_information_id',
                'positions P'                => 'EI.position_id = P.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('transaction_comments TC')
                              ->join($joins)
                              ->where(['P.is_active' => ':is_active']);

            $initQuery = ($id)            ? $initQuery->andWhere(['TC.id' => ':id'])                         : $initQuery;
            $initQuery = ($transactionId) ? $initQuery->andWhere(['TC.transaction_id' => ':transaction_id']) : $initQuery;

            $initQuery = $initQuery->orderBy('TC.id', 'ASC');

            return $initQuery;
        }

        /**
         * `selectSignatories` Query string that will select from table `signatories`.
         * @param  string $id
         * @param  string $transactionId
         * @return string
         */
        public function selectSignatories($id = false, $transactionId = false)
        {
            $fields = [
                'S.id',
                'S.action',
                'P.name as position_name',
                'P.code as position_code',
                'P.id as position_id',
                'CONCAT(PI.fname, " ", PI.lname) as full_name',
                'U.id as user_id',
            ];

            $joins = [
                'signatory_sets SS'          => 'SS.id = S.signatory_set_id',
                'transactions T'             => 'T.signatory_set_id = SS.id',
                'employment_informations EI' => 'S.position_id = EI.position_id',
                'personal_informations PI'   => 'EI.personal_information_id = PI.id',
                'users U'                    => 'PI.id = U.personal_information_id',
                'positions P'                => 'P.id = EI.position_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('signatories S')
                              ->join($joins)
                              ->where(['S.is_active' => ':is_active', 'SS.is_active' => ':is_active', 'T.is_active' => ':is_active']);

            $initQuery = ($id)            ? $initQuery->andWhere(['S.id' => ':id'])             : $initQuery;
            $initQuery = ($transactionId) ? $initQuery->andWhere(['T.id' => ':transaction_id']) : $initQuery;

            $initQuery = $initQuery->orderBy('S.level', 'ASC');

            return $initQuery;
        }

        /**
         * `selectTransactionApprovals` Query string that will select from table `signatories`.
         * @param  boolean $id
         * @param  boolean $transactionId
         * @param  boolean $userId
         * @return string
         */
        public function selectTransactionApprovals($id = false, $transactionId = false, $userId = false)
        {
            $fields = [
                'TA.id',
                'TA.status',
                'TA.date_sended',
                'TA.date_approved',
            ];

            $initQuery = $this->select($fields)
                              ->from('transaction_approvals TA');

            $initQuery = ($id)            ? $initQuery->where(['TA.id' => ':id'])                         : $initQuery;
            $initQuery = ($transactionId) ? $initQuery->where(['TA.transaction_id' => ':transaction_id']) : $initQuery;
            $initQuery = ($userId)        ? $initQuery->andWhere(['TA.current_signatory' => ':user_id'])  : $initQuery;

            return $initQuery;
        }

        public function selectLaborCost($project_id = false, $id = false)
        {
            $fields = array(
                'LC.id',
                'LC.project_id',
                'LC.position_id',
                'LC.manpower',
                'LC.duration',
                'DATE_FORMAT(LC.date_from, "%m/%d/%Y") as date_from',
                'DATE_FORMAT(LC.date_to, "%m/%d/%Y") as date_to',
                'LC.labor_composition',
                'LC.unit_cost_rate',
                'LC.total_cost',
                'LC.total_man_days',
                '"saved" as data_status'
            );

            $initQuery = $this->select($fields)
                              ->from('labor_cost LC')
                              ->where(array('LC.is_active' => ':is_active'));

            $initQuery = ($project_id) ? $initQuery->andWhere(array('LC.project_id' => ':project_id')) : $initQuery;
            $initQuery = ($id) ? $initQuery->andWhere(array('LC.id' => ':id')) : $initQuery;

            return $initQuery;
        }

        public function selectMobilizationPlan($labor_cost_id = false, $id = false)
        {
            $fields = array(
                'MP.id',
                'MP.labor_cost_id',
                'DATE_FORMAT(MP.date, "%m/%d/%Y") as date_of_work',
                'MP.man_days',
                '"saved" as data_status'
            );

            $initQuery = $this->select($fields)
                              ->from('mobilization_plan MP')
                              ->where(array('MP.is_active' => ':is_active'));

            $initQuery = ($labor_cost_id) ? $initQuery->andWhere(array('MP.labor_cost_id' => ':labor_cost_id')) : $initQuery;
            $initQuery = ($id) ? $initQuery->andWhere(array('MP.id' => ':id')) : $initQuery;

            return $initQuery;
        }

        public function selectPsdLabor($ps_swi_direct_id = false, $p_wi_indirect_id = false)
        {
            $fields = array(
                'PL.id',
                'PL.ps_swi_direct_id',
                'PL.p_wi_indirect_id',
                'IF(PL.ps_swi_direct_id IS NULL, 0,PL.skilled_workers) as indirect_skilled_workers',
                'IF(PL.p_wi_indirect_id IS NULL, 0,PL.skilled_workers) as direct_skilled_workers',
                'IF(PL.ps_swi_direct_id IS NULL, 0,PL.common_workers) as indirect_common_workers',
                'IF(PL.p_wi_indirect_id IS NULL, 0,PL.common_workers) as direct_common_workers',
                // 'PL.common_workers',
                'PL.labor_composition_skilled',
                'PL.labor_composition_common',
                'PL.duration',
                'DATE_FORMAT(PL.date_from, "%m/%d/%Y") as date_from',
                'DATE_FORMAT(PL.date_to, "%m/%d/%Y") as date_to',
                'PL.unit_cost_rate_skilled',
                'PL.unit_cost_rate_common',
                'PL.total_cost_skilled',
                'PL.total_cost_common',
                'PL.total_cost',
                'PL.total_mandays',
                '"saved" as data_status'
            );

            $initQuery = $this->select($fields)
                              ->from('psd_labors PL')
                              ->where(array('PL.is_active' => ':is_active'));

            $initQuery = ($ps_swi_direct_id) ? $initQuery->andWhere(array('PL.ps_swi_direct_id' => ':ps_swi_direct_id')) : $initQuery;
            $initQuery = ($p_wi_indirect_id) ? $initQuery->andWhere(array('PL.p_wi_indirect_id' => ':p_wi_indirect_id')) : $initQuery;

            return $initQuery;
        }

        public function selectPsdLaborMobilizationPlan($psd_labor_id = false)
        {
            $fields = array(
                'LMP.id',
                'LMP.psd_labor_id',
                'DATE_FORMAT(LMP.working_date, "%m/%d/%Y") as working_date',
                'LMP.man_days',
                '"saved" as data_status'
            );

            $initQuery = $this->select($fields)
                              ->from('psd_labor_mobilization LMP')
                              ->where(array('LMP.is_active' => ':is_active'));

            $initQuery = ($psd_labor_id) ? $initQuery->andWhere(array('LMP.psd_labor_id' => ':psd_labor_id')) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectPwiEquipments` Query string that will select from table `pwi_equipments`
         * @param  boolean $id
         * @param  boolean $pwiIndirectId
         * @return string
         */
        public function selectPwiEquipments($id = false, $pwiIndirectId = false, $psSwiDirectId = false)
        {
            $fields = [
                'PWIE.id',
                'PWIE.mat_arr',
                'PWIE.no_of_equipment',
                'PWIE.duration',
                'PWIE.capacity',
                'PWIE.equipment_days',
                'DATE_FORMAT( PWIE.mob_plan_from,  "%m/%d/%Y") as mob_plan_dateFrom',
                'DATE_FORMAT( PWIE.mob_plan_to,  "%m/%d/%Y") as mob_plan_dateTo',
                'PWIE.rental_rate',
                'PWIE.total as total_cost',
                'ET.cost_code as equipment_code',
                'ET.name as equipment_desc',
                'ET.unit',
                '"0" as direct'
            ];

            $joins = [
                'equipment_types ET' => 'ET.id = PWIE.equipment_type_id',
            ];

            $initQuery = $this->select($fields)
                              ->from('pwi_equipments PWIE')
                              ->join($joins)
                              ->where(['PWIE.is_active' => ':is_active']);

            $initQuery = ($id)            ? $initQuery->andWhere(['PWIE.id' => ':id'])                             : $initQuery;
            $initQuery = ($pwiIndirectId) ? $initQuery->andWhere(['PWIE.p_wi_indirect_id' => ':p_wi_indirect_id']) : $initQuery;
            $initQuery = ($psSwiDirectId) ? $initQuery->andWhere(['PWIE.ps_swi_direct_id' => ':ps_swi_direct_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectPsdLabors` Query string that will select from table `psd_materials`
         * @param  boolean $id
         * @param  boolean $psSwiDirectId
         * @return string
         */
        public function selectPsdLabors($id = false, $psSwiDirectId = false)
        {
            $fields = [
                'PSDM.id',
                'PSDM.total_quantity as material_quantity',
                'PSDM.unit_cost',
                'PSDM.total_cost',
                'MSBS.code as material_code',
                'MS.specs',
                'LOWER(M.name) as material_name',
                'MC.name as material_category_name',
                'MU.unit',
                // 'MSBS.unit',
                '"1" as direct'
            ];

            $joins = [
                'msb_suppliers MSBS'                => 'MSBS.id = PSDM.msb_supplier_id',
                'material_specification_brands MSB' => 'MSB.id = MSBS.material_specification_brand_id',
                'material_specifications MS'        => 'MS.id = MSB.material_specification_id',
                'materials M'                       => 'M.id = MS.material_id',
                'material_categories MC'            => 'MC.id = M.material_category_id',
                'material_units MU'                 => 'MU.id = MSBS.material_unit_id',
            ];

            $initQuery = $this->select($fields)
                              ->from('psd_materials PSDM')
                              ->join($joins)
                              ->where(['PSDM.is_active' => ':is_active', 'MS.is_active' => ':is_active']);

            $initQuery = ($id)            ? $initQuery->andWhere(['PSDM.id' => ':id'])                             : $initQuery;
            $initQuery = ($psSwiDirectId) ? $initQuery->andWhere(['PSDM.ps_swi_direct_id' => ':ps_swi_direct_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectHeavyEquipmentDistinct` Query string that will select from table `heavy_equipments`.
         * @param  boolean $id
         * @return string
         */
        public function selectHeavyEquipmentDistinct($id = false)
        {
            $fields = [
                'DISTINCT HE.equipment_type_id, HE.capacity',
                'HE.c_unit as capacity_unit',
                'ET.name as equipment_type_name',
                'ET.cost_code'
            ];

            $joins = [
                'equipment_types ET' => 'ET.id = HE.equipment_type_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('heavy_equipments HE')
                              ->join($joins)
                              ->where(['HE.status' => ':status', 'ET.is_active' => ':status']);

            $initQuery = ($id) ? $initQuery->andWhere(['HE.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectLightEquipmentDistinct` Query string that will select from table `light_equipments`.
         * @param  boolean $id
         * @return string
         */
        public function selectLightEquipmentDistinct($id = false)
        {
            $fields = [
                'DISTINCT LE.equipment_type_id, LE.capacity',
                'LE.capacity_unit',
                'ET.name as equipment_type_name',
                'ET.cost_code'
            ];

            $joins = [
                'equipment_types ET' => 'ET.id = LE.equipment_type_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('small_equipments LE')
                              ->join($joins)
                              ->where(['LE.status' => ':status', 'ET.is_active' => ':status']);

            $initQuery = ($id) ? $initQuery->andWhere(['LE.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectProjectAccesses` Query string that will select from table `construction_planning_accesses`.
         * @param  boolean $id
         * @return string
         */
        public function selectProjectAccesses($id = false)
        {
            $fields = [
                'PA.id',
                'PA.user_id',
                'PA.project_id',
                'PA.level'
            ];

            $initQuery = $this->select($fields)
                              ->from('project_accesses PA')
                              ->where(['PA.is_active' => ':is_active', 'user_id' => ':user_id']);

            $initQuery = ($id) ? $initQuery->andWhere(['PA.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectConstructionPlanningLogs` Query string that will select from table `construction_planning_logs`.
         * @param  boolean $id
         * @param  boolean $userId
         * @return string
         */
        public function selectConstructionPlanningLogs($id = false, $userId = false)
        {
            $fields = [
                'CPL.id',
                'CPL.project_id',
                'CPL.file_type',
                'CPL.work_item_name',
                'CPL.subject_name',
                'CPL.column_name',
                'CPL.previous_value',
                'CPL.present_value',
                'CPL.action',
                'CPL.created_by',
                'CPL.created_at',
                'CONCAT(PI.fname, " ", PI.lname) as full_name',
                'P.name as project_name',
                'P.project_code'
            ];

            $joins = [
                'projects P'               => 'P.id = CPL.project_id',
                'users U'                  => 'CPL.created_by = U.id',
                'personal_informations PI' => 'U.personal_information_id = PI.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('construction_planning_logs CPL')
                              ->join($joins)
                              ->where(['CPL.is_active' => ':is_active']);

            $initQuery = ($id)     ? $initQuery->andWhere(['CPL.id' => ':id'])              : $initQuery;
            $initQuery = ($userId) ? $initQuery->andWhere(['CPL.created_by' => ':user_id']) : $initQuery;

            $initQuery = $initQuery->orderBy('CPL.id', 'DESC');

            return $initQuery;
        }

        /**
         * `selectPwiTools` Query string that will select from table `pwi_materials`
         * @param  boolean $id
         * @param  boolean $pwiIndirectId
         * @return string
         */
        public function selectPwiTools($id = false, $pwiIndirectId = false, $psSwiDirectId = false)
        {
            $fields = [
                'PWIT.id',
                'PWIT.total_quantity as material_quantity',
                'PWIT.unit_cost',
                'PWIT.total_cost',
                'IF(PWIT.power_tool_id IS NULL, PWIT.hand_tool_id, PWIT.power_tool_id) as tool_id',
                'IF(PWIT.power_tool_id IS NULL, HT.code, PT.code) as tool_code',
                'IF(PWIT.power_tool_id IS NULL, HT.specification, PT.code) as tool_code',
                // 'MSB.id as msb_id',
                // 'MSBS.code as material_code',
                // 'MS.id as material_specification_id',
                // 'MS.specs',
                'LOWER(M.name) as material_name',
                'MC.name as material_category_name',
                'MU.unit',
                // 'MSBS.unit',
                '"0" as direct',
                '"!empty" as status',
            ];

            $joins = [
                'msb_suppliers MSBS'                => 'MSBS.id = PWIM.msb_supplier_id',
                'material_specification_brands MSB' => 'MSB.id = MSBS.material_specification_brand_id',
                'material_specifications MS'        => 'MS.id = MSB.material_specification_id',
                'materials M'                       => 'M.id = MS.material_id',
                'material_categories MC'            => 'MC.id = M.material_category_id',
                'material_units MU'                 => 'MU.id = MSBS.material_unit_id'
            ];

            $leftJoins = [
                'power_tools PT' => 'PT.id = PWIT.power_tool_id',
                'hand_tools HT' => 'PWIT.hand_tool_id = HT.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('pwi_tools PWIT')
                              ->leftJoin($leftJoins)
                              ->join($joins)
                              ->where(['PWIT.is_active' => ':is_active', 'MS.is_active' => ':is_active']);

            $initQuery = ($id)            ? $initQuery->andWhere(['PWIT.id' => ':id'])                             : $initQuery;
            $initQuery = ($pwiIndirectId) ? $initQuery->andWhere(['PWIT.p_wi_indirect_id' => ':p_wi_indirect_id']) : $initQuery;
            $initQuery = ($psSwiDirectId) ? $initQuery->andWhere(['PWIT.ps_swi_direct_id' => ':ps_swi_direct_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `insertPsdMaterial` Query string that will insert to table `psd_materials`
         * @return string
         */
        public function insertPsdMaterial($data = [])
        {
            $initQuery = $this->insert('psd_materials', $data);

            return $initQuery;
        }

        /**
         * `insertPsdEquipment` Query string that will insert to table `psd_equipments`
         * @return string
         */
        public function insertPsdEquipment($data = [])
        {
            $initQuery = $this->insert('psd_equipments', $data);

            return $initQuery;
        }
        /**
         * `insertPwiEquipment` Query string that will insert to table `pwi_equipments`
         * @return string
         */
        public function insertPwiEquipment($data = [])
        {
            $initQuery = $this->insert('pwi_equipments', $data);

            return $initQuery;
        }

        /**
         * `insertPwiMaterial` Query string that will insert to table `pwi_materials`
         * @return string
         */
        public function insertPwiMaterial($data = [])
        {
            $initQuery = $this->insert('pwi_materials', $data);

            return $initQuery;
        }

        /**
         * `insertProjectMaterialDeliverySequence` Query string that will insert to table `project_material_delivery_sequences`
         * @param  array  $data
         * @return string
         */
        public function insertProjectMaterialDeliverySequence($data = [])
        {
            $initQuery = $this->insert('project_material_delivery_sequences', $data);

            return $initQuery;
        }

        public function insertLaborCost($data = [])
        {
            $initQuery = $this->insert('labor_cost', $data);

            return $initQuery;
        }

        public function insertMobilizationPlan($data = [])
        {
            $initQuery = $this->insert('mobilization_plan', $data);

            return $initQuery;
        }

        public function insertPsdLabor($data = array())
        {
            $initQuery = $this->insert('psd_labors', $data);

            return $initQuery;
        }

        public function insertPsdLaborMobilization($data = array())
        {
            $initQuery = $this->insert('psd_labor_mobilization', $data);

            return $initQuery;
        }

        /**
         * `insertConstructionPlanningLogs` Query string that will insert information from table `construction_planning_logs`.
         * @param  array  $data
         * @return string
         */
        public function insertConstructionPlanningLogs($data = array())
        {
            $initQuery = $this->insert('construction_planning_logs', $data);

            return $initQuery;
        }

        /**
         * `updatePsdMaterial` Query string that will update specific event information from table `psd_materials`
         * @return string
         */
        public function updatePsdMaterial($id = '', $data = [])
        {
            $initQuery = $this->update('psd_materials', $id, $data);

            return $initQuery;
        }

          /**
         * `updatePsdEquipment` Query string that will update specific event information from table `psd_materials`
         * @return string
         */
        public function updatePsdEquipment($id = '', $data = [])
        {
            $initQuery = $this->update('psd_equipments', $id, $data);

            return $initQuery;
        }

        /**
         * `updatePwiEquipment` Query string that will update specific event information from table `psd_materials`
         * @return string
         */
        public function updatePwiEquipment($id = '', $data = [])
        {
            $initQuery = $this->update('pwi_equipments', $id, $data);

            return $initQuery;
        }

        /**
         * `updatePwiMaterial` Query string that will update specific event information from table `pwi_materials`
         * @return string
         */
        public function updatePwiMaterial($id = '', $data = [])
        {
            $initQuery = $this->update('pwi_materials', $id, $data);

            return $initQuery;
        }

        /**
         * `updateProject` Query string that will update specific event information from table `projects`
         * @return string
         */
        public function updateProject($id = '', $data = [])
        {
            $initQuery = $this->update('projects', $id, $data);

            return $initQuery;
        }

        public function updateLaborCost($id = '', $data = [])
        {
            $initQuery = $this->update('labor_cost', $id, $data);

            return $initQuery;
        }

        public function updateMobilizationPlan($id = '', $data = [])
        {
            $initQuery = $this->update('mobilization_plan', $id, $data);

            return $initQuery;
        }

        public function updatePsdLabor($id = '', $data = array())
        {
            $initQuery = $this->update('psd_labors', $id, $data);

            return $initQuery;
        }

        public function updatePsdLaborMobilization($id = '', $data = array())
        {
            $initQuery = $this->update('psd_labor_mobilization', $id, $data);

            return $initQuery;
        }

        /**
         * `deleteProjectMaterialDeliverySequence` Query string that will delete specific delivery sequence from `project_material_delivery_sequences`.
         * @param  boolean $id
         * @param  boolean $psdMaterialId
         * @param  boolean $pwiMaterialId
         * @return string
         */
        public function deleteProjectMaterialDeliverySequence($id = false, $psdMaterialId = false, $pwiMaterialId = false)
        {
            $whereCondition = [];

            ($id)            ? $whereCondition['id'] = ':id'                           : '';
            ($psdMaterialId) ? $whereCondition['psd_material_id'] = ':psd_material_id' : '';
            ($pwiMaterialId) ? $whereCondition['pwi_material_id'] = ':pwi_material_id' : '';

            $initQuery = $this->delete('project_material_delivery_sequences')
                              ->where($whereCondition);

            return $initQuery;
        }
    }