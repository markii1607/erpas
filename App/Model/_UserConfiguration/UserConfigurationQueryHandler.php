<?php 
    namespace App\Model\UserConfiguration;

    class UserConfigurationQueryHandler { 
        /**
         * `selectUserJoinEmployees` Query string that will fetch user and informations.
         * @return string
         */
        public function selectUserJoinEmployees()
        {
            $query = "
                SELECT
                    U.id,
                    U.email,
                    U.password,
                    E.code,
                    CONCAT(E.fname,' ', E.mname,' ', E.lname) as fullname
                FROM
                    users U,
                    employees E
                WHERE
                    U.employee_id = E.id
                ORDER BY
                    E.code ASC
            ";

            return $query;
        }

        /**
         * `selectEmployees` Query string that will select from table `employees`.
         * @return string
         */
        public function selectEmployees()
        {
            $query = "
                SELECT 
                    E.id,
                    E.code,
                    CONCAT(E.fname,' ',E.mname,' ',E.lname) as fullname
                FROM 
                        `employees` E 
                    LEFT JOIN 
                        `users` U 
                    ON 
                        E.id = U.employee_id 
                WHERE 
                        E.id 
                    NOT IN 
                        (SELECT 
                            E.id 
                        FROM 
                            `employees` E, 
                            `users` U 
                        WHERE 
                            E.id = U.employee_id)
            ";

            return $query;
        }

        /**
         * `selectSpecificUserJoinEmployees` Query String that will fetch specific user joined employee.
         * @return string
         */
        public function selectSpecificUserJoinEmployees()
        {
            $query = "
                SELECT 
                    U.id,
                    U.email,
                    U.password,
                    U.employee_id,
                    E.code,
                    CONCAT(E.fname,' ',E.mname,' ',E.lname) as employee_name
                FROM 
                    `users` U, 
                    `employees` E
                WHERE 
                        U.employee_id = E.id
                    AND
                        U.id = :id
            ";

            return $query;
        }

        /**
         * `insertUsers` Query string that will insert to table `users`
         * @return string
         */
        public function insertUsers()
        {
            $query = "
                INSERT INTO `users`
                    (employee_id, email, password, created_at, updated_at)
                VALUES
                    (:employee_id, :email, md5(:password), :created, :updated)
            ";

            return $query;
        }

        /**
         * `updateUsers` Query string that will update specific user information from table `users`
         * @return string
         */
        public function updateUsers()
        {
            $query = "
                UPDATE 
                    `users`
                SET
                    `email`=:email,`password`=md5(:password),`updated_at`=:updated
                WHERE
                    id=:id
            ";

            return $query;
        }

        /**
         * `deleteUsers` Query string that will delete specific user from `users` table.
         * @return string
         */
        public function deleteUsers()
        {
            $query = "
                DELETE FROM
                    `users`
                WHERE
                    id=:id
            ";

            return $query;
        }
    }