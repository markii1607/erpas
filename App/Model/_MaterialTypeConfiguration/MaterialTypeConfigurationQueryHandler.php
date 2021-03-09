<?php 
    namespace App\Model\MaterialTypeConfiguration;

    include_once "..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class MaterialTypeConfigurationQueryHandler extends QueryHandler {

        /**
         * `selectMaterialTypes` Query string that will select material type informations.
         * @return string
         */
        public function selectMaterialTypes($id = false, $name = false)
        {
            $fields = [
                'MT.id',
                'MT.name',
                'MT.cost_code',
                'DATE_FORMAT(MT.created_at, "%m/%d/%Y") as date_added'
            ];

            $conditions = [];

            ($id)             ? $conditions['MT.id']               = ':id'               : '';
            ($name)           ? $conditions['MT.name']             = ':name'             : '';

            $initQuery = $this->select($fields)
                              ->from('material_types MT');

            $initQuery = ($name || $id) ? $initQuery->where($conditions) : $initQuery;

            return $initQuery;
        }

        /**
         * `insertMaterialType` Query string that will insert to table `materials`
         * @return string
         */
        public function insertMaterialType($data = [])
        {
            $initQuery = $this->insert('material_types', $data);

            return $initQuery;
        }

        /**
         * `updateMaterialType` Query string that will update to table `material_types`
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function updateMaterialType($id = '', $data = [])
        {
            $initQuery = $this->update('material_types', $id, $data);

            return $initQuery;
        }

        /**
         * `deleteMaterialType` Query string that will delete specific material type.
         * @param  boolean $id
         * @return string
         */
        public function deleteMaterialType($id = false)
        {
            $initQuery = $this->delete('material_types')
                              ->where(['id' => ':id']);

            return $initQuery;
        }
    }