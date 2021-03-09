<?php 
    namespace App\Model\Report;

    include_once "..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class ActualSurveyQueryHandler extends QueryHandler {

        /**
         * `selectActualSurvey` Query string that will fetch from table `actual_surveys`.
         * @param  boolean $id
         * @param  boolean $transactionId
         * @return string
         */
        public function selectActualSurvey($id = false, $transactionId = false)
        {
            $fields = [
                'AC.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('actual_surveys AC')
                              ->where(['AC.is_active' => 1]);

            $initQuery = ($transactionId) ? $initQuery->andWhere(['AC.transaction_id' => ':transaction_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectSignatories` Query string that will fetch from table `signatories`.
         * @param  boolean $id
         * @param  boolean $signatorySetId
         * @return string
         */
        public function selectSignatories($id = false, $signatorySetId = false)
        {
            $fields = [
                'CONCAT(E.fname," ",E.mname," ",E.lname) as full_name',
                'P.name as position_name',
                'DATE_FORMAT(CURDATE(), "%M %d, %Y") as date',
                '"" as signature',
                'IF(P.id = "31", "Checked by: ", "Noted by: ") as label',
                'U.id as user_id',
                'E.code'
            ];

            $joins = [
                'employees E' => 'S.position_id = E.position_id',
                'positions P' => 'E.position_id = P.id',
                'users U'     => 'U.employee_id = E.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('signatories S')
                              ->join($joins)
                              ->where(['E.status' => 1]);

            $initQuery = ($id)             ? $initQuery->andWhere(['S.id' => ':id'])                             : $initQuery;
            $initQuery = ($signatorySetId) ? $initQuery->andWhere(['S.signatory_set_id' => ':signatory_set_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectUsers` Query string that will select from table `users`.
         * @param  boolean $id
         * @return string
         */
        public function selectUsers($id = false)
        {
            $fields = [
                'CONCAT(E.fname," ",E.mname," ",E.lname) as full_name',
                'P.name as position_name',
                'CURDATE() as date',
                'E.code as signature',
                '"Prepared by: " as label'
            ];

            $joins = [
                'employees E' => 'U.employee_id = E.id',
                'positions P' => 'E.position_id = P.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('users U')
                              ->join($joins)
                              ->where(['E.status' => 1]);

            $initQuery = ($id) ? $initQuery->andWhere(['U.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectTransactionApprovals` Query string that will select from table `transaction_approvals`.
         * @param  boolean $id
         * @param  boolean $transactionId
         * @return string
         */
        public function selectTransactionApprovals($id = false, $transactionId = false)
        {
            $fields = [
                'TA.id',
                'TA.current_signatory',
                'TA.status',
                'DATE_FORMAT(TA.updated_at, "%M %d, %Y") as updated_at',
            ];

            $initQuery = $this->select($fields)
                              ->from('transaction_approvals TA');

            $initQuery = ($transactionId) ? $initQuery->where(['TA.transaction_id' => ':transaction_id']) : $initQuery;

            return $initQuery;
        }
    }