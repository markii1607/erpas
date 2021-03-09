<?php 
    namespace App\Model\Selector;

    include_once "..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class ActualSurveyQueryHandler extends QueryHandler { 
        /**
         * `selectActualSurveys` Query string that will select from table `actual_surveys`.
         * @param  boolean $id
         * @return string
         */
        public function selectActualSurveys($id = false)
        {
            $fields = [
                'AC.id',
                'AC.name',
                'AC.temp_code',
                'AC.location',
                'AC.is_estimated',
                'CONCAT(E.fname, " ", E.mname, " ", E.lname) AS project_manager_name',
                'T.status as transaction_status'
            ];

            $joins = [
                'users U'     => 'U.id = AC.project_manager',
                'employees E' => 'E.id = U.employee_id'
            ];

            $leftJoins = [
                'transactions T' => 'AC.transaction_id = T.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('actual_surveys AC')
                              ->join($joins)
                              ->leftJoin($leftJoins)
                              ->where(['AC.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['AC.id' => ':id']) : $initQuery;

            return $initQuery;
        }
    }