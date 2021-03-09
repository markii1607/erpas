<?php
    namespace App\Model\PayrollCutoffConfiguration;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class PayrollCutoffConfigurationQueryHandler extends QueryHandler {
        /**
         * `selectPayrollCutoffs` Query string that will fetch overtime.
         * @return string
         */
        public function selectPayrollCutoffs($id = false, $name = false)
        {
            $fields = [
                'PC.id',
                'DATE_FORMAT(PC.from_payroll_cutoff, "%m/%d/%Y") as from_payroll_cutoff',
                'DATE_FORMAT(PC.to_payroll_cutoff, "%m/%d/%Y") as to_payroll_cutoff',
                'DATE_FORMAT(PC.from_payroll_period, "%m/%d/%Y") as from_payroll_period',
                'DATE_FORMAT(PC.to_payroll_period, "%m/%d/%Y") as to_payroll_period',
                'PC.payroll_period_type',
                '"" as status'
            ];

            $initQuery = $this->select($fields)
                              ->from('payroll_cutoffs PC')
                              ->where(['PC.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['PC.id' => ':id']) : $initQuery;

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
        //         'D.name'
        //     ];

        //     $initQuery = $this->select($fields)
        //                       ->from('departments D')
        //                       ->where(['D.is_active' => ':is_active']);

        //     return $initQuery;
        // }

        /**
         * `insertPayrollCutoffs` Query string that will insert to table `overtimes`
         * @return string
         */
        public function insertPayrollCutoffs($data = [])
        {
            $initQuery = $this->insert('payroll_cutoffs', $data);

            return $initQuery;
        }

        /**
         * `updatePayrollCutoffs` Query string that will update specific overtime information from table `overtime`
         * @return string
         */
        public function updatePayrollCutoffs($id = '', $data = [])
        {
            $initQuery = $this->update('payroll_cutoffs', $id, $data);

            return $initQuery;
        }
    }
