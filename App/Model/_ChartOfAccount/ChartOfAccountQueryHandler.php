<?php 
    namespace App\Model\ChartOfAccount;

    include_once "..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class ChartOfAccountQueryHandler extends QueryHandler { 
        /**
         * `selectAccounts` Query string that will fetch accounts.
         * @param  boolean $id
         * @return string
         */
        public function selectAccounts($id = false, $code = false, $name = false)
        {
            $fields = [
                'A.id',
                'A.name'
            ];

            $initQuery = $this->select($fields)
                              ->from('accounts A')
                              ->where(['A.status' => ':status']);

            $initQuery = ($id)   ? $initQuery->andWhere(['A.id' => ':id'])         : $initQuery;
            $initQuery = ($code) ? $initQuery->andWhereLike(['A.code' => ':code']) : $initQuery;
            $initQuery = ($name) ? $initQuery->andWhereLike(['A.name' => ':name']) : $initQuery;

            return $initQuery;
        }

        /**
         * `insertAccount` Query string that will insert to table `accounts`
         * @return string
         */
        public function insertAccount($data = [])
        {
            $initQuery = $this->insert('accounts', $data);

            return $initQuery;
        }

        /**
         * `updateAccount` Query string that will update specific account information from table `accounts`
         * @return string
         */
        public function updateAccount($id = '', $data = [])
        {
            $initQuery = $this->update('accounts', $id, $data);

            return $initQuery;
        }
    }