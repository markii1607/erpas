<?php
    namespace App\Model\MarketValueSubclassification;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class MarketValueSubclassificationQueryHandler extends QueryHandler {

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

        public function selectSubClassifications($id = false)
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

            $initQuery = ($id) ? $initQuery->andWhere(['SC.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        public function insertSubClassification($data = [])
        {
            $initQuery = $this->insert('sub_classifications', $data);

            return $initQuery;
        }

        public function updateSubClassification($id, $data = [])
        {
            $initQuery = $this->update('sub_classifications', $id, $data);

            return $initQuery;
        }
    }