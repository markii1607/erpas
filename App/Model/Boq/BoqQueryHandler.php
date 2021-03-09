<?php 
    namespace App\Model\Boq;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class BoqQueryHandler extends QueryHandler { 
        /**
         * `selectProjects` Query string that will fetch project.
         * @return string
         */
        public function selectProjects($id = false, $name = false, $projectCode = false)
        {
            $fields = [
                'P.id',
                'P.revision_no',
                'P.project_code',
                'P.name',
                'P.location',
                'P.contract_days',
                'DATE_FORMAT(P.updated_at, "%m/%d/%Y") as date_prepared',
                'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as prepared_by',
                '"" AS action',
                'PCR.temporary_project_code as temporary_code',
                'P.project_manager as pm_id',
                'CONCAT(PMPI.fname, " ", PMPI.mname, " ", PMPI.lname) as pm_name',
            ];

            $joins = [
                'users U'                   => 'U.id = P.updated_by',
                'personal_informations PI'  => 'PI.id = U.personal_information_id',
            ];

            $leftJoins = [
                'project_code_requests PCR'  => 'P.id = PCR.project_id',
                'personal_informations PMPI' => 'PMPI.id = P.project_manager'
            ];

            $initQuery = $this->select($fields)
                              ->from('projects P')
                              ->join($joins)
                              ->leftJoin($leftJoins)
                              ->where(['P.is_active' => ':is_active']);

            $initQuery = ($id)          ? $initQuery->andWhere(['P.id' => ':id'])                     : $initQuery;
            $initQuery = ($name)        ? $initQuery->andWhere(['P.name' => ':name'])                 : $initQuery;
            $initQuery = ($projectCode) ? $initQuery->andWhere(['P.project_Code' => ':project_code']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectUsers` Query String that will select from table `users` join `employees` and `positions`
         * @return string
         */
        public function selectUsers()
        {
            $fields = [
                'PI.id',
                'CONCAT(PI.fname," ",PI.mname," ",PI.lname) as full_name'
            ];

            $joins = [
                'personal_informations PI'   => 'U.personal_information_id = PI.id',
                'employment_informations EI' => 'PI.id = EI.personal_information_id',
                'positions P'                => 'EI.position_id = P.id'
            ];

            $orOneFieldConditions = [
                3,
                8,
                9,
                17,
                18,
                28,
                31,
                41,
                42,
                60,
                93,
                94,
                194,
                195
            ];

            $initQuery = $this->select($fields)
                              ->from('users U')
                              ->join($joins)
                              ->whereOrOneField('P.id', $orOneFieldConditions);

            return $initQuery;
        }

        /**
         * `selectSubProjects` Query string that will fetch sub project.
         * @return string
         */
        public function selectSubProjects($id = false, $name = false)
        {
            $fields = [
                'SP.id',
                'SP.wbs',
                'SP.name',
                'WD.id as wd_id',
                'WD.name as wd_name',
                'WD.wbs as wd_wbs',
            ];

            $initQuery = $this->select($fields)
                              ->from('sub_projects SP')
                              ->join(['work_disciplines WD' => 'WD.id = SP.work_discipline_id'])
                              ->where(['SP.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['SP.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectSwWis` Query string that will fetch swis.
         * @return string
         */
        public function selectSwWis($id = false, $name = false)
        {
            $fields = [
                'SWI.id',
                'SWI.alternative_name as name',
                'SWI.unit',
                'WI.wbs as wi_wbs',
                'WI.item_no',
                'WI.name as wi_name',
                'SWIC.id as swic_id',
                'SWIC.sub_project_id',
                'WIC.name as wic_name',
                'WIC.code as wic_wbs',
            ];

            $joins = [
                'work_items WI'            => 'WI.id = SWI.work_item_id',
                'spt_wics SWIC'            => 'SWI.spt_wic_id = SWIC.id',
                'work_item_categories WIC' => 'SWIC.work_item_category_id = WIC.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('sw_wis SWI')
                              ->join($joins)
                              ->where(['SWI.is_active' => ':is_active', 'WI.direct' => ':direct']);

            $initQuery = ($id) ? $initQuery->andWhere(['SWI.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectWorkItems` Query string that will select from table `work_items`.
         * @param  boolean $id
         * @return string
         */
        public function selectWorkItems($id = false)
        {
            $fields = [
                'WI.id',
                'WI.name',
                'WI.item_no',
                'WI.unit',
                'WIC.id as wic_id',
                'WIC.name as wic_name'
            ];

            $initQuery = $this->select($fields)
                              ->from('work_items WI')
                              ->join(['work_item_categories WIC' => 'WI.work_item_category_id = WIC.id'])
                              ->where(['WI.is_active' => ':is_active', 'WI.direct' => ':direct']);

            $initQuery = ($id) ? $initQuery->andWhere(['WI.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectWorkOrders` Query string that will select from table `work_orders`.
         * @param  boolean $id
         * @return string
         */
        public function selectWorkOrders($id = false)
        {
            $fields = [
                'WO.id',
                'WO.work_order_no',
                'WO.temporary_code',
                'P.id as project_id',
                'P.project_code',
                'P.name as project_name',
                'P.location as project_location',
                'P.contract_days'
            ];

            $joins = [
                'projects P' => 'P.work_order_id = WO.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('work_orders WO')
                              ->join($joins)
                              ->where(['WO.is_active' => 1, 'P.is_active' => 0]);

            $initQuery = ($id) ? $initQuery->andWhere(['WI.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectProjectCodeRequests` Query string that will select from table `project_code_requests`.
         * @param  boolean $id
         * @return string
         */
        public function selectProjectCodeRequests($id = false)
        {
            $fields = [
                'PCR.id',
                'PCR.temporary_project_code as temporary_code',
                'PCR.status',
                // 'WO.work_order_no',
                // 'WO.temporary_code',
                'P.id as project_id',
                'P.project_code',
                'P.name as project_name',
                'P.location as project_location',
                'P.contract_days'
            ];

            $joins = [
                'projects P' => 'P.id = PCR.project_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('project_code_requests PCR')
                              ->join($joins)
                              ->where(['PCR.is_active' => 1, 'P.is_active' => 0]);
                              // ->andWhereNotNull(['PCR.temporary_code']);

            $initQuery = ($id) ? $initQuery->andWhere(['PCR.id' => ':id']) : $initQuery;

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
                'WD.id',
                'WD.name',
                'WD.wbs',
                'PWD.id as pwd_id',
                '"saved" as data_status'
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
                'SP.id',
                'PWSP.id as pw_sp_id',
                'PWSP.name as tag',
                'SP.name',
                'SP.wbs',
                '"saved" as data_status'
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
        public function selectPsSwiDirects($id = false, $pwSpId = false, $revisionNo = false)
        {
            $fields = [
                'SWI.id',
                'PSWID.id as psd_id',
                'PSWID.revision_no as psd_revision_number',
                'PSWID.quantities',
                'PSWID.weight_factor',
                'PSWID.pw_sp_id',
                'PSWID.sw_wi_id',
                'SWI.alternative_name as swi_name',
                'SWI.unit as swi_unit',
                'WIC.id as wic_id',
                'WIC.name as wic_name',
                'WIC.part',
                'WI.id as wi_id',
                'WI.name',
                'WI.item_no',
                'WI.unit',
                'WI.direct',
                'SWIC.id as swic_id',
                'SWIC.wbs as wic_wbs',
                'SWI.wbs as swi_wbs',
                '"saved" as data_status'
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

            $initQuery = ($id)         ? $initQuery->andWhere(['PSWID.id' => ':id'])                   : $initQuery;
            $initQuery = ($pwSpId)     ? $initQuery->andWhere(['PSWID.pw_sp_id' => ':pw_sp_id'])       : $initQuery;
            $initQuery = ($revisionNo) ? $initQuery->andWhere(['PSWID.revision_no' => ':revision_no']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectPwiIndirects` Query string that will select from table `p_wi_indirects`
         * @param  boolean $id
         * @param  boolean $projectId
         * @return string
         */
        public function selectPwiIndirects($id = false, $projectId = false, $revisionNo = false)
        {
            $fields = [
                'WI.id',
                'PWII.id as pwi_id',
                'PWII.revision_no as pwi_revision_number',
                'PWII.quantities',
                'PWII.weight_factor',
                'WIC.id as wic_id',
                'WIC.name as wic_name',
                'WI.name',
                'WI.item_no',
                'WI.unit',
                'WI.direct',
                'WIC.part',
                'WIC.code as wic_wbs',
                'WI.wbs as wi_wbs',
                '"saved" as data_status'
            ];

            $joins = [
                'work_items WI'            => 'PWII.work_item_id = WI.id',
                'work_item_categories WIC' => 'WI.work_item_category_id = WIC.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('p_wi_indirects PWII')
                              ->join($joins)
                              ->where(['PWII.is_active' => ':is_active', 'WI.is_active' => ':is_active', 'WIC.is_active' => ':is_active']);

            $initQuery = ($id)         ? $initQuery->andWhere(['PWII.id' => ':id'])                   : $initQuery;
            $initQuery = ($projectId)  ? $initQuery->andWhere(['PWII.project_id' => ':project_id'])   : $initQuery;
            $initQuery = ($revisionNo) ? $initQuery->andWhere(['PWII.revision_no' => ':revision_no']) : $initQuery;

            return $initQuery;
        }

        public function selectPsdMaterials($ps_swi_direct_id = false)
        {
            $fields = array(
                'PM.id',
                'PM.ps_swi_direct_id',
                'PM.msb_supplier_id',
                'PM.total_set',
                'PM.total_quantity',
                'PM.unit_cost',
                'PM.total_cost',
                'PM.created_by',
                'PM.created_at',
                'PM.is_active',
            );

            $initQuery = $this->select($fields)
                              ->from('psd_materials PM')
                              ->where(array('PM.is_active' => ':is_active'));

            $initQuery = ($ps_swi_direct_id) ? $initQuery->andWhere(array('PM.ps_swi_direct_id' => ':ps_swi_direct_id')) : $initQuery;

            return $initQuery;
        }

        public function selectPsdLabors($ps_swi_direct_id = false, $p_wi_indirect_id = false)
        {
            $fields = array(
                'PL.id',
                'PL.ps_swi_direct_id',
                'PL.p_wi_indirect_id',
                'PL.position_id',
            );

            $initQuery = $this->select($fields)
                              ->from('psd_labors PL')
                              ->where(array('PL.is_active' => ':is_active'));

            $initQuery = ($ps_swi_direct_id) ? $initQuery->andWhere(array('PL.ps_swi_direct_id' => ':ps_swi_direct_id')) : $initQuery;
            $initQuery = ($p_wi_indirect_id) ? $initQuery->andWhere(array('PL.p_wi_indirect_id' => ':p_wi_indirect_id')) : $initQuery;

            return $initQuery;
        }

        public function selectPsdEquipments($ps_swi_direct_id = false)
        {
            $fields = array(
                'PE.id',
                'PE.ps_swi_direct_id',
                'PE.equipment_id',
                'PE.equipment_type_id',
                'PE.heavy_equipment_id',
                'PE.light_equipment_id',
                'PE.capacity',
                'PE.equipment_days',
                'PE.rental_rate',
                'PE.no_of_equipment',
                'PE.total',
                'PE.duration',
                'PE.mob_plan_from',
                'PE.mob_plan_to',
                'PE.created_by',
                'PE.created_at',
                'PE.is_active',
            );

            $initQuery = $this->select($fields)
                              ->from('psd_equipments PE')
                              ->where(array('PE.is_active' => ':is_active'));

            $initQuery = ($ps_swi_direct_id) ? $initQuery->andWhere(array('PE.ps_swi_direct_id' => ':ps_swi_direct_id')) : $initQuery;

            return $initQuery;
        }

        public function selectPwiMaterials($ps_swi_direct_id = false, $p_wi_indirect_id = false)
        {
            $fields = array(
                'PM.id',
                'PM.ps_swi_direct_id',
                'PM.p_wi_indirect_id',
                'PM.msb_supplier_id',
                'PM.total_Set',
                'PM.total_quantity',
                'PM.unit_cost',
                'PM.total_cost',
                'PM.created_by',
                'PM.created_at',
                'PM.is_active',
            );

            $initQuery = $this->select($fields)
                              ->from('pwi_materials PM')
                              ->where(array('PM.is_active' => ':is_active'));

            $initQuery = ($ps_swi_direct_id) ? $initQuery->andWhere(array('PM.ps_swi_direct_id' => ':ps_swi_direct_id')) : $initQuery;
            $initQuery = ($p_wi_indirect_id) ? $initQuery->andWhere(array('PM.p_wi_indirect_id' => ':p_wi_indirect_id')) : $initQuery;

            return $initQuery;
        }

        public function selectPwiLabors($p_wi_indirect_id = false)
        {
            $fields = array(
                'PL.id',
                'PL.p_wi_indirect_id',
                'PL.position_id',
                'PL.mandays',
                'PL.rate',
                'PL.total',
                'PL.created_by',
                'PL.created_at',
                // 'PL.is_active',
            );

            $initQuery = $this->select($fields)
                              ->from('pwi_labors PL');
                            //   ->where(array('PL.is_active' => ':is_active'));

            $initQuery = ($p_wi_indirect_id) ? $initQuery->where(array('PL.p_wi_indirect_id' => ':p_wi_indirect_id')) : $initQuery;

            return $initQuery;
        }

        public function selectPwiEquipments($ps_swi_direct_id = false, $p_wi_indirect_id = false)
        {
            $fields = array(
                'PE.id',
                'PE.ps_swi_direct_id',
                'PE.p_wi_indirect_id',
                'PE.equipment_id',
                'PE.equipment_type_id',
                'PE.heavy_equipment_id',
                'PE.light_equipment_id',
                'PE.capacity',
                'PE.equipment_days',
                'PE.rental_rate',
                'PE.no_of_equipment',
                'PE.total',
                'PE.mat_arr',
                'PE.duration',
                'PE.mob_plan_from',
                'PE.mob_plan_to',
                'PE.created_by',
                'PE.created_at',
                'PE.is_active',
            );

            $initQuery = $this->select($fields)
                              ->from('pwi_equipments PE')
                              ->where(array('PE.is_active' => ':is_active'));

            $initQuery = ($ps_swi_direct_id) ? $initQuery->andWhere(array('PE.ps_swi_direct_id' => ':ps_swi_direct_id')) : $initQuery;
            $initQuery = ($p_wi_indirect_id) ? $initQuery->andWhere(array('PE.p_wi_indirect_id' => ':p_wi_indirect_id')) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectProjectAccesses` Query string that will select from table `project_accesses`.
         * @param  string $id
         * @return string
         */
        public function selectProjectAccesses($id = '', $userId = '')
        {
            $fields = array(
                'PC.id',
                'PC.project_id',
                'PC.level'
            );

            $initQuery = $this->select($fields)
                              ->from('project_accesses PC')
                              ->where(array('PC.is_active' => ':is_active'));

            $initQuery = ($id)     ? $initQuery->andWhere(array('PC.id' => ':id'))           : $initQuery;
            $initQuery = ($userId) ? $initQuery->andWhere(array('PC.user_id' => ':user_id')) : $initQuery;

            return $initQuery;
        }

        /**
         * `insertProject` Query string that will insert to table `projects`
         * @return string
         */
        public function insertProject($data = [])
        {
            $initQuery = $this->insert('projects', $data);

            return $initQuery;
        }

        /**
         * `insertPwd` Query string that will insert to table `p_wds`
         * @return string
         */
        public function insertPwd($data = [])
        {
            $initQuery = $this->insert('p_wds', $data);

            return $initQuery;
        }

        /**
         * `insertPwSp` Query string that will insert to table `pw_sps`
         * @return string
         */
        public function insertPwSp($data = [])
        {
            $initQuery = $this->insert('pw_sps', $data);

            return $initQuery;
        }

        /**
         * `insertPsSwiDirect` Query string that will insert to table `ps_swi_directs`
         * @return string
         */
        public function insertPsSwiDirect($data = [])
        {
            $initQuery = $this->insert('ps_swi_directs', $data);

            return $initQuery;
        }

        /**
         * `insertPwiIndirect` Query string that will insert to table `p_wi_indirects`
         * @return string
         */
        public function insertPwiIndirect($data = [])
        {
            $initQuery = $this->insert('p_wi_indirects', $data);

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
         * `updateProject` Query string that will update specific department information from table `projects`
         * @return string
         */
        public function updateProject($id = '', $data = [])
        {
            $initQuery = $this->update('projects', $id, $data);

            return $initQuery;
        }

        public function updatePwiIndirect($id = '', $data = [])
        {
            $initQuery = $this->update('p_wi_indirects', $id, $data);

            return $initQuery;
        }

        public function updatePsSwiDirect($id = '', $data = [])
        {
            $initQuery = $this->update('ps_swi_directs', $id, $data);

            return $initQuery;
        }

        public function updatePwSp($id = '', $data = [])
        {
            $initQuery = $this->update('pw_sps', $id, $data);

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

    }