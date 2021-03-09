<?php 
    namespace App\Model\TransactionApproval;

    include_once "..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class TransactionApprovalQueryHandler extends QueryHandler { 
        /**
         * `selectTransactions` Query string that will select from table `transactions`
         * @return string
         */
        public function selectTransactions($id = false, $currentSigId = false)
        {
            $fields = [
                'T.id',
                'T.transaction_type_id',
                'T.department_id',
                'T.transaction_no',
                'T.transaction_date',
                'TT.signatory_set_id',
                'TT.code as transaction_type_code',
                'TT.name as transaction_type_name',
                'D.name as department_name',
                'CONCAT(E.fname," ",E.mname," ",E.lname) as prepared_by',
                'SS.max_level',
                'TA.id as transaction_approval_id'
            ];

            $joins = [
                'departments D'            => 'D.id = T.department_id',
                'transaction_approvals TA' => 'T.id = TA.transaction_id',
                'employees E'              => 'E.id = T.prepared_by',
                'transaction_types TT'     => 'T.transaction_type_id = TT.id',
                'signatory_sets SS'        => 'TT.signatory_set_id = SS.id',
            ];

            $whereConditions = [
                'T.status'  => ':transaction_status',
                'TA.status' => ':transaction_approval_status'
            ];

            $initQuery = $this->select($fields)
                              ->from('transactions T')
                              ->join($joins)
                              ->where($whereConditions);

            $initQuery = ($id)           ? $initQuery->andWhere(['T.id' => ':id'])                                : $initQuery;
            $initQuery = ($currentSigId) ? $initQuery->andWhere(['TA.current_signatory' => ':current_signatory']) : $initQuery;

            return $initQuery;
        }


        /**
         * `selectTransactionDocuments` Query string that will select from table `transaction_documents`
         * @param  boolean $id
         * @param  boolean $transactionTypeId
         * @return string
         */
        public function selectTransactionDocuments($id = false, $transactionTypeId = false)
        {
            $fields = [
                'TD.id',
                'TD.name',
                'TD.link_service'
            ];

            $initQuery = $this->select($fields)
                              ->from('transaction_documents TD');

            $initQuery = ($id) ? $initQuery->where(['TD.id' => ':id']) : $initQuery;
            $initQuery = ($transactionTypeId) ? $initQuery->where(['TD.transaction_type_id' => ':transaction_type_id']) : $initQuery;

            return $initQuery;
        }

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
                'P.boq',
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
    }