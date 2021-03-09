<?php 
    namespace App\Model\WorkOrder;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class WorkOrderQueryHandler extends QueryHandler { 

        /**
         * `selectWorkOrders` Query string that will fetch work order from table `work_orders`.
         * @return string
         */
        public function selectWorkOrders($id = false, $name = false)
        {
            $fields = [
                'WO.id',
                'WO.transaction_id',
                'WO.work_order_no',
                'WO.temporary_code',
                'WO.additional_details',
                'WO.contract_days',
                'DATE_FORMAT(WO.start_date_contract, "%b %d, %Y") as start_date_contract',
                'DATE_FORMAT(WO.updated_at, "%b %d, %Y") as date_prepared',
                'P.project_code',
                'P.name as project_name',
                'P.location as project_location',
                'CL.id as client_id',
                'CL.name as client_name',
                'T.status as t_status',
            ];

            $leftJoins = [
                'clients CL'     => 'WO.client_id = CL.id',
                'projects P'     => 'P.work_order_id = WO.id',
                'transactions T' => 'T.id = WO.transaction_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('work_orders WO')
                              ->leftJoin($leftJoins)
                              ->where(['WO.is_active' => ':is_active', 'WO.created_by' => ':created_by']);

            $initQuery = ($id) ? $initQuery->andWhere(['WO.id' => ':id']) : $initQuery;

            $initQuery = $initQuery->orderBy('WO.id', 'DESC');

            return $initQuery;
        }

        /**
         * `selectDefaultSignatories` Query string that will fetch work order from table `default_signatories`.
         * @return string
         */
        public function selectDefaultSignatories($id = false, $name = false)
        {
            $fields = [
                'DS.id',
            ];

            $initQuery = $this->select($fields)
                              ->from('default_signatories DS')
                              ->where(['DS.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['DS.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectSignatories` Query string that will select from table `signatories`.
         * @param  string $id
         * @param  string $transactionId
         * @return string
         */
        public function selectSignatories($id = false, $transactionId = false)
        {
            $fields = [
                'S.id',
                'S.action',
                'P.name as position_name',
                'P.code as position_code',
                'P.id as position_id',
                'CONCAT(PI.fname, " ", PI.lname) as full_name',
                'U.id as user_id',
            ];

            $joins = [
                'signatory_sets SS'          => 'SS.id = S.signatory_set_id',
                'transactions T'             => 'T.signatory_set_id = SS.id',
                'employment_informations EI' => 'S.position_id = EI.position_id',
                'personal_informations PI'   => 'EI.personal_information_id = PI.id',
                'users U'                    => 'PI.id = U.personal_information_id',
                'positions P'                => 'P.id = EI.position_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('signatories S')
                              ->join($joins);

            $initQuery = ($id)            ? $initQuery->where(['S.id' => ':id'])             : $initQuery;
            $initQuery = ($transactionId) ? $initQuery->where(['T.id' => ':transaction_id']) : $initQuery;

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
                'TA.status',
            ];

            $initQuery = $this->select($fields)
                              ->from('transaction_approvals TA');

            $initQuery = ($id)            ? $initQuery->where(['TA.id' => ':id'])                         : $initQuery;
            $initQuery = ($transactionId) ? $initQuery->where(['TA.transaction_id' => ':transaction_id']) : $initQuery;
            $initQuery = ($userId)        ? $initQuery->andWhere(['TA.current_signatory' => ':user_id'])  : $initQuery;

            return $initQuery;
        }

        /**
         * `selectClients` Query string that will fetch work order from table `clients`.
         * @return string
         */
        public function selectClients($id = false, $name = false)
        {
            $fields = [
                'CL.id',
                'CL.name',
            ];

            $initQuery = $this->select($fields)
                              ->from('clients CL')
                              ->where(['CL.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['CL.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `insertWorkOrder` Query string that will insert to table `work_orders`
         * @return string
         */
        public function insertWorkOrder($data = [])
        {
            $initQuery = $this->insert('work_orders', $data);

            return $initQuery;
        }

        /**
         * `updateWorkOrder` Query string that will update specific work order from table `work_orders`
         * @return string
         */
        public function updateWorkOrder($id = '', $data = [])
        {
            $initQuery = $this->update('work_orders', $id, $data);

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
         * `updateProject` Query string that will update specific work order from table `projects`
         * @return string
         */
        public function updateProject($id = '', $data = [])
        {
            $initQuery = $this->update('projects', $id, $data);

            return $initQuery;
        }
    }