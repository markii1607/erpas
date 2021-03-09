<?php 
    namespace App\Model\RequestorDataControl;

    include_once "..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class RequestorDataControlQueryHandler extends QueryHandler { 
        /**
         * `selectRequestors` Query string that will fetch requestors.
         * @param  boolean $id
         * @return string
         */
        public function selectRequestors($id = false, $name = false)
        {
            $fields = [
                'R.id',
                'R.name'
            ];

            $initQuery = $this->select($fields)
                              ->from('requestors R')
                              ->where(['R.status' => ':status']);

            $initQuery = ($id)   ? $initQuery->andWhere(['R.id' => ':id'])         : $initQuery;
            $initQuery = ($name) ? $initQuery->andWhereLike(['R.name' => ':name']) : $initQuery;

            return $initQuery;
        }

        /**
         * `insertRequestor` Query string that will insert to table `requestors`
         * @return string
         */
        public function insertRequestor($data = [])
        {
            $initQuery = $this->insert('requestors', $data);

            return $initQuery;
        }

        /**
         * `updateRequestor` Query string that will update specific requestor information from table `requestors`
         * @return string
         */
        public function updateRequestor($id = '', $data = [])
        {
            $initQuery = $this->update('requestors', $id, $data);

            return $initQuery;
        }
    }