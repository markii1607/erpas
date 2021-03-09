<?php 
    namespace App\Model\Common;

    include_once "..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class IndirectWorkItemQueryHandler extends QueryHandler {
        /**
         * `selectWorkItems` Query string that will select from table `work_items`
         * @param  boolean $id
         * @return string
         */
        public function selectWorkItems($id = false, $workItemCategoryId = false, $direct = false)
        {
            $fields = [
                'WI.id as work_item_id',
                'WI.work_item_category_id',
                'WI.cost_code',
                'WI.item_no',
                'WI.name',
                'WI.unit',
                'WIC.cost_code as wic_cost_code',
                'WIC.name as work_item_category_name',
            ];

            $initQuery = $this->select($fields)
                              ->from('work_items WI')
                              ->join(['work_item_categories WIC' => 'WIC.id = WI.work_item_category_id'])
                              ->where(['WI.status' => ':status']);

            $initQuery = ($id)                 ? $initQuery->andWhere(['WI.id' => ':id'])                                       : $initQuery;
            $initQuery = ($workItemCategoryId) ? $initQuery->andWhere(['WI.work_item_category_id' => ':work_item_category_id']) : $initQuery;
            $initQuery = ($direct)             ? $initQuery->andWhere(['WI.direct' => 1])                                       : $initQuery->andWhere(['WI.direct' => 0]);
        
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
                'E.id'
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

    }