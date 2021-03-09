<?php 
    namespace App\Model\NewActualSurvey;

    include_once "..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class NewActualSurveyQueryHandler extends QueryHandler {
        /**
         * `selectUsers` Query String that will select from table `users` join `employees` and `positions`
         * @return string
         */
        public function selectUsers()
        {
            $fields = [
                'E.id',
                'CONCAT(E.fname," ",E.mname," ",E.lname) as fullname'
            ];

            $joins = [
                'employees E' => 'U.employee_id = E.id',
                'positions P' => 'E.position_id = P.id'
            ];

            $orOneFieldConditions = [
                8,
                17,
                18,
                19,
                20
            ];

            $initQuery = $this->select($fields)
                              ->from('users U')
                              ->join($joins)
                              ->whereOrOneField('P.id', $orOneFieldConditions);

            return $initQuery;
        }
        
        /**
         * `insertActualSurveyTypeList` Query string that will insert to table `actual_survey_type_lists`
         * @return string
         */
        public function insertActualSurveyTypeList($data = [])
        {
            $initQuery = $this->insert('actual_survey_type_lists', $data);

            return $initQuery;
        }

        /**
         * `insertActualSurvey` Query string that will insert to table `actual_surveys`
         * @return string
         */
        public function insertActualSurvey($data = [])
        {
            $initQuery = $this->insert('actual_surveys', $data);

            return $initQuery;
        }

        /**
         * `insertActualSurveyScopeOfWork` Query string that will insert to table `actual_survey_scope_of_works`
         * @return string
         */
        public function insertActualSurveyScopeOfWork($data = [])
        {
            $initQuery = $this->insert('actual_survey_scope_of_works', $data);

            return $initQuery;
        }
    }