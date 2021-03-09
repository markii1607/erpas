<?php 
    namespace App\Model\EstimateConfiguration;

    include_once "..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class EstimateConfigurationQueryHandler extends QueryHandler {

        /**
         * `selectMaterials` Query string that will select material informations.
         * @return string
         */
        public function selectMaterials($id = false, $materialTypeId = false, $name = false)
        {
            $fields = [
                'M.id',
                'M.material_type_id',
                'M.name',
                'M.cost_code as m_cost_code',
                'MT.cost_code as mt_cost_code',
                'MT.name as material_type_name',
                'DATE_FORMAT(M.created_at, "%m/%d/%Y") as date_added'
            ];

            $conditions = [
                'M.status' => ':status'
            ];

            ($id)             ? $conditions['M.id']               = ':id'               : '';
            ($materialTypeId) ? $conditions['M.material_type_id'] = ':material_type_id' : '';
            ($name)           ? $conditions['M.name']             = ':name'             : '';

            $initQuery = $this->select($fields)
                              ->from('materials M')
                              ->join(['material_types MT' => 'M.material_type_id = MT.id'])
                              ->where($conditions);

            return $initQuery;

        }

        /**
         * `selectWorkItems` Fetching of selected items
         * @param  boolean $id
         * @return string
         */
        public function selectWorkItems($id = false)
        {
            $fields = [
                'WI.id',
                'WI.cost_code',
                'WI.item_no',
                'WI.name',
                'WI.unit',
                'WIC.name as work_item_category_name'
            ];

            $initQuery = $this->select($fields)
                              ->from('work_items WI')
                              ->join(['work_item_categories WIC' => 'WI.work_item_category_id = WIC.id']);

            $initQuery = ($id) ? $initQuery->where(['id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectMaterialMultipliers` Query string that will select multipliers of every material.
         * @return string
         */
        public function selectMaterialMultipliers()
        {
            $fields = [
                'MM.id',
                'MM.material_id',
                'MM.multiplier',
                'MM.remarks'
            ];


            $initQuery = $this->select($fields)
                              ->from('material_multipliers MM')
                              ->where(['MM.status' => ':status'])
                              ->orderBy('MM.id', 'desc');

            return $initQuery;
        }

        /**
         * `selectWorkItemMultipliers` Query string that will select multipliers of every work item.
         * @return string
         */
        public function selectWorkItemMultipliers()
        {
            $fields = [
                'WIM.id',
                'WIM.work_item_id',
                'WIM.multiplier',
                'WIM.remarks'
            ];

            $initQuery = $this->select($fields)
                              ->from('work_item_multipliers WIM')
                              ->where(['WIM.status' => ':status'])
                              ->orderBy('WIM.id', 'desc');

            return $initQuery;
        }

        /**
         * `insertMaterialMultiplier` Query string that will insert to table `material_multipliers`
         * @param  array  $data
         * @return string
         */
        public function insertMaterialMultiplier($data = [])
        {
            $initQuery = $this->insert('material_multipliers', $data);

            return $initQuery;
        }

        /**
         * `insertWorkItemMultiplier` Query string that will insert to table `work_item_multipliers`
         * @param  array  $data
         * @return string
         */
        public function insertWorkItemMultiplier($data = [])
        {
            $initQuery = $this->insert('work_item_multipliers', $data);

            return $initQuery;
        }

        /**
         * `softDeleteMaterialMultiplier` Query string that will update specific material multiplier's status from `material_multipliers` table.
         * @return string
         */
        public function softDeleteMaterialMultiplier($id = '', $data = [])
        {
            $initQuery = $this->update('material_multipliers', $id, $data);

            return $initQuery;
        }

        /**
         * `softDeleteWorkItemMultiplier` Query string that will update specific work item multiplier's status from `work_item_multipliers` table.
         * @return string
         */
        public function softDeleteWorkItemMultiplier($id = '', $data = [])
        {
            $initQuery = $this->update('work_item_multipliers', $id, $data);

            return $initQuery;
        }
    }