<?php 
    namespace App\Model\ProjectPlanning;

    include_once "..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class ProjectPlanningQueryHandler extends QueryHandler {

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
                'users U' 	  => 'U.id = AC.project_manager',
                'employees E' => 'E.id = U.employee_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('actual_surveys AC')
                              ->join($joins)
                              ->where(['AC.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['AC.id' => ':id']) : $initQuery;

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
         * `selectAsWorkDisciplines` Query string that will select from table `as_work_disciplines`
         * @param  boolean $id
         * @param  boolean $actualSurveyId
         * @return string
         */
        public function selectAsWorkDisciplines($id = false, $actualSurveyId = false)
        {
            $fields = [
                'AWD.id',
                'WD.cost_code',
                'WD.name'
            ];

            $initQuery = $this->select($fields)
                              ->from('as_work_disciplines AWD')
                              ->join(['work_disciplines WD' => 'WD.id = AWD.work_discipline_id'])
                              ->where(['AWD.is_active' => ':is_active']);

            $initQuery = ($id)             ? $initQuery->andWhere(['AWD.id' => ':id'])                             : $initQuery;
            $initQuery = ($actualSurveyId) ? $initQuery->andWhere(['AWD.actual_survey_id' => ':actual_survey_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectAsSubProjectTypes` Query string that will select from table `as_sub_project_types`.
         * @param  boolean $id
         * @param  boolean $asWorkDisciplineId
         * @return string
         */
        public function selectAsSubProjectTypes($id = false, $asWorkDisciplineId = false)
        {
            $fields = [
                'ASPT.id',
                'ASPT.name',
                'SPT.cost_code',
                'SPT.name as sub_project_type_name',
                'SP.name as sub_project_name'
            ];

            $joins = [
                'sub_project_types SPT' => 'SPT.id = ASPT.sub_project_type_id',
                'sub_projects SP'       => 'SP.id = SPT.sub_project_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('as_sub_project_types ASPT')
                              ->join($joins)
                              ->where(['ASPT.is_active' => ':is_active']);

            $initQuery = ($id)                 ? $initQuery->andWhere(['ASPT.id' => ':id']) : $initQuery;
            $initQuery = ($asWorkDisciplineId) ? $initQuery->andWhere(['ASPT.as_work_discipline_id' => ':as_work_discipline_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectAsScopeOfWorks` Query string that will select from table `as_scope_of_works`.
         * @param  boolean $id
         * @param  boolean $asSubProjectTypes
         * @return string
         */
        public function selectAsScopeOfWorks($id = false, $asSubProjectTypes = false)
        {
            $fields = [
                'ASOW.id',
                'ASOW.sw_wi_id',
                'SWI.cost_code',
                'SWI.unit',
                'WI.item_no',
                'WI.name',
                'ASOW.quantities',
                'WI.direct',
                'SWIC.work_item_category_id',
                'SWIC.cost_code as swic_cost_code',
                'WIC.name as work_item_category_name'
            ];

            $joins = [
                'sw_wis SWI'               => 'SWI.id = ASOW.sw_wi_id',
                'work_items WI'            => 'WI.id = SWI.work_item_id',
                'spt_wics SWIC'            => 'SWI.spt_wic_id = SWIC.id',
                'work_item_categories WIC' => 'SWIC.work_item_category_id = WIC.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('as_scope_of_works ASOW')
                              ->join($joins)
                              ->where(['ASOW.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['ASOW.id' => ':id']) : $initQuery;
            $initQuery = ($asSubProjectTypes) ? $initQuery->andWhere(['ASOW.as_sub_project_type_id' => ':as_sub_project_type_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectSwWiMaterials` Query string that will select from table `sw_wi_materials`.
         * @param  boolean $id
         * @param  boolean $swWiId
         * @return string
         */
        public function selectSwWiMaterials($id = false, $swWiId = false)
        {
            $fields = [
                'SWIM.id',
                'SWIM.material_id',
                'SWIM.cost_code',
                'SWIM.unit',
                'SWIM.multiplier',
                'M.name'
            ];

            $joins = [
                'materials M' => 'M.id = SWIM.material_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('sw_wi_materials SWIM')
                              ->join($joins)
                              ->where(['SWIM.is_active' => ':is_active']);

            $initQuery = ($id)     ? $initQuery->andWhere(['SWIM.id' => ':id'])             : $initQuery;
            $initQuery = ($swWiId) ? $initQuery->andWhere(['SWIM.sw_wi_id' => ':sw_wi_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectMaterialPrices` Query string that will select from table `material_prices`.
         * @param  boolean $id
         * @param  boolean  $materialId
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
                              ->where(['MP.status' => ':status']);

            $initQuery = ($id)         ? $initQuery->andWhere(['MP.id' => ':id'])                   : $initQuery;
            $initQuery = ($materialId) ? $initQuery->andWhere(['MP.material_id' => ':material_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectSwWiManpowers` Query string that will select from table `sw_wi_manpowers`.
         * @param  boolean $id
         * @param  boolean $swWiId
         * @return string
         */
        public function selectSwWiManpowers($id = false, $swWiId = false)
        {
            $fields = [
                'SWIM.id',
                'SWIM.position_id',
                'SWIM.cost_code',
                'SWIM.work_rate',
                'P.name',
                'P.rate'
            ];

            $joins = [
                'positions P' => 'P.id = SWIM.position_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('sw_wi_manpowers SWIM')
                              ->join($joins)
                              ->where(['SWIM.is_active' => ':is_active']);

            $initQuery = ($id)     ? $initQuery->andWhere(['SWIM.id' => ':id'])             : $initQuery;
            $initQuery = ($swWiId) ? $initQuery->andWhere(['SWIM.sw_wi_id' => ':sw_wi_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectSwWiEquipments` Query string that will select from table `sw_wi_equipments`.
         * @param  boolean $id
         * @param  boolean $swWiId
         * @return string
         */
        public function selectSwWiEquipments($id = false, $swWiId = false)
        {
            $fields = [
                'SWIE.id',
                'SWIE.equipment_type_id',
                'SWIE.cost_code',
                'SWIE.work_rate',
                'SWIE.capacity',
                'ET.name',
                'ET.unit',
            ];

            $joins = [
                'equipment_types ET' => 'ET.id = SWIE.equipment_type_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('sw_wi_equipments SWIE')
                              ->join($joins)
                              ->where(['SWIE.is_active' => ':is_active']);

            $initQuery = ($id)     ? $initQuery->andWhere(['SWIE.id' => ':id'])             : $initQuery;
            $initQuery = ($swWiId) ? $initQuery->andWhere(['SWIE.sw_wi_id' => ':sw_wi_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectEtEs` Query string that will select from table `et_es`.
         * @param  boolean $id
         * @param  boolean $equipmentId
         * @param  boolean $equipmentTypeId
         * @return string
         */
        public function selectEtEs($id = false, $equipmentId = false, $equipmentTypeId = false)
        {
            $fields = [
                'EE.id',
                'EE.cost_code',
                'E.body_no',
                'ERR.rental_rate',
                'ERR.rate_type'
            ];

            $joins = [
                'equipments E'               => 'E.id = EE.equipment_id',
                'equipment_rental_rates ERR' => 'EE.equipment_rental_rate_id = ERR.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('et_es EE')
                              ->join($joins)
                              ->where(['EE.is_active' => ':is_active']);

            $initQuery = ($id)              ? $initQuery->andWhere(['EE.id' => ':id'])                               : $initQuery;
            $initQuery = ($equipmentId)     ? $initQuery->andWhere(['EE.equipment_id' => ':equipment_id'])           : $initQuery;
            $initQuery = ($equipmentTypeId) ? $initQuery->andWhere(['EE.equipment_type_id' => ':equipment_type_id']) : $initQuery;

            $initQuery = $initQuery->orderBy('ERR.rental_rate', 'ASC');

            return $initQuery;
        }
    }