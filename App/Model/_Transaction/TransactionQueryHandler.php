<?php 
    namespace App\Model\Transaction;

    include_once "..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class TransactionQueryHandler extends QueryHandler {
        /**
         * `selectEmployees` Query string that will select from table `employees.`
         * @param  boolean $id
         * @return string
         */
        public function selectEmployees($id = false)
        {
            $fields = [
                'E.department_id',
                'E.position_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('employees E')
                              ->where(['E.status' => 1]);

            $initQuery = ($id) ? $initQuery->andWhere(['E.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectTransactions` Query string that will select from table `transactions`.
         * @return string
         */
        public function selectTransactions()
        {
            $fields = [
                'T.id',
                'T.transaction_no'
            ];

            $initQuery = $this->select($fields)
                              ->from('transactions T');

            return $initQuery;
        }

        /**
         * `selectSignatories` Query string that will select from table `signatories`.
         * @param  boolean $id
         * @param  boolean $signatorySetId
         * @return string
         */
        public function selectSignatories($id = false, $signatorySetId = false, $level = false, $positionId = false)
        {
            $fields = [
                'U.id as user_id',
                'S.level'
            ];

            $joins = [
                'employees E' => 'S.position_id = E.position_id',
                'users U'     => 'E.id = U.employee_id'
            ];

            $whereConditions = [
                'E.status' => 1
            ];

            $initQuery = $this->select($fields)
                              ->from('signatories S')
                              ->join($joins)
                              ->where($whereConditions);

            $initQuery = ($level)          ? $initQuery->andWhere(['S.level' => ':level'])             : $initQuery;
            $initQuery = ($positionId)     ? $initQuery->andWhere(['S.position_id' => ':position_id']) : $initQuery;
            $initQuery = ($signatorySetId) ? $initQuery->andWhere(['S.signatory_set_id' => ':signatory_set_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectTransactionTypes` Query string that will select from table `transaction_types`.
         * @param  boolean $id
         * @return string
         */
        public function selectTransactionTypes($id = false)
        {
            $fields = [
                'TP.id',
                'TP.signatory_set_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('transaction_types TP');

            $initQuery = ($id) ? $initQuery->where(['TP.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `insertTransaction` Query string that will insert to table `transactions`.
         * @return string
         */
        public function insertTransaction($data = [])
        {
            $initQuery = $this->insert('transactions', $data);

            return $initQuery;
        }

        /**
         * `insertTransactionApproval` Query string that will insert to table `transaction_approvals`.
         * @return string
         */
        public function insertTransactionApproval($data = [])
        {
            $initQuery = $this->insert('transaction_approvals', $data);

            return $initQuery;
        }

        // /**
        //  * `insertTransactionDocument` Query string that will insert to table `transaction_documents`.
        //  * @return string
        //  */
        // public function insertTransactionDocument($data = [])
        // {
        //     $initQuery = $this->insert('transaction_documents', $data);

        //     return $initQuery;
        // }

        /**
         * `updateTransactionApproval` Query string that will update to table `transaction_approvals`
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function updateTransactionApproval($id = '', $data = [])
        {
            $initQuery = $this->update('transaction_approvals', $id, $data);

            return $initQuery;
        }

        /**
         * `updateTransaction` Query string that will update to table `transactions`
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function updateTransaction($id = '', $data = [])
        {
            $initQuery = $this->update('transactions', $id, $data);

            return $initQuery;
        }    
    }