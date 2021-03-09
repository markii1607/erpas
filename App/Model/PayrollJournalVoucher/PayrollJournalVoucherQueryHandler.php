<?php
    namespace App\Model\PayrollJournalVoucher;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class PayrollJournalVoucherQueryHandler extends QueryHandler { 
        
        public function selectPayrollJvStepOne($id = false, $payroll_monthly_uploads_id = false)
        {
            $fields = [
                'PJV.id',
                'PJV.acctng_coa_id',
                'PJV.acctng_expenses_id',
                'PJV.payroll_monthly_uploads_id',
                'PJV.empno',
                'PJV.empname',
                'PJV.amount',
                'COA.account_code',
                'COA.account_name',
                'EX.expense_type'
            ];

            $joins = [
                'acctng_coa COA'            => 'PJV.acctng_coa_id = COA.coa_id',
                'acctng_expenses EX'        => 'PJV.acctng_expenses_id = EX.expense_id',
            ];


            $initQuery = $this->select($fields)
                             ->from('payroll_jv_step_one PJV')
                             ->leftJoin($joins)
                             ->where(['PJV.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['PJV.id' => ':id']) : $initQuery;
            $initQuery = ($payroll_monthly_uploads_id) ? $initQuery->andWhere(['PJV.payroll_monthly_uploads_id' => ':payroll_monthly_uploads_id']): $initQuery;

            return $initQuery;

        }
        
        /**
         * `selectPayrollMonthlyUploads` Query string that will fetch payroll_monthly_uploads.
         * @return string
         */

        public function selectPayrollMonthlyUploads($id = false)
        {
            $fields = [
                'PMU.id',
                'PMU.empno',
                'PMU.empname',
                'PMU.mbasic',
                'PMU.halfmbasic',
                'PMU.lteamt',
                'PMU.utamt',
                'PMU.absamt',
                'PMU.ilamt',
                'PMU.slamt',
                'PMU.blamt',
                'PMU.mallow',
                'PMU.regotamt',
                'PMU.sunamt',
                'PMU.sunotamt',
                'PMU.regholamt',
                'PMU.regholotamt',
                'PMU.splholamt',
                'PMU.splholotamt',
                'PMU.regholrdamt',
                'PMU.regholotrdamt',
                'PMU.splholrdamt',
                'PMU.splholrdotamt',
                'PMU.rallowamt',
                'PMU.plusadj',
                'PMU.ssscontrib',
                'PMU.sssconter',
                'PMU.ecc',
                'PMU.phiccontrib',
                'PMU.phiconter',
                'PMU.hmdfcontrib',
                'PMU.hmdfconter',
                'PMU.wtax',
                'PMU.insurance',
                'PMU.sssloanamort',
                'PMU.hmdfloanamort',
                'PMU.hmdflcalamamort',
                'PMU.bankloanamort',
                'PMU.officeloanamort',
                'PMU.adavnces',
                'PMU.negadj',
                'PMU.others',
                'PMU.netinc',
                'DATE_FORMAT(PMU.created_at, "%m/%d/%Y") as created_at',
                'DATE_FORMAT(PMU.co_from, "%b %d, %Y") as co_from',
                'DATE_FORMAT(PMU.co_to, "%b %d, %Y") as co_to',
                'PMU.status'

            ];

            $initQuery = $this->select($fields)
                              ->from('payroll_monthly_uploads PMU')
                              ->where(['PMU.is_active' => ':is_active']);


            $initQuery = ($id)   ? $initQuery->andWhere(['PMU.id' => ':id'])         : $initQuery;

            return $initQuery;
        }

        /**
         * `selectJvCutoff` Query string that will fetch payroll_monthly_uploads.
         * @return string
         */

        public function selectJvCutoff($id = false)
        {
            $fields = [
                // 'DATE_FORMAT(PMU.co_from, "%m/%d/%Y") as co_from',
                // 'DATE_FORMAT(PMU.co_to, "%m/%d/%Y") as co_to',
                'DISTINCT DATE_FORMAT(PMU.co_from, "%b %d, %Y") as co_from, DATE_FORMAT(PMU.co_to, "%b %d, %Y") as co_to'

            ];

            $initQuery = $this->select($fields)
                              ->from('payroll_monthly_uploads PMU')
                              ->where(['PMU.is_active' => ':is_active']);


            $initQuery = ($id)   ? $initQuery->andWhere(['PMU.id' => ':id'])         : $initQuery;

            return $initQuery;
        }

        public function selectPayrollJvCharging($jvChargingId = false, $cutOffDate = false)
        {
            $fields = [
                'PJC.id',
                'PJC.accounting_project_id',
                'PJC.payroll_jv_step_one_id',
                'CH.project_code',
                'CH.project_description',
                'PJV.amount',
                'COA.account_code',
                'COA.account_name',
                'EX.expense_type',
                '"0" as is_new'
                
            ];

            $join = [
                'accounting_projects CH'        => 'PJC.accounting_project_id = CH.project_id',
                'payroll_jv_step_one PJV'       => 'PJC.payroll_jv_step_one_id = PJV.id',
                'acctng_expenses EX '           => 'PJV.acctng_expenses_id =EX.expense_id',
                'acctng_coa COA'                => 'PJV.acctng_coa_id = COA.coa_id',
                'payroll_monthly_uploads PMU'   => 'PMU.id = PJV.payroll_monthly_uploads_id'

            ];

            $initQuery = $this->select($fields)
                              ->from('payroll_jv_charging PJC')
                              ->leftJoin($join)
                              ->where(['PJC.is_active' => ':is_active'])
                              ->andWhereNotEqual(['PJV.amount' => ':amount' ]);

            $initQuery = ($jvChargingId) ? $initQuery->andWhere(array('PJV.payroll_monthly_uploads_id'=>':id')) : $initQuery;
            $initQuery = ($cutOffDate) ? $initQuery->andWhere(array('PMU.co_from'=>':co_from', 'PMU.co_to'=>':co_to')) : $initQuery;

            return $initQuery;
        }

        public function selectSummarizeJv(){

        }


        public function selectChartOfAccounts()
        {
            $fields = [
                'COA.coa_id',
                'COA.account_code',
                'COA.account_name',
                'COA.accttype_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('acctng_coa COA');

            return $initQuery;
        }

        public function selectChargings()
        {
            $fields = [
                'CH.project_id',
                'CH.project_code',
                'CH.project_description',
            ];

            $initQuery = $this->select($fields)
                              ->from('accounting_projects CH');

            return $initQuery;

        }

        public function selectExpenses()
        {
            $fields = [
                'EX.expense_id',
                'EX.expense_type'
            ];

            $initQuery = $this->select($fields)
                              ->from('acctng_expenses EX');

            return $initQuery;
            
        }

        public function selectSubsidiary()
        {
            $fields = [
                'SB.subsidiary_id',
                'SB.subsidiary_account'
            ];

            $initQuery = $this->select($fields)
                              ->from('acctng_subsidiary SB');

            return $initQuery;
        }

        /**
         * `insertPayrollData` Query string that will update specific department information from table `departments`
         * @return string
         */
        public function insertPayrollData($data = [])
        {
            $initQuery = $this->insert('payroll_jv_step_one', $data);

            return $initQuery;
        }

        /**
         * `insertToPayrollCharging` Query string that will insert to table `departments`
         * @return string
         */
        public function insertToPayrollCharging($data = [])
        {
            $initQuery = $this->insert('payroll_jv_charging', $data);

            return $initQuery;
        }


        public function updatePayrollMonthlyUpload($id = '', $data = [])
        {
            $initQuery = $this->update('payroll_monthly_uploads', $id, $data);

            return $initQuery;
        }

    }