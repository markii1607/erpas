<?php 
    namespace App\Model\Common;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class SendQueryHandler extends QueryHandler { 

        /**
         * `selectPersonalInformations` Query string that will fetch `personal_informations`.
         * @param  boolean $id
         * @param  boolean $isSignatory
         * @return string
         */
        public function selectPersonalInformations($id = false, $isSignatory = false)
        {
            $fields = [
                'PI.id',
                'EI.employee_no',
                'CONCAT(PI.fname, " ", PI.lname) as full_name',
                'P.name as position_name',
                'P.id as position_id',
                'U.id as user_id'
            ];

            $joins = [
                'users U'                    => 'U.personal_information_id = PI.id',
                'employment_informations EI' => 'PI.id = EI.personal_information_id',
                'positions P'                => 'EI.position_id = P.id',
            ];

            $initQuery = $this->select($fields)
                              ->from('personal_informations PI')
                              ->join($joins)
                              ->where(['PI.is_active' => ':is_active']);

            $initQuery = ($id)          ? $initQuery->andWhere(['PI.id' => ':id'])                    : $initQuery;
            $initQuery = ($isSignatory) ? $initQuery->andWhere(['P.is_signatory' => ':is_signatory']) : $initQuery;

            $initQuery = $initQuery->orderBy('PI.lname', 'asc');

            return $initQuery;
        }
    }