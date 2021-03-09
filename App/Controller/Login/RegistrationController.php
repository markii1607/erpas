<?php
    namespace App\Controller\Login;

    require_once("LoginController.php");

    use App\Controller\Login\LoginController as ModuleController;
use Exception;

class RegistrationController extends ModuleController {

        /**
         * `getDetails` Fetching first needed details in employee requisition form.
         */
        public function getDetails($data = [])
        {
            $output = [
                'departments'   => $this->getDepartments(),
                'positions'     => $this->getPositions(),
                'employees'     => $this->getInformation(),
            ];

            return $output;
        }

        /**
         * `getInformation` Fetching all department and information.
         * @return array
         */
        public function getInformation($id = '')
        {
            $idCondition = ($id == '') ? false : true;

            $data = [
                'is_active' => 1
            ];

            $employees = $this->dbCon->prepare($this->queryHandler->selectPersonalInformations($idCondition)->end());

            ($id != '') ? $data['id'] = $id : '';

            $employees->execute($data);

            return $employees->fetchAll(\PDO::FETCH_ASSOC);
        }

        /**
         * `getPositions` Fetching all positions.
         * @return array
         */
        public function getPositions($id = '')
        {
            $idCondition = ($id == '') ? false : true;

            $data = [
                'is_active' => 1
            ];

            ($id != '') ? $data['id'] = $id : '';

            $positions = $this->dbCon->prepare($this->queryHandler->selectPositions($idCondition)->end());
            $positions->execute($data);

            return $positions->fetchAll(\PDO::FETCH_ASSOC);
        }

        /**
         * `getDepartments` Fetching all departments.
         * @return array
         */
        public function getDepartments($id = '')
        {
            $idCondition = ($id == '') ? false : true;

            $data = [
                'is_active' => 1
            ];

            ($id != '') ? $data['id'] = $id : '';

            $department = $this->dbCon->prepare($this->queryHandler->selectDepartments($idCondition)->end());
            $department->execute($data);

            return $department->fetchAll(\PDO::FETCH_ASSOC);
        }

        /**
         * `getUserLevel` Fetching all departments.
         * @return array
         */
        public function getPefUserLevel($name = '')
        {
            $nameCondition = ($name == '') ? false : true;

            $data = [
                'is_active' => 1
            ];

            ($name != '') ? $data['name'] = $name : '';

            $user_level = $this->dbCon->prepare($this->queryHandler->selectPefUserLevel($nameCondition)->end());
            $user_level->execute($data);

            return $user_level->fetchAll(\PDO::FETCH_ASSOC);
        }

        /**
         * 'saveRegistration' Saving of New Registered User
         *
         * @param [type] $input
         * @return void
         */
        public function saveRegistration($input)
        {
            // print_r($input);
            // die();
            try {

                $this->dbCon->beginTransaction();

                $input = $this->arrayToObject($input);
    
                // required
                $piId = $this->savePersonalInformation($input->pi);
    
                $this->saveUser($input->pi, $piId);
                $this->saveEmploymentInformation($input->ei, $piId);
                
                $this->dbCon->commit();
                
                $returnData = [
                    'id'     => $piId,
                    'status' => true
                ];

                return $returnData;

            } catch (Exception $e) {
                echo $e->getMessage();
                $this->dbCon->rollBack();
            }
        }

        /**
         * `saveUser` Saving of user per employee.
         * @param  string | int $piId
         * @return mixed
         */
        public function saveUser($input, $piId)
        {
            $salt     = '+^7*_<>/?absdia7has723n7as123';
            $lname    = str_replace(' ', '', $input->lname);
            $userData = [
                'personal_information_id' => $piId,
                'username'                => strtolower(str_split($input->fname)[0].''.$lname),
                'password'                => crypt(12345, $salt),
                'created_at'              => date('Y-m-d H:i:s'),
                'updated_at'              => date('Y-m-d H:i:s')
            ];

            $insertUser = $this->dbCon->prepare($this->queryHandler->insertUser($userData));
            $insertUser->execute($userData);

            $userId = $this->dbCon->lastInsertId();

            $this->systemLogs($userId, 'users', 'add_users', 'add', 1);

            return $userId;
        }

