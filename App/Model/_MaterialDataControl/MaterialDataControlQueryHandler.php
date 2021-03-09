<?php 
    namespace App\Model\MaterialDataControl;

    include_once "..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class MaterialDataControlQueryHandler extends QueryHandler {

        /**
         * `selectMaterials` Query string that will select material informations.
         * @return string
         */
        public function selectMaterials($id = false, $materialTypeId = false, $name = false)
        {
            $fields = [
                'M.id',
                'M.material_type_id',
                'M.name',
                'M.cost_code as m_cost_code',
                'MT.cost_code as mt_cost_code',
                'MT.name as material_type_name',
                'DATE_FORMAT(M.created_at, "%m/%d/%Y") as date_added'
            ];

            $conditions = [
                'M.status' => ':status'
            ];

            ($id)             ? $conditions['M.id']               = ':id'               : '';
            ($materialTypeId) ? $conditions['M.material_type_id'] = ':material_type_id' : '';
            ($name)           ? $conditions['M.name']             = ':name'             : '';

            $initQuery = $this->select($fields)
                              ->from('materials M')
                              ->join(['material_types MT' => 'M.material_type_id = MT.id'])
                              ->where($conditions);

            return $initQuery;
        }

        /**
         * `selectMaterialPrices` Query string that will select prices of every material.
         * @return string
         */
        public function selectMaterialPrices($id = false)
        {
            $fields = [
                'MP.id',
                'MP.material_id',
                'MP.price',
                'MP.unit',
                'DATE_FORMAT(MP.`updated_at`, "%m/%d/%Y") as date_updated',
                'S.id as supplier_id',
                'S.name as supplier_name'
            ];


            $initQuery = $this->select($fields)
                              ->from('material_prices MP')
                              ->leftJoin(['suppliers S' => 'MP.supplier_id = S.id'])
                              ->where(['MP.status' => ':status']);

            $initQuery = ($id) ? $initQuery->andWhere(['MP.id' => ':id']) : $initQuery;;

            return $initQuery->orderBy('MP.updated_at', 'desc');
        }        

        /**
         * `selectMaterialTypes` Query String that will select from table `material_types`
         * @return string
         */
        public function selectMaterialTypes($id = false)
        {
            $fields = [
                'MT.id',
                'MT.name',
                'MT.cost_code'
            ];

            $initQuery = $this->select($fields)
                              ->from('material_types MT');

            $initQuery = ($id) ? $initQuery->where(['id' => ':id']) : $initQuery;
                              
            return $initQuery;
        }

        // /**
        //  * `checkMaterialEstimateUsage` Query string that will check material if it is used in project estimate.
        //  * @return string
        //  */
        // public function checkMaterialEstimateUsage()
        // {
        //     $fields     = [
        //         'SOWM.id'
        //     ];

        //     $joins      = [
        //         'scope_of_works SOW'     => 'SOWM.scope_of_work_id = SOW.id',
        //         'project_type_lists PTL' => 'SOW.project_type_list_id = PTL.id',
        //         'projects P'             => 'PTL.project_id = P.id',
        //         'material_prices MP'     => 'MP.id = SOWM.material_price_id'
        //     ];
            
        //     $conditions = [
        //         'MP.material_id'      => ':material_id',
        //         'P.estimate_approval' => ':estimate_status',
        //         'MP.status'           => 1
        //     ];


        //     $initQuery = $this->select($fields)
        //                       ->from('scope_of_work_materials SOWM')
        //                       ->join($joins)
        //                       ->where($conditions);

        //     return $initQuery;
        // }

        /**
         * `selectSuppliers` Query string that will select all suppliers.
         * @return string
         */
        public function selectSuppliers()
        {
            $fields = [
                'S.id',
                'S.name'
            ];

            $initQuery = $this->select($fields)
                              ->from('suppliers S');

            return $initQuery;
        }

        /**
         * `insertMaterials` Query string that will insert to table `materials`
         * @return string
         */
        public function insertMaterial($data = [])
        {
            $initQuery = $this->insert('materials', $data);

            return $initQuery;
        }

        /**
         * `insertMaterialPrice` Query string that will insert to table `material_prices`
         * @param  array  $data
         * @return string
         */
        public function insertMaterialPrice($data = [])
        {
            $initQuery = $this->insert('material_prices', $data);

            return $initQuery;
        }

        /**
         * `updateMaterial` Query string that will update to table `materials`
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function updateMaterial($id = '', $data = [])
        {
            $initQuery = $this->update('materials', $id, $data);

            return $initQuery;
        }
        
        /**
         * `updateMaterialPrice` Query string that will update specific material price's status from `material_prices` table.
         * @return string
         */
        public function updateMaterialPrice($id = '', $data = [])
        {
            $initQuery = $this->update('material_prices', $id, $data);

            return $initQuery;
        }

        // /**
        //  * `deleteMaterial` Query string that will delete specific material.
        //  * @param  boolean $id
        //  * @return string
        //  */
        // public function deleteMaterial($id = false)
        // {
        //     $initQuery = $this->delete('materials')
        //                       ->where(['id' => ':id']);

        //     return $initQuery;
        // }
    }