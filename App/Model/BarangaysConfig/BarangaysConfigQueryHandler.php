<?php
    namespace App\Model\BarangaysConfig;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class BarangaysConfigQueryHandler extends QueryHandler {

        public function selectBarangays($id = false)
        {
            $fields = [
                'B.id',
                'B.code',
                'B.no_of_sections',
                'B.name',
                'B.created_by',
                'B.created_at',
                'B.updated_by',
                'B.updated_at',
            ];

            $initQuery = $this->select($fields)
                              ->from('barangays B')
                              ->where(['B.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['B.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        public function insertBarangay($data = [])
        {
            $initQuery = $this->insert('barangays', $data);

            return $initQuery;
        }

        public function updateBarangay($id, $data = [])
        {
            $initQuery = $this->update('barangays', $id, $data);

            return $initQuery;
        }
    }