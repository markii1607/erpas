<?php 
    namespace App\Model\Main;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class MainQueryHandler extends QueryHandler { 

        /**
         * `selectUsers` Query string that will select from table `users`.
         * @param  boolean $id`
         * @return string
         */
        public function selectUsers($id = false)
        {
            $fields = [
                'U.id as user_id',
                'U.id',
                'U.username',
                'U.password',
                'U.access_type',
                'U.department',
                'U.position',
                'CONCAT_WS(" ", NULLIF(U.fname, ""), NULLIF(U.mname, ""), NULLIF(U.lname, "")) as full_name',
            ];

            $initQuery = $this->select($fields)
                              ->from('users U')
                              ->where(['U.is_active' => 1]);

            $initQuery = ($id) ? $initQuery->andWhere(['U.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `updateUser` Query string that will update specific department information from table `users`
         * @return string
         */
        public function updateUser($id = '', $data = array())
        {
            $initQuery = $this->update('users', $id, $data);

            return $initQuery;
        }
    }