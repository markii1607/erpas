<?php

namespace App\Model\SignatoriesConfiguration;

require_once("../../AbstractClass/QueryHandler.php");

use App\AbstractClass\QueryHandler;

class SignatoriesConfigurationQueryHandler extends QueryHandler
{
    public function selectApprovers($id = false)
    {
        $fields = [
            'A.id',
            'A.approvers',
            'DATE_FORMAT(A.created_at, "%M %d, %Y %r") as created_at',
        ];

        $initQuery = $this->select($fields)
                          ->from('approver_sets A')
                          ->where(['A.is_active' => ':is_active']);

        $initQuery = ($id) ? $initQuery->andWhere(['A.id' => ':id']) : $initQuery;

        return $initQuery;
    }

    public function insertApproverSet($data = [])
    {
        $initQuery = $this->insert('approver_sets', $data);

        return $initQuery;
    }

    public function updateApproverSet($id = '', $data = [])
    {
        $initQuery = $this->update('approver_sets', $id, $data);

        return $initQuery;
    }
}
