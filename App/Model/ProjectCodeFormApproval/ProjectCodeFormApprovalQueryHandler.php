<?php
    namespace App\Model\ProjectCodeFormApproval;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class ProjectCodeFormApprovalQueryHandler extends QueryHandler {

        public function selectProjectCodeRequests($id = false, $updated_by = false)
        {
            $fields = array(
                'PCR.id',
                'PCR.project_id',
                'PCR.temporary_project_code as tc_project',
                'DATE_FORMAT(PCR.date_requested, "%b-%d-%Y %r") as date_requested',
                'DATE_FORMAT(PCR.updated_at, "%b-%d-%Y %r") as date_approved',
                'IF(PCR.status IS NULL, "for_approval", PCR.status) as status',
                'PCR.updated_by',
                'IF(P.project_code IS NULL, "", P.project_code) as project_code',
                'P.name as project_title',
                'P.location as project_location',
                'P.contract_code',
                'C.name as client_name',
                'CONCAT(PI.lname, ", ", PI.fname, " ", PI.mname, ".") as request_by',
                'POS.name as position'
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

            $initQuery = ($id) ? $initQuery->andWhere(array('PCR.id' => ':id')) : $initQuery;
            $initQuery = ($updated_by) ? $initQuery->andWhere(array('PCR.updated_by' => ':updated_by')) : $initQuery;

            return $initQuery;
        }

        public function selectEmployeeInformation($user_id = false)
        {
            $fields = array(
                'CONCAT(PI.lname, ", ", PI.fname, " ", PI.mname) as fullname',
                'P.name as position',
            );

            $joins = array(
                'personal_informations PI'      =>  'PI.id = U.personal_information_id',
                'employment_informations EI'    =>  'EI.personal_information_id = PI.id',
                'positions P'                   =>  'P.id = EI.position_id'
            );

            $initQuery = $this->select($fields)
                              ->from('users U')
                              ->join($joins)
                              ->where(array('U.is_active' => ':is_active'));

            $initQuery = ($user_id) ? $initQuery->andWhere(array('U.id' => ':user_id')) : $initQuery;

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
