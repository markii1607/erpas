<?php 
    namespace App\Model\ActualSurveyForm;

    include_once "..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class ActualSurveyFormQueryHandler extends QueryHandler {
        /**
         * `selectUsers` Query String that will select from table `users` join `employees` and `positions`
         * @return string
         */
        public function selectUsers()
        {
            $fields = [
                'E.id',
                'CONCAT(E.fname," ",E.mname," ",E.lname) as fullname'
            ];

            $joins = [
                'employees E' => 'U.employee_id = E.id',
                'positions P' => 'E.position_id = P.id'
            ];

            $orOneFieldConditions = [
                8,
                17,
                18,
                19,
                20
            ];

            $initQuery = $this->select($fields)
                              ->from('users U')
                              ->join($joins)
                              ->whereOrOneField('P.id', $orOneFieldConditions);

            return $initQuery;
        }

        /**
         * `selectWorkOrders` Query string that will select from table `admin_work_orders`.
         * @param  boolean $id
         * @return string
         */
        public function selectWorkOrders($id = false)
        {
          $fields = [
            'AWO.name',
            'AWO.location',
            'AWO.updated_by',
            'CONCAT(E.fname, " ", E.mname, " ",E.lname) as project_manager_name'
          ];

          $joins = [
            'users U'     => 'U.id = AWO.updated_by',
            'employees E' => 'E.id = U.employee_id'
          ];

          $initQuery = $this->select($fields)
                    ->from('admin_work_orders AWO')
                    ->join($joins);

          $initQuery = ($id) ? $initQuery->where(['AWO.id' => ':id']) : $initQuery;

          return $initQuery;
        }

        /**
         * `selectSwWis` Query string that will fetch from table `sw_wis`
         * @param  boolean $id
         * @return string
         */
        public function selectSwWis($id = false)
        {
            $fields = [
                'SW.id',
                'SW.cost_code',
                'SW.unit',
                'WI.name',
                'WI.item_no',
                'SWC.id as spt_wic_id',
                'SWC.cost_code as swc_cost_code',
                'WIC.name as wic_name',
                'SPT.id as spt_id',
                'SPT.name as spt_name',
                'SP.id as sp_id',
                'SP.name as sp_name',
                'SP.cost_code as sp_cost_code',
                'WD.id as wd_id',
                'WD.name as wd_name',
                'WD.cost_code as wd_cost_code'
            ];

            $joins = [
                'work_items WI'            => 'SW.work_item_id = WI.id',
                'spt_wics SWC'             => 'SW.spt_wic_id = SWC.id',
                'work_item_categories WIC' => 'SWC.work_item_category_id = WIC.id',
                'sub_project_types SPT'    => 'SPT.id = SWC.sub_project_type_id',
                'sub_projects SP'          => 'SP.id = SPT.sub_project_id',
                'work_disciplines WD'      => 'WD.id = SP.work_discipline_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('sw_wis SW')
                              ->join($joins)
                              ->where(['SW.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['SW.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectSubProjectTypes` Query strong that will select from table `sub_project_types`.
         * @param  boolean $id
         * @return string
         */
        public function selectSubProjectTypes($id = false)
        {
            $fields = [
                'SPT.id',
                'SPT.name',
                'SP.id as sp_id',
                'SP.name as sp_name'
            ];

            $initQuery = $this->select($fields)
                              ->from('sub_project_types SPT')
                              ->join(['sub_projects SP' => 'SP.id = SPT.sub_project_id'])
                              ->where(['SPT.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['SPT.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `insertActualSurveyTypeList` Query string that will insert to table `actual_survey_type_lists`
         * @return string
         */
        public function insertActualSurveyTypeList($data = [])
        {
            $initQuery = $this->insert('actual_survey_type_lists', $data);

            return $initQuery;
        }

        /**
         * `insertActualSurvey` Query string that will insert to table `actual_surveys`
         * @return string
         */
        public function insertActualSurvey($data = [])
        {
            $initQuery = $this->insert('actual_surveys', $data);

            return $initQuery;
        }

        /**
         * `insertActualSurveyScopeOfWork` Query string that will insert to table `actual_survey_scope_of_works`
         * @return string
         */
        public function insertActualSurveyScopeOfWork($data = [])
        {
            $initQuery = $this->insert('actual_survey_scope_of_works', $data);

            return $initQuery;
        }

        /**
         * `insertAsScopeOfWork` Query string that will insert to table `as_scope_of_works`
         * @return string
         */
        public function insertAsScopeOfWork($data = [])
        {
            $initQuery = $this->insert('as_scope_of_works', $data);

            return $initQuery;
        }

        /**
         * `insertAsWorkDiscipline` Query string that will insert to table `as_work_disciplines`
         * @return string
         */
        public function insertAsWorkDiscipline($data = [])
        {
            $initQuery = $this->insert('as_work_disciplines', $data);

            return $initQuery;
        }

        /**
         * `insertAsSubProjectType` Query string that will insert to table `as_sub_project_types`
         * @return string
         */
        public function insertAsSubProjectType($data = [])
        {
            $initQuery = $this->insert('as_sub_project_types', $data);

            return $initQuery;
        }

        /**
         * `updateAdminWorkOrder` Query string that will update specific work order information from table `admin_work_orders`
         * @return string
         */
        public function updateAdminWorkOrder($id = '', $data = [])
        {
            $initQuery = $this->update('admin_work_orders', $id, $data);

            return $initQuery;
        }
    }