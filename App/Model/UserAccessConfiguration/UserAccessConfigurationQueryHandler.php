<?php

namespace App\Model\UserAccessConfiguration;

require_once("../../AbstractClass/QueryHandler.php");

use App\AbstractClass\QueryHandler;

class UserAccessConfigurationQueryHandler extends QueryHandler
{
    public function selectUsers($id = false)
    {
        $fields = [
            'U.id',
            'U.username',
            'U.fname',
            'U.mname',
            'U.lname',
            'CONCAT_WS(" ", NULLIF(U.fname, ""), NULLIF(U.mname, ""), NULLIF(U.lname, "")) as full_name',
            'U.department',
            'U.position',
            'U.access_type',
        ];

        $initQuery = $this->select($fields)
                          ->from('users U')
                          ->where(['U.is_active' => ':is_active']);

        $initQuery = ($id) ? $initQuery->andWhere(['U.id' => ':id']) : $initQuery;

        return $initQuery;
    }

    public function insertUser($data = [])
    {
        $initQuery = $this->insert('users', $data);

        return $initQuery;
    }

    public function updateUser($id = '', $data = [])
    {
        $initQuery = $this->update('users', $id, $data);

        return $initQuery;
    }
}
