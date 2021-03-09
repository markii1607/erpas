<?php 
    namespace App\Model\ProjectEstimate;

    include_once "..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class ProjectEstimateQueryHandler extends QueryHandler {

        /**
         * `selectActualSurveys` Query string that will select from table `actual_surveys`.
         * @param  boolean $id
         * @return string
         */
        public function selectActualSurveys($id = false)
        {
            $fields = [
                'AC.id',
                'AC.temp_code as code',
                'AC.name',
                'AC.location',
                'CONCAT(E.fname," ",E.mname," ",E.lname) as project_manager_name'
            ];

            $joins = [
                'employees E' => 'E.id = AC.project_manager'
            ];

            $initQuery = $this->select($fields)
                              ->from('actual_surveys AC')
                              ->join($joins)
                              ->where(['AC.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['AC.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectActualSurveyTypeLists` Query string that will select from table `actual_survey_type_lists`.
         * @param  boolean $id
         * @param  boolean $actualSurveyId
         * @return string
         */
        public function selectActualSurveyTypeLists($id = false, $actualSurveyId = false)
        {
            $fields = [
                'ASTL.id',
                'ASTL.name',
                'ASTL.project_type_id',
                'ASTL.name as project_type_name'
            ];

            $joins = [
                'project_types PT' => 'PT.id = ASTL.project_type_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('actual_survey_type_lists ASTL')
                              ->join($joins)
                              ->where(['ASTL.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['ASTL.id' => ':id']) : $initQuery; 
            $initQuery = ($actualSurveyId) ? $initQuery->andWhere(['ASTL.actual_survey_id' => ':actual_survey_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectActualSurveyScopeOfWorks` Query string that will select scope of works of specific project from `actual_survey_scope_of_works` table.
         * @param  boolean $id
         * @param  boolean $projectTypeId
         * @return string
         */
        public function selectActualSurveyScopeOfWorks($id = false, $projectTypeListId = false, $direct = false)
        {
            $fields = [
                'ASSOW.id',
                'ASSOW.work_item_id',
                'ASSOW.quantities as quantity',
                'WI.unit',
                'WI.item_no',
                'WI.name',
                'WI.work_item_category_id',
                'WIC.name as work_item_category_name',
                'WIC.cost_code as wic_cost_code'
            ];

            $joins = [
                'work_items WI'            => 'ASSOW.work_item_id = WI.id',
                'work_item_categories WIC' => 'WIC.id = WI.work_item_category_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('actual_survey_scope_of_works ASSOW')
                              ->join($joins)
                              ->where(['ASSOW.is_active' => ':is_active']);

            $initQuery = ($id)                ? $initQuery->andWhere(['ASSOW.id' => ':id']) : $initQuery;
            $initQuery = ($projectTypeListId) ? $initQuery->andWhere(['ASSOW.actual_survey_type_list_id' => ':actual_survey_type_list_id']) : $initQuery;
            $initQuery = ($direct)            ? $initQuery->andWhere(['WI.direct' => ':direct']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectProjects` Query string that will select projects from table `projects` join to table `employees`.
         * @return string
         */
        public function selectProjects($id = false)
        {
            $fields = [
                'P.id',
                'P.project_code as code',
                'P.name',
                'P.location',
                'P.project_manager as project_manager_id',
                'CONCAT(E.fname," ",E.mname," ",E.lname) as project_manager_name'
            ];

            $initQuery = $this->select($fields)
                              ->from('projects P')
                              ->join(['employees E' => 'P.project_manager = E.id'])
                              ->where(['P.status' => ':status']);

            $initQuery = ($id) ? $initQuery->andWhere(['P.id' => ':id']) : $initQuery;

            $initQuery = $initQuery->orderBy('P.project_code', 'desc');

            return $initQuery;
        }

        /**
         * `selectProjectTypeLists` Query string that will select from table `project_type_lists`.
         * @param  boolean $id
         * @param  boolean $prijectId
         * @return string
         */
        public function selectProjectTypeLists($id = false, $projectId = false)
        {
            $fields = [
                'PTL.id',
                'PTL.name',
                'PTL.project_type_id',
                'PT.name as project_type_name'
            ];

            $joins = [
                'project_types PT'   => 'PTL.project_type_id = PT.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('project_type_lists PTL')
                              ->join($joins)
                              ->where(['PTL.status' => ':status']);

            $initQuery = ($id)        ? $initQuery->andWhere(['PTL.id' => ':id'])                 : $initQuery;
            $initQuery = ($projectId) ? $initQuery->andWhere(['PTL.project_id' => ':project_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectScopeOfWorks` Query string that will select scope of works of specific project from `scope_of_works` table.
         * @param  boolean $id
         * @param  boolean $projectTypeId
         * @return string
         */
        public function selectScopeOfWorks($id = false, $projectTypeListId = false, $direct = false)
        {
            $fields = [
                'SOW.id',
                'SOW.work_item_id',
                'SOW.quantities as quantity',
                'WI.unit',
                'WI.item_no',
                'WI.name',
                'WI.work_item_category_id',
                'WIC.name as work_item_category_name',
                'WIC.cost_code as wic_cost_code'
            ];

            $joins = [
                'work_items WI'            => 'SOW.work_item_id = WI.id',
                'work_item_categories WIC' => 'WIC.id = WI.work_item_category_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('scope_of_works SOW')
                              ->join($joins)
                              ->where(['SOW.status' => ':status']);

            $initQuery = ($id) ? $initQuery->andWhere(['SOW.id' => ':id']) : $initQuery;
            $initQuery = ($projectTypeListId) ? $initQuery->andWhere(['SOW.project_type_list_id' => ':project_type_list_id']) : $initQuery;
            $initQuery = ($direct) ? $initQuery->andWhere(['WI.direct' => ':direct']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectIndirectScopeOfWorks` Query string that will select from table `indirect_scope_of_works`.
         * @param  boolean $id
         * @param  boolean $pId
         * @return string
         */
        public function selectIndirectScopeOfWorks($id = false, $pId = false, $wicId = false)
        {
            $fields = [
                'ISOW.id',
                'ISOW.quantities as quantity',
                'WI.item_no',
                'WI.name',
                'WI.unit',
                'WI.id as work_item_id',
                'WI.work_item_category_id',
                'WIC.name as work_item_category_name',
                'WIC.cost_code as wic_cost_code'
            ];

            $joins = [
                'work_items WI'            => 'WI.id = ISOW.work_item_id',
                'work_item_categories WIC' => 'WIC.id = WI.work_item_category_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('indirect_scope_of_works ISOW')
                              ->join($joins)
                              ->where(['WI.status' => ':status', 'ISOW.status' => 1]);

            $initQuery = ($id)    ? $initQuery->andWhere(['ISOW.id' => ':id'])                                     : $initQuery;
            $initQuery = ($pId)   ? $initQuery->andWhere(['ISOW.project_id' => ':project_id'])                     : $initQuery;
            $initQuery = ($wicId) ? $initQuery->andWhere(['WI.work_item_category_id' => ':work_item_category_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectWorkItemMaterials` Query string that will select from table `work_item_materials`
         * @param  boolean $id
         * @param  boolean $workItemId
         * @return string
         */
        public function selectWorkItemMaterials($id = false, $workItemId = false)
        {
            $fields = [
                'WIM.id',
                'WIM.multiplier',
                'WIM.unit',
                'M.cost_code',
                'M.name',
                'M.id as material_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('work_item_materials WIM')
                              ->join(['materials M' => 'WIM.material_id = M.id'])
                              ->where(['WIM.status' => 1]);

            $initQuery = ($id)         ? $initQuery->andWhere(['WIM.id' => ':id'])                     : $initQuery;
            $initQuery = ($workItemId) ? $initQuery->andWhere(['WIM.work_item_id' => ':work_item_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectMaterialPrices` Query string that will select from table `material_prices`.
         * @param  boolean $id
         * @param  boolean $materialId
         * @return string
         */
        public function selectMaterialPrices($id = false, $materialId = false)
        {
            $fields = [
                'MP.id',
                'MP.price',
                'MP.unit'
            ];

            $initQuery = $this->select($fields)
                              ->from('material_prices MP')
                              ->where(['MP.status' => 1]);

            $initQuery = ($materialId) ? $initQuery->andWhere(['MP.material_id' => ':material_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectWorkItemEquipments` Query string that will select from table `work_item_equipments`
         * @param  boolean $id
         * @param  boolean $workItemId
         * @return string
         */
        public function selectWorkItemEquipments($id = false, $workItemId = false)
        {
            $fields = [
                'WIE.id',
                'WIE.work_rate',
                'WIE.capacity',
                'WIE.equipment_type_id',
                'ET.cost_code',
                'ET.name as equipment_type_name',
                'ET.unit'
            ];

            $initQuery = $this->select($fields)
                              ->from('work_item_equipments WIE')
                              ->join(['equipment_types ET' => 'WIE.equipment_type_id = ET.id'])
                              ->where(['WIE.status' => 1]);

            $initQuery = ($id)         ? $initQuery->andWhere(['WIE.id' => ':id'])                     : $initQuery;
            $initQuery = ($workItemId) ? $initQuery->andWhere(['WIE.work_item_id' => ':work_item_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectEquipments` Query string that will select from table `equipments`.
         * @param  boolean $id
         * @param  boolean $equipmentTypeId
         * @return string
         */
        public function selectEquipments($id = false, $equipmentTypeId = false)
        {
            $fields = [
                'E.id',
                'E.body_no'
            ];

            $initQuery = $this->select($fields)
                              ->from('equipments E')
                              ->where(['E.status' => ':status']);

            $initQuery = ($id) ? $initQuery->andWhere(['E.id' => ':id']) : $initQuery;
            $initQuery = ($equipmentTypeId) ? $initQuery->andWhere(['E.equipment_type_id' => ':equipment_type_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectEquipmentRentalRates` Query string that will select from table `equipment_rental_rates`.
         * @param  boolean $id
         * @param  boolean $equipmentId
         * @return string
         */
        public function selectEquipmentRentalRates($id = false, $equipmentId = false)
        {
            $fields = [
                'ERR.id',
                'ERR.rental_rate'
            ];

            $initQuery = $this->select($fields)
                              ->from('equipment_rental_rates ERR')
                              ->where(['ERR.status' => ':status']);

            $initQuery = ($id) ? $initQuery->andWhere(['ERR.id' => ':id']) : $initQuery;
            $initQuery = ($equipmentId) ? $initQuery->andWhere(['ERR.equipment_id' => ':equipment_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectWorkItemManpowers` Query string that will select from table `work_item_manpowers`.
         * @param  boolean $id
         * @param  boolean $workItemId
         * @return string
         */
        public function selectWorkItemManpowers($id = false, $workItemId = false)
        {
            $fields = [
                'WIM.id',
                'WIM.work_rate',
                'P.cost_code',
                'P.name as position_name',
                'P.rate',
            ];

            $initQuery = $this->select($fields)
                              ->from('work_item_manpowers WIM')
                              ->join(['positions P' => 'P.id = WIM.position_id'])
                              ->where(['WIM.status' => 1]);

            $initQuery = ($id) ? $initQuery->andWhere(['WIM.id' => ':id']) : $initQuery;
            $initQuery = ($workItemId) ? $initQuery->andWhere(['WIM.work_item_id' => ':work_item_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectWorkItemIndirectCosts` Query string that will select from table `work_item_indirect_costs`
         * @param  boolean $id
         * @param  boolean $workItemId
         * @return string
         */
        public function selectWorkItemIndirectCosts($id = false, $workItemId = false)
        {
            $fields = [
                'WIIC.id',
                'WIIC.indirect_cost_description_id',
                'WIIC.unit',
                'ICD.name as indirect_cost_description_name',
            ];

            $initQuery = $this->select($fields)
                              ->from('work_item_indirect_costs WIIC')
                              ->join(['indirect_cost_descriptions ICD' => 'ICD.id = WIIC.indirect_cost_description_id'])
                              ->where(['WIIC.status' => 1]);

            $initQuery = ($id)         ? $initQuery->andWhere(['WIIC.id' => ':id'])                     : $initQuery;
            $initQuery = ($workItemId) ? $initQuery->andWhere(['WIIC.work_item_id' => ':work_item_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `insertActualSurveySowMaterials` Query string that will insert to table `actual_survey_sow_materials`
         * @return string
         */
        public function insertActualSurveySowMaterials($data = [])
        {
            $initQuery = $this->insert('actual_survey_sow_materials', $data);

            return $initQuery;
        }

        /**
         * `insertActualSurveySowEquipments` Query string that will insert to table `actual_survey_sow_equipments`
         * @return string
         */
        public function insertActualSurveySowEquipments($data = [])
        {
            $initQuery = $this->insert('actual_survey_sow_equipments', $data);

            return $initQuery;
        }

        /**
         * `insertActualSurveySowManpowers` Query string that will insert to table `actual_survey_sow_manpowers`
         * @return string
         */
        public function insertActualSurveySowManpowers($data = [])
        {
            $initQuery = $this->insert('actual_survey_sow_manpowers', $data);

            return $initQuery;
        }

        // // /**
        // //  * `selectScopeOfWorkMaterials` Query string that will select materials of specific scope of work.
        // //  * @param  boolean $id
        // //  * @param  boolean $scopeOfWorkId
        // //  * @return string
        // //  */
        // // public function selectScopeOfWorkMaterials($id = false, $scopeOfWorkId = false)
        // // {
        // //     $fields = [
        // //         'SOWM.id',
        // //         'M.cost_code',
        // //         'M.name',
        // //         'SOWM.quantity',
        // //         'MP.unit',
        // //         'MP.price',
        // //         'SOWM.total_amount'
        // //     ];

        // //     $joins = [
        // //         'material_prices MP' => 'MP.id = SOWM.material_price_id',
        // //         'materials M'        => 'M.id = MP.material_id'
        // //     ];

        // //     $conditions = [
        // //         'MP.status' => 1,
        // //         'M.status'  => 1
        // //     ];

        // //     ($scopeOfWorkId) ? $conditions['SOWM.scope_of_work_id'] = ':scope_of_work_id' : '' ;

        // //     $initQuery = $this->select($fields)
        // //                       ->from('scope_of_Work_materials SOWM')
        // //                       ->join($joins)
        // //                       ->where($conditions);

        // //     return $initQuery;
        // // }

        // // /**
        // //  * `selectScopeOfWorkEquipments` Query string that will select equipment of specific equipment
        // //  * @param  boolean $id
        // //  * @param  boolean $scopeOfWorkId
        // //  * @return string
        // //  */
        // // public function selectScopeOfWorkEquipments($id = false, $scopeOfWorkId = false)
        // // {
        // //     $fields = [
        // //         'SOWE.id',
        // //         'E.cost_code',
        // //         'E.model',
        // //         'SOWE.quantity',
        // //         'SOWE.days',
        // //         'ERR.rental_rate',
        // //         'SOWE.total_amount'
        // //     ];

        // //     $joins = [
        // //         'equipment_rental_rates ERR' => 'ERR.id = SOWE.equipment_rental_rate_id',
        // //         'equipments E'               => 'E.id = ERR.equipment_id'
        // //     ];

        // //     $conditions = [
        // //         'ERR.status' => 1,
        // //         'E.status'   => 1
        // //     ];

        // //     ($scopeOfWorkId) ? $conditions['SOWE.scope_of_work_id'] = ':scope_of_work_id' : '' ;

        // //     $initQuery = $this->select($fields)
        // //                       ->from('scope_of_work_equipments SOWE')
        // //                       ->join($joins)
        // //                       ->where($conditions);

        // //     return $initQuery;
        // // }

        // // /**
        // //  * `selectMaterials` Query string that will select material informations.
        // //  * @return string
        // //  */
        // // public function selectMaterials($id = false, $materialTypeId = false, $name = false)
        // // {
        // //     $fields = [
        // //         'M.id',
        // //         'M.material_type_id',
        // //         'M.name',
        // //         'M.cost_code as m_cost_code',
        // //         'MT.cost_code as mt_cost_code',
        // //         'MT.name as material_type_name',
        // //         'DATE_FORMAT(M.created_at, "%m/%d/%Y") as date_added'
        // //     ];

        // //     $conditions = [
        // //         'M.status' => ':status'
        // //     ];

        // //     ($id)             ? $conditions['M.id']               = ':id'               : '';
        // //     ($materialTypeId) ? $conditions['M.material_type_id'] = ':material_type_id' : '';
        // //     ($name)           ? $conditions['M.name']             = ':name'             : '';

        // //     $initQuery = $this->select($fields)
        // //                       ->from('materials M')
        // //                       ->join(['material_types MT' => 'M.material_type_id = MT.id'])
        // //                       ->where($conditions);

        // //     return $initQuery;
        // // }

        // // /**
        // //  * `selectMaterialPrices` Query string that will select prices of every material.
        // //  * @return string
        // //  */
        // // public function selectMaterialPrices()
        // // {
        // //     $fields = [
        // //         'MP.id',
        // //         'MP.material_id',
        // //         'MP.price',
        // //         'MP.unit',
        // //         'DATE_FORMAT(MP.`updated_at`, "%m/%d/%Y") as date_updated',
        // //         'S.name as supplier_name',
        // //         'M.name',
        // //         'M.cost_code'
        // //     ];


        // //     $initQuery = $this->select($fields)
        // //                       ->from('material_prices MP')
        // //                       ->join(['materials M' => 'M.id = MP.material_id'])
        // //                       ->leftJoin(['suppliers S' => 'MP.supplier_id = S.id'])
        // //                       ->where(['MP.status' => ':status'])
        // //                       ->orderBy('MP.updated_at', 'desc');

        // //     return $initQuery;
        // // }

        // // /**
        // //  * `selectMaterialMultipliers` Query string that will select multipliers of every material.
        // //  * @return string
        // //  */
        // // public function selectMaterialMultipliers()
        // // {
        // //     $fields = [
        // //         'MM.id',
        // //         'MM.material_id',
        // //         'MM.multiplier',
        // //         'MM.remarks'
        // //     ];

        // //     $initQuery = $this->select($fields)
        // //                       ->from('material_multipliers MM')
        // //                       ->where(['MM.status' => ':status'])
        // //                       ->orderBy('MM.id', 'desc');

        // //     return $initQuery;
        // // }

        // // /**
        // //  * `selectIndirectScopeOfWorkMaterials` Query string that will select from table `indirect_scope_of_work_materials`
        // //  * @param  boolean $id
        // //  * @param  boolean $indirectScopeOfWorkId
        // //  * @return string
        // //  */
        // // public function selectIndirectScopeOfWorkMaterials($id = false, $indirectScopeOfWorkId = false)
        // // {
        // //     $fields = [
        // //         'ISOWM.id',
        // //         'M.cost_code',
        // //         'M.name',
        // //         'ISOWM.quantity',
        // //         'MP.unit',
        // //         'MP.price',
        // //         'ISOWM.total_amount'
        // //     ];

        // //     $conditions = [
        // //         'MP.status' => 1,
        // //         'M.status'  => 1
        // //     ];

        // //     $joins = [
        // //         'material_prices MP' => 'MP.id = ISOWM.material_price_id',
        // //         'materials M'        => 'M.id = MP.material_id'
        // //     ];

        // //     ($indirectScopeOfWorkId) ? $conditions['ISOWM.indirect_scope_of_work_id'] = ':indirect_scope_of_work_id' : '' ;

        // //     $initQuery = $this->select($fields)
        // //                       ->from('indirect_scope_of_work_materials ISOWM')
        // //                       ->join($joins)
        // //                       ->where($conditions);

        // //     return $initQuery;
        // // }

        // // /**
        // //  * `selectIndirectScopeOfWorkEquipments` Query string that will select equipment of specific equipment
        // //  * @param  boolean $id
        // //  * @param  boolean $scopeOfWorkId
        // //  * @return string
        // //  */
        // // public function selectIndirectScopeOfWorkEquipments($id = false, $indirectScopeOfWorkId = false)
        // // {
        // //     $fields = [
        // //         'ISOWE.id',
        // //         'E.cost_code',
        // //         'E.model',
        // //         'ISOWE.quantity',
        // //         'ISOWE.days',
        // //         'ERR.rental_rate',
        // //         'ISOWE.total_amount'
        // //     ];

        // //     $joins = [
        // //         'equipment_rental_rates ERR' => 'ERR.id = ISOWE.equipment_rental_rate_id',
        // //         'equipments E'               => 'E.id = ERR.equipment_id'
        // //     ];

        // //     $conditions = [
        // //         'ERR.status' => 1,
        // //         'E.status'   => 1
        // //     ];

        // //     ($indirectScopeOfWorkId) ? $conditions['ISOWE.indirect_scope_of_work_id'] = ':indirect_scope_of_work_id' : '' ;

        // //     $initQuery = $this->select($fields)
        // //                       ->from('indirect_scope_of_work_equipments ISOWE')
        // //                       ->join($joins)
        // //                       ->where($conditions);

        // //     return $initQuery;
        // // }

        // // /**
        // //  * `insertScopeOfWorkMaterial` Query string that will insert to table `scope_of_work_materials`
        // //  * @return string
        // //  */
        // // public function insertScopeOfWorkMaterial($data = [])
        // // {
        // //     $initQuery = $this->insert('scope_of_work_materials', $data);

        // //     return $initQuery;
        // // }

        // // /**
        // //  * `projectEstimateStatus` Query string that will update of specific project's statuses from `projects` table.
        // //  * @return string
        // //  */
        // // public function projectEstimateStatus($id = '', $data = [])
        // // {
        // //     $initQuery = $this->update('projects', $id, $data);

        // //     return $initQuery;
        // // }
    }