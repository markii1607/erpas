<?php
    namespace App\Model\MarketValueClassification;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class MarketValueClassificationQueryHandler extends QueryHandler {

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

        public function insertClassification($data = [])
        {
            $initQuery = $this->insert('classifications', $data);

            return $initQuery;
        }

        public function updateClassification($id, $data = [])
        {
            $initQuery = $this->update('classifications', $id, $data);

            return $initQuery;
        }
    }