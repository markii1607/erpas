<?php
    namespace App\Controller\TaxDeclaration;

    require_once("TaxDeclarationController.php");

    use App\Controller\TaxDeclaration\TaxDeclarationController as ModuleController;
    use Exception;

    class EditTaxDeclarationController extends ModuleController {
        
        public function getTDDetails($data)
        {
            $output = [
                'td_classifications'    => $this->getTaxDeclarationClassifications($data['id']),
                'revision_years'        => $this->getRevisionYears(),
                'barangays'             => $this->getBarangays(),
                'classifications'       => $this->getClassifications(),
                'td_nos'                => $this->getTDNumbers(),
                'approvers'             => $this->getApproverSets(),
            ];

            return $output;
        }

        public function updateTaxDeclaration($input)
        {
            // print_r($input);
            // die();

            try {
                $this->dbCon->beginTransaction();

                $tdEntryData = [
                    'revision_year_id'     =>   $input->td_no->rev->id,
                    'td_no'                 =>  $this->setTaxDecNo($input->td_no),
                    'pin'                   =>  $this->setPIN($input->pin),
                    'owner'                 =>  $input->owner,
                    'owner_tin'             =>  isset($input->owner_tin) ? (!empty($input->owner_tin) ? $input->owner_tin : null) : null,
                    'owner_address'         =>  $input->owner_address,
                    'beneficiary'           =>  isset($input->beneficiary) ? (!empty($input->beneficiary) ? $input->beneficiary : null) : null,
                    'beneficiary_tin'       =>  isset($input->beneficiary_tin) ? (!empty($input->beneficiary_tin) ? $input->beneficiary_tin : null) : null,
                    'beneficiary_address'   =>  isset($input->beneficiary_address) ? (!empty($input->beneficiary_address) ? $input->beneficiary_address : null) : null,
                    'beneficiary_tel_no'    =>  isset($input->beneficiary_tel_no) ? (!empty($input->beneficiary_tel_no) ? $input->beneficiary_tel_no : null) : null,
                    'prop_location_street'  =>  isset($input->prop_location_street) ? (!empty($input->prop_location_street) ? $input->prop_location_street : null) : null,
                    'barangay_id'           =>  $input->barangay->id,
                    'oct_tct_cloa_no'       =>  isset($input->oct_tct_cloa_no) ? (!empty($input->oct_tct_cloa_no) ? $input->oct_tct_cloa_no : null) : null,
                    'cct'                   =>  isset($input->cct) ? (!empty($input->cct) ? $input->cct : null) : null,
                    'survey_no'             =>  isset($input->survey_no) ? (!empty($input->survey_no) ? $input->survey_no : null) : null,
                    'lot_no'                =>  isset($input->lot_no) ? (!empty($input->lot_no) ? $input->lot_no : null) : null,
                    'block_no'              =>  isset($input->block_no) ? (!empty($input->block_no) ? $input->block_no : null) : null,
                    'dated'                 =>  isset($input->dated) ? (!empty($input->dated) ? $this->formatDate($input->dated) : null) : null,
                    'boundaries'            =>  isset($input->boundaries) ? (!empty($input->boundaries) ? $input->boundaries : null) : null,
                    'boundaries_north'      =>  isset($input->boundaries_north) ? (!empty($input->boundaries_north) ? $input->boundaries_north : null) : null,
                    'boundaries_south'      =>  isset($input->boundaries_south) ? (!empty($input->boundaries_south) ? $input->boundaries_south : null) : null,
                    'boundaries_east'       =>  isset($input->boundaries_east) ? (!empty($input->boundaries_east) ? $input->boundaries_east : null) : null,
                    'boundaries_west'       =>  isset($input->boundaries_west) ? (!empty($input->boundaries_west) ? $input->boundaries_west : null) : null,
                    'property_kind'         =>  $input->property_kind,
                    'description'           =>  isset($input->description) ? (!empty($input->description) ? $input->description : null) : null,
                    'no_of_storey'          =>  isset($input->no_of_storey) ? (!empty($input->no_of_storey) ? $input->no_of_storey : null) : null,
                    'others_specified'      =>  isset($input->others_specified) ? (!empty($input->others_specified) ? $input->others_specified : null) : null,
                    'total_market_value'    =>  $input->total_market_value,
                    'total_assessed_value'  =>  $input->total_assessed_value,
                    'total_assessed_value_words'  =>  $input->total_assessed_value_words,
                    'is_taxable'            =>  ($input->tax_exempt == 'taxable') ? 1 : null,
                    'is_exempt'             =>  ($input->tax_exempt == 'exempt')  ? 1 : null,
                    'effectivity'           =>  $input->effectivity,
                    'canceled_td_id'        =>  isset($input->prev_td->id) ? $input->prev_td->id : null,
                    'ordinance_no'          =>  isset($input->ordinance_no) ? (!empty($input->ordinance_no) ? $input->ordinance_no : null) : null,
                    'ordinance_date'        =>  isset($input->ordinance_date) ? (!empty($input->ordinance_date) ? $this->formatDate($input->ordinance_date) : null) : null,
                    'approvers'             =>  json_encode($input->approvers),
                    'memoranda'             =>  isset($input->memoranda) ? (!empty($input->memoranda) ? $input->memoranda : null) : null,
                    'updated_by'            =>  $_SESSION['user_id'],
                    'updated_at'            =>  date('Y-m-d H:i:s'),
                ];

                $updateTaxDec = $this->dbCon->prepare($this->queryHandler->updateTblData('tax_declarations', $input->id, $tdEntryData));
                $updateTaxDec->execute($tdEntryData);
                $this->systemLogs($input->id, 'tax_declarations', 'Tax Declaration of Real Property - EDIT', 'update');

                $prevTD = isset($input->prev_td) ? $input->prev_td : [];
                $this->updateCanceledTd($prevTD, $input->canceled_td);

                foreach ($input->details as $key => $value) {
                    if ($value->data_type == 'new') {
                        $tdClassEntryData = [
                            'tax_declaration_id'    =>  $input->id,
                            'classification_id'     =>  $value->classification->id,
                            'market_value_id'       =>  isset($value->sub_classification) ? $value->sub_classification->id : null,
                            'area'                  =>  $value->area,
                            'unit_measurement'      =>  $value->unit->name,
                            'area_in_sqm'           =>  ($value->unit->name == 'sq.m') ? $value->area : $this->convertValue($value->area, 'sq.m'),
                            'area_in_ha'            =>  ($value->unit->name == 'ha.')  ? $value->area : $this->convertValue($value->area, 'ha.'),
                            'market_value'          =>  $value->market_value,
                            'actual_use'            =>  $value->actual_use,
                            'assessment_level'      =>  $value->assessment_level,
                            'assessed_value'        =>  $value->assessed_value,
                            'created_by'            =>  $_SESSION['user_id'],
                            'created_at'            =>  date('Y-m-d H:i:s'),
                            'updated_by'            =>  $_SESSION['user_id'],
                            'updated_at'            =>  date('Y-m-d H:i:s'),
                        ];
    
                        $insertTaxDecClass = $this->dbCon->prepare($this->queryHandler->insertTblData('tax_declaration_classifications', $tdClassEntryData));
                        $status = $insertTaxDecClass->execute($tdClassEntryData);
                        $newTDClassId = $this->dbCon->lastInsertId();
                        $this->systemLogs($newTDClassId, 'tax_declaration_classifications', 'Tax Declaration of Real Property - EDIT', 'insert');

                    } else if ($value->data_type == 'saved') {
                        $tdClassEntryData = [
                            'classification_id'     =>  $value->classification->id,
                            'market_value_id'       =>  isset($value->sub_classification) ? $value->sub_classification->id : null,
                            'area'                  =>  $value->area,
                            'unit_measurement'      =>  $value->unit->name,
                            'area_in_sqm'           =>  ($value->unit->name == 'sq.m') ? $value->area : $this->convertValue($value->area, 'sq.m'),
                            'area_in_ha'            =>  ($value->unit->name == 'ha.')  ? $value->area : $this->convertValue($value->area, 'ha.'),
                            'market_value'          =>  $value->market_value,
                            'actual_use'            =>  $value->actual_use,
                            'assessment_level'      =>  $value->assessment_level,
                            'assessed_value'        =>  $value->assessed_value,
                            'updated_by'            =>  $_SESSION['user_id'],
                            'updated_at'            =>  date('Y-m-d H:i:s'),
                        ];
    
                        $updateTaxDecClass = $this->dbCon->prepare($this->queryHandler->updateTblData('tax_declaration_classifications', $value->id, $tdClassEntryData));
                        $status = $updateTaxDecClass->execute($tdClassEntryData);
                        $this->systemLogs($value->id, 'tax_declaration_classifications', 'Tax Declaration of Real Property - EDIT', 'update');
                    }
                    
                }
            
                $this->dbCon->commit();

                $output = [
                    'status'    => $status,
                    'rowData'   => $this->getTaxDeclarations($input->id)[0]
                ];

                return $output;
            
            } catch (Exception $exc) {
                echo $exc->getMessage();
                $this->dbCon->rollBack();
            }
            
        }

        public function updateCanceledTd($newEntryPrevTd, $savedEntryPrevTd)
        {
            if (!empty($newEntryPrevTd)) {
                $this->updateTdTable('3', $newEntryPrevTd->id);
                if (!empty($savedEntryPrevTd)) {
                    if ($newEntryPrevTd->id != $savedEntryPrevTd->id) {
                        $this->updateTdTable('1', $savedEntryPrevTd->id);
                    }
                }
            } else {
                if (!empty($savedEntryPrevTd)) {
                    $this->updateTdTable('1', $savedEntryPrevTd->id);
                }
            }
            
        }

        public function updateTdTable($status, $tblId)
        {
            $updateTdEntryData = [
                'status'        => $status,
                'updated_by'    => $_SESSION['user_id'],
                'updated_at'    => date('Y-m-d H:i:s')
            ];

            $updateTaxDec = $this->dbCon->prepare($this->queryHandler->updateTblData('tax_declarations', $tblId, $updateTdEntryData));
            $updateTaxDec->execute($updateTdEntryData);
            $this->systemLogs($tblId, 'tax_declarations', 'Tax Declaration of Real Property - EDIT', 'update status');
        }

        public function archiveTdClassification($input)
        {
            try {
                $this->dbCon->beginTransaction();

                $entryData = [
                    'is_active'     => 0,
                    'updated_by'    => $_SESSION['user_id'],
                    'updated_at'    => date('Y-m-d H:i:s')
                ];

                $archiveTdClass = $this->dbCon->prepare($this->queryHandler->updateTblData('tax_declaration_classifications', $input['id'], $entryData));
                $status = $archiveTdClass->execute($entryData);
                $this->systemLogs($input['id'], 'tax_declaration_classifications', 'Tax Declaration Monitoring - EDIT', 'archive');
            
                $this->dbCon->commit();

                $output = [
                    'status' => $status
                ];

                return $output;
            
            } catch (Exception $exc) {
                echo $exc->getMessage();
                $this->dbCon->rollBack();
            }
            
        }

        public function setTaxDecNo($data)
        {
            $strOutput = $data->rev->year.'-'.$data->mun_code.'-'.$data->brgy->code.'-'.$data->td_code;
            $strOutput .= isset($data->td_code_2) ? (!empty($data->td_code_2) ? '-'.$data->td_code_2 : '') : '';

            return $strOutput;
        }

        public function setPIN($data)
        {
            $strOutput = $data->prov_code.'-'.$data->mun_code.'-'.$data->brgy_code->code.'-'.$data->brgy_code->no_of_sections.'-'.$data->prop_no;
            $strOutput .= isset($data->bldg_no) ? (!empty($data->bldg_no) ? '-'.$data->bldg_no : '') : '';

            return $strOutput;
        }

        public function convertValue($area, $givenUnit)
        {
            if ($givenUnit == 'sq.m') {
                // convert area to ha.
                $convertedValue = floatval($area) * 10000;
            } else if ($givenUnit == 'ha.') {
                // convert area to sq.m
                $convertedValue = floatval($area) * 0.0001;
            }
            
            return $convertedValue;
        }
    }