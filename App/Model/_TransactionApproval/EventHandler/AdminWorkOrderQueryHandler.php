<?php 
    namespace App\Model\TransactionApproval\EventHandler;

    use App\AbstractClass\QueryHandler;

    class AdminWorkOrderQueryHandler extends QueryHandler {

        /**
         * `selectAdminWorkOrders` Query string that will select from table `admin_work_orders`.
         * @param  boolean $id
         * @param  boolean $transactionId
         * @return string
         */
        public function selectAdminWorkOrders($id = false, $transactionId = false)
        {
            $fields = [
                'AWO.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('admin_work_orders AWO')
                              ->where(['AWO.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['AWO.id' => ':id']) : $initQuery;
            $initQuery = ($transactionId) ? $initQuery->andWhere(['AWO.transaction_id' => ':transaction_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `updateAdminWorkOrder` Query string that will update to table `admin_work_orders`
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function updateAdminWorkOrder($id = '', $data = [], $fk = '', $fkValue = '')
        {
            $initQuery = $this->update('admin_work_orders', $id, $data, $fk, $fkValue);

            return $initQuery;
        }
    }