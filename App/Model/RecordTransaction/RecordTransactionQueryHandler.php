<?php 
    namespace App\Model\RecordTransaction;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class RecordTransactionQueryHandler extends QueryHandler {

        /**
         * `selectEmploymentInformations` Query string that will select from table `employment_informations`.
         * @param  boolean $id
         * @return string
         */
        public function selectEmploymentInformations($id = false)
        {
            $fields = [
                'EI.position_id',
                'P.department_id'
            ];

            $joins = [
            	'positions P'   => 'EI.position_id = P.id',
            	'departments D' => 'P.department_id = D.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('employment_informations EI')
                              ->join($joins)
                              ->where(['EI.personal_information_id' => ':personal_information_id']);

            $initQuery = ($id) ? $initQuery->andWhere(['EI.id' => ':id']) : $initQuery;

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
         * @param  boolean $code
         * @return string
         */
        public function selectTransactionTypes($id = false, $code = false)
        {
            $fields = [
                'TP.id',
                'TP.signatory_set_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('transaction_types TP')
                              ->where(['TP.is_active' => ':is_active']);

            $initQuery = ($id)   ? $initQuery->andWhere(['TP.id' => ':id'])     : $initQuery;
            $initQuery = ($code) ? $initQuery->andWhere(['TP.code' => ':code']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectSignatorySets` Query string that will select from table `signatory_sets`.
         * @param  boolean $id
         * @param  boolean $menuId
         * @return string
         */
        public function selectSignatorySets($id = false, $menuId = false)
        {
            $fields = [
                'SS.id',
                'SS.set_no'
            ];

            $initQuery = $this->select($fields)
                              ->from('signatory_sets SS')
                              ->where(['status' => ':status']);

            $initQuery = ($id)     ? $initQuery->andWhere(['SS.id' => ':id'])           : $initQuery;
            $initQuery = ($menuId) ? $initQuery->andWhere(['SS.menu_id' => ':menu_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectTransactionApprovals` Query string that will select from table `signatories`.
         * @param  boolean $id
         * @param  boolean $transactionId
         * @param  boolean $userId
         * @return string
         */
        public function selectTransactionApprovals($id = false, $transactionId = false, $userId = false)
        {
            $fields = [
                'TA.id',
            ];

            $initQuery = $this->select($fields)
                              ->from('transaction_approvals TA');

            $initQuery = ($id)            ? $initQuery->where(['TA.id' => ':id'])                                    : $initQuery;
            $initQuery = ($transactionId) ? $initQuery->where(['TA.transaction_id' => ':transaction_id'])            : $initQuery;
            $initQuery = ($userId)        ? $initQuery->andWhere(['TA.current_signatory' => ':user_id'])             : $initQuery;

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
         * `insertSignatorySet` Query string that will insert to table `signatory_sets`.
         * @return string
         */
        public function insertSignatorySet($data = [])
        {
            $initQuery = $this->insert('signatory_sets', $data);

            return $initQuery;
        }

        /**
         * `insertSignatory` Query string that will insert to table `signatories`.
         * @return string
         */
        public function insertSignatory($data = [])
        {
            $initQuery = $this->insert('signatories', $data);

            return $initQuery;
        }

        /**
         * `insertTransactionAttachment` Query string that will insert to table `transaction_attachments`.
         * @return string
         */
        public function insertTransactionAttachment($data = [])
        {
            $initQuery = $this->insert('transaction_attachments', $data);

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