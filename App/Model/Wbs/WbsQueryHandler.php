<?php 
    namespace App\Model\Wbs;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class WbsQueryHandler extends QueryHandler { 
        /**
         * `selectWorkDisciplines` Query string that will fetch work disciplines from table `work_disciplines`.
         * @return string
         */
        public function selectWorkDisciplines($id = false)
        {
            $fields = [
                'WD.id',
                'WD.code',
                'WD.wbs',
                'WD.name',
                '"wd" AS identifier'
            ];

            $orWhereCondition = array(
                'WD.wbs'  => ':filter_val',
                'WD.name' => ':filter_val',
            );

            $initQuery = $this->select($fields)
                              ->from('work_disciplines WD')
                              ->where(['WD.is_active' => ':is_active'])
                              ->logicEx('AND')
                              ->orWhereLike($orWhereCondition);

            $initQuery = ($id) ? $initQuery->andWhere(['WD.id' => ':id']) : $initQuery;

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

            $initQuery = ($id)   ? $initQuery->andWhere(['SP.id' => ':id']) 								: $initQuery;
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

            $initQuery = ($id)   ? $initQuery->andWhere(['SPT.id' => ':id']) 						 : $initQuery;
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
                'PT.specification'
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
    }