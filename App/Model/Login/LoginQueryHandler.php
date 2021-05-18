<?php

namespace App\Model\Login;

require_once("../../AbstractClass/QueryHandler.php");

use App\AbstractClass\QueryHandler;

class LoginQueryHandler extends QueryHandler
{

    /**
     * `selectUsers` Query String that will select active users from table `users`.
     * @return string
     */
    public function selectUsers()
    {
        $fields = [
            'U.id',
            'U.username',
            'U.password',
            'U.access_type',
            'U.department',
            'U.position',
            'CONCAT_WS(" ", NULLIF(U.fname, ""), NULLIF(U.mname, ""), NULLIF(U.lname, "")) as full_name',
        ];

        $whereCondition = [
            'U.username'   => ':username',
            'U.password'   => ':password',
            'U.is_active'  => 1,
        ];

        $initQuery = $this->select($fields)
                          ->from('users U')
                          ->where($whereCondition);

        return $initQuery;
    }

        /**
     * `selectUsersDevMode` Query String that will select active users from table `users` that does not require password.
     * @return string
     */
    public function selectUsersDevMode()
    {
        $fields = [
            'U.id',
            'U.username',
            'U.password',
            'U.access_type',
            'U.department',
            'U.position',
            'CONCAT_WS(" ", NULLIF(U.fname, ""), NULLIF(U.mname, ""), NULLIF(U.lname, "")) as full_name',
        ];

        $whereCondition = [
            'U.is_active'  => 1,
            'U.username'   => ':username',
        ];

        $initQuery = $this->select($fields)
                          ->from('users U')
                          ->where($whereCondition);

        return $initQuery;
    }

    /**
     * `selectUserName` Query String that will select username from table `users`.
     * @return string
     */
    public function selectUserName($user_id = false)
    {
        $fields = [
            'U.id',
            'U.username',
        ];

        $initQuery = $this->select($fields)
            ->from('users U');

        $initQuery = ($user_id) ? $initQuery->Where(array('U.id' => ':user_id')) : $initQuery;

        return $initQuery;
    }

    /**
     * `selectSessionLogs` Query String that will select existing entry of IP loggen in table `session_logs`.
     * @return string
     */
    public function selectSessionLogs($hasIp = false)
    {
        $fields = [
            'SL.id',
            'SL.ip_address',
            'SL.user_id',
            'SL.session_data',
            'SL.status',
        ];

        $initQuery = $this->select($fields)
            ->from('session_logs SL');

        $initQuery = ($hasIp) ? $initQuery->Where(array('SL.ip_address' => ':ip_address')) : $initQuery;

        return $initQuery;
    }


    /**
     * `insertSessionLogs` Query String that will insert Login Data to table `session_logs`.
     * @return string
     */
    public function insertSessionLogs($data = array())
    {
        $initQuery = $this->insert('session_logs', $data);
        return $initQuery;
    }


    /**
     * `insertUser` Query string that will insert to table `users`
     * @return string
     */
    public function insertUser($data = [])
    {
        $initQuery = $this->insert('users', $data);

        return $initQuery;
    }

    /**
     * `updateSessionLogs` Query String that will update Login Data to table `session_logs`.
     * @return string
     */
    public function updateSessionLogs($id = '', $data = [])
    {
        $initQuery = $this->update('session_logs', $id, $data);

        return $initQuery;
    }
}
