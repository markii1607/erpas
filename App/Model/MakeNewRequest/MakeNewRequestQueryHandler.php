<?php
    namespace App\Model\MakeNewRequest;

    require_once('../../AbstractClass/QueryHandler.php');

    use App\AbstractClass\QueryHandler;

    class MakeNewRequestQueryHandler extends QueryHandler {

        /**
         * `selectNewNumber` Query string that will select from table `purchase_requisitions`.
         * @param  boolean $prs_no
         * @return string
         */
        public function selectNewNumber($prs_no = false)
        {
            $fields = array(
                'PR.id',
                'PR.prs_no'
            );

            $initQuery = $this->select($fields)
                              ->from('purchase_requisitions PR ORDER BY PR.id DESC LIMIT 0, 1');

            $initQuery = ($prs_no) ? $initQuery->andWhere(array('RT.prs_no' => ':prs_no')) : $initQuery;

            return $initQuery;
        }

        /**
         * selectSignatories from dave
         *
         * @param boolean $id
         * @return void
         */
        public function selectSignatories($id = false)
        {
            $fields = array(
                'SS.id',
                'SS.menu_id',
                'SS.signatories',
                'SS.no_of_signatory',
            );

            $initQuery = $this->select($fields)
                              ->from('signatory_sets SS')
                              ->where(array('SS.status' => ':status'));

            $initQuery = ($id) ? $initQuery->andWhere(array('SS.menu_id' => ':id')) : $initQuery;

            return $initQuery;
        }

        /**
         * selectEmployees from dave
         *
         * @return void
         */
        public function selectEmployees($id = false, $department_id = false)
        {
            $fields = array(
                'U.id',
                'EI.position_id',
                'P.department_id',
                'P.name as position_name',
                'D.charging',
                'D.name as department_name',
                'CONCAT(PI.lname,", ",PI.fname," ",PI.mname) as fullname',
            );

            $join = array(
                'employment_informations EI' => 'PI.id = EI.personal_information_id',
                'positions P' => 'EI.position_id = P.id',
                'departments D' => 'P.department_id = D.id',
                'users U' => 'U.personal_information_id = PI.id'
            );

            $initQuery = $this->select($fields)
                              ->from('personal_informations PI')
                              ->leftJoin($join)
                              ->where(array('PI.is_active' => ':status'));

            $initQuery = ($id)            ? $initQuery->andWhere(array('PI.id' => ':id'))                      : $initQuery;
            $initQuery = ($department_id) ? $initQuery->andWhere(array('P.department_id' => ':department_id')) : $initQuery;

            return $initQuery;
        }

        /**
         * `insertNewRequest` Query string that will insert from table `purchase_requisitions`.
         * @param  array  $data
         * @return string
         */
        public function insertNewRequest($data = array())
        {
            $initQuery = $this->insert('purchase_requisitions', $data);

            return $initQuery;
        }

        /**
         * `insertNewRequestMaterialPpe` from dave
         * @param  array  $data
         * @return string
         */
        public function insertNewRequestMaterialPpe($data = array())
        {
            $initQuery = $this->insert('prs_ppe_descriptions', $data);
            return $initQuery;
        }

        /**
         * `insertNewRequestMaterialSequence` from dave
         * @param  array  $data
         * @return string
         */
        public function insertNewRequestMaterialSequence($data = array())
        {
            $initQuery = $this->insert('prd_delivery_sequences', $data);
            return $initQuery;
        }

        /**
         * `updateRequest` from dave.
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function updateRequest($id = '', $data = array())
        {
            $initQuery = $this->update('purchase_requisitions', $id, $data);
            return $initQuery;
        }

        /**
         * `updateRequestData` from dave
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function updateRequestData($id = '', $data = array())
        {
            $initQuery = $this->update('prs_ppe_descriptions', $id, $data);
            return $initQuery;
        }
    }
