<?php

namespace App\Model\HdfVisitor;

require_once('../../AbstractClass/QueryHandler.php');

use App\AbstractClass\QueryHandler;

class HdfVisitorQueryHandler extends QueryHandler {

    public function insertHdfVisitor($data = [])
    {
        $initQuery = $this->insert('osh_hdf_visitors', $data);

        return $initQuery;
    }
}