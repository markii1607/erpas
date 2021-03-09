<?php
    namespace App\Controller\Main;

    require_once("MainController.php");

    use App\Controller\Main\MainController as ModuleController;

    class UpdateProfilesController extends ModuleController {
        /**
         * `getDetails` Get details needed in edit department
         * @param  string $id
         * @return array
         */
        public function getDetails($data = [])
        {
            // print_r($data['id']);
            // die();
            $output = [
                'informations'            => $this->getInformation('', $_SESSION['user_id']),
                'employment_informations' => $this->getEmploymentInformations('', $data['id']),
                'family_backgrounds'      => $this->getFamilyBackgrounds('', $data['id']),
                'educational_backgrounds' => $this->getEducationalBackgrounds('', $data['id']),
                'license_certificates'    => $this->getLicenseCertificates('', $data['id']),
                'other_details'           => $this->getOtherDetails('', $data['id']),
                'employment_histories'    => $this->getEmploymentHistories('', $data['id']),
                'training_seminars'       => $this->getTrainingSeminars('', $data['id']),
                'spouses'                 => $this->getSpouses('', $data['id']),
            ];

            return $output;
        }

        /**
         * `uploadImage` Uploading of image.
         * @param  [type] $input
         * @param  [type] $files
         * @return 
         */
    	public function uploadImage($input, $files)
    	{
    		print_r($files);
    	}

         /**
         * `update` Saving of 201 Files of employee.
         * @param object $input
         * @param array $files
         * 
         * @return array
         */
        public function updatePi($input, $files)
        {
            // print_r($input);
            // die();
            try {
                $this->dbCon->beginTransaction();
                
                $input = $this->arrayToObject($input);
                
                // required
                (!empty($input->pi)) ? $this->editPersonalInformation($input->pi) : '';

                // $this->editUser($input->pi, $input->pi->id);
                (!empty($input->ei)) ? $this->editEmploymentInformation($input->ei, $input->pi->id) : '';

                // not-required
                // $this->editPhoto($files, $input->pi->id);
                if (isset($input->fb)) {
                    if (!empty($input->fb)) {
                        if (isset($input->fb->fathers_name) || isset($input->fb->mothers_name)) {
                            if (isset($input->fb->id)) {
                                $this->editFamilyBackground($input->fb, $input->pi->id);
                            }else{
                                $this->saveFamilyBackground($input->fb,$input->pi->id);
                            }
                        }
                    }
                }

                if (isset($input->od)) {
                    if (!empty($input->od)) {
                        if (isset($input->od->sss_no) || isset($input->od->tin) || isset($input->od->pagibig_no) || isset($input->od->philhealth_no) || isset($input->od->cp_number)) {
                            if (isset($input->od->id)) {
                                $this->editOtherDetails($input->od, $input->pi->id);
                            }else {
                                $this->saveOtherDetails($input->od,$input->pi->id);
                            }
                        }
                    }
                }
                // (isset($input->fb)) ? $this->editFamilyBackground($input->fb, $input->pi->id) : '';
                // (isset($input->ei)) ? $this->editOtherDetails($input->od, $input->pi-    >id) : '';

                // print_r($input->eb);
                // die();
                if (isset($input->eb)) {
                    if (!empty($input->eb)) {
                        foreach ($input->eb as $ebKey => $ebVal) {
                            if (isset($ebVal->data_status)) {
                                if ($ebVal->data_status =='saved') {
                                    $this->editEducationalBackground($ebVal);
                                }else if ($ebVal->data_status =='new') {
                                    $this->saveEducationalBackground($ebVal, $input->pi->id);
                                }
                            }else {
                                $this->saveEducationalBackground($ebVal, $input->pi->id);
                            }
                        }
                    }
                }

                if (isset($input->lc)) {
                    if (!empty($input->lc)) {
                        foreach ($input->lc as $lcKey => $lcVal) {
                            if (isset($lcVal->data_status)) {
                                if ($lcVal->data_status =='saved') {
                                    $this->editLicenseCertificate($lcVal, $input->pi->id);
                                } else if ($lcVal->data_status =='new'){
                                    $this->saveLicenseCertificate($lcVal, $input->pi->id);
                                }
                            }else {
                                $this->saveLicenseCertificate($lcVal, $input->pi->id);
                            }
                        }
                    }
                }

                if (isset($input->eh)) {
                    if (!empty($input->eh)) {
                        foreach ($input->eh as $ehKey => $ehVal) {
                            if (isset($ehVal->data_status)) {
                                if ($ehVal->data_status == 'saved') {
                                    $this->editEmploymentHistory($ehVal, $input->pi->id);
                                } else if ($ehVal->data_status == 'new') {
                                    $this->saveEmploymentHistory($ehVal, $input->pi->id);
                                }
                            }else {
                                $this->saveEmploymentHistory($ehVal, $input->pi->id);
                            }
                        }
                    }
                }
                
                if (isset($input->ts)) {
                    if (!empty($input->ts)) {
                        foreach ($input->ts as $tsKey => $tsVal) {
                            if (isset($tsVal->data_status)) {
                                if ($tsVal->data_status == 'saved') {
                                    $this->editTrainingSeminar($tsVal, $input->pi->id);
                                } else if ($tsVal->data_status == 'new') {
                                    $this->saveTrainingSeminar($tsVal, $input->pi->id);
                                }
                            }else {
                                $this->saveTrainingSeminar($tsVal, $input->pi->id);
                            }
                        }
                    }
                }

                $this->dbCon->commit();

                $returnData = [
                    'id'     => $input->pi->id,
                    'status' => true
                ];

                return $returnData;

            } catch (Exception $e) {
                echo $e->getMessage();
                $this->dbCon->rollBack();
            }
        }


        /**
         * `getEmploymentInformations` Fetching all employment informations.
         * @return array
         */
        public function getEmploymentInformations($id = '', $eiId = '')
        {
            $idCondition = ($id == '') ? false : true;
            $eiIdCondition = ($eiId == '') ? false : true;

            $data = [
                'is_active' => 1
            ];

            $ei_info = $this->dbCon->prepare($this->queryHandler->selectEmploymentInformations($idCondition, $eiIdCondition)->end());

            ($id != '')   ? $data['id'] = $id                        : '';
            ($eiId != '') ? $data['personal_information_id'] = $eiId : '';

            $ei_info->execute($data);

            return $ei_info->fetchAll(\PDO::FETCH_ASSOC);
        }

         /**
         * `getFamilyBackgrounds` Fetching all employment informations.
         * @return array
         */
        public function getFamilyBackgrounds($id = '', $fbId = '')
        {
            $idCondition = ($id == '') ? false : true;
            $fbIdCondition = ($fbId == '') ? false : true;

            $data = [
                'is_active' => 1
            ];

            $fb_info = $this->dbCon->prepare($this->queryHandler->selectFamilyBackgrounds($idCondition, $fbIdCondition)->end());

            ($id != '')   ? $data['id'] = $id                        : '';
            ($fbId != '') ? $data['personal_information_id'] = $fbId : '';

            $fb_info->execute($data);

            return $fb_info->fetchAll(\PDO::FETCH_ASSOC);
        }

        /**
         * `getEducationalBackgrounds` Fetching all employment informations.
         * @return array
         */
        public function getEducationalBackgrounds($id = '', $piId = '')
        {
            $idCondition   = ($id == '')   ? false : true;
            $piIdCondition = ($piId == '') ? false : true;

            $data = [
                'is_active' => 1
            ];

            $eb_info = $this->dbCon->prepare($this->queryHandler->selectEducationalBackgrounds($idCondition, $piIdCondition)->end());

            ($id != '')   ? $data['id'] = $id                        : '';
            ($piId != '') ? $data['personal_information_id'] = $piId : '';

            $eb_info->execute($data);

            return $eb_info->fetchAll(\PDO::FETCH_ASSOC);
        }

        /**
         * `getLicenseCertificates` Fetching all employment informations.
         * @return array
         */
        public function getLicenseCertificates($id = '', $lcId = '')
        {
            $idCondition   = ($id == '') ? false : true;
            $lcIdCondition = ($lcId == '') ? false : true;

            $data = [
                'is_active' => 1
            ];

            $lc_info = $this->dbCon->prepare($this->queryHandler->selectLicenseCertificates($idCondition, $lcIdCondition)->end());

            ($id != '')   ? $data['id'] = $id : '';
            ($lcId != '') ? $data['personal_information_id'] = $lcId : '';

            $lc_info->execute($data);

            return $lc_info->fetchAll(\PDO::FETCH_ASSOC);
        }

        /**
         * `getOtherDetails` Fetching all employment informations.
         * @return array
         */
        public function getOtherDetails($id = '', $odId = '')
        {
            $idCondition   = ($id == '') ? false : true;
            $odIdCondition = ($odId == '') ? false : true;

            $data = [
                'is_active' => 1
            ];

            $od_info = $this->dbCon->prepare($this->queryHandler->selectOtherDetails($idCondition, $odIdCondition)->end());

            ($id != '')   ? $data['id'] = $id : '';
            ($odId != '') ? $data['personal_information_id'] = $odId : '';

            $od_info->execute($data);

            return $od_info->fetchAll(\PDO::FETCH_ASSOC);
        }

        /**
         * `getEmploymentHistories` Fetching all employment informations.
         * @return array
         */
        public function getEmploymentHistories($id = '', $ehId = '')
        {
            $idCondition   = ($id == '') ? false : true;
            $ehIdCondition = ($ehId == '') ? false : true;

            $data = [
                'is_active' => 1
            ];

            $eh_info = $this->dbCon->prepare($this->queryHandler->selectEmploymentHistories($idCondition, $ehIdCondition)->end());

            ($id != '')   ? $data['id'] = $id : '';
            ($ehId != '') ? $data['personal_information_id'] = $ehId : '';

            $eh_info->execute($data);

            return $eh_info->fetchAll(\PDO::FETCH_ASSOC);
        }

        /**
         * `getTrainingSeminars` Fetching all employment informations.
         * @return array
         */
        public function getTrainingSeminars($id = '', $tsId = '')
        {
            $idCondition   = ($id == '') ? false : true;
            $tsIdCondition = ($tsId == '') ? false : true;

            $data = [
                'is_active' => 1
            ];

            $eh_info = $this->dbCon->prepare($this->queryHandler->selectTrainingSeminars($idCondition, $tsIdCondition)->end());

            ($id != '')   ? $data['id'] = $id : '';
            ($tsId != '') ? $data['personal_information_id'] = $tsId : '';

            $eh_info->execute($data);

            return $eh_info->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function getSpouses($id = '', $spId = '')
        {
            $output = [];

            $idCondition   = ($id == '') ? false : true;
            $spIdCondition = ($spId == '') ? false : true;

            $data = [
                'is_active' => 1
            ];

            $sp_info = $this->dbCon->prepare($this->queryHandler->selectSpouses($idCondition, $spIdCondition)->end());

            ($id != '') ? $data['id'] = $id : '';
            ($spId != '') ? $data['personal_information_id'] = $spId : '';

            $sp_info->execute($data);

            $spouses =  $sp_info->fetchAll(\PDO::FETCH_ASSOC);

             foreach ($spouses as $key => $value) {
             
            $value['children'] = $this->getSpouseChildrens ('', $value['id']);
                
            array_push($output, $value);
            
            }
            return $output;
        }

        public function getSpouseChildrens ($id = '', $scId = '')
        {
            $idCondition   = ($id == '') ? false : true;
            $scIdCondition = ($scId == '') ? false : true;

            $data = [
                'is_active' => 1
            ];

            $sc_info = $this->dbCon->prepare($this->queryHandler->selectSpouseChildrens($idCondition, $scIdCondition)->end());

            ($id != '')   ? $data['id'] = $id : '';
            ($scId != '') ? $data['spouse_id'] = $scId : '';

            $sc_info->execute($data);

            return $sc_info->fetchAll(\PDO::FETCH_ASSOC);
        }

        /**
         * `editPhoto` Saving of photo.
         * @param  array $files
         * @param  string $piId
         * @return mixed
         */
        public function editPhoto($files, $piId)
        {
            if ($files['file']['error'] == '0' || $files['file']['error'] == 0) {

                $baseFileUrl = __DIR__.'/../../../public/';
                // $baseFileUrl = __DIR__.'/../../../public/files/rfq_attachments/'.$files['file']['name'][$fKey];

                $data = array(
                    'personal_information_id' => $piId,
                    'file_name'               => $files['file']['name'],
                    'created_by'              => $_SESSION['user_id'],
                    'updated_by'              => $_SESSION['user_id'],
                    'created_at'              => date('Y-m-d H:i:s'),
                    'updated_at'              => date('Y-m-d H:i:s')
                );

                $updatePiPhoto = $this->dbCon->prepare($this->queryHandler->updatePiPhoto('', $data, 'personal_information_id', $piId));
                $status      = $updatePiPhoto->execute($data);

                $piPhotoId = $this->dbCon->lastInsertId();

                $this->systemLogs($_SESSION['user_id'], $piPhotoId, 'Employee Records', 'New Emplyoee', 'add');

                // if `public/files` doesn't exist, system with create folder.
                if (!is_dir($baseFileUrl.'/files')) {
                    mkdir($baseFileUrl.'/files');
                }

                // if `public/files/employee_records` doesn't exist, system with create folder.
                if (!is_dir($baseFileUrl.'/files/employee_records')) {
                    mkdir($baseFileUrl.'/files/employee_records');
                } else {
                    move_uploaded_file($files['file']['tmp_name'], $baseFileUrl.'/files/employee_records/'.$files['file']['name']);
                }

                $data['id']     = $piPhotoId;
                $data['status'] = $status;
            } else {
                $data['photo'] = $files['file']['name'];
                $data['status']     = $status;
            }

            return $data;
        }

         /**
         * `editUser` Saving of user per employee.
         * @param  string | int $piId
         * @return mixed
         */
        // public function editUser($input, $piId)
        // {
        //     $salt     = '+^7*_<>/?absdia7has723n7as123';
        //     $lname    = str_replace(' ', '', $input->lname);
        //     $userData = [
        //         'username'                => strtolower(str_split($input->fname)[0].''.$lname),
        //         'updated_at'              => date('Y-m-d H:i:s')
        //     ];

        //     $insertUser = $this->dbCon->prepare($this->queryHandler->updateUser('', $userData, 'personal_information_id', $piId));
        //     $insertUser->execute($userData);

        //     $userId = $this->dbCon->lastInsertId();

        //     $this->systemLogs($userId, 'users', 'add_users', 'add');

        //     return $userId;
        // }

        /**
         * `editPersonalInformation` Updating of personal information in table `personal_informations`.
         * @param  object $input
         * @return boolean | string | int
         */
        public function editPersonalInformation($input)
        {          
            // print_r(empty($input->pr_province));
            // die();

            $piData = [
                'fname'              => ucwords(strtolower($input->fname)),
                'mname'              => empty($input->mname)              ? ''   : ucwords(strtolower($input->mname)),
                'lname'              => $input->lname,
                'sname'              => empty($input->sname)              ? ''   : ucwords(strtolower($input->sname)),
                'citizenship'        => ucwords(strtolower($input->citizenship)),
                'sex'                => ucwords(strtolower($input->sex)),
                'birthdate'          => empty($input->birthdate)          ? null : $this->formatDate($input->birthdate),
                'age'                => '',
                'height'             => empty($input->height)             ? 0    : $input->height,
                'weight'             => empty($input->weight)             ? 0    : $input->weight,
                'religion'           => empty($input->religion)           ? ''   : ucwords(strtolower($input->religion)),
                'birthplace'         => empty($input->birthplace)         ? ''   : ucwords(strtolower($input->birthplace)),
                'civil_status'       => $input->civil_status,
                'no_of_dependents'   => empty($input->no_of_dependents)   ? 0    : $input->no_of_dependents,
                'tel_no'             => empty($input->tel_no)             ? 0    : $input->tel_no,
                'mobile_no'          => empty($input->mobile_number)      ? ''   : $input->mobile_number,
                'email'              => empty($input->email)              ? ''   : $input->email,
                'ps_region_id'       => empty($input->ps_region->id)      ? null : $input->ps_region->id,
                'ps_province_id'     => empty($input->ps_province->id)    ? null : $input->ps_province->id,
                'ps_city_id'         => empty($input->ps_city->id)        ? null : $input->ps_city->id,
                'ps_barangay_id'     => empty($input->ps_barangay->id)    ? null : $input->ps_barangay->id,
                'ps_house_no_street' => empty($input->ps_house_no_street) ? ''   : $input->ps_house_no_street,
                'ps_type'            => $input->ps_type,
                'pr_region_id'       => (empty($input->temp_address_condition) || $input->temp_address_condition == 'null') ? null : ( ($input->temp_address_condition == 'yes') ? ((empty($input->ps_region) || $input->ps_region == 'null')                   ? null : $input->ps_region->id)           : ((empty($input->pr_region) || $input->pr_region == 'null')                   ? null : $input->pr_region->id) ),
                'pr_province_id'     => (empty($input->temp_address_condition) || $input->temp_address_condition == 'null') ? null : ( ($input->temp_address_condition == 'yes') ? ((empty($input->ps_province) || $input->ps_province == 'null')               ? null : $input->ps_province->id)         : ((empty($input->pr_province) || $input->pr_province == 'null')               ? null : $input->pr_province->id) ),
                'pr_city_id'         => (empty($input->temp_address_condition) || $input->temp_address_condition == 'null') ? null : ( ($input->temp_address_condition == 'yes') ? ((empty($input->ps_city) || $input->ps_city == 'null')                       ? null : $input->ps_city->id)             : ((empty($input->pr_city) || $input->pr_city == 'null')                       ? null : $input->pr_city->id) ),
                'pr_barangay_id'     => (empty($input->temp_address_condition) || $input->temp_address_condition == 'null') ? null : ( ($input->temp_address_condition == 'yes') ? ((empty($input->ps_barangay) || $input->ps_barangay == 'null')               ? null : $input->ps_barangay->id)         : ((empty($input->pr_barangay) || $input->pr_barangay == 'null')               ? null : $input->pr_barangay->id) ),
                // 'pr_house_no_street' => (empty($input->temp_address_condition) || $input->temp_address_condition == 'null') ? null : ( ($input->temp_address_condition == 'yes') ? ((empty($input->ps_house_no_street) || $input->ps_house_no_street == 'null') ? null : $input->ps_house_no_street->id ) : ((empty($input->pr_house_no_street) || $input->pr_house_no_street == 'null') ? null : $input->pr_house_no_street->id) ),
                'pr_house_no_street' => empty($input->pr_house_no_street) ? null : $input->pr_house_no_street,
                'pr_type'            => empty($input->pr_type)            ? null : $input->pr_type,
                'address_condition'  => $input->temp_address_condition,
                'updated_by'         => $_SESSION['user_id'],
                'updated_at'         => date('Y-m-d H:i:s')
            ];

            $updatePiFiles = $this->dbCon->prepare($this->queryHandler->updatePiFiles($input->id, $piData));
            $updatePiFiles->execute($piData);

            $this->systemLogs($input->id, 'personal_informations', 'updated_personal_informations', 'edit');

            return $input->id;
        }


        /**
         * `deleteRequestQuotationDescription` Hard Delete of rfq description.
         * @param  string $id
         * @param  string $rfqMaterialId
         * @return void
         */
        // public function deleteRequestQuotationDescription($id = '', $rfqMaterialId = '')
        // {
        //     $hardDeleteRfqDesc = $this->dbCon->prepare($this->queryHandler->deleteRequestQuotationDescription($id, $rfqMaterialId));
        //     $hardDeleteRfqDesc->execute();
        // }

        /**
         * `editEmploymentInformation` Saving of employment information from table `employment_informations`.
         * @param  object       $input
         * @param  string | int $piId
         * @return boolean | int | string
         */
        public function editEmploymentInformation($input, $piId)
        {
            $eiData = [
                'position_id' => $input->position->id,
                'head_id'     => empty($input->head->id)    ? null         : $input->head->id,
                'employee_no' => empty($input->employee_no) ? '-'          : $input->employee_no,
                'date_hired'  => empty($input->date_hired)  ? '0000-00-00' : $this->formatDate($input->date_hired),
                'status'      => $input->status,
                'ho'          => ($input->ho == 'no') ? 0 : 1,
                'fo'          => ($input->fo == 'no') ? 0 : 1,
                'updated_by'  => $_SESSION['user_id'],
                'updated_at'  => date('Y-m-d H:i:s')
            ];

            $updateEmploymentInformation = $this->dbCon->prepare($this->queryHandler->updateEmploymentInformation('', $eiData, 'personal_information_id', $piId));
            $updateEmploymentInformation->execute($eiData);

            $this->systemLogs($piId, 'employment_informations', 'update_employment_informations', 'edit');

            return $piId;
        }

        /**
         * `editFamilyBackground` Saving of family background from table `family_backgrounds`.
         * @param  object $input
         * @param  string | int $piId
         * @return boolean | int | string
         */
        public function editFamilyBackground($input, $piId)
        {
            $fbData = [
                'fathers_name'      => isset($input->fathers_name) ? ucwords(strtolower($input->fathers_name)) : '',
                'fathers_age'       => isset($input->fathers_age) ? $input->fathers_age : '',
                'mothers_name'      => isset($input->mothers_name) ? ucwords(strtolower($input->mothers_name)) : '', 
                'mothers_age'       => isset($input->mothers_age) ? $input->mothers_age : '',
                'updated_by'        => $_SESSION['user_id'],
                'updated_at'        => date('Y-m-d H:i:s')
            ];

            $updateFamilyBackground = $this->dbCon->prepare($this->queryHandler->updateFamilyBackground($input->id, $fbData));
            $updateFamilyBackground->execute($fbData);

            if (isset($input->spouses)) {
                if(!empty($input->spouses)) {
                    foreach ($input->spouses as $spouseKey => $spouseVal) {
                        if (isset($lcVal->data_status)) {
                            if ($spouseVal->data_status =='saved') {
                                $this->editSpouse($spouseVal, $input->pi->id);
                            } else if ($spouseVal->data_status =='new'){
                                $this->saveSpouse($spouseVal, $input->pi->id);
                            }
                        }else {
                            $this->saveSpouse($spouseVal, $input->pi->id);
                        }
                    }
                }
            }

        }

        /**
         * `saveFamilyBackground` Saving of family background from table `family_backgrounds`.
         * @param  object $input
         * @param  string | int $piId
         * @return boolean | int | string
         */
        public function saveFamilyBackground($input, $piId)
        {
            if (!empty($input->fathers_name) && $input->fathers_age != 0) {
                $fbData = [
                    'personal_information_id' => $piId,
                    'fathers_name'            => isset($input->fathers_name) ? ucwords(strtolower($input->fathers_name)) : '',
                    'fathers_age'             => isset($input->fathers_age) ? $input->fathers_age : '',
                    'mothers_name'            => isset($input->mothers_name) ? ucwords(strtolower($input->mothers_name)) : '',
                    'mothers_age'             => isset($input->mothers_age) ? $input->mothers_age : '',
                    'created_by'              => $_SESSION['user_id'],
                    'updated_by'              => $_SESSION['user_id'],
                    'created_at'              => date('Y-m-d H:i:s'),
                    'updated_at'              => date('Y-m-d H:i:s')
                ];

                $insertFamilyBackground = $this->dbCon->prepare($this->queryHandler->insertFamilyBackground($fbData));
                $insertFamilyBackground->execute($fbData);

                $fbId = $this->dbCon->lastInsertId();

                $this->systemLogs($fbId, 'family_backgrounds', 'add_family_background', 'add');

                if(!empty($input->spouses)){
                    foreach ($input->spouses as $spouseKey => $spouseVal) {
                        $this->saveSpouse($spouseVal, $piId);
                    }
                }

                return $fbId;
            }
        }

        /**
         * `editSpouse` Saving of spouse from table `spouses`
         * @param  object       $input
         * @param  string | int $piId
         * @return mixed
         */
        public function editSpouse($input, $piId)
        {
            /**
             * - Update current spouse from `is_active = 1` to `is_active = 0`
             */
            $oldSpouseData = [
                // 'is_active'  => 0,
                'name'                    => ucwords(strtolower($input->spouse_name)),
                'business_address'        => ucwords(strtolower($input->business_address)),
                'updated_by' => $_SESSION['user_id'],
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $updateOldSpouse = $this->dbCon->prepare($this->queryHandler->updateSpouse($input->id, $oldSpouseData));
            $updateOldSpouse->execute($oldSpouseData);

            if (count($input->children) > 0) {
                foreach ($input->children as $childKey => $childVal) {
                    $this->editSpouseChildren($childVal, $piId);
                }
            }

            return $piId;
        }

        /**
         * `editSpouseChildren` Saving of spouse children from table `spouse_children`.
         * @param  object       $input
         * @param  string | int $sId
         * @return mixed
         */
        public function editSpouseChildren($input, $sId)
        {
            $spouseChildData = [
                'name'       => ucwords(strtolower($input->ch_name)),
                'age'        => $input->ch_age,
                'updated_by' => $_SESSION['user_id'],
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $updateSpouseChildren = $this->dbCon->prepare($this->queryHandler->updateSpouseChildren($input->id, $spouseChildData));
            $updateSpouseChildren->execute($spouseChildData);

        }

         /**
         * `saveSpouse` Saving of spouse from table `spouses`
         * @param  object       $input
         * @param  string | int $piId
         * @return mixed
         */
        public function saveSpouse($input, $piId)
        {
            $spouseData = [
                'personal_information_id' => $piId,
                'name'                    => ucwords(strtolower($input->spouse_name)),
                'business_address'        => ucwords(strtolower($input->business_address)),
                'created_by'              => $_SESSION['user_id'],
                'updated_by'              => $_SESSION['user_id'],
                'created_at'              => date('Y-m-d H:i:s'),
                'updated_at'              => date('Y-m-d H:i:s')
            ];

            $insertSpouse = $this->dbCon->prepare($this->queryHandler->insertSpouse($spouseData));
            $insertSpouse->execute($spouseData);

            $sId = $this->dbCon->lastInsertId();

            $this->systemLogs($sId, 'spouses', 'add_spouse', 'add');

            if (count($input->children) > 0) {
                foreach ($input->children as $childKey => $childVal) {
                    $this->saveSpouseChildren($childVal, $sId);
                }
            }

            return $sId;
        }

        /**
         * `saveSpouseChildren` Saving of spouse children from table `spouse_children`.
         * @param  object       $input
         * @param  string | int $sId
         * @return mixed
         */
        public function saveSpouseChildren($input, $sId)
        {
            $spouseChildData = [
                'spouse_id'  => $sId,
                'name'       => ucwords(strtolower($input->ch_name)),
                'age'        => $input->ch_age,
                'created_by' => $_SESSION['user_id'],
                'updated_by' => $_SESSION['user_id'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $insertSpouseChildren = $this->dbCon->prepare($this->queryHandler->insertSpouseChildren($spouseChildData));
            $insertSpouseChildren->execute($spouseChildData);

            $scId = $this->dbCon->lastInsertId();

            $this->systemLogs($scId, 'spouse_childrens', 'add_spouse_child', 'add');

            return $scId;
        }

        /**
         * `editOtherDetails` Saving of other details from table `other_details`.
         * @param  object       $input
         * @param  string | int $piId
         * @return mixed
         */
        public function editOtherDetails($input)
        {
            if (!empty($input->sss_no) || !empty($input->tin) || !empty($input->pagibig_no) || !empty($input->philhealth_no) || !empty($input->cp_number)) {
                $odData = [
                    'sss_no'                  => empty($input->sss_no) ? '' : $input->sss_no,
                    'pagibig_no'              => empty($input->pagibig_no) ? '' : $input->pagibig_no,
                    'tin'                     => empty($input->tin) ? '' : $input->tin,
                    'philhealth_no'           => empty($input->philhealth_no) ? '' : $input->philhealth_no,
                    'contact_person'          => isset($input->contact_person) ? ucwords(strtolower($input->contact_person)) : '',
                    'cp_number'               => empty($input->cp_number) ? '' : $input->cp_number,
                    'cp_address'              => isset($input->cp_address) ? $input->cp_address : '',
                    'updated_by'              => $_SESSION['user_id'],
                    'updated_at'              => date('Y-m-d H:i:s')
                ];

                $updateOtherDetail = $this->dbCon->prepare($this->queryHandler->updateOtherDetail($input->id, $odData));
                $updateOtherDetail->execute($odData);
            }
        }

         /**
         * `saveOtherDetails` Saving of other details from table `other_details`. 
         * @param  object       $input
         * @param  string | int $piId
         * @return mixed
         */
        public function saveOtherDetails($input, $piId)
        {
            if (!empty($input->sss_no) || !empty($input->tin) || !empty($input->pagibig_no) || !empty($input->philhealth_no) || !empty($input->cp_number) || !empty($input->contact_person) || !empty($input->cp_address)) {
                $odData = [
                    'personal_information_id' => $piId,
                    'sss_no'                  => empty($input->sss_no) ? '' : $input->sss_no,
                    'pagibig_no'              => empty($input->pagibig_no) ? '' : $input->pagibig_no,
                    'tin'                     => empty($input->tin) ? '' : $input->tin,
                    'philhealth_no'           => empty($input->philhealth_no) ? '' : $input->philhealth_no,
                    'contact_person'          => empty($input->contact_person) ? '' : ucwords(strtolower($input->contact_person)),
                    'cp_number'               => empty($input->cp_number) ? '' : $input->cp_number,
                    'cp_address'              => empty($input->cp_address) ? '' : $input->cp_address,
                    'created_by'              => $_SESSION['user_id'],
                    'updated_by'              => $_SESSION['user_id'],
                    'created_at'              => date('Y-m-d H:i:s'),
                    'updated_at'              => date('Y-m-d H:i:s')
                ];

                $insertOtherDetail = $this->dbCon->prepare($this->queryHandler->insertOtherDetail($odData));
                $insertOtherDetail->execute($odData);

                $odId = $this->dbCon->lastInsertId();

                $this->systemLogs($odId, 'other_details', 'add_other_details', 'add');

                return $odId;
            }
        }

        /**
         * `editEducationalBackground` Saving of educational background from table `educational_backgrounds`.
         * @param  object $input
         * @param  string | int $piId
         * @return mixed
         */
        public function editEducationalBackground($input)
        {
            $ebData = [
                'attainment_level_id'     => isset($input->attainment) ? $input->attainment->id : $input->attainment_level_id,
                'school_id'               => isset($input->school) ?  $input->school->id : $input->school_id,
                'course_id'               => isset($input->course) ?  $input->course->id : $input->course_id,
                'date_graduated'          => $this->formatDate($input->date_graduated),
                'honors'                  => $input->honors,
                'updated_by'              => $_SESSION['user_id'],
                'updated_at'              => date('Y-m-d H:i:s')
            ];

            // printf($ebData);

            $updateEducationalBackground = $this->dbCon->prepare($this->queryHandler->updateEducationalBackground($input->id, $ebData));
            $updateEducationalBackground->execute($ebData);

        }

         /**
         * `saveEducationalBackground` Saving of educational background from table `educational_backgrounds`.
         * @param  object $input
         * @param  string | int $piId
         * @return mixed
         */
        public function saveEducationalBackground($input, $piId)
        {
            $ebData = [
                'personal_information_id' => $piId,
                'attainment_level_id'     => empty($input->attainment->id) ? '' :$input->attainment->id,
                'school_id'               => empty($input->school->id) ? '' : $input->school->id,
                'course_id'               => empty($input->course->id) ? '' : $input->course->id,
                'date_graduated'          => empty($input->date_graduated) ? '' : $this->formatDate($input->date_graduated),
                'honors'                  => empty($input->honors) ? '' : $input->honors,
                'created_by'              => $_SESSION['user_id'],
                'updated_by'              => $_SESSION['user_id'],
                'created_at'              => date('Y-m-d H:i:s'),
                'updated_at'              => date('Y-m-d H:i:s')
            ];

            $insertEducationalBackground = $this->dbCon->prepare($this->queryHandler->insertEducationalBackground($ebData));
            $insertEducationalBackground->execute($ebData);

            $ebId = $this->dbCon->lastInsertId();

            $this->systemLogs($ebId, 'educational_backgrounds', 'add_educational_background', 'add');

            return $ebId;
        }


        /**
         * `editLicenseCertificate` Saving of license certificate from table `license_certificates`.
         * @param  object $input
         * @param  string | int $piId
         * @return mixed
         */
        public function editLicenseCertificate($input)
        {
            $lcData = [
                'name'                    => isset($input->name) ? ucwords(strtolower($input->name)) : ucwords(strtolower($input->name)),
                'date_taken'              => $this->formatDate($input->date_taken),
                'rating'                  => $input->rating,
                'updated_by'              => $_SESSION['user_id'],
                'updated_at'              => date('Y-m-d H:i:s')
            ];

            // print_r($lcData);
            // die();

            $updateLicenseCertificate = $this->dbCon->prepare($this->queryHandler->updateLicenseCertificate($input->id, $lcData));
            $updateLicenseCertificate->execute($lcData);

        }

        /**
         * `saveLicenseCertificate` Saving of license certificate from table `license_certificates`.
         * @param  object $input
         * @param  string | int $piId
         * @return mixed
         */
        public function saveLicenseCertificate($input, $piId)
        {
            $lcData = [
                'personal_information_id' => $piId,
                // 'name'                    => ucwords(strtolower($input->name)),
                // 'date_taken'              => $this->formatDate($input->date_taken),
                'name'                    => empty(ucwords(strtolower($input->name))) ? '' : ucwords(strtolower($input->name)),
                'date_taken'              => empty($input->date_taken) ? '' : $this->formatDate($input->date_taken),
                'rating'                  => empty($input->rating) ? '' : $input->rating,
                'created_by'              => $_SESSION['user_id'],
                'updated_by'              => $_SESSION['user_id'],
                'created_at'              => date('Y-m-d H:i:s'),
                'updated_at'              => date('Y-m-d H:i:s')
            ];

            $insertLicenseCertificate = $this->dbCon->prepare($this->queryHandler->insertLicenseCertificate($lcData));
            $insertLicenseCertificate->execute($lcData);

            $lcId = $this->dbCon->lastInsertId();

            $this->systemLogs($lcId, 'license_certificates', 'add_license_certificate', 'add');

            return $lcId;
        }


        /**
         * `editEmploymentHistory` Saving of employment history from table `employment_histories`.
         * @param  object $input
         * @param  string | int $piId
         * @return mixed
         */
        public function editEmploymentHistory($input, $piId)
        {
            $ehData = [
                'department'              => ucwords(strtolower($input->department_name)),
                'position'                => ucwords(strtolower($input->position_name)),
                'salary'                  => $this->formatMoney($input->salary_range),
                'from_date'               => $this->formatDate($input->from_date),
                'to_date'                 => $this->formatDate($input->to_date),
                'updated_by'              => $_SESSION['user_id'],
                'updated_at'              => date('Y-m-d H:i:s')
            ];

            $updateEmploymentHistory = $this->dbCon->prepare($this->queryHandler->updateEmploymentHistory($input->id, $ehData));
            $updateEmploymentHistory->execute($ehData);

        }

        /**
         * `saveEmploymentHistory` Saving of employment history from table `employment_histories`.
         * @param  object $input
         * @param  string | int $piId
         * @return mixed
         */
        public function saveEmploymentHistory($input, $piId)
        {
            $ehData = [
                'personal_information_id' => $piId,
                'department'              => empty(ucwords(strtolower($input->department_name))) ? '' : ucwords(strtolower($input->department_name)),
                'position'                => empty(ucwords(strtolower($input->position_name))) ? '' : ucwords(strtolower($input->position_name)),
                'salary'                  => empty($input->salary_range) ? '' : $this->formatMoney($input->salary_range),
                'from_date'               => empty($input->from_date) ? '' : $this->formatDate($input->from_date),
                'to_date'                 => empty($input->to_date) ? '' : $this->formatDate($input->to_date),
                'created_by'              => $_SESSION['user_id'],
                'updated_by'              => $_SESSION['user_id'],
                'created_at'              => date('Y-m-d H:i:s'),
                'updated_at'              => date('Y-m-d H:i:s')
            ];

            $insertEmploymentHistory = $this->dbCon->prepare($this->queryHandler->insertEmploymentHistory($ehData));
            $insertEmploymentHistory->execute($ehData);

            $ehId = $this->dbCon->lastInsertId();

            $this->systemLogs($ehId, 'employment_histories', 'add_employment_history', 'add');

            return $this->formatMoney($input->salary_range);
        }

        /**
         * `editTrainingSeminar` Saving of training seminar from table `training_seminars`.
         * @param  object       $input
         * @param  string | int $piId
         * @return mixed
         */
        public function editTrainingSeminar($input, $piId)
        {
            $tsData = [
                'topic'                   => $input->topic,
                'organizer'               => $input->organizer,
                'from_date'               => $this->formatDate($input->from_date),
                'to_date'                 => $this->formatDate($input->to_date),
                'updated_by'              => $_SESSION['user_id'],
                'updated_at'              => date('Y-m-d H:i:s')
            ];

            $updateTrainingSeminar = $this->dbCon->prepare($this->queryHandler->updateTrainingSeminar($input->id, $tsData));
            $updateTrainingSeminar->execute($tsData);

        }

        /**
         * `saveTrainingSeminar` Saving of training seminar from table `training_seminars`.
         * @param  object       $input
         * @param  string | int $piId
         * @return mixed
         */
        public function saveTrainingSeminar($input, $piId)
        {
            $tsData = [
                'personal_information_id' => $piId,
                'topic'                   => empty($input->topic) ? '' : $input->topic,
                'organizer'               => empty($input->organizer) ? '' : $input->organizer,
                'from_date'               => empty($input->from_date) ? '' : $this->formatDate($input->from_date),
                'to_date'                 => empty($input->to_date) ? '' :$this->formatDate($input->to_date),
                'created_by'              => $_SESSION['user_id'],
                'updated_by'              => $_SESSION['user_id'],
                'created_at'              => date('Y-m-d H:i:s'),
                'updated_at'              => date('Y-m-d H:i:s')
            ];

            $insertTrainingSeminar = $this->dbCon->prepare($this->queryHandler->insertTrainingSeminar($tsData));
            $insertTrainingSeminar->execute($tsData);

            $tsId = $this->dbCon->lastInsertId();

            $this->systemLogs($tsId, 'training_seminars', 'add_training_seminars', 'add');

            return $tsId;
        }

    }