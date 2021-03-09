<?php 
    namespace App\Model\NewProject;

    include_once "..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class NewProjectQueryHandler extends QueryHandler {
        /**
         * `selectUsers` Query String that will select from table `users` join `employees` and `positions`
         * @return string
         */
        public function selectUsers()
        {
            $fields = [
                'E.id',
                'CONCAT(E.fname," ",E.mname," ",E.lname) as fullname'
            ];

            $joins = [
                'employees E' => 'U.employee_id = E.id',
                'positions P' => 'E.position_id = P.id'
            ];

            $orOneFieldConditions = [
                8,
                17,
                18,
                19,
                20
            ];

            $initQuery = $this->select($fields)
                              ->from('users U')
                              ->join($joins)
                              ->whereOrOneField('P.id', $orOneFieldConditions);

            return $initQuery;
        }

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
                'WI.unit'
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
         * `selectProjectTypes` Query string that will select from table `project_types`.
         * @param  boolean $id
         * @return string
         */
        public function selectProjectTypes($id = false)
        {
            $fields = [
                'PT.id',
                'PT.name'
            ];

            $initQuery = $this->select($fields)
                              ->from('project_types PT');

            $initQuery = ($id) ? $initQuery->where(['PT.id' => ':id']) : $initQuery;

            return $initQuery;
        }
        
        /**
         * `insertProjectTypeList` Query string that will insert to table `project_type_lists`
         * @return string
         */
        public function insertProjectTypeList($data = [])
        {
            $initQuery = $this->insert('project_type_lists', $data);

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
         * `insertIndirectScopeOfWork` Query string that will insert to table `indirect_scope_of_works`
         * @return string
         */
        public function insertIndirectScopeOfWork($data = [])
        {
            $initQuery = $this->insert('indirect_scope_of_works', $data);

            return $initQuery;
        }

        /**
         * `insertScopeOfWork` Query string that will insert to table `scope_of_works`
         * @return string
         */
        public function insertScopeOfWork($data = [])
        {
            $initQuery = $this->insert('scope_of_works', $data);

            return $initQuery;
        }
    }