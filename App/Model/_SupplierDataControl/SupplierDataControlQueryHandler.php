<?php 
    namespace App\Model\SupplierDataControl;

    include_once "..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class SupplierDataControlQueryHandler extends QueryHandler { 
        /**
         * `selectSuppliers` Query string that will fetch suppliers.
         * @param  boolean $id
         * @return string
         */
        public function selectSuppliers($id = false, $code = false, $name = false)
        {
            $fields = [
                'S.id',
                'S.name'
            ];

            $initQuery = $this->select($fields)
                              ->from('suppliers S')
                              ->where(['S.status' => ':status']);

            $initQuery = ($id)   ? $initQuery->andWhere(['S.id' => ':id'])         : $initQuery;
            $initQuery = ($code) ? $initQuery->andWhereLike(['S.cost_code' => ':code']) : $initQuery;
            $initQuery = ($name) ? $initQuery->andWhereLike(['S.name' => ':name']) : $initQuery;

            return $initQuery;
        }

        /**
         * `insertSupplier` Query string that will insert to table `suppliers`
         * @return string
         */
        public function insertSupplier($data = [])
        {
            $initQuery = $this->insert('suppliers', $data);

            return $initQuery;
        }

        /**
         * `updateSupplier` Query string that will update specific supplier information from table `suppliers`
         * @return string
         */
        public function updateSupplier($id = '', $data = [])
        {
            $initQuery = $this->update('suppliers', $id, $data);

            return $initQuery;
        }
    }