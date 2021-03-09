<?php 
    namespace App\Model\WorkItemDataControl;

    include_once "..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class WorkItemDataControlQueryHandler extends QueryHandler { 
        /**
         * `selectWorkItems` Query string that will fetch work item with category.
         * @return string
         */
        public function selectWorkItems($id = false, $workItemCategoryId = false, $itemNo = false, $status = false)
        {
            $fields = [
                'WI.id',
                'WI.cost_code',
                'Wi.item_no',
                'WI.name',
                'WI.unit',
                'WIC.name as category_name',
                'WIC.id as category_id',
                'WIC.cost_code as wic_cost_code'
            ];

            $initQuery = $this->select($fields)
                              ->from('work_items WI')
                              ->join(['work_item_categories WIC' => 'WI.work_item_category_id = WIC.id']);

            $initQuery = ($status)             ? $initQuery->where(['WI.status' => ':status'])                               : $initQuery;
            $initQuery = ($id)                 ? $initQuery->andWhere(['WI.id' => ':id'])                                    : $initQuery;
            $initQuery = ($workItemCategoryId) ? $initQuery->andWhere(['WI.work_item_category_id' => ':work_item_category_id']) : $initQuery;
            $initQuery = ($itemNo)             ? $initQuery->andWhere(['WI.item_no' => ':item_no'])                          : $initQuery;

            return $initQuery;
        }

        /**
         * `selectWorkItemCategories` Query string that will select from table `work_item_categories`
         * @return string
         */
        public function selectWorkItemCategories($id = false)
        {
            $fields = [
                'WIC.id',
                'WIC.cost_code',
                'WIC.name'
            ];

            $initQuery = $this->select($fields)
                              ->from('work_item_categories WIC');

            $initQuery = ($id) ? $initQuery->where(['WIC.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectEquipmentTypes` Query string that will select from table `equipment_types`
         * @param  boolean $id
         * @return string
         */
        public function selectEquipmentTypes($id = false)
        {
            $fields = [
                'ET.id',
                'ET.name',
                'ET.cost_code',
                'ET.unit'
            ];

            $initQuery = $this->select($fields)
                              ->from('equipment_types ET');

            $initQuery = ($id) ? $initQuery->where(['ET.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectPositions` Query string that will select from table `positions`.
         * @param  boolean $id
         * @return string
         */
        public function selectPositions($id = false)
        {
            $fields = [
                'P.id',
                'P.name'
            ];

            $initQuery = $this->select($fields)
                              ->from('positions P')
                              ->where(['P.status' => 1]);

            $initQuery = ($id) ? $initQuery->andWhere(['P.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectWorkItemIndirectCosts` Query string that will select from table `work_item_indirect_costs`.
         * @param  boolean $id
         * @param  boolean $workItemId
         * @return string
         */
        public function selectWorkItemIndirectCosts($id = false, $workItemId = false)
        {
            $fields = [
                'WIIC.id',
                'ICD.id as indirect_cost_id',
                'ICD.name',
                'WIIC.unit'
            ];

            $joins = [
                'indirect_cost_descriptions ICD' => 'ICD.id = WIIC.indirect_cost_description_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('work_item_indirect_costs WIIC')
                              ->join($joins)
                              ->where(['WIIC.status' => ':status']);

            $initQuery = ($id)         ? $initQuery->andWhere(['WIIC.id' => ':id'])                     : $initQuery;
            $initQuery = ($workItemId) ? $initQuery->andWhere(['WIIC.work_item_id' => ':work_item_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectWorkItemMaterials` Query string that will select from table `work_item_materials`.
         * @param  boolean $id
         * @param  boolean $workItemId
         * @return string
         */
        public function selectWorkItemMaterials($id = false, $workItemId = false)
        {
            $fields = [
                'WIM.id',
                'M.name',
                'M.id as material_id',
                'WIM.multiplier',
                'WIM.unit'
            ];

            $joins = [
                'materials M' => 'M.id = WIM.material_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('work_item_materials WIM')
                              ->join($joins)
                              ->where(['WIM.status' => ':status']);

            $initQuery = ($id)         ? $initQuery->andWhere(['WIM.id' => ':id'])                     : $initQuery;
            $initQuery = ($workItemId) ? $initQuery->andWhere(['WIM.work_item_id' => ':work_item_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectWorkItemEquipment` Query string that will select from table `work_item_equipments`.
         * @param  boolean $id
         * @param  boolean $workItemId
         * @return string
         */
        public function selectWorkItemEquipment($id = false, $workItemId = false)
        {
            $fields = [
                'WIE.id',
                'ET.id as equipment_id',
                'ET.name',
                'WIE.capacity',
                'ET.unit',
                'WIE.work_rate'
            ];

            $joins = [
                'equipment_types ET' => 'ET.id = WIE.equipment_type_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('work_item_equipments WIE')
                              ->join($joins)
                              ->where(['WIE.status' => ':status']);

            $initQuery = ($id)         ? $initQuery->andWhere(['WIE.id' => ':id'])                     : $initQuery;
            $initQuery = ($workItemId) ? $initQuery->andWhere(['WIE.work_item_id' => ':work_item_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectWorkItemManpower` Query string that will select from table `work_item_manpowers`.
         * @param  boolean $id
         * @param  boolean $workItemId
         * @return string
         */
        public function selectWorkItemManpower($id = false, $workItemId = false)
        {
            $fields = [
                'WIM.id',
                'P.id as position_id',
                'P.name',
                'WIM.work_rate'
            ];

            $joins = [
                'positions P' => 'P.id = WIM.position_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('work_item_manpowers WIM')
                              ->join($joins)
                              ->where(['WIM.status' => ':status']);

            $initQuery = ($id)         ? $initQuery->andWhere(['WIM.id' => ':id'])                     : $initQuery;
            $initQuery = ($workItemId) ? $initQuery->andWhere(['WIM.work_item_id' => ':work_item_id']) : $initQuery;

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
                'M.name',
                'M.cost_code'
            ];

            $initQuery = $this->select($fields)
                              ->from('materials M')
                              ->where(['M.status' => 1]);

            return $initQuery;
        }

        /**
         * `selectIndirectCostDescriptions` Query string that will select from table `indirect_cost_descriptions`.
         * @param  boolean $id
         * @return string
         */
        public function selectIndirectCostDescriptions($id = false)
        {
            $fields = [
                'ICD.id',
                'ICD.name'
            ];

            $initQuery = $this->select($fields)
                              ->from('indirect_cost_descriptions ICD')
                              ->where(['ICD.status' => 1]);

            $initQuery = ($id) ? $initQuery->andWhere(['ICD.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `insertWorkItems` Query string that will insert to table `work_items`
         * @return string
         */
        public function insertWorkItem($data = [])
        {
            $initQuery = $this->insert('work_items', $data);

            return $initQuery;
        }

        /**
         * `insertWorkItemIndirectCost` Query string that will insert to table `work_item_indirect_costs`
         * @return string
         */
        public function insertWorkItemIndirectCost($data = [])
        {
            $initQuery = $this->insert('work_item_indirect_costs', $data);

            return $initQuery;
        }

        /**
         * `insertWorkItemMaterial` Query string that will insert to table `work_item_materials`
         * @return string
         */
        public function insertWorkItemMaterial($data = [])
        {
            $initQuery = $this->insert('work_item_materials', $data);

            return $initQuery;
        }

        /**
         * `insertWorkItemEquipment` Query string that will insert to table `work_item_equipments`
         * @return string
         */
        public function insertWorkItemEquipment($data = [])
        {
            $initQuery = $this->insert('work_item_equipments', $data);

            return $initQuery;
        }

        /**
         * `insertWorkItemManpower` Query string that will insert to table `work_item_manpowers`
         * @return string
         */
        public function insertWorkItemManpower($data = [])
        {
            $initQuery = $this->insert('work_item_manpowers', $data);

            return $initQuery;
        }

        /**
         * `updateWorkItems` Query string that will update specific work item information from table `work_items`
         * @return string
         */
        public function updateWorkItem($id = '', $data = [])
        {
            $initQuery = $this->update('work_items', $id, $data);

            return $initQuery;
        }

        /**
         * `updateWorkItemIndirectCost` Query string that will update specific work item information from table `work_item_indirect_costs`
         * @return string
         */
        public function updateWorkItemIndirectCost($id = '', $data = [])
        {
            $initQuery = $this->update('work_item_indirect_costs', $id, $data);

            return $initQuery;
        }

        /**
         * `updateWorkItemMaterial` Query string that will update specific work item information from table `work_item_materials`
         * @return string
         */
        public function updateWorkItemMaterial($id = '', $data = [])
        {
            $initQuery = $this->update('work_item_materials', $id, $data);

            return $initQuery;
        }

        /**
         * `updateWorkItemEquipment` Query string that will update specific work item information from table `work_item_equipments`
         * @return string
         */
        public function updateWorkItemEquipment($id = '', $data = [])
        {
            $initQuery = $this->update('work_item_equipments', $id, $data);

            return $initQuery;
        }

        /**
         * `updateWorkItemManpower` Query string that will update specific work item information from table `work_item_manpowers`
         * @return string
         */
        public function updateWorkItemManpower($id = '', $data = [])
        {
            $initQuery = $this->update('work_item_manpowers', $id, $data);

            return $initQuery;
        }

        /**
         * `deleteWorkItemEquipment` Query string that will delete specific `work_item_equipments`.
         * @param  boolean $id
         * @return string
         */
        public function deleteWorkItemEquipment($id = false)
        {
            $initQuery = $this->delete('work_item_equipments');

            $initQuery = ($id) ? $initQuery->where(['id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `deleteWorkItemMaterial` Query string that will delete specific `work_item_materials`.
         * @param  boolean $id
         * @return string
         */
        public function deleteWorkItemMaterial($id = false)
        {
            $initQuery = $this->delete('work_item_materials');

            $initQuery = ($id) ? $initQuery->where(['id' => ':id']) : $initQuery;

            return $initQuery;
        }
    }