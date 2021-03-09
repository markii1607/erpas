<?php 
    namespace App\Model\EquipmentDataControl;

    include_once "..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class EquipmentDataControlQueryHandler extends QueryHandler { 
        /**
         * `selectEquipments` Query string that will select from table `equipments`.
         * @param  boolean $id
         * @param  boolean $equipmentType
         * @return string
         */
        public function selectEquipments($id = false, $equipmentType = false, $bodyNo = false)
        {
            $fields = [
                'E.id',
                'E.cost_code',
                'E.body_no',
                'E.brand',
                'E.model',
                'E.equipment_status',
                'E.equipment_type_id',
                'E.capacity',
                'E.capacity_unit',
                'ET.cost_code as et_cost_code',
                'ET.name as equipment_type_name',
                'C.id as make_id',
                'C.country_name as make_name',
                'ERR.id as equipment_rental_rate_id',
                'ERR.rental_rate',
            ];

            $joins = [
                'equipment_types ET' => 'E.equipment_type_id = ET.id',
            ];

            $leftJoins = [
                'countries C'                => 'C.id = E.make',
                'equipment_rental_rates ERR' => 'E.id = ERR.equipment_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('equipments E')
                              ->join($joins)
                              ->leftJoin($leftJoins)
                              ->where(['E.status' => 1, 'ERR.status' => 1]);

            $initQuery = ($id)            ? $initQuery->andWhere(['E.id' => ':id']) : $initQuery;
            $initQuery = ($equipmentType) ? $initQuery->andWhere(['E.equipment_type_id' => ':equipment_type_id']) : $initQuery;
            $initQuery = ($bodyNo) ? $initQuery->andWhere(['E.body_no' => ':body_no']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectEquipmentTypes` Query string that will select from table `equipment_types`.
         * @return string
         */
        public function selectEquipmentTypes($id = false)
        {
            $fields = [
                'ET.id',
                'ET.name',
                'ET.cost_code'
            ];

            $initQuery = $this->select($fields)
                              ->from('equipment_types ET');

            $initQuery = ($id) ? $initQuery->where(['ET.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectCountries` Query string that will select from table `countries`.
         * @return string
         */
        public function selectCountries($id = false)
        {
            $fields = [
                'C.id',
                'C.country_name as name'
            ];

            $initQuery = $this->select($fields)
                              ->from('countries C');

            $initQuery = ($id) ? $initQuery->where(['C.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `insertEquipment` Query string that will insert to table `equipments`
         * @return string
         */
        public function insertEquipment($data = [])
        {
            $initQuery = $this->insert('equipments', $data);

            return $initQuery;
        }

        /**
         * `insertEquipmentRentalRate` Query string that will insert to table `equipment_rental_rates`
         * @return string
         */
        public function insertEquipmentRentalRate($data = [])
        {
            $initQuery = $this->insert('equipment_rental_rates', $data);

            return $initQuery;
        }

        /**
         * `updateEquipment` Query string that will update to table `equipments`
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function updateEquipment($id = '', $data = [])
        {
            $initQuery = $this->update('equipments', $id, $data);

            return $initQuery;
        }

        /**
         * `updateEquipmentRentalRate` Query string that will update to table `equipment_rental_rates`
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function updateEquipmentRentalRate($id = '', $data = [])
        {
            $initQuery = $this->update('equipment_rental_rates', $id, $data);

            return $initQuery;
        }

        // /**
        //  * `selectEquipmentTypes` Query string that will select from table `equipment_types`
        //  * @return string
        //  */
        // public function selectEquipmentTypes()
        // {
        //     $query = "                
        //         SELECT
        //             ET.`id`,
        //             ET.`name`,
        //             ET.`cost_code`
        //         FROM
        //             equipment_types ET
        //     ";

        //     return $query;
        // }

        // /**
        //  * `selectEquipmentJoinEquipmentTypes` Query string that will fetch equipment with type.
        //  * @return string
        //  */
        // public function selectEquipmentJoinEquipmentTypes()
        // {
        //     $query = "
        //         SELECT
        //             E.`id`,
        //             E.`equipment_type_id`,
        //             E.`model`,
        //             E.`body_no`,
        //             E.`cost_code`,
        //             E.`capacity`,
        //             E.`capacity_unit`,
        //             E.`equipment_status`,
        //             ET.`name` as equipment_type_name,
        //             ERR.`rental_rate`
        //         FROM
        //             equipments E,
        //             equipment_types ET,
        //             equipment_rental_rates ERR
        //         WHERE
        //                 E.`equipment_type_id` = ET.`id`
        //             AND
        //                 ERR.`equipment_id` = E.`id`
        //             AND
        //                 E.`status` = 1
        //             AND
        //                 ERR.`status` = 1
        //         ORDER BY
        //             E.`created_at` DESC
        //     ";

        //     return $query;
        // }

        // /**
        //  * `selectEquipmentViaBodyNo` Query string that will select specific equipment via body_no from table `equipments`
        //  * @return string
        //  */
        // public function selectEquipmentViaBodyNo()
        // {
        //     $query = "                
        //         SELECT
        //             E.`id`
        //         FROM
        //             equipments E
        //         WHERE
        //                 E.`body_no` = :body_no
        //             AND
        //                 E.`status` = 1
        //     ";

        //     return $query;
        // }

        // /**
        //  * `selectSpecificEquipmentViaBodyNo` Query string that will select specific equipment via body_no from table `equipments`
        //  * @return string
        //  */
        // public function selectSpecificEquipmentViaBodyNo()
        // {
        //     $query = "                
        //         SELECT
        //             E.`id`
        //         FROM
        //             equipments E
        //         WHERE
        //                 E.`id` != :id
        //             AND
        //                 E.`body_no` = :body_no
        //             AND
        //                 E.`status` = 1
        //     ";

        //     return $query;
        // }

        // /**
        //  * `selectEquipmentSpecificTypes` Query String that will fetch equipment with specific type.
        //  * @return string
        //  */
        // public function selectEquipmentSpecificTypes()
        // {
        //     $query = "
        //         SELECT 
        //             ET.`cost_code` as et_cost_code,
        //             E.`cost_code` as e_cost_code
        //         FROM 
        //             `equipments` E, 
        //             `equipment_types` ET 
        //         WHERE 
        //                 E.equipment_type_id = ET.id
        //             AND
        //                 E.equipment_type_id = :equipment_type_id
        //     ";

        //     return $query;
        // }

        // /**
        //  * `selectEquipmentTypeSpecificId` Query string that will select specific equipment type via id from table `equipment_types`
        //  * @return string
        //  */
        // public function selectEquipmentTypeSpecificId()
        // {
        //     $query = "                
        //         SELECT
        //             ET.`id`,
        //             ET.`name`,
        //             ET.`cost_code`
        //         FROM
        //             equipment_types ET
        //         WHERE
        //             ET.`id` = :id
        //     ";

        //     return $query;
        // }


        // /**
        //  * `selectSpecificEquipmentJoinEquipmentTypes` Query string that will fetch equipment with type.
        //  * @return string
        //  */
        // public function selectSpecificEquipmentJoinEquipmentTypes()
        // {
        //     $query = "
        //         SELECT
        //             E.`id`,
        //             E.`equipment_type_id`,
        //             E.`model`,
        //             E.`body_no`,
        //             E.`cost_code`,
        //             E.`capacity`,
        //             E.`capacity_unit`,
        //             E.`equipment_status`,
        //             E.`file`,
        //             ET.`name` as equipment_type_name,
        //             ERR.`rental_rate`
        //         FROM
        //             equipments E,
        //             equipment_types ET,
        //             equipment_rental_rates ERR
        //         WHERE
        //                 E.`equipment_type_id` = ET.`id`
        //             AND
        //                 ERR.`equipment_id` = E.`id`
        //             AND
        //                 E.`status` = 1
        //             AND
        //                 ERR.`status` = 1
        //             AND
        //                 E.`id` = :id
        //     ";

        //     return $query;
        // }

        // /**
        //  * `checkEquipmentEstimateUsage` Query string that will check equipment if used in estimate that is not yet approved.
        //  * @return string
        //  */
        // public function checkEquipmentEstimateUsage()
        // {            
        //     $query = "
        //         SELECT
        //             SOWE.`id`
        //         FROM 
        //             `scope_of_work_equipments` SOWE,
        //             `equipment_rental_rates` ERR,
        //             `scope_of_works` SOW,
        //             `project_type_lists` PTL,
        //             `projects` P
        //         WHERE
        //                 ERR.id = SOWE.equipment_rental_rate_id
        //             AND
        //                 SOWE.`scope_of_work_id` = SOW.`id`
        //             AND
        //                 SOW.`project_type_list_id` = PTL.`id`
        //             AND
        //                 PTL.`project_id` = P.`id`
        //             AND
        //                 ERR.`equipment_id` = :equipment_id
        //             AND
        //                 ERR.`status` = 1
        //             AND
        //                 P.`estimate_approval` = 1
        //     ";

        //     return $query;
        // }

        // /**
        //  * `selectWorkItemSpecificItemNoCategories` Query String that will fetch work item with specific item_no and category.
        //  * @return string
        //  */
        // // public function selectWorkItemSpecificItemNoCategories()
        // // {
        // //     $query = "
        // //         SELECT
        // //             WI.`cost_code`
        // //         FROM
        // //             work_items WI
        // //         WHERE
        // //                 WI.`item_no` = :item_no
        // //             AND
        // //                 WI.`work_item_category_id` = :work_item_category_id
        // //     ";

        // //     return $query;
        // // }

        // /**
        //  * `selectWorkItemSpecificCostCode` Query String that will fetch work item with specific cost code.
        //  * @return string
        //  */
        // // public function selectWorkItemSpecificCostCode()
        // // {
        // //     $query = "
        // //         SELECT
        // //             WI.`id`,
        // //             WI.`work_item_category_id`,
        // //             WI.`cost_code`,
        // //             WI.`item_no`,
        // //             WI.`name`
        // //         FROM
        // //             work_items WI
        // //         WHERE
        // //             WI.`cost_code` = :cost_code
        // //     ";

        // //     return $query;
        // // }

        // /**
        //  * `insertEquipment` Query string that will insert to table `equipments`
        //  * @return string
        //  */
        // public function insertEquipment()
        // {
        //     $query = "
        //         INSERT INTO `equipments`
        //             (equipment_type_id, model, body_no, cost_code, capacity, capacity_unit, file, equipment_status, created_by, updated_by, created_at, updated_at)
        //         VALUES
        //             (:equipment_type_id, :model, :body_no, :cost_code, :capacity, :capacity_unit, :file, :equipment_status, :created_by, :updated_by, :created, :updated)
        //     ";

        //     return $query;
        // }

        // /**
        //  * `insertEquipmentRentalRate` Query string that will insert to table `equipment_rental_rates`
        //  * @return string
        //  */
        // public function insertEquipmentRentalRate()
        // {
        //     $query = "
        //         INSERT INTO `equipment_rental_rates`
        //             (equipment_id, rate_type, rental_rate, created_by, updated_by, created_at, updated_at)
        //         VALUES
        //             (:equipment_id, :rate_type, :rental_rate, :created_by, :updated_by, :created, :updated)
        //     ";

        //     return $query;
        // }

        // /**
        //  * `updateEquipment` Query string that will update specific equipment information from table `equipments`
        //  * @return string
        //  */
        // public function updateEquipment()
        // {
        //     $query = "
        //         UPDATE 
        //             `equipments`
        //         SET
        //             `equipment_type_id`=:equipment_type_id, `model`=:model, `body_no`=:body_no, `cost_code`=:cost_code, `capacity`=:capacity, `capacity_unit`=:capacity_unit, `file`=:file, `equipment_status`=:equipment_status, `updated_by`=:updated_by, `updated_at`=:updated
        //         WHERE
        //             id=:id
        //     ";

        //     return $query;
        // }

        // /**
        //  * `updateSpecificEquipmentStatus` Query string that will update specific equipment status from table `equipments`
        //  * @return [type] [description]
        //  */
        // public function updateSpecificEquipmentStatus()
        // {
        //     $query = "
        //         UPDATE
        //             `equipments`
        //         SET
        //             `status`=:status, `updated_by`=:updated_by, `updated_at`=:updated
        //         WHERE
        //             id=:id
        //     ";

        //     return $query;
        // }

        // /**
        //  * `updateEquipmentRentalRates` Query string that will update specific equipment rental rate.
        //  * @return string
        //  */
        // public function updateEquipmentRentalRates()
        // {
        //     $query = "
        //         UPDATE
        //             `equipment_rental_rates`
        //         SET
        //             `rental_rate`=:rental_rate, `updated_by`=:updated_by, `updated_at`=:updated
        //         WHERE
        //             equipment_id=:equipment_id
        //     ";

        //     return $query;
        // }

        // /**
        //  * `deleteWorkItems` Query string that will delete specific work item from `work_items` table.
        //  * @return string
        //  */
        // // public function deleteWorkItems()
        // // {
        // //     $query = "
        // //         DELETE FROM
        // //             `work_items`
        // //         WHERE
        // //             id=:id
        // //     ";

        // //     return $query;
        // // }
    }