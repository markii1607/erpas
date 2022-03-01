<?php
    namespace App\Model\TreasurerTdMonitoring;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class TreasurerTdMonitoringQueryHandler extends QueryHandler {

        public function selectGeneratedOrNumbers($id = false, $total = false)
        {
            $fields = [
                'PTD.id',
                'PTD.user_id',
                'DATE_FORMAT(PTD.transaction_date, "%M %d, %Y") as transaction_date',
                'PTD.or_no',
                'PTD.amount_paid',
                'PTD.paid_by',
                'PTD.has_check_no',
                'CONCAT_WS(" ", NULLIF(U.fname, ""), NULLIF(CONCAT(LEFT(U.mname,1), "."), ""), NULLIF(U.lname, "")) as collector_name',
                'U.position as collector_position',
            ];

            $fields = ($total) ? array('COUNT(PTD.id) as td_count') : $fields;

            $orWhereConditions = array(
                'DATE_FORMAT(PTD.transaction_date, "%M %d, %Y")' => ':filter_val',
                'PTD.or_no'         => ':filter_val',
                'PTD.amount_paid'   => ':filter_val',
                'PTD.paid_by'       => ':filter_val',
                'U.fname'           => ':filter_val',
                'U.mname'           => ':filter_val',
                'U.lname'           => ':filter_val',
                'U.position'        => ':filter_val',
            );

            $initQuery = $this->select($fields)
                              ->from('paid_tax_declarations PTD')
                              ->join(['users U' => 'U.id = PTD.user_id'])
                              ->where(['PTD.is_active' => ':is_active'])
                              ->logicEx('AND')
                              ->orWhereLike($orWhereConditions);

            $initQuery = ($id) ? $initQuery->andWhere(['PTD.id' => ':id']) : $initQuery;
            // $initQuery = ($date_from && $date_to) ? $initQuery->andWhereRange('PTD.transaction_date', [':date_from', ':date_to']) : $initQuery;

            return $initQuery;
        }

        public function selectGeneratedChkNumbers($id = false, $total = false)
        {
            $fields = [
                'TC.id',
                'TC.user_id',
                'DATE_FORMAT(TC.date_generated, "%M %d, %Y") as date_generated',
                'TC.check_no',
                'TC.total_amount',
                'CONCAT_WS(" ", NULLIF(U.fname, ""), NULLIF(CONCAT(LEFT(U.mname,1), "."), ""), NULLIF(U.lname, "")) as collector_name',
                'U.position as collector_position',
            ];

            $fields = ($total) ? array('COUNT(TC.id) as tc_count') : $fields;

            $orWhereConditions = array(
                'DATE_FORMAT(TC.date_generated, "%M %d, %Y")' => ':filter_val',
                'TC.check_no'       => ':filter_val',
                'U.fname'           => ':filter_val',
                'U.mname'           => ':filter_val',
                'U.lname'           => ':filter_val',
                'U.position'        => ':filter_val',
            );

            $initQuery = $this->select($fields)
                              ->from('treasurer_collections TC')
                              ->join(['users U' => 'U.id = TC.user_id'])
                              ->where(['TC.is_active' => ':is_active'])
                              ->logicEx('AND')
                              ->orWhereLike($orWhereConditions);

            $initQuery = ($id) ? $initQuery->andWhere(['TC.id' => ':id']) : $initQuery;
            // $initQuery = ($date_from && $date_to) ? $initQuery->andWhereRange('PTD.date_generated', [':date_from', ':date_to']) : $initQuery;

            return $initQuery;
        }

        public function selectTreasurerCollectionDetails($tc_id = false)
        {
            $fields = [
                'TCD.id',
                'TCD.treasurer_collection_id',
                'TCD.paid_tax_declaration_id',
                'PTD.or_no'
            ];

            $initQuery = $this->select($fields)
                              ->from('treasurer_collection_details TCD')
                              ->join(['paid_tax_declarations PTD' => 'PTD.id = TCD.paid_tax_declaration_id'])
                              ->where(['TCD.is_active' => ':is_active']);

            $initQuery = ($tc_id) ? $initQuery->andWhere(['TCD.treasurer_collection_id' => ':tc_id']) : $initQuery;

            return $initQuery;
        }

        public function selectBarangays($id = false)
        {
            $fields = [
                'B.id',
                'B.code',
                'B.name',
                'B.no_of_sections',
            ];

            $initQuery = $this->select($fields)
                              ->from('barangays B')
                              ->where(['B.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['B.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        public function selectRevisionYears($id = false)
        {
            $fields = [
                'RY.id',
                'RY.year',
            ];

            $initQuery = $this->select($fields)
                              ->from('revision_years RY')
                              ->where(['RY.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['RY.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        public function selectTDNumbers($rev_id = false, $id = false, $td_no = false)
        {
            $fields = [
                'TD.id',
                'TD.revision_year_id',
                'TD.td_no',
                'TD.td_no as tdn',
                'TD.pin',
                'TD.pin as pin_1',
                'TD.owner',
                'TD.owner_tin',
                'TD.owner_address',
                'TD.beneficiary',
                'TD.beneficiary_tin',
                'TD.beneficiary_address',
                'TD.beneficiary_tel_no',
                'TD.prop_location_street',
                'TD.barangay_id',
                'TD.oct_tct_cloa_no',
                'TD.cct',
                'TD.survey_no',
                'TD.lot_no',
                'TD.block_no',
                'DATE_FORMAT(TD.dated, "%m/%d/%Y") as dated',
                'DATE_FORMAT(TD.dated, "%M %d, %Y") as dated_view',
                'TD.boundaries',
                'TD.boundaries_north',
                'TD.boundaries_south',
                'TD.boundaries_east',
                'TD.boundaries_west',
                'TD.property_kind',
                'TD.description',
                'TD.no_of_storey',
                'TD.others_specified',
                'TD.total_market_value',
                'TD.total_assessed_value',
                'TD.total_assessed_value_words',
                'TD.is_taxable',
                'TD.is_exempt',
                'TD.effectivity',
                'TD.canceled_td_id',
                'TD.ordinance_no',
                'DATE_FORMAT(TD.ordinance_date, "%m/%d/%Y") as ordinance_date',
                'TD.approvers',
                'TD.memoranda',
                'TD.status',
                'TD.is_paid',
                'TD.payment_details',
                'TD.is_paid',
                'RY.year as revision_year'
            ];

            $initQuery = $this->select($fields)
                              ->from('tax_declarations TD')
                              ->join(['revision_years RY' => 'RY.id = TD.revision_year_id'])
                              ->where(['TD.is_active' => ':is_active']);

            $initQuery = ($rev_id)  ? $initQuery->andWhere(['TD.revision_year_id' => ':rev_id']) : $initQuery;
            $initQuery = ($id)      ? $initQuery->andWhere(['TD.id' => ':id']) : $initQuery;
            $initQuery = ($td_no)   ? $initQuery->andWhere(['TD.td_no' => ':td_no']) : $initQuery;

            return $initQuery;
        }

        public function selectTdRecords($lot_no = false, $td_id = false, $owner = false)
        {
            $fields = [
                'TD.id',
                'TD.revision_year_id',
                'TD.td_no',
                'TD.td_no as tdn',
                'TD.pin',
                'TD.pin as pin_1',
                'TD.owner',
                'TD.owner_tin',
                'TD.owner_address',
                'TD.beneficiary',
                'TD.beneficiary_tin',
                'TD.beneficiary_address',
                'TD.beneficiary_tel_no',
                'TD.prop_location_street',
                'TD.barangay_id',
                'TD.oct_tct_cloa_no',
                'TD.cct',
                'TD.survey_no',
                'TD.lot_no',
                'TD.block_no',
                'DATE_FORMAT(TD.dated, "%m/%d/%Y") as dated',
                'DATE_FORMAT(TD.dated, "%M %d, %Y") as dated_view',
                'TD.boundaries',
                'TD.boundaries_north',
                'TD.boundaries_south',
                'TD.boundaries_east',
                'TD.boundaries_west',
                'TD.property_kind',
                'TD.description',
                'TD.no_of_storey',
                'TD.others_specified',
                'TD.total_market_value',
                'TD.total_assessed_value',
                'TD.total_assessed_value_words',
                'TD.is_taxable',
                'TD.is_exempt',
                'TD.effectivity',
                'TD.canceled_td_id',
                'TD.ordinance_no',
                'DATE_FORMAT(TD.ordinance_date, "%m/%d/%Y") as ordinance_date',
                'TD.approvers',
                'TD.memoranda',
                'TD.status',
                'TD.is_paid',
                'TD.payment_details',
                'TD.is_paid',
            ];

            $orWhereLikeConditions = [
                'TD.oct_tct_cloa_no'    => ':lot_no',
                'TD.cct'                => ':lot_no',
                'TD.survey_no'          => ':lot_no',
                'TD.lot_no'             => ':lot_no',
                'TD.block_no'           => ':lot_no',
                'TD.boundaries_north'   => ':lot_no',
                'TD.boundaries_south'   => ':lot_no',
                'TD.boundaries_east'    => ':lot_no',
                'TD.boundaries_west'    => ':lot_no'
            ];

            $initQuery = $this->select($fields)
                              ->from('tax_declarations TD')
                              ->where(['TD.is_active'  =>  ':is_active'])
                              ->andWhereNull(['TD.is_paid']);

            $initQuery = ($lot_no) ? $initQuery->logicEx('AND')->orWhereLike($orWhereLikeConditions) : $initQuery;
            $initQuery = ($td_id)  ? $initQuery->andWhere(['TD.id' => ':td_id']) : $initQuery;
            $initQuery = ($owner)  ? $initQuery->andWhereLike(['TD.owner' => ':owner']) : $initQuery;

            return $initQuery;
        }

        public function selectSpecifiedOrNumbers()
        {
            $fields = [
                'PTD.id',
                'PTD.user_id',
                'DATE_FORMAT(PTD.transaction_date, "%M %d, %Y") as transaction_date',
                'PTD.or_no',
                'PTD.amount_paid',
                'PTD.paid_by',
                'PTD.has_check_no'
            ];

            $initQuery = $this->select($fields)
                              ->from('paid_tax_declarations PTD')
                              ->where(['PTD.is_active' => ':is_active'])
                              ->andWhereNull(['PTD.has_check_no'])
                              ->andWhereRange('PTD.transaction_date', [':from_date', ':to_date']);

            return $initQuery;
        }

        public function selectUsers($id = false)
        {
            $fields = [
                'U.id',
                'U.username',
                'U.fname',
                'U.mname',
                'U.lname',
                'CONCAT_WS(" ", NULLIF(U.fname, ""), NULLIF(CONCAT(LEFT(U.mname,1), "."), ""), NULLIF(U.lname, "")) as full_name',
                'U.department',
                'U.position',
                'U.access_type',
            ];

            $initQuery = $this->select($fields)
                            ->from('users U')
                            ->where(['U.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['U.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        public function selectPaidTaxDecDetails($ptd_id = false)
        {
            $fields = [
                'PTDD.id',
                'PTDD.paid_tax_declaration_id',
                'PTDD.tax_declaration_id',
            ];

            $initQuery = $this->select($fields)
                              ->from('paid_tax_declaration_details PTDD')
                              ->where(['PTDD.is_active' => ':is_active']);

            $initQuery = ($ptd_id) ? $initQuery->andWhere(['PTDD.paid_tax_declaration_id' => ':ptd_id']) : $initQuery;

            return $initQuery;
        }

        public function insertTblData($table, $data = [])
        {
            $initQuery = $this->insert($table, $data);

            return $initQuery;
        }

        public function updateTblData($table, $id, $data = [])
        {
            $initQuery = $this->update($table, $id, $data);

            return $initQuery;
        }
    }