<?php 
    namespace App\Model\WorkItems;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class WorkItemsQueryHandler extends QueryHandler { 
        /**
         * `selectWorkItems` Query string that will fetch work items from table `work_items`.
         * @return string
         */
        public function selectWorkItems($id = false, $wicId = false)
        {
            $fields = [
                'WI.id',
                'WI.work_item_category_id',
                'WI.wbs',
                'WI.item_no',
                'WI.name',
                'WI.unit',
                'WIC.name as work_item_category_name',
                'WIC.code as wic_wbs'
            ];

            $initQuery = $this->select($fields)
                              ->from('work_items WI')
                              ->join(['work_item_categories WIC' => 'WIC.id = WI.work_item_category_id'])
                              ->where(['WI.is_active' => ':is_active']);

            $initQuery = ($id)    ? $initQuery->andWhere(['WI.id' => ':id'])                                       : $initQuery;
            $initQuery = ($wicId) ? $initQuery->andWhere(['WI.work_item_category_id' => ':work_item_category_id']) : $initQuery;

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

            $initQuery = $this->select($fields)
                              ->from('work_item_categories WIC')
                              ->where(['WIC.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['WIC.id' => ':id']) : $initQuery;

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

        /**
         * `updateWorkItem` Query string that will update specific work items from table `work_items`
         * @return string
         */
        public function updateWorkItem($id = '', $data = [])
        {
            $initQuery = $this->update('work_items', $id, $data);

            return $initQuery;
        }
    }