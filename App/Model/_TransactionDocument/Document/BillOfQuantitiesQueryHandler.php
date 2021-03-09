<?php 
    namespace App\Model\TransactionDocument\Document;

    // include_once "..\TransactionDocumentQueryHandler.php";

    // use App\Model\TransactionDocument\TransactionDocumentQueryHandler;

    // include_once "..\..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class BillOfQuantitiesQueryHandler extends QueryHandler {
        /**
         * `selectProjects` Query string that will select from table `projects`
         * @param  boolean $id
         * @param  boolean $transactionId
         * @return string
         */
        public function selectProjects($id = false, $transactionId = false)
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

            $initQuery = $this->select($fields)
                              ->from('projects P')
                              ->leftJoin($leftJoins);

            $initQuery = ($transactionId) ? $initQuery->where(['P.transaction_id' => ':transaction_id']) : $initQuery;

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
    }