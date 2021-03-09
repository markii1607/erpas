<?php 
    namespace App\Model\Base;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class BaseQueryHandler extends QueryHandler { 

        /**
         * `selectDefaultSignatories` Query string that will select default signatories from `default_signatories`.
         * @param  boolean $id
         * @return string
         */
        public function selectDefaultSignatories($id = false)
        {
            $fields = [
                'DS.id',
                'DS.position_id',
                'P.name as position_name',
                'P.code as position_code',
                'P.id as position_id',
                'CONCAT(PI.fname, " ", PI.lname) as full_name',
                'U.id as user_id'
            ];

            $joins = [
                'default_signatory_sets DSS' => 'DSS.id = DS.default_signatory_set_id',
                'transaction_types TT'       => 'TT.default_signatory_set_id = DSS.id',
                'users U'                    => 'DS.user_id = U.id',
                'personal_informations PI'   => 'U.personal_information_id = PI.id',
                'employment_informations EI' => 'PI.id = EI.personal_information_id',
                'positions P'                => 'EI.position_id = P.id',
            ];

            $initQuery = $this->select($fields)
                              ->from('default_signatories DS')
                              ->join($joins)
                              ->where(['DS.is_active' => ':is_active', 'TT.code' => ':code']);

            $initQuery = ($id) ? $initQuery->andWhere(['DS.id' => ':id']) : $initQuery;

            return $initQuery;
        }
    }