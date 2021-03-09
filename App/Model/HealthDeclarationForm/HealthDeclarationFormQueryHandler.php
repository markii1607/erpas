<?php

namespace App\Model\HealthDeclarationForm;

require_once('../../AbstractClass/QueryHandler.php');

use App\AbstractClass\QueryHandler;

class HealthDeclarationFormQueryHandler extends QueryHandler {

    public function selectHealthDeclarationForms($id = false, $user_id = false, $current_date = false)
    {
        $fields = [
            'OH.id',
            'OH.user_id',
            'OH.firstname',
            'OH.lastname',
            'OH.middlename',
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
        ];

        $initQuery = $this->select($fields)
                          ->from('osh_hdf OH')
                          ->where(['OH.is_active' => ':is_active']);
 
        $initQuery = ($id)              ?   $initQuery->andWhere(['OH.id' => ':id'])                    : $initQuery;
        $initQuery = ($user_id)         ?   $initQuery->andWhere(['OH.user_id' => ':user_id'])          : $initQuery;
        $initQuery = ($current_date)    ?   $initQuery->andWhere(['DATE_FORMAT(OH.created_at, "%Y-%m-%d")' => ':current_date'])  : $initQuery;


        return $initQuery;
    }

    public function selectHdfRevisions($osh_hdf_id = false, $current_date = false)
    {
        $fields = [
            'OHR.id',
            'OHR.osh_hdf_id',
            'OHR.present_temperature',
            'OHR.local_places',
            'OHR.is_sick',
            'OHR.sick_description',
            'OHR.has_symptoms',
            'OHR.has_symptoms_desc',
            'OHR.symptoms_occurrence',
            'OHR.has_animal_contact',
            'OHR.animal_contact_desc',
            'OHR.is_with_frontliner',
            'OHR.is_in_pum_list',
            'DATE_FORMAT(OHR.created_at, "%b %d, %Y") as date_accomplished',
            'OHR.is_updated',
        ];

        $initQuery = $this->select($fields)
                          ->from('osh_hdf_revisions OHR')
                          ->where(['OHR.is_active' => ':is_active']);
 
        $initQuery = ($osh_hdf_id)      ? $initQuery->andWhere(['OHR.osh_hdf_id' => ':osh_hdf_id'])     : $initQuery;
        $initQuery = ($current_date)    ? $initQuery->andWhere(['DATE_FORMAT(OHR.created_at, "%Y-%m-%d")' => ':current_date'])   : $initQuery;


        return $initQuery;
    }

    public function selectHdfEditRequests($created_by = false)
    {
        $fields = [
            'HER.id',
            'HER.osh_hdf_id',
            'HER.reason',
            'HER.approved_by',
            'HER.status',
            'HER.approver_remarks',
            'HER.created_by',
        ];

        $initQuery = $this->select($fields)
                          ->from('osh_hdf_edit_requests HER')
                          ->where(['HER.is_active' => ':is_active']);

        $initQuery = ($created_by) ? $initQuery->andWhere(['HER.created_by' => ':created_by']) : $initQuery;

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

    public function selectOshSignatories()
    {
        $fields = [
            'U.id as user_id',
            'P.name as position',
            'D.name as department',
            'CONCAT(PI.fname, " ", PI.lname) as fullname'
        ];

        $leftJoins = [
            'personal_informations PI'      =>  'PI.id = U.personal_information_id',
            'employment_informations EI'    =>  'EI.personal_information_id = PI.id',
            'positions P'                   =>  'P.id = EI.position_id',
            'departments D'                 =>  'D.id = P.department_id'
        ];

        $initQuery = $this->select($fields)
                          ->from('users U')
                          ->leftJoin($leftJoins)
                          ->where(['U.is_active' => ':is_active'])
                          ->logicEx('AND')
                          ->orWhereLike(['D.name' => ':dept_name']);

        return $initQuery;
    }

    public function insertHdf($data = [])
    {
        $initQuery = $this->insert('osh_hdf', $data);

        return $initQuery;
    }

    public function insertHdfRevision($data = [])
    {
        $initQuery = $this->insert('osh_hdf_revisions', $data);

        return $initQuery;
    }

    public function updateHdf($id = '', $data = [])
    {
        $initQuery = $this->update('osh_hdf', $id, $data);

        return $initQuery;
    }
}