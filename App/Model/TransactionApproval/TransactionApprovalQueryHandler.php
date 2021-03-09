<?php 
    namespace App\Model\TransactionApproval;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class TransactionApprovalQueryHandler extends QueryHandler {

        /**
         * `selectPersonalInformations` Query string that will select from table `personal_informations.`
         * @param  boolean $id
         * @return string
         */
        // public function selectPersonalInformations($id = false)
        // {
        //     $fields = [
        //         'E.department_id',
        //         'E.position_id'
        //     ];

        //     $joins = [
        //      'employment_informations EI' => 'PI.id = EI.personal_information_id'
        //     ];

        //     $initQuery = $this->select($fields)
        //                       ->from('personal_informations PI')
        //                       ->where(['PI.is_active' => ':is_active']);

        //     $initQuery = ($id) ? $initQuery->andWhere(['PI.id' => ':id']) : $initQuery;

        //     return $initQuery;
        // }

        /**
         * `selectTransactions` Query string that will select from table `transactions`.
         * @return string
         */
        // public function selectTransactions()
        // {
        //     $fields = [
        //         'T.id',
        //         'T.transaction_no'
        //     ];

        //     $initQuery = $this->select($fields)
        //                       ->from('transactions T');

        //     return $initQuery;
        // }

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
                'employment_informations EI' => 'S.position_id = EI.position_id',
                'users U'                    => 'EI.personal_information_id = U.personal_information_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('signatories S')
                              ->join($joins)
                              ->where(['U.is_active' => ':is_active']);

            $initQuery = ($id)             ? $initQuery->andWhere(['S.id' => ':id'])                             : $initQuery;
            $initQuery = ($level)          ? $initQuery->andWhere(['S.level' => ':level'])                       : $initQuery;
            $initQuery = ($positionId)     ? $initQuery->andWhere(['S.position_id' => ':position_id'])           : $initQuery;
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
         * `selectTransactionApprovals` Query string that will select from table `signatories`.
         * @param  boolean $id
         * @param  boolean $transactionId
         * @param  boolean $userId
         * @param  boolean $previousSignatory
         * @return string
         */
        public function selectTransactionApprovals($id = false, $transactionId = false, $userId = false, $previousSignatory = false)
        {
            $fields = [
                'TA.id',
                'TA.status as ta_status'
            ];

            $initQuery = $this->select($fields)
                              ->from('transaction_approvals TA');

            $initQuery = ($id)                ? $initQuery->where(['TA.id' => ':id'])                                    : $initQuery;
            $initQuery = ($transactionId)     ? $initQuery->where(['TA.transaction_id' => ':transaction_id'])            : $initQuery;
            $initQuery = ($userId)            ? $initQuery->andWhere(['TA.current_signatory' => ':user_id'])             : $initQuery;
            $initQuery = ($previousSignatory) ? $initQuery->andWhere(['TA.previous_signatory' => ':previous_signatory']) : $initQuery;

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

        /**
         * `insertTransactionComment` Query string that will insert to table `transaction_comments`.
         * @return string
         */
        public function insertTransactionComment($data = [])
        {
            $initQuery = $this->insert('transaction_comments', $data);

            return $initQuery;
        }

        /**
         * `insertNotification` Query string that will insert to table `notifications`.
         * @return string
         */
        public function insertNotification($data = [])
        {
            $initQuery = $this->insert('notifications', $data);

            return $initQuery;
        }

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