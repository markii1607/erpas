<?php
    namespace App\Model\PropertiesDec;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class PropertiesDecQueryHandler extends QueryHandler {

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
            ];

            $initQuery = $this->select($fields)
                              ->from('released_certifications RC')
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
                'RCD.property_kind',
                'RCD.property_location',
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
                'IF(TD.property_kind = "Others", TD.others_specified, TD.property_kind) as property_kind',
                'TD.owner as declarant',
                'TD.lot_no',
                'CONCAT(TDC.area, " ", TDC.unit_measurement) as area',
                'TDC.market_value',
                'TDC.assessed_value',
                'IF(TD.prop_location_street IS NULL, B.name, CONCAT(TD.prop_location_street, ", ", B.name)) as property_location'
            ];

            $initQuery = $this->select($fields)
                              ->from('tax_declaration_classifications TDC')
                              ->join(['tax_declarations TD' => 'TD.id = TDC.tax_declaration_id', 'barangays B' => 'B.id = TD.barangay_id'])
                              ->where(['TDC.id' => ':tdc_id']);

            return $initQuery;
        }

        public function selectDeclarantPropertyRecords($owners = [])
        {
            $fields = [
                'TDC.id',
                'TD.td_no',
                'TD.owner',
                'IF(TD.property_kind = "Others", TD.others_specified, TD.property_kind) as property_kind',
                'TD.prop_location_street',
                '(SELECT name FROM barangays WHERE id = TD.barangay_id) as brgy_name',
                'TD.lot_no',
                'CONCAT(TDC.area, " ", TDC.unit_measurement) as area',
                'TDC.market_value',
                'TDC.assessed_value',
                '"saved" as data_type'
            ];

            $whereConditions = [
                'TDC.is_active' => ':is_active',
                'TD.is_active'  => ':is_active',
                'TD.status'     => ':status',
            ];

            $initQuery = $this->select($fields)
                              ->from('tax_declaration_classifications TDC')
                              ->join(['tax_declarations TD' => 'TD.id = TDC.tax_declaration_id'])
                              ->where($whereConditions)
                              ->andWhereIn('TD.owner', $owners);

            return $initQuery;
        }

        public function selectLotOwners()
        {
            $fields = [
                'TD.id',
                'TD.td_no',
                'TD.owner'
            ];

            $initQuery = $this->select($fields)
                              ->from('tax_declarations TD')
                              ->where(['TD.is_active' => ':is_active', 'TD.status' => ':status']);

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