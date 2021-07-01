<?php
    namespace App\Model\ImprovementCertification;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class ImprovementCertificationQueryHandler extends QueryHandler {

        public function selectReleasedCertifications($id = false)
        {
            $fields = [
                'RC.id',
                'RC.type',
                'RC.tax_declaration_id',
                'RC.declaree',
                'RC.requestor',
                'RC.purpose',
                'DATE_FORMAT(RC.request_date, "%M %d, %Y") as request_date',
                'RC.amount_paid',
                'RC.or_no',
                'RC.prepared_by',
                'RC.verified_by',
                'IF(RC.tax_declaration_id IS NOT NULL, TD.td_no, RC.td_no) as td_no',
                'IF(RC.tax_declaration_id IS NOT NULL, TD.lot_no, RC.td_lot_no) as lot_no',
                'IF(RC.tax_declaration_id IS NOT NULL, TD.effectivity, RC.td_effectivity) as effectivity',
                'IF(RC.tax_declaration_id IS NOT NULL, TD.prop_location_street, RC.td_prop_location) as prop_location_street',
                'IF(RC.tax_declaration_id IS NOT NULL, (SELECT name FROM barangays WHERE id = TD.barangay_id), "") as brgy_name',
                // 'TD.effectivity',
                // 'TD.td_no',
                // 'TD.lot_no',
                // 'TD.prop_location_street',
                // '(SELECT name FROM barangays WHERE id = TD.barangay_id) as brgy_name'
            ];

            $initQuery = $this->select($fields)
                              ->from('released_certifications RC')
                              ->leftJoin(['tax_declarations TD' => 'TD.id = RC.tax_declaration_id'])
                              ->where(['RC.is_active' => ':is_active', 'RC.type' => ':type']);

            $initQuery = ($id) ? $initQuery->andWhere(['RC.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        public function selectReleasedCertificationDetails()
        {
            $fields = [
                'RCD.id',
                'RCD.released_certification_id',
                'RCD.tax_declaration_classification_id',
                'RCD.td_no',
                'RCD.declarant',
                'RCD.lot_no',
                'RCD.area',
                'RCD.market_value',
                'RCD.assessed_value',
            ];

            $initQuery = $this->select($fields)
                              ->from('released_certification_details RCD')
                              ->where(['RCD.is_active' => ':is_active', 'RCD.released_certification_id' => ':cert_id']);

            return $initQuery;
        }

        public function selectTaxDeclarationClassification()
        {
            $fields = [
                'TD.td_no',
                'TD.owner as declarant',
                'TD.lot_no',
                'CONCAT(TDC.area, " ", TDC.unit_measurement) as area',
                'TDC.market_value',
                'TDC.assessed_value',
            ];

            $initQuery = $this->select($fields)
                              ->from('tax_declaration_classifications TDC')
                              ->join(['tax_declarations TD' => 'TD.id = TDC.tax_declaration_id'])
                              ->where(['TDC.id' => ':tdc_id']);

            return $initQuery;
        }

        public function selectLotOwners()
        {
            $fields = [
                'TD.id',
                'TD.td_no',
                'TD.effectivity',
                'TD.owner',
                'TD.oct_tct_cloa_no',
                'TD.cct',
                'TD.survey_no',
                'TD.lot_no',
                'TD.block_no',
                'TD.boundaries_north',
                'TD.boundaries_south',
                'TD.boundaries_east',
                'TD.boundaries_west',
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
                              ->where(['TD.is_active' => ':is_active', 'TD.status' => ':status'])
                              ->logicEx('AND')
                              ->orWhereLike($orWhereLikeConditions);

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

        public function selectDBImprovementRecords()
        {
            $fields = [
                'TDC.id',
                'TD.td_no',
                'TD.owner',
                'TD.oct_tct_cloa_no',
                'TD.cct',
                'TD.survey_no',
                'TD.lot_no',
                'TD.block_no',
                'TD.boundaries_north',
                'TD.boundaries_south',
                'TD.boundaries_east',
                'TD.boundaries_west',
                'TDC.actual_use',
                'CONCAT(TDC.area, " ", TDC.unit_measurement) as area',
                'TDC.market_value',
                'TDC.assessed_value',
                'C.name as classification',
                '"saved" as data_type'
            ];

            $joins = [
                'tax_declarations TD'   =>  'TD.id = TDC.tax_declaration_id',
                'classifications C'     =>  'C.id = TDC.classification_id'
            ];

            $whereConditions = [
                'TDC.is_active' =>  ':is_active',
                'TD.is_active'  =>  ':is_active',
                'TD.status'     =>  ':status',
            ];

            $andWhereLikeConditions = [
                'C.name'    =>  ':classification'
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
                              ->from('tax_declaration_classifications TDC')
                              ->join($joins)
                              ->where($whereConditions)
                              ->andWhereLike($andWhereLikeConditions)
                              ->logicEx('AND')
                              ->orWhereLike($orWhereLikeConditions);

            return $initQuery;
        }

        public function insertToTable($table, $data = [])
        {
            $initQuery = $this->insert($table, $data);

            return $initQuery;
        }

        public function updateTable($table, $id = '', $data = [])
        {
            $initQuery = $this->update($table, $id, $data);

            return $initQuery;
        }
    }