<?php

namespace App\Model\HDFMonitoring;

require_once('../../AbstractClass/QueryHandler.php');

use App\AbstractClass\QueryHandler;

class HDFMonitoringQueryHandler extends QueryHandler {

    public function selectHealthDeclarationForms($dept_id = false, $count = false)
    {
        if ($count) {
            $fields = [
                'count(OH.id) as hdf_count'
            ];
        } else {
            $fields = [
                'OH.id',
                'OH.user_id',
                'OH.firstname',
                'OH.lastname',
                'OH.middlename',
                'CONCAT(OH.lastname, ", ", OH.firstname, " ", OH.middlename) as fullname',
                'OH.nationality',
                'OH.sex',
                'OH.age',
                'OH.contact_no',
                'OH.email_add',
                'OH.permanent_address',
                'OH.present_address',
                'OH.present_temperature',
                'OH.local_places',
                'OH.is_sick',
                'OH.sick_description',
                'OH.has_symptoms',
                'OH.has_symptoms_desc',
                'OH.symptoms_occurrence',
                'OH.has_animal_contact',
                'OH.animal_contact_desc',
                'OH.is_with_frontliner',
                'OH.is_in_pum_list',
                'DATE_FORMAT(OH.created_at, "%b %d, %Y") as date_accomplished',
                'OH.is_updated',
                'D.id as department_id',
                'D.name as department'
            ];
        }

        $orWhereCondition = array(
            'OH.firstname'  => ':filter_val',
            'OH.middlename' => ':filter_val',
            'OH.lastname'   => ':filter_val',
            'D.name'        => ':filter_val',
        );

        $joins = [
            'users U'                       =>  'U.id = OH.user_id',
            'employment_informations EI'    =>  'EI.personal_information_id = U.personal_information_id',
            'positions P'                   =>  'P.id = EI.position_id',
            'departments D'                 =>  'D.id = P.department_id'
        ];

        $initQuery = $this->select($fields)
                          ->from('osh_hdf OH')
                          ->join($joins)
                          ->where(['OH.is_active' => ':is_active'])
                          ->logicEx('AND')
                          ->orWhereLike($orWhereCondition);
 
        $initQuery = ($dept_id) ?   $initQuery->andWhere(['D.id' => ':dept_id'])  : $initQuery;


        return $initQuery;
    }

    public function selectHdfEditRequests()
    {
        $fields = [
            'HER.id',
            'HER.osh_hdf_id',
            'HER.reason',
            'HER.approved_by',
            'HER.status',
            'HER.approver_remarks',
            'HER.created_by',
            'DATE_FORMAT(HER.created_at, "%b %d, %Y") as created_at',
        ];

        $initQuery = $this->select($fields)
                          ->from('osh_hdf_edit_requests HER')
                          ->where(['HER.is_active' => ':is_active', 'HER.approved_by' => ':approver'])
                          ->andWhereNull(['HER.status']);

        return $initQuery;
    }

    public function selectUserInformation($id = false)
    {
        $fields = [
            'U.id',
            'PI.fname',
            'PI.mname',
            'PI.lname',
            'PI.sex',
            'PI.citizenship as nationality',
        ];

        $join = [
            'personal_informations PI'  =>  'PI.id = U.personal_information_id'
        ];

        $initQuery = $this->select($fields)
                          ->from('users U')
                          ->join($join)
                          ->where(['U.is_active' => ':is_active']);

        $initQuery = ($id) ? $initQuery->andWhere(['U.id' => ':id']) : $initQuery;

        return $initQuery;
    }

    public function selectDepartments()
    {
        $fields = [
            'D.id',
            'D.name',
            'D.charging',
        ];

        $initQuery = $this->select($fields)
                          ->from('departments D')
                          ->where(['D.is_active' => ':is_active']);

        return $initQuery;
    }

    public function updateHdfEditRequests($id = '', $data = [])
    {
        $initQuery = $this->update('osh_hdf_edit_requests', $id, $data);

        return $initQuery;
    }
}