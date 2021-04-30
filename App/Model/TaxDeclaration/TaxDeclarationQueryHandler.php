<?php
    namespace App\Model\TaxDeclaration;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class TaxDeclarationQueryHandler extends QueryHandler {

        public function selectTaxDeclarations()
        {
            # code...
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