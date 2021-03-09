<?php 
    namespace App\Model\InventoryMonitoring;

    include_once "..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class InventoryMonitoringQueryHandler extends QueryHandler { 
        /**
         * `selectMaterialInventoryJoinMaterial` Query string that will select employees with position and department.
         * @return string
         */
        public function selectMaterialInventoryJoinMaterial()
        {
            $query = "
                SELECT
                    MI.`id`,
                    MI.`material_id`,
                    M.`cost_code`,
                    M.`name`,
                    MT.`name` as material_type_name,
                    MI.`quantity`,
                    MI.`unit`,
                    DATE_FORMAT(MI.`created_at`, '%m/%d/%Y') as inventory_date
                FROM
                    material_inventories MI,
                    materials M,
                    material_types MT
                WHERE
                        M.id = MI.material_id
                    AND
                        MT.id = M.material_type_id
                    AND
                        MI.status = 1
            ";

            return $query;
        }

        /**
         * `selectMaterials` Query String that will select from table `materials`
         * @return string
         */
        public function selectMaterials()
        {
            $query = "
                SELECT
                    M.`id`,
                    M.`name`,
                    M.`cost_code`
                FROM
                    materials M
                WHERE
                    M.status = 1
            ";

            return $query;
        }

        /**
         * `selectMaterialInventoryViaMaterialIdUnit` Query String that will select from table `material_inventories`
         * @return string
         */
        public function selectMaterialInventoryViaMaterialIdUnit()
        {
            $query = "
                SELECT
                    MI.`id`
                FROM
                    `material_inventories` MI
                WHERE
                        MI.material_id = :material_id
                    AND
                        MI.unit LIKE :unit
                    AND
                        MI.status = 1
            ";

            return $query;
        }

        /**
         * `selectSpecificMaterialInventoryJoinMaterials` Query String that will fetch specific inventory of material.
         * @return string
         */
        public function selectSpecificMaterialInventoryJoinMaterials()
        {
            $query = "
                SELECT
                    MI.`id`,
                    MI.`material_id`,
                    MI.`quantity`,
                    MI.`unit`,
                    M.`name` as material_name,
                    M.`cost_code`
                FROM
                    material_inventories MI,
                    materials M
                WHERE
                        MI.material_id = M.id
                    AND
                        MI.`id` = :id
            ";

            return $query;
        }

        /**
         * `insertInventories` Query string that will insert to table `material_inventories`
         * @return string
         */
        public function insertInventories()
        {
            $query = "
                INSERT INTO `material_inventories`
                    (material_id, quantity, unit, created_by, updated_by, created_at, updated_at)
                VALUES
                    (:material_id, :quantity, :unit, :created_by, :updated_by, :created, :updated)
            ";

            return $query;
        }

        /**
         * `updateMaterialInventoryStatus` Query string that will update status of specific material inventory from table `material_inventories`
         * @return string
         */
        public function updateMaterialInventoryStatus()
        {
            $query = "
                UPDATE 
                    `material_inventories`
                SET
                    `status`=:status, `updated_by`=:updated_by, `updated_at`=:updated
                WHERE
                    id=:id
            ";

            return $query;
        }

        /**
         * `softDeleteMaterialInventories` Query string that will soft delete specific material inventory from `material_inventories` table.
         * @return string
         */
        public function softDeleteMaterialInventories()
        {            
            $query = "
                UPDATE
                    `material_inventories`
                SET
                    `status`=0,`updated_by`=:updated_by,`updated_at`=:updated_at
                WHERE
                    id=:id
            ";

            return $query;
        }
    }