        /**
         * `savePersonalInformation` Saving of personal information in table `personal_informations`.
         * @param  object $input
         * @return boolean | string | int
         */
        public function savePersonalInformation($input)
        {            
            $piData = [
                'fname'              => ucwords(strtolower($input->fname)),
                'lname'              => $input->lname,
                'age'                => '',
                'mname'              => empty($input->mname)                  ? ''   : ucwords(strtolower($input->mname)),
                'sname'              => empty($input->sname)                  ? ''   : ucwords(strtolower($input->sname)),
                'citizenship'        => empty($input->citizenship)            ? '' : ucwords(strtolower($input->citizenship)),
                'sex'                => empty($input->sex)                    ? '' : ucwords(strtolower($input->sex)),
                'birthdate'          => empty($input->birthdate)              ? null : $this->formatDate($input->birthdate),
                'height'             => empty($input->height)                 ? 0    : $input->height,
                'weight'             => empty($input->weight)                 ? 0    : $input->weight,
                'religion'           => empty($input->religion)               ? ''   : ucwords(strtolower($input->religion)),
                'birthplace'         => empty($input->birthplace)             ? ''   : ucwords(strtolower($input->birthplace)),
                'civil_status'       => empty($input->civil_status)           ? ''   : $input->civil_status,
                'no_of_dependents'   => empty($input->no_of_dependents)       ? 0    : $input->no_of_dependents,
                'tel_no'             => empty($input->tel_no)                 ? 0    : $input->tel_no,
                'mobile_no'          => empty($input->mobile_number)          ? ''   : $input->mobile_number,
                'email'              => empty($input->email)                  ? ''   : $input->email,
                'address_condition'  => empty($input->temp_address_condition) ? null : $input->temp_address_condition,
                'ps_region_id'       => empty($input->ps_region->id)          ? null : $input->ps_region->id,
                'ps_province_id'     => empty($input->ps_province->id)        ? null : $input->ps_province->id,
                'ps_city_id'         => empty($input->ps_city->id)            ? null : $input->ps_city->id,
                'ps_barangay_id'     => empty($input->ps_barangay->id)        ? null : $input->ps_barangay->id,
                'ps_house_no_street' => empty($input->ps_house_no_street)     ? ''   : $input->ps_house_no_street,
                'ps_type'            => empty($input->ps_type)                ? ''   : $input->ps_type,
                'pr_region_id'       => empty($input->pr_region)              ? null : $input->pr_region->id,
                'pr_province_id'     => empty($input->pr_province)            ? null : $input->pr_province->id,
                'pr_city_id'         => empty($input->pr_city)                ? null : $input->pr_city->id,
                'pr_barangay_id'     => empty($input->pr_barangay)            ? null : $input->pr_barangay->id,
                'pr_house_no_street' => empty($input->pr_house_no_street)     ? null : $input->pr_house_no_street,
                'pr_type'            => empty($input->pr_type)                ? null : $input->pr_type,
                'created_by'         => 1,
                'updated_by'         => 1,
                'created_at'         => date('Y-m-d H:i:s'),
                'updated_at'         => date('Y-m-d H:i:s')
            ];

            $insertPersonalInformation = $this->dbCon->prepare($this->queryHandler->insertPersonalInformation($piData));
            $insertPersonalInformation->execute($piData);

            $piId = $this->dbCon->lastInsertId();

            $this->systemLogs($piId, 'personal_informations', 'add_personal_informations', 'add', 1);

            return $piId;
        }

         /**
         * `saveEmploymentInformation` Saving of employment information from table `employment_informations`.
         * @param  object       $input
         * @param  string | int $piId
         * @return boolean | int | string
         */
        public function saveEmploymentInformation($input, $piId)
        {
            $eiData = [
                'personal_information_id' => $piId,
                'position_id'             => $input->position->id,
                'head_id'                 => empty($input->head->id)    ? null         : $input->head->id,
                'employee_no'             => empty($input->employee_no) ? '-'          : $input->employee_no,
                'date_hired'              => empty($input->date_hired)  ? '0000-00-00' : $this->formatDate($input->date_hired),
                'ho'                      => ($input->ho == 'no')       ? 0            : 1,
                'fo'                      => ($input->fo == 'no')       ? 0            : 1,
                'status'                  => $input->status,
                'created_by'              => 1,
                'updated_by'              => 1,
                'created_at'              => date('Y-m-d H:i:s'),
                'updated_at'              => date('Y-m-d H:i:s')
            ];
        
            $insertEmploymentInformation = $this->dbCon->prepare($this->queryHandler->insertEmploymentInformation($eiData));
            $insertEmploymentInformation->execute($eiData);

            $eiId = $this->dbCon->lastInsertId();

            $this->systemLogs($eiId, 'employment_informations', 'add_employment_informations', 'add', 1);

            return $eiId;
        }


    }