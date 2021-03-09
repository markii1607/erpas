<?php 
    namespace App\Model\StockCard;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class StockCardQueryHandler extends QueryHandler { 
        /**
         * `selectDepartments` Query string that will fetch department.
         * @return string
         */
        public function selectDepartments($id = false, $name = false)
        {
            $fields = [
                'D.id',
                'IF(D.code IS NULL, "", D.code) as code',
                'IF(D.charging IS NULL, "", D.charging) as charging',
                'D.name',
            ];

            $initQuery = $this->select($fields)
                              ->from('departments D')
                              ->where(['D.is_active' => ':is_active']);


            $initQuery = ($id)   ? $initQuery->andWhere(['D.id' => ':id'])         : $initQuery;
            $initQuery = ($name) ? $initQuery->andWhereLike(['D.name' => ':name']) : $initQuery;

            return $initQuery;
        }

        /**
         * `insertDepartment` Query string that will insert to table `departments`
         * @return string
         */
        public function insertDepartment($data = [])
        {
            $initQuery = $this->insert('departments', $data);

            return $initQuery;
        }

        /**
         * `updateDepartment` Query string that will update specific department information from table `departments`
         * @return string
         */
        public function updateDepartment($id = '', $data = [])
        {
            $initQuery = $this->update('departments', $id, $data);

            return $initQuery;
        }
    }