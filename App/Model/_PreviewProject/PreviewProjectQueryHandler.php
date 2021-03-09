<?php 
    namespace App\Model\PreviewProject;

    include_once "..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class PreviewProjectQueryHandler extends QueryHandler {
        /**
         * `selectProjects` Query String that will select from table `projects`
         * @return string
         */
        public function selectProjects($id = false)
        {
            $fields = [
                'P.id',
                'P.project_code',
                'P.name as project_name',
                'P.location as project_location',
                'P.project_manager',
                'P.boq_approval',
                'P.status',
                'P.is_revision',
                'P.transaction_id',
                'DATE_FORMAT(P.updated_at, "%m/%d/%Y") as date_revised',
                'T.status as transaction_status',
                'CONCAT(E.fname," ",E.mname," ",E.lname) as project_manager_name'
            ];

            $leftJoins = [
                'employees E'    => 'P.project_manager = E.id',
                'transactions T' => 'T.id = P.transaction_id'
            ];


            $initQuery = $this->select($fields)
                              ->from('projects P')
                              ->leftJoin($leftJoins)
                              ->where(['P.status' => ':status']);

            $initQuery = ($id) ? $initQuery->andWhere(['P.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectIndirectScopeOfWorks` Query string that will select from table `indirect_scope_of_works`.
         * @param  boolean $id
         * @param  boolean $projectId
         * @return string
         */
        public function selectIndirectScopeOfWorks($id = false, $projectId = false)
        {
            $fields = [
                'ISOW.id',
                'ISOW.quantities as quantity',
                'WI.item_no',
                'WI.name',
                'WI.unit',
                'WI.work_item_category_id',
                'WI.cost_code',
                'WIC.cost_code as wic_cost_code',
                'WIC.name as work_item_category_name'
            ];

            $joins = [
                'work_items WI'            => 'WI.id = ISOW.work_item_id',
                'work_item_categories WIC' => 'WIC.id = WI.work_item_category_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('indirect_scope_of_works ISOW')
                              ->join($joins)
                              ->where(['ISOW.status' => 1]);

            $initQuery = ($id) ? $initQuery->andWhere(['ISOW.id' => ':id']) : $initQuery;
            $initQuery = ($projectId) ? $initQuery->andWhere(['ISOW.project_id' => ':project_id']) : $initQuery;
        
            return $initQuery;
        }

        /**
         * `selectProjectTypeLists` Query string that will select from table `project_type_lists`.
         * @param  boolean $id
         * @param  boolean $projectId
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

            $initQuery = $this->select($fields)
                              ->from('project_type_lists PTL')
                              ->join(['project_types PT' => 'PT.id = PTL.project_type_id'])
                              ->where(['PTL.status' => 1]);

            $initQuery = ($id)        ? $initQuery->andWhere(['PTL.id' => ':id'])                 : $initQuery;
            $initQuery = ($projectId) ? $initQuery->andWhere(['PTL.project_id' => ':project_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectScopeOfWorks` Query string that will select from table `scope_of_works`.
         * @param  boolean $id
         * @param  boolean $projectTypeListId
         * @return string
         */
        public function selectScopeOfWorks($id = false, $projectTypeListId = false)
        {
            $fields = [
                'SOW.id',
                'SOW.quantities as quantity',
                'WI.id as work_item_id',
                'WI.item_no',
                'WI.name',
                'WI.unit',
                'WI.work_item_category_id',
                'WI.cost_code',
                'WIC.name as work_item_category_name',
                'WIC.cost_code as wic_cost_code'
            ];

            $joins = [
                'work_items WI'            => 'WI.id = SOW.work_item_id',
                'work_item_categories WIC' => 'WIC.id = WI.work_item_category_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('scope_of_works SOW')
                              ->join($joins);

            $initQuery = ($id) ? $initQuery->where(['SOW.id' => ':id']) : $initQuery;
            $initQuery = ($projectTypeListId) ? $initQuery->where(['SOW.project_type_list_id' => ':project_type_list_id']) : $initQuery;
        
            return $initQuery;
        }

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

        /**
         * `insertProjectTransaction` Query string that will insert to table `project_transactions`
         * @return string
         */
        public function insertProjectTransaction($data = [])
        {
            $initQuery = $this->insert('project_transactions', $data);

            return $initQuery;
        }
        
        /**
         * `updateProject` Query string that will update specific indirect item information from table `indirect_scope_of_works`
         * @return string
         */
        public function updateProject($id = '', $data = [], $fk = '', $fkValue = '')
        {
            $initQuery = $this->update('projects', $id, $data);

            return $initQuery;
        }

        /**
         * `updateIndirectScopeOfWork` Query string that will update specific indirect item information from table `indirect_scope_of_works`
         * @return string
         */
        public function updateIndirectScopeOfWork($id = '', $data = [], $fk = '', $fkValue = '')
        {
            $initQuery = $this->update('indirect_scope_of_works', $id, $data);

            return $initQuery;
        }

        /**
         * `updateProjectTypeList` Query string that will update specific indirect item information from table `project_type_lists`
         * @return string
         */
        public function updateProjectTypeList($id = '', $data = [])
        {
            $initQuery = $this->update('project_type_lists', $id, $data);

            return $initQuery;
        }

        /**
         * `updateScopeOfWork` Query string that will update specific scope of work information from table `scope_of_works`
         * @return string
         */
        public function updateScopeOfWork($id = '', $data = [], $fk = '', $fkValue = '')
        {
            $initQuery = $this->update('scope_of_works', $id, $data, $fk, $fkValue);

            return $initQuery;
        }
    }