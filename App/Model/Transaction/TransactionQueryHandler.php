<?php 
    namespace App\Model\Transaction;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class TransactionQueryHandler extends QueryHandler { 
        /**
         * `selectTransactions` Query string that will fetch transaction from table `transactions`.
         * @return string
         */
        public function selectTransactions($id = false, $curSig = false)
        {
            $fields = [
                'T.id',
                'T.transaction_no',
                'T.signatory_set_id',
                'T.status as t_status',
                'TA.id as transaction_approval_id',
                'TA.previous_signatory',
                'DATE_FORMAT(TA.date_sended, "%b %d, %Y %h:%i %p") as date_sended',
                'TT.name',
                'TT.code',
                'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as prepared_by',
                'SS.max_level',
                // '"" as action',
                'TA.status as ta_status',
            ];

            $joins = [
                'transaction_types TT'     => 'TT.id = T.transaction_type_id',
                'transaction_approvals TA' => 'T.id = TA.transaction_id',
                'personal_informations PI' => 'PI.id = T.prepared_by',
                'signatory_sets SS'        => 'T.signatory_set_id = SS.id',
            ];

            // $leftJoins = [
            //     'signatories S' => 'S.id = S.signatory_set_id'
            // ];

            $initQuery = $this->select($fields)
                              ->from('transactions T')
                              ->join($joins)
                              // ->leftJoin($leftJoins)
                              ->where(['T.is_active' => ':is_active']);

            $initQuery = ($id)     ? $initQuery->andWhere(['T.id' => ':id'])                   : $initQuery;
            $initQuery = ($curSig) ? $initQuery->andWhere(['TA.current_signatory' => ':cur_sig']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectSignatories` Query string that will select from table `signatories`.
         * @param  boolean $id
         * @param  boolean $transactionId
         * @param  boolean $userId
         * @return string
         */
        public function selectSignatories($id = false, $transactionId = false, $userId = false)
        {
            $fields = [
                'S.id',
                'S.action',
                'S.level',
            ];

            $joins = [
                'signatory_sets SS'          => 'SS.id = S.signatory_set_id',
                'transactions T'             => 'T.signatory_set_id = SS.id',
                'employment_informations EI' => 'S.position_id = EI.position_id',
                'personal_informations PI'   => 'EI.personal_information_id = PI.id',
                'users U'                    => 'PI.id = U.personal_information_id',
            ];

            $initQuery = $this->select($fields)
                              ->from('signatories S')
                              ->join($joins);

            $initQuery = ($id)            ? $initQuery->where(['S.id' => ':id'])             : $initQuery;
            $initQuery = ($transactionId) ? $initQuery->where(['T.id' => ':transaction_id']) : $initQuery;
            $initQuery = ($userId)        ? $initQuery->andWhere(['U.id' => ':user_id'])     : $initQuery;

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
                'TP.signatory_set_id',
                'TP.code'
            ];

            $initQuery = $this->select($fields)
                              ->from('transaction_types TP')
                              ->where(['TP.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['TP.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectPersonalInformations` Query string that will select from table `personal_informations`.
         * @param  boolean $id
         * @return string
         */
        public function selectPersonalInformations($id = false)
        {
            $fields = [
                'PI.id',
                'P.department_id'
            ];

            $joins = [
                'employment_informations IE' => 'PI.id = IE.personal_information_id',
                'positions P'                => 'IE.position_id = P.id',
            ];

            $initQuery = $this->select($fields)
                              ->from('personal_informations PI')
                              ->join($joins)
                              ->where(['PI.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['PI.id' => ':id']) : $initQuery;

            return $initQuery;
        }
        
        /**
         * `selectWorkOrders` Query string that will fetch work order from table `work_orders`.
         * @param  boolean $id
         * @param  boolean $transactionId
         * @return string
         */
        public function selectWorkOrders($id = false, $transactionId = false)
        {
            $fields = [
                'WO.id',
                'WO.transaction_id',
                'WO.work_order_no',
                'WO.temporary_code',
                'WO.additional_details',
                'DATE_FORMAT(WO.updated_at, "%b %d, %Y") as date_prepared',
                'P.project_code',
                'P.name as project_name',
                'P.location as project_location',
                'P.contract_days',
                'DATE_FORMAT(P.date_started, "%b %d, %Y") as start_date_contract',
                '"" as status',
                'CL.id as client_id',
                'CL.name as client_name',
            ];

            $leftJoins = [
                'clients CL' => 'CL.id = WO.client_id',
                'projects P' => 'WO.id = P.work_order_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('work_orders WO')
                              ->leftJoin($leftJoins)
                              ->where(['WO.is_active' => ':is_active']);

            $initQuery = ($id)            ? $initQuery->andWhere(['WO.id' => ':id'])                         : $initQuery;
            $initQuery = ($transactionId) ? $initQuery->andWhere(['WO.transaction_id' => ':transaction_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectProjects` Query string that will fetch work order from table `projects`.
         * @param  boolean $id
         * @param  boolean $transactionId
         * @return string
         */
        public function selectProjects($id = false, $transactionId = false)
        {
            $fields = [
                'P.id',
                'P.transaction_id',
                'P.project_code',
                'P.name as project_name',
                'P.location as project_location',
                'P.contract_days',
                'DATE_FORMAT(P.date_started, "%b %d, %Y") as start_date_contract',
                'WO.temporary_code',
                'WO.work_order_no',
                'C.id as client_id',
                'C.name as client_name',
                'T.message'
            ];

            $leftJoins = [
                'work_orders WO' => 'WO.id = P.work_order_id',
                'clients C'      => 'C.id = WO.client_id',
                'transactions T' => 'T.id = P.transaction_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('projects P')
                              ->leftJoin($leftJoins)
                              ->where(['P.is_active' => ':is_active']);

            $initQuery = ($id)            ? $initQuery->andWhere(['P.id' => ':id'])                         : $initQuery;
            $initQuery = ($transactionId) ? $initQuery->andWhere(['P.transaction_id' => ':transaction_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectTransactionComments` Query string that will select from table `transaction_comments`.
         * @param  string $id
         * @param  string $transactionId
         * @return string
         */
        public function selectTransactionComments($id = '', $transactionId = '')
        {
            $fields = [
                'TC.id',
                'TC.reference_id',
                'TC.comment',
                'TC.comment_by',
                'TC.created_at',
                'EI.position_id',
                'P.name as position_name',
                'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as full_name',
            ];

            $joins = [
                'users U'                    => 'TC.comment_by = U.id',
                'personal_informations PI'   => 'U.personal_information_id = PI.id',
                'employment_informations EI' => 'PI.id = EI.personal_information_id',
                'positions P'                => 'EI.position_id = P.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('transaction_comments TC')
                              ->join($joins)
                              ->where(['P.is_active' => ':is_active']);

            $initQuery = ($id)            ? $initQuery->andWhere(['TC.id' => ':id'])                         : $initQuery;
            $initQuery = ($transactionId) ? $initQuery->andWhere(['TC.transaction_id' => ':transaction_id']) : $initQuery;

            $initQuery = $initQuery->orderBy('TC.id', 'ASC');

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
         * `updateTransactionApproval` Query string that will update specific work order from table `transaction_approvals`
         * @return string
         */
        public function updateTransactionApproval($id = '', $data = [])
        {
            $initQuery = $this->update('transaction_approvals', $id, $data);

            return $initQuery;
        }

        /**
         * `updateTransaction` Query string that will update specific work order from table `transaction_approvals`
         * @return string
         */
        public function updateTransaction($id = '', $data = [])
        {
            $initQuery = $this->update('transactions', $id, $data);

            return $initQuery;
        }
    }