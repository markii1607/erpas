<?php
    namespace App\Model\NoPropertyCertification;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class NoPropertyCertificationQueryHandler extends QueryHandler {

        public function selectNoPropertyCertifications($id = false)
        {
            $fields = [
                'RC.id',
                'RC.type',
                'RC.declaree',
                'RC.requestor',
                'RC.purpose',
                'DATE_FORMAT(RC.request_date, "%M %d, %Y") as request_date',
                'RC.amount_paid',
                'RC.or_no',
                'RC.prepared_by',
                'RC.verified_by',
            ];

            $initQuery = $this->select($fields)
                              ->from('released_certifications RC')
                              ->where(['RC.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['RC.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        public function selectTaxDeclarationRecords()
        {
            $fields = [
                'TD.id',
                'TD.td_no',
                'TD.owner',
            ];

            
            $initQuery = $this->select($fields)
                              ->from('tax_declarations TD')
                              ->where(['TD.is_active' => ':is_active']);

            return $initQuery;
        }

        public function selectUsers($id = false)
        {
            $fields = [
                'U.id',
                'U.username',
                'U.fname',
                'U.mname',
                'U.lname',
                'CONCAT_WS(" ", NULLIF(U.fname, ""), NULLIF(CONCAT(LEFT(U.mname,1), "."), ""), NULLIF(U.lname, "")) as full_name',
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

        public function insertCertification($data = [])
        {
            $initQuery = $this->insert('released_certifications', $data);

            return $initQuery;
        }
    }