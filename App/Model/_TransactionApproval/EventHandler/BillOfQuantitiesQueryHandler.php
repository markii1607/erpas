<?php 
    namespace App\Model\TransactionApproval\EventHandler;

    use App\AbstractClass\QueryHandler;

    class BillOfQuantitiesQueryHandler extends QueryHandler {
        /**
         * `selectProjects` Query string that will select from table `projects`
         * @param  boolean $id
         * @param  boolean $transactionId
         * @return string
         */
        public function selectProjects($id = false, $transactionId = false)
        {
            $fields = [
                'P.id',
                'P.project_code',
                'P.name',
                'P.location',
                'P.project_manager',
                'P.boq_approval',
                'P.status',
                'P.transaction_id',
                'T.status as transaction_status',
                'CONCAT(E.fname," ",E.mname," ",E.lname) as project_manager_name'
            ];

            $leftJoins = [
                'employees E'    => 'P.project_manager = E.id',
                'transactions T' => 'T.id = P.transaction_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('projects P')
                              ->leftJoin($leftJoins)
                              ->where(['P.status' => 1]);

            $initQuery = ($transactionId) ? $initQuery->andWhere(['P.transaction_id' => ':transaction_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `updateProject` Query string that will update to table `projects`
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function updateProject($id = '', $data = [])
        {
            $initQuery = $this->update('projects', $id, $data);

            return $initQuery;
        }
    }