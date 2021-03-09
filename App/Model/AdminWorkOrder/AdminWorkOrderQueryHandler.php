<?php 
    namespace App\Model\AdminWorkOrder;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class AdminWorkOrderQueryHandler extends QueryHandler { 
        /**
         * `selectAdminWorkOrders` Query string that will fetch administrative work order from table `admin_work_orders`.
         * @return string
         */
        public function selectAdminWorkOrders($id = false, $name = false)
        {
            $fields = [
                'AWO.id',
                'AWO.transaction_id',
                'AWO.work_order_no',
                'name',
                'location',
                'client',
                'description',
                'priority_level',
                'type as wo_type',
                'AWO.updated_at as date_prepared',
                '"" as status',
            ];

            $initQuery = $this->select($fields)
                              ->from('admin_work_orders AWO')
                              ->where(['AWO.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['AWO.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `insertAdminWorkOrder` Query string that will insert to table `admin_work_orders`
         * @return string
         */
        public function insertAdminWorkOrder($data = [])
        {
            $initQuery = $this->insert('admin_work_orders', $data);

            return $initQuery;
        }

        /**
         * `updateAdminWorkOrder` Query string that will update specific administrative work order from table `admin_work_orders`
         * @return string
         */
        public function updateAdminWorkOrder($id = '', $data = [])
        {
            $initQuery = $this->update('admin_work_orders', $id, $data);

            return $initQuery;
        }
    }