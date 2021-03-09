<?php 
    namespace App\Model\Deduction;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class DeductionQueryHandler extends QueryHandler { 
        /**
         * `selectDeductions` Query string that will fetch deduction.
         * @return string
         */
        public function selectDeductions($id = false, $name = false)
        {
            $fields = [
                'D.id',
                'D.deduction_code',
                'D.deduction_name',
                'D.amount',
            ];

            $initQuery = $this->select($fields)
                              ->from('deductions D')
                              ->where(['D.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['D.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectDepartments` Query String that will select from table `departments`
         * @return string
         */
        // public function selectDepartments($id = false)
        // {
        //     $fields = [
        //         'D.id',
        //         'D.charging',
        //     ];

        //     $initQuery = $this->select($fields)
        //                       ->from('departments D')
        //                       ->where(['D.is_active' => ':is_active']);

        //     return $initQuery;
        // }

        /**
         * `insertDeduction` Query string that will insert to table `deductions`
         * @return string
         */
        public function insertDeduction($data = [])
        {
            $initQuery = $this->insert('deductions', $data);

            return $initQuery;
        }

        /**
         * `updateDeduction` Query string that will update specific deduction information from table `deduction`
         * @return string
         */
        public function updateDeduction($id = '', $data = [])
        {
            $initQuery = $this->update('deductions', $id, $data);

            return $initQuery;
        }
    }