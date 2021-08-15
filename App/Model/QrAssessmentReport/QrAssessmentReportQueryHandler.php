<?php
    namespace App\Model\QrAssessmentReport;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class QrAssessmentReportQueryHandler extends QueryHandler {

        public function selectTotalLandArea($condition)
        {
            $sumField = [
                'SUM(TDC.area_in_sqm) as total_area'
            ];

            $brgyField = [
                'TD.barangay_id'
            ];

            $fields = ($condition == 'land_area') ? $sumField : $brgyField;

            $joins = [
                'tax_declarations TD'   => 'TD.id = TDC.tax_declaration_id',
                'classifications C'     => 'C.id = TDC.classification_id'
            ];

            $whereConditions = [
                'TDC.is_active' => ':is_active',
                'TD.is_active'  => ':is_active',
                'TD.status'     => ':status'
            ];

            $initQuery = $this->select($fields)
                              ->from('tax_declaration_classifications TDC')
                              ->join($joins)
                              ->where($whereConditions)
                              ->logicEx('AND DATE_FORMAT(TD.created_at, "%Y-%m-%d") BETWEEN '.':from_date'.' AND '.':to_date');
                            //   ->log('TD.created_at', [':from_date', ':to_date']);

            return $initQuery;
        }

        public function selectTotalNumberOfRPU()
        {
            $fields = [
                'COUNT(TDC.id) as total_rpu'
            ];

            $joins = [
                'tax_declarations TD'   => 'TD.id = TDC.tax_declaration_id',
                'classifications C'     => 'C.id = TDC.classification_id'
            ];

            $whereConditions = [
                'TDC.is_active' => ':is_active',
                'TD.is_active'  => ':is_active',
                'TD.status'     => ':status'
            ];

            $initQuery = $this->select($fields)
                              ->from('tax_declaration_classifications TDC')
                              ->join($joins)
                              ->where($whereConditions)
                              ->andWhereLike(['TD.property_kind' => ':prop_kind'])
                              ->logicEx('AND DATE_FORMAT(TD.created_at, "%Y-%m-%d") BETWEEN '.':from_date'.' AND '.':to_date');
                            //   ->andWhereRange('TD.created_at', [':from_date', ':to_date']);

            return $initQuery;
        }

        public function selectTotalMarketValue()
        {
            $fields = [
                'SUM(TDC.market_value) as total_market_value'
            ];

            $joins = [
                'tax_declarations TD'   => 'TD.id = TDC.tax_declaration_id',
                'classifications C'     => 'C.id = TDC.classification_id'
            ];

            $whereConditions = [
                'TDC.is_active' => ':is_active',
                'TD.is_active'  => ':is_active',
                'TD.status'     => ':status'
            ];

            $initQuery = $this->select($fields)
                              ->from('tax_declaration_classifications TDC')
                              ->join($joins)
                              ->where($whereConditions)
                              ->andWhereLike(['TD.property_kind' => ':prop_kind'])
                              ->logicEx('AND DATE_FORMAT(TD.created_at, "%Y-%m-%d") BETWEEN '.':from_date'.' AND '.':to_date');
                            //   ->andWhereRange('TD.created_at', [':from_date', ':to_date']);

            return $initQuery;
        }

        public function selectTotalAssessedValue()
        {
            $fields = [
                'SUM(TDC.assessed_value) as total_assessed_value'
            ];

            $joins = [
                'tax_declarations TD'   => 'TD.id = TDC.tax_declaration_id',
                'classifications C'     => 'C.id = TDC.classification_id'
            ];

            $whereConditions = [
                'TDC.is_active' => ':is_active',
                'TD.is_active'  => ':is_active',
                'TD.status'     => ':status'
            ];

            $initQuery = $this->select($fields)
                              ->from('tax_declaration_classifications TDC')
                              ->join($joins)
                              ->where($whereConditions)
                              ->andWhereLike(['TD.property_kind' => ':prop_kind'])
                              ->logicEx('AND DATE_FORMAT(TD.created_at, "%Y-%m-%d") BETWEEN '.':from_date'.' AND '.':to_date');
                            //   ->andWhereRange('TD.created_at', [':from_date', ':to_date']);

            return $initQuery;
        }

        public function selectMunicipalAssessorData()
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
                            ->where(['U.is_active' => ':is_active', 'U.position' => ':position']);

            return $initQuery;
        }
    }