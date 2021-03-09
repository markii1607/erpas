<?php 
    namespace App\Model\AuditMonitoring;

    include_once "..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class AuditMonitoringQueryHandler extends QueryHandler { 
        /**
         * `selectAccounts` Query string that will fetch accounts.
         * @param  boolean $id
         * @return string
         */
        public function selectAccounts($id = false)
        {
            $fields = [
                'A.id',
                'A.name'
            ];

            $initQuery = $this->select($fields)
                              ->from('accounts A')
                              ->where(['A.status' => ':status']);

            $initQuery = ($id) ? $initQuery->andWhere(['A.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectChargings` Query string that will fetch chargings.
         * @param  boolean $id
         * @return string
         */
        public function selectChargings($id = false)
        {
            $fields = [
                'C.id',
                'C.name'
            ];

            $initQuery = $this->select($fields)
                              ->from('chargings C')
                              ->where(['C.status' => ':status']);

            $initQuery = ($id) ? $initQuery->andWhere(['C.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectSuppliers` Query string that will fetch suppliers.
         * @param  boolean $id
         * @return string
         */
        public function selectSuppliers($id = false)
        {
            $fields = [
                'S.id',
                'S.name'
            ];

            $initQuery = $this->select($fields)
                              ->from('suppliers S')
                              ->where(['S.status' => ':status']);

            $initQuery = ($id) ? $initQuery->andWhere(['S.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectRequestors` Query string that will fetch requestors.
         * @param  boolean $id
         * @return string
         */
        public function selectRequestors($id = false)
        {
            $fields = [
                'R.id',
                'R.name'
            ];

            $initQuery = $this->select($fields)
                              ->from('requestors R')
                              ->where(['R.status' => ':status']);

            $initQuery = ($id) ? $initQuery->andWhere(['R.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectTaxRates` Query string that will fetch tax rates.
         * @param  boolean $id
         * @return string
         */
        public function selectTaxRates($id = false)
        {
            $fields = [
                'TR.id',
                'TR.name',
                'TR.rate'
            ];

            $initQuery = $this->select($fields)
                              ->from('tax_rates TR')
                              ->where(['TR.status' => ':status']);

            $initQuery = ($id) ? $initQuery->andWhere(['TR.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectAuditMonitorings` Query string that will fetch from `audit_monitorings` table.
         * @param  string $id
         * @return string
         */
        public function selectAuditMonitorings($id = '')
        {
            $fields = [
                'AM.id',
                'AM.account_id',
                'AM.charging_id',
                'AM.supplier_id',
                'AM.requestor_id',
                'AM.tax_rate_id',
                'A.name as account_name',
                'C.name as charging_name',
                'S.name as supplier_name',
                'R.name as requestor_name',
                'TR.name as tax_rate_name',
                'TR.rate as tax_rate',
                'AM.gross',
                'AM.gross as new_gross',
                'AM.net_of_value',
                'AM.income_tax',
                'AM.ewt',
                'AM.net_of_tax',
                'AM.particular',
                'AM.remarks',
                'AM.audit_ref_no',
                'AM.po_no',
                'AM.po_quantity',
                'AM.prs_no',
                'AM.prs_quantity',
                'AM.prs_balance',
                'AM.eq_plate_no',
                'AM.consumption',
                'DATE_FORMAT(AM.period_start, "%m/%d/%Y") as period_start',
                'DATE_FORMAT(AM.period_end, "%m/%d/%Y") as period_end',
                'DATE_FORMAT(AM.previous, "%m/%d/%Y") as previous',
                'DATE_FORMAT(AM.present, "%m/%d/%Y") as present',
                'AM.orsici',
                'DATE_FORMAT(AM.orsici_date, "%m/%d/%Y") as orsici_date',
                'AM.account_no',
                'AM.gv_no',
                'AM.cdv_no',
                'IF(AM.vatable = "1", "vatable", "non-vatable") as vatable',
                'DATE_FORMAT(AM.date_posted, "%m/%d/%Y") as date_posted',
                'CONCAT(EC.fname, " ", EC.mname, " ", EC.lname) as encoded_by',
                'DATE_FORMAT(AM.created_at, "%m/%d/%Y") as encoded_at',
                'CONCAT(EU.fname, " ", EU.mname, " ", EU.lname) as updated_by',
                'DATE_FORMAT(AM.updated_at, "%m/%d/%Y") as updated_at'
            ];

            $joins = [
                'accounts A'   => 'A.id = AM.account_id',
                'chargings C'  => 'AM.charging_id = C.id',
                'suppliers S'  => 'S.id = AM.supplier_id',
                'requestors R' => 'AM.requestor_id = R.id',
                'users UC'     => 'AM.created_by = UC.id',
                'employees EC' => 'UC.employee_id = EC.id',
                'users UU'     => 'AM.updated_by = UU.id',
                'employees EU' => 'UU.employee_id = EU.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('audit_monitorings AM')
                              ->join($joins)
                              ->leftJoin(['tax_rates TR' => 'TR.id = AM.tax_rate_id'])
                              ->where(['AM.status' => ':status']);
         
            $initQuery = ($id) ? $initQuery->andWhere(['AM.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `insertAuditMonitoring` Query string that will insert to table `audit_monitorings`
         * @return string
         */
        public function insertAuditMonitoring($data = [])
        {
            $initQuery = $this->insert('audit_monitorings', $data);

            return $initQuery;
        }

        /**
         * `updateAuditMonitoring` Query string that will update specific audit entry information from table `audit_monitorings`
         * @return string
         */
        public function updateAuditMonitoring($id = '', $data = [])
        {
            $initQuery = $this->update('audit_monitorings', $id, $data);

            return $initQuery;
        }
    }