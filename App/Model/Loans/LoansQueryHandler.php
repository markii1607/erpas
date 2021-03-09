<?php 
    namespace App\Model\Loans;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class LoansQueryHandler extends QueryHandler { 
      /**
         * `selectDeductions` Query string that will fetch deduction.
         * @return string
         */
        public function selectLoans($id = false, $name = false)
        {
            $fields = [
                'L.id',
                'L.loans_code',
                'L.loans_name',
                'L.loans_amount',
                'DATE_FORMAT(L.loans_from, "%m/%d/%Y") as loans_from',
                'DATE_FORMAT(L.loans_to, "%m/%d/%Y") as loans_to',
            ];

            $initQuery = $this->select($fields)
                              ->from('loans L')
                              ->where(['L.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['L.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `insertLoans` Query string that will insert to table `loans`
         * @return string
         */
        public function insertLoans($data = [])
        {
            $initQuery = $this->insert('loans', $data);

            return $initQuery;
        }

        /**
         * `updateLoans` Query string that will update specific department information from table `loans`
         * @return string
         */
        public function updateLoans($id = '', $data = [])
        {
            $initQuery = $this->update('loans', $id, $data);

            return $initQuery;
        }
    }