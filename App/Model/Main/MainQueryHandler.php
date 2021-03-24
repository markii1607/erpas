<?php 
    namespace App\Model\Main;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class MainQueryHandler extends QueryHandler { 

        /**
         * `selectUsers` Query string that will select from table `users`.
         * @param  boolean $id`
         * @return string
         */
        public function selectUsers($id = false)
        {
            $fields = [
                'U.id as user_id',
                'PI.fname',
                'PI.mname',
                'PI.lname',
                'EI.ho',
                'EI.fo',
                'EI.date_hired',
                'EI.position_id',
                'P.name as position_name',
                'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as full_name',
                'P.is_signatory',
            ];

            $joins = [
                'personal_informations PI'   => 'U.personal_information_id = PI.id',
                'employment_informations EI' => 'PI.id = EI.personal_information_id',
                'positions P'                => 'EI.position_id = P.id',
            ];

            $initQuery = $this->select($fields)
                              ->from('users U')
                              ->join($joins)
                              ->where(['U.is_active' => 1, 'PI.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['U.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectMenus` Fetching of parent menus of specific user.
         * @param  boolean $id
         * @param  boolean $userId
         * @return string
         */
        public function selectMenus($id = false, $userId = false)
        {
            $fields = [
                'DISTINCT(M.id) as id',
                'M.name',
                'M.level',
                'M.icon',
                'M.box_color',
                'M.link',
                'M.office'
            ];

            $initQuery = $this->select($fields)
                              ->from('menus M')
                              ->join(['user_accesses UA' => 'M.id = UA.menu_id'])
                              ->where(['UA.is_active' => ':is_active', 'M.is_active' => ':is_active', 'M.system' => 2]);

            $initQuery = ($userId) ? $initQuery->andWhere(['UA.user_id' => ':user_id']) : $initQuery;

            $initQuery = $initQuery->orderBy('M.id', 'asc');

            return $initQuery;
        }

        /**
         * `updateUser` Query string that will update specific department information from table `users`
         * @return string
         */
        public function updateUser($id = '', $data = array())
        {
            $initQuery = $this->update('users', $id, $data);

            return $initQuery;
        }

        //////////////////////////////////////////////For Employee Records////////////////////////////////

           /**
         * `selectPersonalInformations` Query string that will select from table `personal_informations`.
         * @param  boolean $id
         * @param  boolean $code
         * @return string
         */
        public function selectPersonalInformations($id = false, $user_id = false)
        {
            $fields = [
                'PI.id',
                'CONCAT(PI.lname, ", ", PI.fname, " ", PI.mname) as full_name',
                'PI.lname',
                'PI.fname',
                'PI.mname',
                'PI.sname',
                'PI.citizenship',
                'PI.sex',
                'DATE_FORMAT(PI.birthdate, "%m/%d/%Y") as birthdate',
                'PI.age',
                'PI.height',
                'PI.weight',
                'PI.religion',
                'PI.birthplace',
                'PI.civil_status',
                'PI.no_of_dependents',
                'PI.tel_no',
                'PI.mobile_no as mobile_number',
                'PI.email',
                'PI.address_condition',
                'PI.ps_region_id',
                'PI.ps_province_id',
                'PI.ps_city_id',
                'PI.ps_barangay_id',
                'PI.ps_house_no_street',
                'PI.ps_type',
                'R.name as ps_region_name',
                'PR.name as ps_province_name',
                'CM.name as ps_city_name',
                'BR.name as ps_barangay_name',
                'PI.pr_region_id',
                'PI.pr_province_id',
                'PI.pr_city_id',
                'PI.pr_barangay_id',
                'PI.pr_house_no_street',
                'PI.pr_type',
                'R.name as pr_region_name',
                'PR.name as pr_province_name',
                'CM.name as pr_city_name',
                'BR.name as pr_barangay_name',
                'PI.signature',
                'EI.employee_no',
                'EI.position_id',
                'DATE_FORMAT(EI.date_hired, "%m/%d/%Y") as date_hired',
                'EI.status',
                'P.name as position_name',
                'P.department_id',
                'D.name as department_name',
                'file_name as photo',
                'U.id as users_id'
            ];

            $joins = [
                'users U'                    => 'U.personal_information_id = PI.id',
                'employment_informations EI' => 'EI.personal_information_id = PI.id',
                'positions P'                => 'P.id = EI.position_id',
                'departments D'              => 'D.id = P.department_id',
            ];
            
            $leftJoins = [
                'pi_photos PP' => 'PI.id = PP.personal_information_id',
                'regions R'                  => 'PI.ps_region_id = R.id',
                'provinces PR'               => 'PR.id = PI.ps_province_id',
                'city_municipalities CM'     => 'PI.ps_city_id = CM.id',
                'barangays BR'               => 'BR.id = PI.ps_barangay_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('personal_informations PI')
                              ->join($joins)
                              ->leftJoin($leftJoins)
                              ->where(['PI.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['PI.id' => ':id']) : $initQuery;
            $initQuery = ($user_id) ? $initQuery->andWhere(['U.id' => ':user_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * 'selectEmploymentInformations' Query String that will select from table `employment_informations`.
         *
         * @param boolean $id
         * @return void
         */
        public function selectEmploymentInformations($id = false, $eiId = false)
        {
            $fields = [
                'EI.id',
                'EI.personal_information_id',
                'EI.position_id',
                'EI.head_id',
                'EI.employee_no',
                'DATE_FORMAT(EI.date_hired, "%m/%d/%Y") as date_hired',
                'EI.status',
                'EI.ho',
                'EI.fo',
                'EI.is_department_head',
                'P.name as position_name',
                'CONCAT(PIH.lname, ", ", PIH.fname, " ", PIH.mname) as head_name',
                'D.id = department_id',
                'D.name as department_name'
            ];

            $joins = [
                'positions P'    => 'P.id = EI.position_id',
                'personal_informations PIH' => 'EI.head_id = PIH.id',
                'departments D'               => 'D.id = P.department_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('employment_informations EI')
                              ->join($joins)
                              ->where(['EI.is_active' => ':is_active']);
                              
            $initQuery = ($id)   ? $initQuery->andWhere(['EI.id' => ':id']) : $initQuery;
            $initQuery = ($eiId) ? $initQuery->andWhere(['EI.personal_information_id' => ':personal_information_id']) : $initQuery;

            return $initQuery;
        }
    }