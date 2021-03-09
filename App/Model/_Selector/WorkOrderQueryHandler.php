<?php 
    namespace App\Model\Selector;

    include_once "..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class WorkOrderQueryHandler extends QueryHandler { 
        /**
         * `selectAdminWorkOrders` Query string that will select from table `admin_work_orders`.
         * @param  boolean $id
         * @return string
         */
        public function selectAdminWorkOrders($id = false)
        {
            $fields = [
                'AWO.id',
                'AWO.work_order_no',
                'AWO.name',
                'AWO.location',
                'DATE_FORMAT(AWO.updated_at, "%c/%e/%Y %l:%i %p") as date_revised',
                'CONCAT(E.fname, " ", E.mname, " ", E.lname) as project_manager_name',
            ];

            $joins = [
                'users U'     => 'U.id = AWO.updated_by',
                'employees E' => 'U.employee_id = E.id'
            ];

            $whereConditions = [
                'AWO.is_active'                 => ':is_active',
                'AWO.is_approved'               => ':is_approved',
                'AWO.is_assigned_actual_survey' => ':is_assigned_actual_survey'
            ];

            $initQuery = $this->select($fields)
                              ->from('admin_work_orders AWO')
                              ->join($joins)
                              ->where($whereConditions);

            $initQuery = ($id) ? $initQuery->andWhere(['AWO.id' => ':id']) : $initQuery;

            return $initQuery;
        }
    }