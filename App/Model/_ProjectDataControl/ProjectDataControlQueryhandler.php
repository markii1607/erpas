<?php 
    namespace App\Model\ProjectDataControl;

    include_once "..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class ProjectDataControlQueryHandler extends QueryHandler {

        /**
         * `selectProjects` Query string that will select projects from table `projects` join to table `employees`.
         * @return string
         */
        public function selectProjects($id = false)
        {
            $fields = [
                'P.id',
                'P.project_code',
                'P.name',
                'P.location',
                'P.project_manager',
                'P.boq_approval',
                'P.status',
                'P.transaction_id',
                'T.status as transaction_status',
                'CONCAT(E.fname," ",E.mname," ",E.lname) as project_manager_name'
            ];

            $leftJoins = [
                'employees E'    => 'P.project_manager = E.id',
                'transactions T' => 'T.id = P.transaction_id'
            ];

            $conditions = [
                'P.status'      => ':status',
                'P.awarded'     => ':awarded',
                'P.not_awarded' => ':not_awarded',
                'P.ongoing'     => ':ongoing',
                'P.finished'    => ':finished'
            ];

            $andOrConditions = [
                0,
                1
            ];

            ($id) ? $conditions['P.id'] = ':id' : '';

            $initQuery = $this->select($fields)
                              ->from('projects P')
                              ->leftJoin($leftJoins)
                              ->where($conditions)
                              ->whereAndOrOneField('P.estimate', $andOrConditions)
                              ->orderBy('P.project_code', 'desc');

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
         * `selectWorkItems` Query string that will select work items from `work_items` table.
         * @return string
         */
        public function selectWorkItems($id = false, $direct = false)
        {
            $fields = [
                'WI.id',
                'WI.cost_code',
                'WI.name',
                'WI.item_no',
                'WI.unit',
                'WI.direct'
            ];

            $initQuery = $this->select($fields)
                              ->from('work_items WI');

            $initquery = ($direct) ? $initQuery->where(['WI.direct' => ':direct']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectScopeOfWorks` Query string that will select scope of works of specific project from `scope_of_works` table.
         * @param  boolean $id
         * @param  boolean $projectTypeId
         * @return string
         */
        public function selectScopeOfWorks($id = false, $projectTypeListId = false)
        {
            $fields = [
                'SOW.id as sow_id',
                'SOW.work_item_id as id',
                'SOW.quantities',
                'WI.unit',
                'WI.item_no',
                'WI.name'
            ];

            $initQuery = $this->select($fields)
                              ->from('scope_of_works SOW')
                              ->join(['work_items WI' => 'SOW.work_item_id = WI.id'])
                              ->where(['SOW.project_type_list_id' => ':project_type_list_id']);

            return $initQuery;
        }

        /**
         * `selectProjectTypes` Query string that will select from table `project_types`.
         * @return string
         */
        public function selectProjectTypes()
        {
            $fields = [
                'PT.id',
                'PT.name'
            ];

            $initQuery = $this->select($fields)
                              ->from('project_types PT');

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

            $conditions = [];

            ($id)        ? $conditions['PTL.id']           = ':id' : '';
            ($projectId) ? $conditions['PTL.project_id']   = ':project_id' : '';

            $initQuery = $this->select($fields)
                              ->from('project_type_lists PTL')
                              ->join($joins)
                              ->where($conditions);

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
                'WI.id',
                'ISOW.id as isow_id',
                'ISOW.quantities',
                'WI.item_no',
                'WI.name',
                'WI.unit'
            ];

            $initQuery = $this->select($fields)
                              ->from('indirect_scope_of_works ISOW')
                              ->join(['work_items WI' => 'WI.id = ISOW.work_item_id']);

            $initQuery = ($projectId) ? $initQuery->where(['ISOW.project_id' => ':project_id']) : $initQuery;

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
         * `insertProjectTypeList` Query string that will insert to table `project_type_lists`
         * @return string
         */
        public function insertProjectTypeList($data = [])
        {
            $initQuery = $this->insert('project_type_lists', $data);

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
         * `insertIndirectScopeOfWork` Query string that will insert to table `indirect_scope_of_works`
         * @return string
         */
        public function insertIndirectScopeOfWork($data = [])
        {
            $initQuery = $this->insert('indirect_scope_of_works', $data);

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
         * `updateProject` Query string that will update to table `projects`
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function updateProject($id = '', $data = [])
        {
            $initQuery = $this->update('projects', $id, $data);

            return $initQuery;
        }

        /**
         * `updateScopeOfWork` Query string that will update to table `scope_of_works`
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function updateScopeOfWork($id = '', $data = [])
        {
            $initQuery = $this->update('scope_of_works', $id, $data);

            return $initQuery;
        }

        /**
         * `updateIndirectScopeOfWork` Query string that will update to table `indirect_scope_of_works`
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function updateIndirectScopeOfWork($id = '', $data = [])
        {
            $initQuery = $this->update('indirect_scope_of_works', $id, $data);

            return $initQuery;
        }

        /**
         * `updateProjectTypeList` Query string that will update to table `project_type_lists`
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function updateProjectTypeList($id = '', $data = [])
        {
            $initQuery = $this->update('project_type_lists', $id, $data);

            return $initQuery;
        }

        /**
         * `deleteScopeOfWorks` Query string that will delete specific scope of works via projectTypeListId.
         * @param  boolean $id
         * @param  boolean $projectTypeListId
         * @return string
         */
        public function deleteScopeOfWorks($id = false, $projectTypeListId = false)
        {
            $initQuery = $this->delete('scope_of_works');

            $initquery = ($id)                ? $initQuery->where(['id' => ':id'])                                     : $initQuery;
            $initquery = ($projectTypeListId) ? $initQuery->where(['project_type_list_id' => ':project_type_list_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `deleteIndirectScopeOfWorks` Query string that will delete specific indirect scope of works.
         * @param  boolean $id
         * @return string
         */
        public function deleteIndirectScopeOfWorks($id = false)
        {
            $initQuery = $this->delete('indirect_scope_of_works');

            $initquery = ($id) ? $initQuery->where(['id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `deleteProject` Query string that will delete specific project.
         * @param  boolean $id
         * @return string
         */
        public function deleteProject($id = false)
        {
            $initQuery = $this->delete('projects');

            $initquery = ($id) ? $initQuery->where(['id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `deleteProjectTypeList` Query string that will delete specific project type list.
         * @param  boolean $id
         * @return string
         */
        public function deleteProjectTypeList($id = false)
        {
            $initQuery = $this->delete('project_type_lists');

            $initquery = ($id) ? $initQuery->where(['id' => ':id']) : $initQuery;

            return $initQuery;
        }
    }