<?php 
    namespace App\Model\PiFiles;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class PiFilesQueryHandler extends QueryHandler { 

        /**
         * `selectPersonalInformations` Query string that will select from table `personal_informations`.
         * @param  boolean $id
         * @param  boolean $code
         * @return string
         */
        public function selectPersonalInformations($id = false, $code = false)
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
                'PP.file_name as photo'
            ];

            $joins = [
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
                              ->leftJoin($joins)
                              ->leftJoin($leftJoins)
                              ->where(['PI.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['PI.id' => ':id']) : $initQuery;

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

        /**
         * 'selectFamilyBackgrounds' Query String that will select from table `family_backgrounds`.
         *
         * @param boolean $id
         * @return void
         */
        public function selectFamilyBackgrounds($id = false, $fbId = false)
        {
            $fields = [
                'FB.id',
                'FB.personal_information_id',
                'FB.fathers_name',
                'FB.fathers_age',
                'FB.mothers_name',
                'FB.mothers_age'
            ];

            $initQuery = $this->select($fields)
                             ->from('family_backgrounds FB')
                             ->where(['FB.is_active' => ':is_active']);
            
            $initQuery = ($id)   ? $initQuery->andWhere(['FB.id' => ':id']) : $initQuery;
            $initQuery = ($fbId) ? $initQuery->andWhere(['FB.personal_information_id' => ':personal_information_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * 'selectEducationalBackgrounds' Query String that will select from table `educational_backgrounds`.
         *
         * @param boolean $id
         * @return void
         */
        public function selectEducationalBackgrounds($id = false, $piId = false)
        {
            $fields = [
                'EB.id',
                'EB.personal_information_id',
                'EB.attainment_level_id',
                'EB.school_id',
                'EB.course as course_name',
                'DATE_FORMAT(EB.date_graduated, "%m/%d/%Y") as date_graduated',
                'EB.honors',
                'EB.file',
                'AL.name as attainment_level_name',
                'SC.name as school_name',
            ];

            $joins = [
                'attainment_levels AL'  => 'EB.attainment_level_id = AL.id',
                'schools SC'            => 'EB.school_id = SC.id',

            ];

            $initQuery = $this->select($fields)
                              ->from('educational_backgrounds EB')
                              ->join($joins)
                              ->where(['EB.is_active' => ':is_active']);

            $initQuery = ($id)   ? $initQuery->andWhere(['EB.id' => ':id']) : $initQuery;
            $initQuery = ($piId) ? $initQuery->andWhere(['EB.personal_information_id' => ':personal_information_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * 'selectLicenseCertificates' Query String that will select from table `license_certificates`.
         *
         * @param boolean $id
         * @return void
         */
        public function selectLicenseCertificates($id = false, $lcId = false)
        {
            $fields = [
                'LC.id',
                'LC.personal_information_id',
                'LC.name',
                'DATE_FORMAT(LC.date_taken, "%m/%d/%Y") as date_taken',
                'LC.rating',
                'LC.file'
            ];

            $initQuery = $this->select($fields)
                              ->from('license_certificates LC')
                              ->where(['LC.is_active' => ':is_active']);
            
            $initQuery = ($id) ? $initQuery->andWhere(['LC.id' => ':id']) : $initQuery;
            $initQuery = ($lcId) ? $initQuery->andWhere(['LC.personal_information_id' => ':personal_information_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * 'selectOtherDetails' Query String that will select from table `other_details`.
         *
         * @param boolean $id
         * @return void
         */
        public function selectOtherDetails($id = false, $odId = false)
        {
            $fields = [
                'OD.id',
                'OD.personal_information_id',
                'OD.sss_no',
                'OD.pagibig_no',
                'OD.tin',
                'OD.philhealth_no',
                'OD.contact_person',
                'OD.cp_number',
                'OD.cp_address'
            ];

            $initQuery = $this->select($fields)
                              ->from('other_details OD')
                              ->where(['OD.is_active' => ':is_active']);
            
            $initQuery = ($id) ? $initQuery->andWhere(['OD.id' => ':id']) : $initQuery;
            $initQuery = ($odId) ? $initQuery->andWhere(['OD.personal_information_id' => ':personal_information_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * 'selectEmploymentHistories' Query String that will select from table `employment_histories`.
         *
         * @param boolean $id
         * @return void
         */
        public function selectEmploymentHistories($id = false, $ehId = false)
        {
            $fields = [
                'EH.id',
                'EH.personal_information_id',
                'EH.department as department_name',
                'EH.position as position_name',
                'EH.salary as salary_range',
                'DATE_FORMAT(EH.from_date, "%m/%d/%Y") as from_date',
                'DATE_FORMAT(EH.to_date, "%m/%d/%Y") as to_date',
                'EH.file',
            ];

            $initQuery = $this->select($fields)
                              ->from('employment_histories EH')
                              ->where(['EH.is_active' => ':is_active']);
            
            $initQuery = ($id) ? $initQuery->andWhere(['EH.id' => ':id']) : $initQuery;
            $initQuery = ($ehId) ? $initQuery->andWhere(['EH.personal_information_id' => ':personal_information_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * 'selectTrainingSeminars' Query String that will select from table `training_seminars`.
         *
         * @param boolean $id
         * @return void
         */
        public function selectTrainingSeminars($id = false, $tsId = false)
        {
            $fields = [
                'TS.id',
                'TS.personal_information_id',
                'TS.topic',
                'TS.organizer',
                'DATE_FORMAT(TS.from_date, "%m/%d/%Y") as from_date',
                'DATE_FORMAT(TS.to_date, "%m/%d/%Y") as to_date',
                'TS.file',
            ];

            $initQuery = $this->select($fields)
                              ->from('training_seminars TS')
                              ->where(['TS.is_active' => ':is_active']);
            
            $initQuery = ($id) ? $initQuery->andWhere(['TS.id' => ':id']) : $initQuery;
            $initQuery = ($tsId) ? $initQuery->andWhere(['TS.personal_information_id' => ':personal_information_id']) : $initQuery;

            return $initQuery;

        }

        /**
         * 'selectSpouses' Query String that will select from table `training_seminars`.
         *
         * @param boolean $id
         * @return void
         */
        public function selectSpouses($id = false, $spId = false)
        {
            $fields = [
                'SP.id',
                'SP.personal_information_id',
                'SP.name as spouse_name',
                'SP.business_address',
                '(SELECT COUNT(SC.id) AS ch_count FROM spouse_childrens SC WHERE SC.spouse_id = SP.id) as ch_count'
            ];

            $initQuery = $this->select($fields)
                              ->from('spouses SP')
                              ->where(['SP.is_active' => ':is_active']);
            
            $initQuery = ($id) ? $initQuery->andWhere(['SP.id' => ':id']) : $initQuery;
            $initQuery = ($spId) ? $initQuery->andWhere(['SP.personal_information_id' => ':personal_information_id']) : $initQuery;

            return $initQuery;

        }

        /**
         * 'selectSpouses' Query String that will select from table `training_seminars`.
         *
         * @param boolean $id
         * @return void
         */
        public function selectSpouseChildrens($id = false, $scId = false)
        {
            $fields = [
                'SC.id',
                'SC.spouse_id',
                'SC.name as ch_name',
                'SC.age as ch_age'
            ];

            $initQuery = $this->select($fields)
                              ->from('spouse_childrens SC')
                              ->where(['SC.is_active' => ':is_active']);
            
            $initQuery = ($id) ? $initQuery->andWhere(['SC.id' => ':id']) : $initQuery;
            $initQuery = ($scId) ? $initQuery->andWhere(['SC.spouse_id' => ':spouse_id']) : $initQuery;

            return $initQuery;

        }

        /**
         * `selectRegions` Query String that will select from table `regions`
         * @return string
         */
        public function selectRegions($id = false, $code = false)
        {
            $fields = [
                'R.id',
                'R.psgc_code',
                'R.name',
                'R.code'
            ];

            $initQuery = $this->select($fields)
                              ->from('regions R');
            
            $initQuery = ($id) ? $initQuery->where(array('R.id' => ':id')) : $initQuery;
            $initQuery = ($code) ? $initQuery->where(array('R.code' => ':code')) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectProvinces` Query String that will select from table `provinces`
         * @return string
         */
        public function selectProvinces($id = false, $code = false)
        {
            $fields = [
                'P.id',
                'P.psgc_code',
                'P.name',
                'P.region_code',
                'P.code'
            ];

            $initQuery = $this->select($fields)
                              ->from('provinces P');                              
            
            $initQuery = ($id) ? $initQuery->where(array('P.id' => ':id')) : $initQuery;
            $initQuery = ($code) ? $initQuery->where(array('P.region_code' => ':code')) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectCities` Query String that will select from table `cities`
         * @return string
         */
        public function selectCities($id = false, $code = false)
        {
            $fields = [
                'CM.id',
                'CM.psgc_code',
                'CM.name',
                'CM.region_code',
                'CM.province_code',
                'CM.code'
            ];

            $initQuery = $this->select($fields)
                              ->from('city_municipalities CM');
                              
            $initQuery = ($id) ? $initQuery->where(array('CM.id' => ':id')) : $initQuery;
            $initQuery = ($code) ? $initQuery->where(array('CM.province_code' => ':code')) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectBarangays` Query String that will select from table `barangays`
         * @return string
         */
        public function selectBarangays($id = false, $code = false)
        {
            $fields = [
                'B.id',
                'B.psgc_code',
                'B.name',
                'B.region_code',
                'B.province_code',
                'B.city_municipality_code'
            ];

            $initQuery = $this->select($fields)
                              ->from('barangays B');

            $initQuery = ($id) ? $initQuery->where(array('B.id' => ':id')) : $initQuery;
            $initQuery = ($code) ? $initQuery->where(array('B.city_municipality_code' => ':code')) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectPositions` Query String that will select from table `positions`
         * @return string
         */
        public function selectPositions($id = false, $like = false)
        {
            $fields = [
                'P.id',
                'P.name',
                'P.department_id',
                'P.head_id',
                'D.name as department_name'
            ];

            $initQuery = $this->select($fields)
                              ->from('positions P')
                              ->leftJoin(['departments D' => 'D.id = P.department_id'])
                              ->where(['P.is_active' => 1]);

            $initQuery = ($like) ? $initQuery->andWhereLike(['P.name' => ':search'])->limit(10) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectDepartments` Query String that will select from table `departments`
         * @return string
         */
        public function selectDepartments($id = false, $like = false)
        {
            $fields = [
                'D.id',
                'D.name',
            ];

            $initQuery = $this->select($fields)
                              ->from('departments D')
                              ->where(['D.is_active' => 1]);

            $initQuery = ($like) ? $initQuery->andWhereLike(['D.name' => ':search'])->limit(10) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectAttainmentLevels` Query string that will select from table `attainment_levels`.
         * @return string
         */
        public function selectAttainmentLevels()
        {
            $fields = [
                'AL.id',
                'AL.name',
            ];

            $initQuery = $this->select($fields)
                              ->from('attainment_levels AL');

            return $initQuery;
        }

        /**
         * `selectCourses` Query string that will select from table `courses`.
         * @return string
         */
        // public function selectCourses()
        // {
        //     $fields = [
        //         'C.id',
        //         'C.name',
        //     ];

        //     $initQuery = $this->select($fields)
        //                       ->from('courses C');

        //     return $initQuery;
        // }

        /**
         * `selectSchools` Query string that will select from table `schools`.
         * @return string
         */
        public function selectSchools($search = false, $id = false)
        {
            $fields = [
                'S.id',
                'S.name',
            ];

            $initQuery = $this->select($fields)
                              ->from('schools S');

            $initQuery = ($search) ? $initQuery->whereLike(['S.name' => ':search'])->limit(10) : $initQuery->limit(10);
            
            return $initQuery;
        }

        /**
         * `insertUser` Query string that will insert to table `users`
         * @return string
         */
        public function insertUser($data = [])
        {
            $initQuery = $this->insert('users', $data);

            return $initQuery;
        }

        /**
         * `insertAttainmentLevel` Query string that will insert to table `attainment_levels`
         * @return string
         */
        public function insertAttainmentLevel($data = [])
        {
            $initQuery = $this->insert('attainment_levels', $data);

            return $initQuery;
        }

        /**
         * `insertSchool` Query string that will insert to table `school`
         * @return string
         */
        public function insertSchool($data = [])
        {
            $initQuery = $this->insert('schools', $data);

            return $initQuery;
        }

        /**
         * `insertCourse` Query string that will insert to table `courses`
         * @return string
         */
        public function insertCourse($data = [])
        {
            $initQuery = $this->insert('courses', $data);

            return $initQuery;
        }

        /**
         * `insertPersonalInformation` Query string that will insert to table `personal_informations`
         * @return string
         */
        public function insertPersonalInformation($data = [])
        {
            $initQuery = $this->insert('personal_informations', $data);

            return $initQuery;
        }

        /**
         * `insertEmploymentInformation` Query string that will insert to table `employment_informations`
         * @return string
         */
        public function insertEmploymentInformation($data = [])
        {
            $initQuery = $this->insert('employment_informations', $data);

            return $initQuery;
        }

        /**
         * `insertFamilyBackground` Query string that will insert to table `family_backgrounds`
         * @return string
         */
        public function insertFamilyBackground($data = [])
        {
            $initQuery = $this->insert('family_backgrounds', $data);

            return $initQuery;
        }

        /**
         * `insertSpouse` Query string that will insert to table `spouse`
         * @return string
         */
        public function insertSpouse($data = [])
        {
            $initQuery = $this->insert('spouses', $data);

            return $initQuery;
        }

        /**
         * `insertSpouseChildren` Query string that will insert to table `spouse_childrens`
         * @return string
         */
        public function insertSpouseChildren($data = [])
        {
            $initQuery = $this->insert('spouse_childrens', $data);

            return $initQuery;
        }

        /**
         * `insertEducationalBackground` Query string that will insert to table `educational_backgrounds`
         * @return string
         */
        public function insertEducationalBackground($data = [])
        {
            $initQuery = $this->insert('educational_backgrounds', $data);

            return $initQuery;
        }

        /**
         * `insertLicenseCertificate` Query string that will insert to table `license_certificates`
         * @return string
         */
        public function insertLicenseCertificate($data = [])
        {
            $initQuery = $this->insert('license_certificates', $data);

            return $initQuery;
        }

        /**
         * `insertOtherDetail` Query string that will insert to table `other_details`
         * @return string
         */
        public function insertOtherDetail($data = [])
        {
            $initQuery = $this->insert('other_details', $data);

            return $initQuery;
        }

        /**
         * `insertEmploymentHistory` Query string that will insert to table `employment_histories`
         * @return string
         */
        public function insertEmploymentHistory($data = [])
        {
            $initQuery = $this->insert('employment_histories', $data);

            return $initQuery;
        }

        /**
         * `insertTrainingSeminar` Query string that will insert to table `training_seminars`
         * @return string
         */
        public function insertTrainingSeminar($data = [])
        {
            $initQuery = $this->insert('training_seminars', $data);

            return $initQuery;
        }

        /**
         * `insertPiPhoto` Query string that will insert to table `pi_photos`
         * @return string
         */
        public function insertPiPhoto($data = [])
        {
            $initQuery = $this->insert('pi_photos', $data);

            return $initQuery;
        }

        /**
         * `updateEmployee` Query string that will update to table `employees`
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function updateEmployee($id = '', $data = [])
        {
            $initQuery = $this->update('employees', $id, $data);

            return $initQuery;
        }

        /**
         * `updateUser` Query string that will update to table `users`
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function updateUser($id = '', $data = [], $fk = '', $fkValue = '')
        {
            $initQuery = $this->update('users', $id, $data, $fk, $fkValue);

            return $initQuery;
        }

        /**
         * `updatePiFiles` Query string that will update information from table `personal_informations`
         * @return string
         */
        public function updatePiFiles($id = '', $data = [])
        {
            $initQuery = $this->update('personal_informations', $id, $data);

            return $initQuery;
        }

        /**
         * `updateEmploymentInformation` Query string that will update information from table `employment_informations`
         * @return string
         */
        public function updateEmploymentInformation($id = '', $data = [], $fk = '', $fkValue = '')
        {
            $initQuery = $this->update('employment_informations', $id, $data, $fk, $fkValue);

            return $initQuery;
        }

        /**
         * `updateFamilyBackground` Query string that will update information from table `family_backgrounds`
         * @return string
         */
        public function updateFamilyBackground($id = '', $data = [], $fk = '', $fkValue = '')
        {
            $initQuery = $this->update('family_backgrounds', $id, $data, $fk, $fkValue);

            return $initQuery;
        }

        /**
         * `updateSpouse` Query string that will update information from table `spouses`
         * @return string
         */
        public function updateSpouse($id = '', $data = [], $fk = '', $fkValue = '')
        {
            $initQuery = $this->update('spouses', $id, $data, $fk, $fkValue);

            return $initQuery;
        }

        /**
         * `updateSpouseChildren` Query string that will update information from table `spouse_childrens`
         * @return string
         */
        public function updateSpouseChildren($id = '', $data = [], $fk = '', $fkValue = '')
        {
            $initQuery = $this->update('spouse_childrens', $id, $data, $fk, $fkValue);

            return $initQuery;
        }

        /**
         * `updateOtherDetail` Query string that will update information from table `other_details`
         * @return string
         */
        public function updateOtherDetail($id = '', $data = [], $fk = '', $fkValue = '')
        {
            $initQuery = $this->update('other_details', $id, $data, $fk, $fkValue);

            return $initQuery;
        }

        /**
         * `updateEducationalBackground` Query string that will update information from table `educational_backgrounds`
         * @return string
         */
        public function updateEducationalBackground($id = '', $data = [], $fk = '', $fkValue = '')
        {
            $initQuery = $this->update('educational_backgrounds', $id, $data, $fk, $fkValue);

            return $initQuery;
        }

        /**
         * `updateLicenseCertificate` Query string that will update information from table `license_certificates`
         * @return string
         */
        public function updateLicenseCertificate($id = '', $data = [], $fk = '', $fkValue = '')
        {
            $initQuery = $this->update('license_certificates', $id, $data, $fk, $fkValue);

            return $initQuery;
        }

        /**
         * `updateEmploymentHistory` Query string that will update information from table `employment_histories`
         * @return string
         */
        public function updateEmploymentHistory($id = '', $data = [], $fk = '', $fkValue = '')
        {
            $initQuery = $this->update('employment_histories', $id, $data, $fk, $fkValue);

            return $initQuery;
        }

        /**
         * `updateTrainingSeminar` Query string that will update information from table `training_seminars`
         * @return string
         */
        public function updateTrainingSeminar($id = '', $data = [], $fk = '', $fkValue = '')
        {
            $initQuery = $this->update('training_seminars', $id, $data, $fk, $fkValue);

            return $initQuery;
        }

        /**
         * `updateTrainingSeminar` Query string that will update information from table `pi_photos`
         * @return string
         */
        public function updatePiPhoto($id = '', $data = [], $fk = '', $fkValue = '')
        {
            $initQuery = $this->update('pi_photos', $id, $data, $fk, $fkValue);

            return $initQuery;
        }

        /**
         * `deleteRequestQuotationDescription` Hard delete details from table `request_quotation_descriptions`.
         * @param  string $id
         * @return string
         */
        public function deleteRequestQuotationDescription($id = '', $rfqMaterialId = '')
        {
            $initQuery = $this->delete('request_quotation_descriptions RQD')
                              ->where(array('RQD.rfq_material_id' => $rfqMaterialId));

            return $initQuery;
        }
    }