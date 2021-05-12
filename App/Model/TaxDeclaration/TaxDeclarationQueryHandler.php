<?php
    namespace App\Model\TaxDeclaration;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class TaxDeclarationQueryHandler extends QueryHandler {

        public function selectTaxDeclarations($id = false, $status = false, $rev_id = false, $td_no = false, $pin = false, $owner = false, $lot_no = false, $brgy_id = false, $type = false, $date_from = false, $date_to = false, $tdCategory = '', $tdIDs = [], $total = false)
        {
            $fields = [
                'TD.id',
                'TD.revision_year_id',
                'TD.td_no',
                'TD.pin',
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
                'RY.year as rev_year',
                'B.name as brgy',
                'B.code as brgy_code',
            ];

            $fields = ($total) ? array('COUNT(Td.id) as td_count') : $fields;

            $orWhereCondition = array(
                'TD.td_no'                  => ':filter_val',
                'TD.pin'                    => ':filter_val',
                'TD.owner'                  => ':filter_val',
                'TD.property_kind'          => ':filter_val',
                'TD.prop_location_street'   => ':filter_val',
                'RY.year'                   => ':filter_val',
                'B.name'                    => ':filter_val',
                'B.code'                    => ':filter_val',
            );

            $joins = [
                'revision_years RY' => 'RY.id = TD.revision_year_id',
                'barangays B'       => 'B.id = TD.barangay_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('tax_declarations TD')
                              ->join($joins)
                              ->where(['TD.is_active' => ':is_active'])
                              ->logicEx('AND')
                              ->orWhereLike($orWhereCondition);

            $initQuery = ($id)      ? $initQuery->andWhere(['TD.id' => ':id'])           : $initQuery;
            $initQuery = ($status)  ? $initQuery->andWhere(['TD.status' => ':status'])   : $initQuery;
            $initQuery = ($rev_id)  ? $initQuery->andWhere(['RY.id' => ':rev_id'])       : $initQuery;
            $initQuery = ($td_no)   ? $initQuery->andWhereLike(['TD.td_no' => ':td_no']) : $initQuery;
            $initQuery = ($pin)     ? $initQuery->andWhereLike(['TD.pin' => ':pin'])     : $initQuery;
            $initQuery = ($owner)   ? $initQuery->andWhereLike(['TD.owner' => ':owner']) : $initQuery;
            $initQuery = ($lot_no)  ? $initQuery->andWhereLike(['TD.lot_no' => ':lot_no'])  : $initQuery;
            $initQuery = ($brgy_id) ? $initQuery->andWhere(['B.id' => ':brgy_id'])          : $initQuery;
            $initQuery = ($type)    ? $initQuery->andWhereLike(['TD.property_kind' => ':type']) : $initQuery;
            $initQuery = ($date_from && $date_to) ? $initQuery->andWhereRange('TD.created_at', [':date_from', ':date_to']) : $initQuery;
            $initQuery = !empty($tdIDs) ? $initQuery->andWhereIn('TD.id', $tdIDs) : $initQuery;
            if ($tdCategory == 'taxable') {
                $initQuery = $initQuery->andWhereNotNull(['TD.is_taxable']);
            } else if ($tdCategory == 'exempt') {
                $initQuery = $initQuery->andWhereNotNull(['TD.is_exempt']);
            }

            return $initQuery;
        }

        public function selectTaxDeclarationClassifications($td_id = false)
        {
            $fields = [
                'TDC.id',
                'TDC.tax_declaration_id',
                'TDC.classification_id',
                'TDC.market_value_id',
                'TDC.area',
                'TDC.unit_measurement',
                'TDC.area_in_sqm',
                'TDC.area_in_ha',
                'TDC.market_value',
                'TDC.actual_use',
                'TDC.assessment_level',
                'TDC.assessed_value',
                '"saved" as data_type'
            ];

            $initQuery = $this->select($fields)
                              ->from('tax_declaration_classifications TDC')
                              ->where(['TDC.is_active' => ':is_active']);

            $initQuery = ($td_id) ? $initQuery->andWhere(['TDC.tax_declaration_id' => ':td_id'])    : $initQuery;

            return $initQuery;
        }

        public function selectFilteredTDClassifications($class_id = false, $actual_use = false)
        {
            $fields = [
                'TDC.id',
                'TDC.tax_declaration_id',
                'TDC.classification_id',
                'TDC.market_value_id',
                'TDC.area',
                'TDC.unit_measurement',
                'TDC.area_in_sqm',
                'TDC.area_in_ha',
                'TDC.market_value',
                'TDC.actual_use',
                'TDC.assessment_level',
                'TDC.assessed_value',
                '"saved" as data_type'
            ];

            $initQuery = $this->select($fields)
                              ->from('tax_declaration_classifications TDC')
                              ->join(['tax_declarations TD' => 'TD.id = TDC.tax_declaration_id'])
                              ->where(['TDC.is_active' => ':is_active', 'TD.is_active' => ':is_active']);

            $initQuery = ($class_id)    ? $initQuery->andWhere(['TDC.classification_id' => ':class_id'])  : $initQuery;
            $initQuery = ($actual_use)  ? $initQuery->andWhereLike(['TDC.actual_use' => ':actual_use'])   : $initQuery;

            return $initQuery;
        }

        public function selectClassifications($id = false)
        {
            $fields = [
                'C.id',
                'C.name',
            ];

            $initQuery = $this->select($fields)
                              ->from('classifications C')
                              ->where(['C.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['C.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        public function selectSubClassifications($class_id = false)
        {
            $fields = [
                'SC.id',
                'SC.classification_id',
                'SC.name',
            ];

            $initQuery = $this->select($fields)
                              ->from('classifications SC')
                              ->where(['SC.is_active' => ':is_active']);

            $initQuery = ($class_id) ? $initQuery->andWhere(['SC.classification_id' => ':class_id']) : $initQuery;

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

        public function selectTDNumbers($rev_id = false, $id = false)
        {
            $fields = [
                'TD.id',
                'TD.revision_year_id',
                'TD.td_no',
                'TD.pin',
                'TD.total_assessed_value',
                'RY.year as revision_year'
            ];

            $initQuery = $this->select($fields)
                              ->from('tax_declarations TD')
                              ->join(['revision_years RY' => 'RY.id = TD.revision_year_id'])
                              ->where(['TD.is_active' => ':is_active']);

            $initQuery = ($rev_id)  ? $initQuery->andWhere(['TD.revision_year_id' => ':rev_id']) : $initQuery;
            $initQuery = ($id)      ? $initQuery->andWhere(['TD.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        public function selectMarketValues($class_id = false, $rev_id = false, $id = false)
        {
            $fields = [
                'MV.id',
                'MV.sub_classification_id',
                'MV.revision_year_id',
                'MV.market_value',
                'MV.unit_measurement as unit',
                'MV.description',
                'C.id as classification_id',
                'SC.name as sub_classification',
                'C.name as classification',
                'RY.year as revision_year'
            ];

            $joins = [
                'sub_classifications SC'    =>  'SC.id = MV.sub_classification_id',
                'classifications C'         =>  'C.id = SC.classification_id',
                'revision_years RY'         =>  'RY.id = MV.revision_year_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('market_values MV')
                              ->join($joins)
                              ->where(['MV.is_active' => ':is_active']);

            $initQuery = ($class_id) ? $initQuery->andWhere(['C.id' => ':class_id'])                : $initQuery;
            $initQuery = ($rev_id)   ? $initQuery->andWhere(['MV.revision_year_id' => ':rev_id'])   : $initQuery;
            $initQuery = ($id)       ? $initQuery->andWhere(['MV.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        public function selectApproverSets()
        {
            $fields = [
                'AP.id',
                'AP.approvers',
            ];

            $initQuery = $this->select($fields)
                              ->from('approver_sets AP')
                              ->where(['AP.is_active' => ':is_active']);

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