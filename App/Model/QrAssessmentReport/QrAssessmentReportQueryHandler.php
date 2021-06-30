<?php
    namespace App\Model\QrAssessmentReport;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class QrAssessmentReportQueryHandler extends QueryHandler {

        public function selectTotalLandArea()
        {
            $fields = [
                'SUM(TDC.area_in_sqm) as total_area'
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
                              ->andWhereRange('TD.created_at', [':from_date', ':to_date']);

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
                              ->andWhereRange('TD.created_at', [':from_date', ':to_date']);

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
                              ->andWhereRange('TD.created_at', [':from_date', ':to_date']);

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
                              ->andWhereRange('TD.created_at', [':from_date', ':to_date']);

            return $initQuery;
        }
    }