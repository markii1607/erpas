<?php 
    namespace App\Model\AdminWorkOrder;

    include_once "..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class AdminWorkOrderQueryHandler extends QueryHandler { 
        /**
         * `selectAdminWorkOrders` Query string that will fetch position.
         * @return string
         */
        public function selectAdminWorkOrders($id = false, $createdBy = false, $workOrderNo = false)
        {
            $fields = [
                'AWO.id',
                'AWO.work_order_no',
                'AWO.name',
                'AWO.location',
                'AWO.agency',
                'AWO.description',
                'AWO.is_active',
                'AWO.is_revision',
                'AWO.is_approved',
                'AWO.created_by',
                'AWO.updated_by',
                'AWO.updated_at',
                'AWO.created_at',
                'DATE_FORMAT(AWO.updated_at, "%c/%e/%Y %l:%i %p") as date_prepared',
                'CONCAT(E.fname, " ",E.mname, " ",E.lname) as prepared_by',
                'T.id as transaction_id',
                'IFNULL(T.status,"") as transaction_status'
            ];

            $leftJoins = [
                'transactions T' => 'T.id = AWO.transaction_id',
                'users U'        => 'AWO.updated_by = U.id',
                'employees E'    => 'U.employee_id = E.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('admin_work_orders AWO')
                              ->leftJoin($leftJoins)
                              ->where(['AWO.is_active' => ':is_active']);

            $initQuery = ($id)          ? $initQuery->andWhere(['AWO.id' => ':id'])                       : $initQuery;
            $initQuery = ($createdBy)   ? $initQuery->andWhere(['AWO.created_by' => ':created_by'])       : $initQuery;
            $initQuery = ($workOrderNo) ? $initQuery->andWhere(['AWO.work_order_no' => ':work_order_no']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectRevAdminWorkOrders` Query string that will fetch rev_admin_work_orders.
         * @return string
         */
        public function selectRevAdminWorkOrders($id = false, $adminWorkOrderId = false)
        {
            $fields = [
                'RAWO.id',
                'RAWO.work_order_no',
                'RAWO.name',
                'RAWO.location',
                'RAWO.agency',
                'RAWO.description',
                'RAWO.created_by',
                'RAWO.updated_by',
                'DATE_FORMAT(RAWO.updated_at, "%c/%e/%Y %l:%i %p") as date_prepared',
                'RAWO.updated_at as date_revised',
                'RAWO.created_at',
                'RAWO.description',
                'RAWO.is_revision',
                'RAWO.is_active',
                'CONCAT(E.fname, " ",E.mname, " ",E.lname) as project_manager',
            ];

            $leftJoins = [
                'users U'        => 'RAWO.created_by = U.id',
                'employees E'    => 'U.employee_id = E.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('rev_admin_work_orders RAWO')
                              ->leftJoin($leftJoins);

            $initQuery = ($id)               ? $initQuery->where(['RAWO.id' => ':id'])                       : $initQuery;
            $initQuery = ($adminWorkOrderId) ? $initQuery->where(['RAWO.admin_work_order_id' => ':admin_work_order_id'])       : $initQuery;

            return $initQuery;
        }

        /**
         * `selectUsers` Query string that will select from table users.
         * @param  boolean $id
         * @return string
         */
        public function selectUsers($id = false)
        {
            $fields = [
                'U.id',
                'CONCAT(E.fname, " ", E.mname, " ", E.lname) as full_name'
            ];

            $initQuery = $this->select($fields)
                              ->from('users U')
                              ->join(['employees E' => 'U.employee_id = E.id']);

            $initQuery = ($id) ? $initQuery->where(['U.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `insertAdminWorkOrder` Query string that will insert to table `admin_work_orders`.
         * @return string
         */
        public function insertAdminWorkOrder($data = [])
        {
            $initQuery = $this->insert('admin_work_orders', $data);

            return $initQuery;
        }

        /**
         * `insertRevAdminWorkOrder` Query string that will insert to table `rev_admin_work_orders`.
         * @return string
         */
        public function insertRevAdminWorkOrder($data = [])
        {
            $initQuery = $this->insert('rev_admin_work_orders', $data);

            return $initQuery;
        }

        /**
         * `updateAdminWorkOrder` Query string that will update specific administrative work order's information from table `administrative_work_orders`
         * @return string
         */
        public function updateAdminWorkOrder($id = '', $data = [])
        {
            $initQuery = $this->update('admin_work_orders', $id, $data);

            return $initQuery;
        }
    }