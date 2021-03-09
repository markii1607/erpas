<?php 
    namespace App\Model\TransactionApproval\EventHandler;

    use App\AbstractClass\QueryHandler;

    class ActualSurveyQueryHandler extends QueryHandler {
        /**
         * `selectActualSurveys` Query string that will select from table `actual_surveys`
         * @param  boolean $id
         * @param  boolean $transactionId
         * @return string
         */
        public function selectActualSurveys($id = false, $transactionId = false)
        {
            $fields = [
                'AC.id',
                'AC.temp_code',
                'AC.name',
                'AC.location',
                'AC.project_manager',
                'AC.is_active',
                'AC.transaction_id',
                'T.status as transaction_status',
                'CONCAT(E.fname," ",E.mname," ",E.lname) as project_manager_name'
            ];

            $leftJoins = [
                'employees E'    => 'AC.project_manager = E.id',
                'transactions T' => 'T.id = AC.transaction_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('actual_surveys AC')
                              ->leftJoin($leftJoins)
                              ->where(['AC.is_active' => 1]);

            $initQuery = ($transactionId) ? $initQuery->andWhere(['AC.transaction_id' => ':transaction_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `updateActualSurvey` Query string that will update to table `actual_surveys`
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function updateActualSurvey($id = '', $data = [], $fk = '', $fkValue = '')
        {
            $initQuery = $this->update('actual_surveys', $id, $data, $fk, $fkValue);

            return $initQuery;
        }
    }