<?php 
    namespace App\Model\Notification;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class NotificationQueryHandler extends QueryHandler { 
        /** 
         * `archiveTransactionApproval` Query string that will update to table `DevelopmentTools`
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function archiveTransactionApproval($id = false, $data = [])
        {
            $initQuery = $this->update('DevelopmentTools', $id, $data);

            return $initQuery;
        }


        
        /**
         * 'selectNotifications' Query String that will select from table `notifications`.
         *
         * @param boolean $id
         * @return void
         */
        public function selectNotifications($transac_type, $reference_id = false)
        {
            $fields = [
                'NT.id',
                'NT.requestor_id',
                'NT.prs_id',
                'NT.is_prs',
                'NT.is_aob',
                'NT.is_po',
                'NT.is_withdrawal',
                'NT.is_unique_notifications',
                'NT.is_unique_tasks',
                'NT.message',
                'NT.assignee_id',
                'NT.notif_status',
                'NT.read_status',
                'NT.created_at',
                'NT.updated_at',
            ];
            switch($transac_type){
                case 'prs':
                    $transaction_type_query = 'NT.is_prs';
                    $reference_param = 'NT.prs_id';
                break;
            }
            
            $initQuery = $this->select($fields)
                              ->from('notifications NT')
                              ->where([$transaction_type_query => 1]);

            $initQuery = ($reference_id) ? $initQuery->andWhere([ $reference_param => ':prs_id']) : $initQuery;
            
            return $initQuery;

        }



        
        /**
         * 'selectPrsInfo' Query String that will select from table `notifications`.
         *
         * @param boolean $id
         * @return void
         */
        public function selectPrsInfo($prs_id = false, $status = false)
        {
            $fields = [
                'PRS.id',
                'PRS.project_id',
                'PRS.department_id',
                'PRS.user_id',
                'PRS.category',
                'PRS.request_type_id',
                'PRS.prs_no',
                'PRS.date_requested',
                'PRS.signatories',
                'PRS.status',
                'PRS.for_cancelation',
                'PRS.head_id',
                'PRS.remarks',
                'PRS.created_by',
                'PRS.updated_by',
                'PRS.created_at',
                'PRS.updated_at',
            ];

            $initQuery = $this->select($fields)
                              ->from('purchase_requisitions PRS')
                              ->where(['PRS.is_active' => 1]);

            $initQuery = ($prs_id) ? $initQuery->andWhere(['PRS.id' => ':prs_id']) : $initQuery;
            $initQuery = ($status) ? $initQuery->andWhere(['PRS.status' => ':status']) : $initQuery;
        
            return $initQuery;

        }



        public function insertNewNotification($data = array())
        {
          $initQuery = $this->insert('notifications', $data);
          return $initQuery;
        }

    }