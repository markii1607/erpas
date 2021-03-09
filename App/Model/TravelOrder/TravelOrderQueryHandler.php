<?php
    namespace App\Model\TravelOrder;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class TravelOrderQueryHandler extends QueryHandler {
        /**
         * `selectToAr` Query string that will fetch travel_order.
         * @return string
         */
        // public function selectToAr($id = false, $name = false)
        // {
        //     $fields = [
        //         'T.id',
        //         'T.to_no',
        //         'DATE_FORMAT(T.date_filed, "%m/%d/%Y") as date_filed',
        //         'DATE_FORMAT(TA.date, "%m/%d/%Y") as date',
        //         'TA.project_id',
        //         'TA.purpose',
        //         'TA.time_arrival',
        //         'TA.time_departure',
        //         'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as full_name',
        //         'P.name as project_name',
        //         '"" as remarks'

        //     ];

        //     $joins = [
        //         'personal_informations PI' => 'PI.id = T.personal_information_id',
        //         'to_destinations TA' => 'TA.travel_order_id = T.id',
        //         'projects P' => 'P.id = TA.project_id'

        //     ];

        //     $initQuery = $this->select($fields)
        //                       ->from('travel_orders T')
        //                       ->join($joins)
        //                       ->where(['T.is_active' => ':is_active']);

        //     $initQuery = ($id)   ? $initQuery->andWhere(['T.id' => ':id'])         : $initQuery;
        //     $initQuery = ($name) ? $initQuery->andWhereLike(['T.name' => ':name']) : $initQuery;

        //     return $initQuery;
        // }

        /**
         * `selectTravelOrders` Query string that will select from table `travel_orders`.
         * @param  boolean $id
         * @return string
         */
        public function selectTravelOrders($id = false)
        {
            $fields = [
                'T.id',
                'T.to_no',
                'T.department_id',
                'T.project_id',
                'IF(T.department_id IS NULL, "P", "D") as charging_type',
                'IF(T.department_id IS NULL, T.project_id, T.department_id) as charging_id',
                'T.type',
                'T.status',
                'DATE_FORMAT(T.date_filed, "%m/%d/%Y") as date_filed',
                // 'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as full_name',
                // 'TD.id as td_id',
                // 'TD.destination',
                // 'TD.purpose',
                // 'DATE_FORMAT(TD.time_arrival, "%h:%i %p") as time_arrival',
                // 'DATE_FORMAT(TD.time_departure, "%h:%i %p") as time_departure',
                // // 'TD.time_departure',
                // 'DATE_FORMAT(TD.date_destination, "%m/%d/%Y") as date_destination',
                // 'DATE_FORMAT(TD.start_date, "%m/%d/%Y") as start_date',
                // 'DATE_FORMAT(TD.end_date, "%m/%d/%Y") as end_date',
                '"" AS remarks'
            ];

            $leftjoins = [
                'projects P'    => 'P.id = T.project_id',
                'departments D' => 'T.department_id = D.id',
                // 'to_destinations TD'       => 'TD.travel_order_id = T.id',
                // 'to_personnels TP'         => 'T.id = TP.travel_order_id',
                // 'personal_informations PI' => 'TP.personal_information_id = PI.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('travel_orders T')
                              ->leftJoin($leftjoins)
                              ->where(['T.is_active' => ':is_active', 'T.created_by' => ':created_by']);

            $initQuery = ($id) ? $initQuery->andWhere(['T.id' => ':id']) : $initQuery;

            return $initQuery;
        }

         /**
         * `selectToDestination` Query String that will select from table `to_destinations`
         * @return string
         */
        public function selectToDestination($toId = false)
        {
            $fields = [
                'TD.id',
                'TD.travel_order_id',
                'DATE_FORMAT(TD.date_destination, "%m/%d/%Y") as date_destination',
                'DATE_FORMAT(TD.start_date, "%m/%d/%Y") as start_date',
                'DATE_FORMAT(TD.end_date, "%m/%d/%Y") as end_date',
                'TD.location',
                'TD.purpose',
                // 'TD.time_arrival',
                // 'TD.time_departure',
                'DATE_FORMAT(TD.time_arrival, "%h:%i %p") as time_arrival',
                'DATE_FORMAT(TD.time_departure, "%h:%i %p") as time_departure',
            ];

            $initQuery = $this->select($fields)
                              ->from('to_destinations TD')
                              ->where(['TD.is_active' => ':is_active']);

            // $initQuery = ($id)   ? $initQuery->andWhere(['TD.id' => ':id'])                           : $initQuery;
            $initQuery = ($toId) ? $initQuery->andWhere(['TD.travel_order_id' => ':travel_order_id']) : $initQuery;

            return $initQuery;
        }

         /**
         * `selectToDestination` Query String that will select from table `to_destinations`
         * @return string
         */
        public function selectToEmployee($emId = false)
        {
            $fields = [
                'TE.id',
                'TE.travel_order_id',
                'TE.personal_information_id',
                'CONCAT(PI.lname, ", ", PI.fname, " ", PI.mname) as full_name',
                'EI.employee_no',
                'P.name as position_name',
                'D.name as department_name',
                'P.id as position_id'
            ];

            $joins = array(
                'personal_informations PI'      =>      'PI.id = TE.personal_information_id',
                'employment_informations EI'    =>      'EI.personal_information_id = PI.id',
                'positions P'                   =>      'P.id = EI.position_id',
                'departments D'                 =>      'D.id = P.department_id'
            );

            $initQuery = $this->select($fields)
                              ->from('to_personnels TE')
                              ->leftJoin($joins)
                              ->where(['TE.is_active' => ':is_active']);

            // $initQuery = ($id)   ? $initQuery->andWhere(['TE.id' => ':id'])                           : $initQuery;
            $initQuery = ($emId) ? $initQuery->andWhere(['TE.travel_order_id' => ':travel_order_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectProjects` Query string that will select from table `projects`
         * @return string
         */
        public function selectProjects()
        {
            $fields = [
                'P.id',
                'P.project_code as charging',
                'P.name',
                '"P" as pd_type'
            ];

            $initQuery = $this->select($fields)
                              ->from('projects P')
                              ->where(['P.is_active' => ':is_active']);

            return $initQuery;
        }

         /**
         * `selectDepartments` Query String that will select from table `departments`
         * @return string
         */
        public function selectDepartments($id = false)
        {
            $fields = [
                'D.id',
                'D.charging',
                'D.name',
                '"D" as pd_type'
            ];

            $initQuery = $this->select($fields)
                              ->from('departments D')
                              ->where(['D.is_active' => ':is_active']);

            return $initQuery;
        }

         /**
         * Undocumented function
         *
         * @param boolean $to_id
         * @return void
         */
        public function selectRqSignatories($to_id = false)
        {
            $fields = array(
                'TS.id',
                'TS.to_id',
                'TS.signatory_id',
                'TS.seq',
                'TS.is_approved',
                'IF(TS.remarks IS NULL, "", TS.remarks) as remarks',
                'DATE_FORMAT(TS.updated_at, "%M %d, %Y %h:%i:%s %p") as date_approved',
                'CONCAT(PI.lname, ", ", PI.fname, " ", PI.mname) as full_name',
                'EI.employee_no',
                'P.name as position_name',
                'D.name as department_name',
                'P.id as position_id'
            );

            $joins = array(
                'users U'                       =>      'U.id = TS.signatory_id',
                'personal_informations PI'      =>      'PI.id = U.personal_information_id',
                'employment_informations EI'    =>      'EI.personal_information_id = PI.id',
                'positions P'                   =>      'P.id = EI.position_id',
                'departments D'                 =>      'D.id = P.department_id'
            );

            $initQuery = $this->select($fields)
                              ->from('to_signatories TS')
                              ->leftJoin($joins)
                              ->where(array('TS.is_active' => ':is_active'));

            $initQuery = ($to_id) ? $initQuery->andWhere(array('TS.to_id' => ':to_id')) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectPersonalInformations` Query string that will from table `personal_informations`.
         * @param  string $id
         * @return string
         */
        public function selectPersonalInformations($id = '')
        {
            $fields = [
                'PI.id',
                'PI.fname',
                'PI.mname',
                'PI.lname',
                'PI.sname',
                'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as full_name',
                'EI.id as ei_id',
                'EI.employee_no',
                'P.id as position_id',
                'P.name as position_name',
                'D.name as department_name',
                'D.id as department_id'
            ];

            $joins = [
                'employment_informations EI' => 'PI.id = EI.personal_information_id',
                'positions P'                => 'EI.position_id = P.id',
                'departments D'              => 'P.department_id = D.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('personal_informations PI')
                              ->join($joins)
                              ->where(['P.is_active' => ':is_active']);

            return $initQuery;
        }

        public function getRqSignatories($requisition_id = '')
        {
            $rqIdStatus = empty($requisition_id) ? false : true;

            $data = array(
                'is_active' => 1
            );

            ($rqIdStatus) ? $data['requisition_id'] = $requisition_id : '';
            // print_r($data);

            // die();

            $requisition_signatories = $this->dbCon->prepare($this->queryHandler->selectRqSignatories($rqIdStatus)->orderBy('RS.seq', 'ASC')->end());
            $requisition_signatories->execute($data);

            return $requisition_signatories->fetchAll(\PDO::FETCH_ASSOC);
        }


        /**
         * `insertTravelOrder` Query string that will insert to table `travel_order`
         * @return string
         */
        public function insertTravelOrder($data = [])
        {
            $initQuery = $this->insert('travel_orders', $data);

            return $initQuery;
        }

        /**
         * `insertToDestination` Query string that will insert to table `to_destinations`
         * @return string
         */
        public function insertToDestination($data = [])
        {
            $initQuery = $this->insert('to_destinations', $data);

            return $initQuery;
        }

        /**
         * `insertToPersonnel` Query string that will insert to table `to_personnels`
         * @return string
         */
        public function insertToPersonnel($data = [])
        {
            $initQuery = $this->insert('to_personnels', $data);

            return $initQuery;
        }

        public function insertRqSignatories($data = [])
        {
            $initQuery = $this->insert('to_signatories', $data);

            return $initQuery;
        }

        /**
         * `updateTravelOrder` Query string that will update specific travel order information from table `travel_orders`
         * @return string
         */
        public function updateToAr($id = '', $data = [])
        {
            $initQuery = $this->update('travel_orders', $id, $data);

            return $initQuery;
        }

        /**
         * `updateTravelOrder` Query string that will update specific travel order information from table `travel_orders`
         * @return string
         */
        public function updateDestination($id = '', $data = [])
        {
            $initQuery = $this->update('to_destinations', $id, $data);

            return $initQuery;
        }

        public function deleteToDestination($id = false)
        {
            $initQuery = $this->delete('to_destinations')
                              ->where(['travel_order_id' => ':travel_order_id']);

            return $initQuery;
        }
    }