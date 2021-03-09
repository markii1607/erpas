<?php 
    namespace App\Model\PreviewActualSurvey;

    include_once "..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class PreviewActualSurveyQueryHandler extends QueryHandler {
        /**
         * `selectActualSurveys` Query String that will select from table `actual_surveys`
         * @return string
         */
        public function selectActualSurveys($id = false)
        {
            $fields = [
                'AC.id',
                'AC.temp_code',
                'AC.name as project_name',
                'AC.location as project_location',
                'AC.project_manager',
                'AC.is_active',
                'AC.is_revision',
                'AC.transaction_id',
                'AC.created_by',
                'AC.updated_by',
                'DATE_FORMAT(AC.updated_at, "%m/%d/%Y") as date_revised',
                'T.status as transaction_status',
                'CONCAT(E.fname," ",E.mname," ",E.lname) as project_manager_name'
            ];

            $leftJoins = [
                'users U'        => 'AC.project_manager = U.id',
                'employees E'    => 'U.employee_id = E.id',
                'transactions T' => 'T.id = AC.transaction_id'
            ];


            $initQuery = $this->select($fields)
                              ->from('actual_surveys AC')
                              ->leftJoin($leftJoins)
                              ->where(['AC.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['AC.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectAsWorkDisciplines` Query string that will fetch from table `as_work_disciplines`.
         * @param  boolean $id
         * @param  boolean $actualSurveyId
         * @return string
         */
        public function selectAsWorkDisciplines($id = false, $actualSurveyId = false)
        {
            $fields = [
                'AWD.id',
                'AWD.actual_survey_id',
                'WD.name'
            ];

            $initQuery = $this->select($fields)
                              ->from('as_work_disciplines AWD')
                              ->join(['work_disciplines WD' => 'WD.id = AWD.work_discipline_id'])
                              ->where(['AWD.is_active' => ':is_active']);

            $initQuery = ($id)             ? $initQuery->andWhere(['AWD.id' => ':id'])                             : $initQuery;
            $initQuery = ($actualSurveyId) ? $initQuery->andWhere(['AWD.actual_survey_id' => ':actual_survey_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectAsSubProjectTypes` Query string that will fetch from table `as_sub_project_types`.
         * @param  boolean $id
         * @param  boolean $asWorkDisciplineId
         * @return string
         */
        public function selectAsSubProjectTypes($id = false, $asWorkDisciplineId = false)
        {
            $fields = [
                'ASPT.id',
                'ASPT.as_work_discipline_id',
                'ASPT.sub_project_type_id',
                'ASPT.name',
                'ASPT.location',
                'SPT.name as sub_project_type_name',
                'SP.name as sub_project_name'
            ];

            $joins = [
                'sub_project_types SPT' => 'SPT.id = ASPT.sub_project_type_id',
                'sub_projects SP'       => 'SP.id = SPT.sub_project_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('as_sub_project_types ASPT')
                              ->join($joins)
                              ->where(['ASPT.is_active' => ':is_active']);

            $initQuery = ($id)                 ? $initQuery->andWhere(['ASPT.id' => ':id']) : $initQuery;
            $initQuery = ($asWorkDisciplineId) ? $initQuery->andWhere(['ASPT.as_work_discipline_id' => ':as_work_discipline_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectAsScopeOfWorks` Query string that will select from table `as_scope_of_works`.
         * @param  boolean $id
         * @param  boolean $asSubProjectTypeId
         * @return string
         */
        public function selectAsScopeOfWorks($id = false, $asSubProjectTypeId = false)
        {
            $fields = [
                'ASOW.id',
                'ASOW.quantities',
                'SWI.cost_code',
                'SWI.unit',
                'SWIC.work_item_category_id',
                'WIC.name as work_item_category_name',
                'SWIC.cost_code as swic_cost_code',
                'WI.item_no',
                'WI.name'
            ];

            $joins = [
                'sw_wis SWI'               => 'SWI.id = ASOW.sw_wi_id',
                'spt_wics SWIC'            => 'SWIC.id = SWI.spt_wic_id',
                'work_item_categories WIC' => 'WIC.id = SWIC.work_item_category_id',
                'work_items WI'            => 'SWI.work_item_id = WI.id',
            ];

            $initQuery = $this->select($fields)
                              ->from('as_scope_of_works ASOW')
                              ->join($joins)
                              ->where(['ASOW.is_active' => ':is_active']);

            $initQuery = ($id)                 ? $initQuery->andWhere(['ASOW.id' => ':id']) : $initQuery;
            $initQuery = ($asSubProjectTypeId) ? $initQuery->andWhere(['ASOW.as_sub_project_type_id' => ':as_sub_project_type_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectActualSurveyTypeLists` Query s tring that will select from table `actual_survey_type_lists`.
         * @param  boolean $id
         * @param  boolean $actualSurveyId
         * @return string
         */
        public function selectActualSurveyTypeLists($id = false, $actualSurveyId = false)
        {
            $fields = [
                'ASTL.id',
                'ASTL.name',
                'ASTL.project_type_id',
                'PT.name as project_type_name'
            ];

            $initQuery = $this->select($fields)
                              ->from('actual_survey_type_lists ASTL')
                              ->join(['project_types PT' => 'PT.id = ASTL.project_type_id'])
                              ->where(['ASTL.is_active' => 1]);

            $initQuery = ($id)             ? $initQuery->andWhere(['ASTL.id' => ':id'])                		: $initQuery;
            $initQuery = ($actualSurveyId) ? $initQuery->andWhere(['ASTL.actual_survey_id' => ':actual_survey_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectActualSurveyScopeOfWorks` Query string that will select from table `actual_survey_scope_of_works`.
         * @param  boolean $id
         * @param  boolean $actualSurveyTypeListId
         * @return string
         */
        public function selectActualSurveyScopeOfWorks($id = false, $actualSurveyTypeListId = false)
        {
            $fields = [
                'ASSOW.id',
                'ASSOW.quantities as quantity',
                'WI.id as work_item_id',
                'WI.item_no',
                'WI.name',
                'WI.unit',
                'WI.work_item_category_id',
                'WI.cost_code',
                'WIC.name as work_item_category_name',
                'WIC.cost_code as wic_cost_code'
            ];

            $joins = [
                'work_items WI'            => 'WI.id = ASSOW.work_item_id',
                'work_item_categories WIC' => 'WIC.id = WI.work_item_category_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('actual_survey_scope_of_works ASSOW')
                              ->join($joins)
                              ->where(['ASSOW.is_active' => 1]);

            $initQuery = ($id) ? $initQuery->andWhere(['ASSOW.id' => ':id']) : $initQuery;
            $initQuery = ($actualSurveyTypeListId) ? $initQuery->andWhere(['ASSOW.actual_survey_type_list_id' => ':actual_survey_type_list_id']) : $initQuery;
        
            return $initQuery;
        }

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
         * `selectWorkItems` Query string that will select from table `work_items`
         * @param  boolean $id
         * @return string
         */
        public function selectWorkItems($id = false, $workItemCategoryId = false, $direct = false)
        {
            $fields = [
                'WI.id as work_item_id',
                'WI.cost_code',
                'WI.item_no',
                'WI.name',
                'WI.unit'
            ];

            $initQuery = $this->select($fields)
                              ->from('work_items WI')
                              ->join(['work_item_categories WIC' => 'WIC.id = WI.work_item_category_id'])
                              ->where(['WI.status' => ':status']);

            $initQuery = ($id)                 ? $initQuery->andWhere(['WI.id' => ':id'])                                       : $initQuery;
            $initQuery = ($workItemCategoryId) ? $initQuery->andWhere(['WI.work_item_category_id' => ':work_item_category_id']) : $initQuery;
            $initQuery = ($direct)             ? $initQuery->andWhere(['WI.direct' => 1])                                       : $initQuery->andWhere(['WI.direct' => 0]);
        
            return $initQuery;
        }

        /**
         * `selectProjectTypes` Query string that will select from table `project_types`.
         * @param  boolean $id
         * @return string
         */
        public function selectProjectTypes($id = false)
        {
            $fields = [
                'PT.id',
                'PT.name'
            ];

            $initQuery = $this->select($fields)
                              ->from('project_types PT');

            $initQuery = ($id) ? $initQuery->where(['PT.id' => ':id']) : $initQuery;

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
         * `insertActualSurveyTypeList` Query string that will insert to table `actual_survey_type_lists`
         * @return string
         */
        public function insertActualSurveyTypeList($data = [])
        {
            $initQuery = $this->insert('actual_survey_type_lists', $data);

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
         * `insertActualSurveyTransaction` Query string that will insert to table `actual_survey_transactions`
         * @return string
         */
        public function insertActualSurveyTransaction($data = [])
        {
            $initQuery = $this->insert('actual_survey_transactions', $data);

            return $initQuery;
        }
        
        /**
         * `updateActualSurvey` Query string that will update specific actual survey from table `actual_surveys`
         * @return string
         */
        public function updateActualSurvey($id = '', $data = [], $fk = '', $fkValue = '')
        {
            $initQuery = $this->update('actual_surveys', $id, $data);

            return $initQuery;
        }

        /**
         * `updateActualSurveyTypeList` Query string that will update specific actual survey type list from table `actual_survey_type_lists`
         * @return string
         */
        public function updateActualSurveyTypeList($id = '', $data = [])
        {
            $initQuery = $this->update('actual_survey_type_lists', $id, $data);

            return $initQuery;
        }

        /**
         * `updateActualSurveyScopeOfWork` Query string that will update specific actual survey scope of work information from table `actual_survey_scope_of_works`
         * @return string
         */
        public function updateActualSurveyScopeOfWork($id = '', $data = [], $fk = '', $fkValue = '')
        {
            $initQuery = $this->update('actual_survey_scope_of_works', $id, $data, $fk, $fkValue);

            return $initQuery;
        }
    }