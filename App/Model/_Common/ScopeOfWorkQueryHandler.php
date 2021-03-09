<?php 
    namespace App\Model\Common;

    include_once "..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class ScopeOfWorkQueryHandler extends QueryHandler {
        /**
         * `selectWorkItems` Query string that will select from table `work_items`
         * @param  boolean $id
         * @return string
         */
        public function selectWorkItems($id = false, $workItemCategoryId = false, $direct = false)
        {
            $fields = [
                'WI.id as work_item_id',
                'WI.cost_code',
                'WI.item_no',
                'WI.name',
                'WI.unit',
                'WIC.name as work_item_category_name'
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
         * `selectSubProjects` Query string that will select from table `sub_projects`.
         * @param  boolean $id
         * @param  boolean $workDisciplineId
         * @return string
         */
        public function selectSubProjects($id = false, $workDisciplineId = false)
        {
            $fields = [
                'SP.id',
                'SP.cost_code',
                'SP.name'
            ];

            $initQuery = $this->select($fields)
                              ->from('sub_projects SP')
                              ->where(['SP.is_active' => ':is_active']);

            $initQuery = ($id)               ? $initQuery->andWhere(['SP.id' => ':id']) : $initQuery;
            $initQuery = ($workDisciplineId) ? $initQuery->andWhere(['SP.work_discipline_id' => ':work_discipline_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectSubProjectTypes` Query string that will select from table `sub_project_types`
         * @param  boolean $id
         * @return string
         */
        public function selectSubProjectTypes($id = false)
        {
            $fields = [
                'SPT.id',
                'SPT.sub_project_id',
                'SPT.cost_code',
                'SPT.name'
            ];

            $initQuery = $this->select($fields)
                              ->from('sub_project_types SPT')
                              ->where(['SPT.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['SPT.id' => ':id']) : $initQuery;

            return $initQuery;
        }
    }