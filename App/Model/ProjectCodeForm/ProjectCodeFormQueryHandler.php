<?php
    namespace App\Model\ProjectCodeForm;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class ProjectCodeFormQueryHandler extends QueryHandler {

        public function selectProjectCodeRequests($id = false, $projectCodeNotNull = false)
        {
            $fields = array(
                'PCR.id',
                'PCR.project_id',
                'DATE_FORMAT(PCR.date_requested, "%b-%d-%Y %r") as date_requested',
                'DATE_FORMAT(PCR.updated_at, "%b-%d-%Y %r") as date_approved',
                'IF(PCR.status IS NULL, "for_approval", PCR.status) as status',
                'PCR.updated_by',
                'IF(P.project_code IS NULL, "", P.project_code) as project_code',
                'P.name as project_title',
                'P.location as project_location',
                'P.client_id',
                'P.contract_code',
                'C.name as client_name',
                'CONCAT(PI.lname, ", ", PI.fname, " ", PI.mname, ".") as request_by',
                'POS.name as position',
            );

            $joins = array(
                'projects P'                =>      'P.id = PCR.project_id',
                'clients C'                 =>      'C.id = P.client_id',
                'users U'                   =>      'U.id = PCR.request_by',
                'personal_informations PI'  =>      'PI.id = U.personal_information_id',
                'employment_informations EI'=>      'EI.personal_information_id = PI.id',
                'positions POS'             =>      'POS.id = EI.position_id'
            );

            $initQuery = $this->select($fields)
                              ->from('project_code_requests PCR')
                              ->leftJoin($joins)
                              ->where(array('PCR.is_active' => ':is_active'));

            $initQuery = ($id)                 ? $initQuery->andWhere(array('PCR.id' => ':id'))       : $initQuery;
            $initQuery = ($projectCodeNotNull) ? $initQuery->andWhereNotNull(array('P.project_code')) : $initQuery;

            return $initQuery;
        }

        public function selectEmployeeInformation($user_id = false)
        {
            $fields = array(
                'CONCAT(PI.lname, ", ", PI.fname, " ", LEFT(PI.mname, 1)) as fullname',
                'P.name as position',
            );

            $joins = array(
                'personal_informations PI'      =>  'PI.id = U.personal_information_id',
                'employment_informations EI'    =>  'EI.personal_information_id = PI.id',
                'positions P'                   =>  'P.id = EI.position_id'
            );

            $initQuery = $this->select($fields)
                              ->from('users U')
                              ->join($joins);
                            //   ->where(array('U.is_active' => ':is_active'));

            $initQuery = ($user_id) ? $initQuery->where(array('U.id' => ':user_id')) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectClient` Query string that will select from table `projects`
         * @return string
         */
        public function selectClients($id = false)
        {
            $fields = [
                'C.id',
                'C.name',

            ];

            $initQuery = $this->select($fields)
                              ->from('clients C')
                              ->where(['C.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(array('C.id' => ':id')) : $initQuery;

            return $initQuery;
        }

        public function insertProject($data = array())
        {
            $initQuery = $this->insert('projects', $data);

            return $initQuery;
        }

        public function insertProjectCodeRequest($data = array())
        {
            $initQuery = $this->insert('project_code_requests', $data);

            return $initQuery;
        }

        public function updateProject($id = '', $data = array())
        {
            $initQuery = $this->update('projects', $id, $data);

            return $initQuery;
        }

        public function updateProjectCodeRequest($id = '', $data = array())
        {
            $initQuery = $this->update('project_code_requests', $id, $data);

            return $initQuery;
        }

    }
