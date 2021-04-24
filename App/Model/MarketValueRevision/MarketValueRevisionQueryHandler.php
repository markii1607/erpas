<?php
    namespace App\Model\MarketValueRevision;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class MarketValueRevisionQueryHandler extends QueryHandler {

        public function selectMarketValues($id = false)
        {
            $fields = [
                'MV.id',
                'MV.sub_classification_id',
                'MV.revision_year_id',
                'MV.market_value',
                'MV.description',
                'MV.created_by',
                'MV.created_at',
                'MV.updated_by',
                'MV.updated_at',
            ];

            $initQuery = $this->select($fields)
                              ->from('market_values MV')
                              ->where(['MV.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['MV.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        public function selectClassifications($id = false)
        {
            $fields = [
                'C.id',
                'C.name',
                'C.created_by',
                'C.created_at',
                'C.updated_by',
                'C.updated_at',
            ];

            $initQuery = $this->select($fields)
                              ->from('classifications C')
                              ->where(['C.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['C.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        public function selectSubClassifications($id = false, $class_id = false)
        {
            $fields = [
                'SC.id',
                'SC.classification_id',
                'SC.name',
                'SC.created_by',
                'SC.created_at',
                'SC.updated_by',
                'SC.updated_at',
            ];

            $initQuery = $this->select($fields)
                              ->from('sub_classifications SC')
                              ->where(['SC.is_active' => ':is_active']);

            $initQuery = ($id)          ? $initQuery->andWhere(['SC.id' => ':id']) : $initQuery;
            $initQuery = ($class_id)    ? $initQuery->andWhere(['SC.classification_id' => ':class_id']) : $initQuery;

            return $initQuery;
        }

        public function selectRevisionYears($id = false)
        {
            $fields = [
                'RY.id',
                'RY.year',
                'RY.created_by',
                'RY.created_at',
                'RY.updated_by',
                'RY.updated_at',
            ];

            $initQuery = $this->select($fields)
                              ->from('revision_years RY')
                              ->where(['RY.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['RY.id' => ':id']) : $initQuery;

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