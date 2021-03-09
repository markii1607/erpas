<?php 
    namespace App\Model\PositionDataBank;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class PositionDataBankQueryHandler extends QueryHandler { 
        /**
         * `selectPositions` Query string that will fetch position.
         * @return string
         */
        public function selectPositions($id = false, $departmentId = false, $name = false)
        {
            $fields = [
                'P.id',
                'IF(P.code IS NULL, "", P.code) as code',
                'P.name',
                'P.default_rate',
                'P.head_id',
                'IF(D.name IS NULL, "", D.name) as department_name',
                'IF(D.id IS NULL, "", D.id) as department_id',
                '(SELECT H.name FROM positions H WHERE H.id=P.head_id) AS head_name'
            ];

            $leftJoins = [
                'departments D' => 'D.id = P.department_id',
            ];

            $initQuery = $this->select($fields)
                              ->from('positions P')
                              ->leftJoin($leftJoins)
                              ->where(['P.is_active' => ':is_active']);

            $initQuery = ($id)           ? $initQuery->andWhere(['P.id' => ':id'])                       : $initQuery;
            $initQuery = ($departmentId) ? $initQuery->andWhere(['P.department_id' => ':department_id']) : $initQuery;
            $initQuery = ($name)         ? $initQuery->andWhereLike(['P.name' => ':name'])               : $initQuery;

            return $initQuery;
        }

        /**
         * `selectDepartments` Query String that will select from table `departments`
         * @return string
         */
        public function selectDepartments()
        {
            $fields = [
                'D.id',
                'D.name'
            ];

            $initQuery = $this->select($fields)
                              ->from('departments D')
                              ->where(['D.is_active' => 1])
                              ->andWhereLike(['D.name' => ':search'])
                              ->limit(10);

            return $initQuery;
        }

        /**
         * `selectSearchingPositions` Query String that will select from table `positions`
         * @return string
         */
        public function selectSearchingPositions()
        {
            $fields = [
                'P.id',
                'P.name'
            ];

            $initQuery = $this->select($fields)
                              ->from('positions P')
                              ->where(['P.is_active' => 1])
                              ->andWhereLike(['P.name' => ':search'])
                              ->limit(10);

            return $initQuery;
        }

        /**
         * `insertPosition` Query string that will insert to table `positions`
         * @return string
         */
        public function insertPosition($data = [])
        {
            $initQuery = $this->insert('positions', $data);

            return $initQuery;
        }

        /**
         * `updatePosition` Query string that will update specific position information from table `positions`
         * @return string
         */
        public function updatePosition($id = '', $data = [])
        {
            $initQuery = $this->update('positions', $id, $data);

            return $initQuery;
        }
    }