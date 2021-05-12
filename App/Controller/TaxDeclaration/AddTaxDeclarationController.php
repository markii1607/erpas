<?php
    namespace App\Controller\TaxDeclaration;

    require_once("TaxDeclarationController.php");

    use App\Controller\TaxDeclaration\TaxDeclarationController as ModuleController;
    use Exception;

    class AddTaxDeclarationController extends ModuleController {
        
        public function getSelectionDetails()
        {
            $tax_dec_nos = $this->getTDNumbers();
            $new_pin     = !empty($tax_dec_nos) ? $this->getMaxSequence($tax_dec_nos, 'pin', 4) : '01';
            
            $output = [
                'revision_years'    => $this->getRevisionYears(),
                'barangays'         => $this->getBarangays(),
                'classifications'   => $this->getClassifications(),
                'td_nos'            => $tax_dec_nos,
                'new_pin'           => $new_pin,
                'approvers'         => $this->getApproverSets(),
            ];

            return $output;
        }

        public function getIncrementingTDNumber($data)
        {
            $tax_dec_nos = $this->getTDNumbers($data['rev_id']);
            
            $output = [
                'new_td_no' => !empty($tax_dec_nos) ? $this->getMaxSequence($tax_dec_nos, 'td_no', 3) : '00001'
            ];

            return $output;
        }

        public function getMvOfSelectedClassification($data)
        {
            $output = [
                'market_values' => $this->getMarketValues($data->classification->id, $data->revision_year->id)
            ];

            return $output;
        }

        public function getMaxSequence($arrayData, $column, $index)
        {
            $intStr = [];
            foreach ($arrayData as $key => $value) {
                $strExplode = explode('-', $value[$column]);
                array_push($intStr, $strExplode[$index]);
            }

            $maxNum = max($intStr);

            $count          = str_pad(intval($maxNum)+1, strlen($maxNum), '0', STR_PAD_LEFT);
            $newIncrement   = $count;

            return $newIncrement;
        }

        public function saveNewTaxDeclaration($input)
        {
            // print_r($this->convertValue(15, 'ha.'));
            // die();

            try {
                $this->dbCon->beginTransaction();

                $tdEntryData = [
                    'revision_year_id'     =>   $input->td_no->rev->id,
                    'td_no'                 =>  $this->setTaxDecNo($input->td_no),
                    'pin'                   =>  $this->setPIN($input->pin),
                    'owner'                 =>  $input->owner,
                    'owner_tin'             =>  isset($input->owner_tin) ? (!empty($input->owner_tin) ? $input->owner_tin : null) : null,
                    'owner_address'         =>  $input->owner_addr,
                    'beneficiary'           =>  isset($input->admin_user) ? (!empty($input->admin_user) ? $input->admin_user : null) : null,
                    'beneficiary_tin'       =>  isset($input->admin_user_tin) ? (!empty($input->admin_user_tin) ? $input->admin_user_tin : null) : null,
                    'beneficiary_address'   =>  isset($input->admin_user_addr) ? (!empty($input->admin_user_addr) ? $input->admin_user_addr : null) : null,
                    'beneficiary_tel_no'    =>  isset($input->admin_user_tel) ? (!empty($input->admin_user_tel) ? $input->admin_user_tel : null) : null,
                    'prop_location_street'  =>  isset($input->loc->no_street) ? (!empty($input->loc->no_street) ? $input->loc->no_street : null) : null,
                    'barangay_id'           =>  $input->loc->brgy->id,
                    'oct_tct_cloa_no'       =>  isset($input->oct_no) ? (!empty($input->oct_no) ? $input->oct_no : null) : null,
                    'cct'                   =>  isset($input->cct) ? (!empty($input->cct) ? $input->cct : null) : null,
                    'survey_no'             =>  isset($input->survey_no) ? (!empty($input->survey_no) ? $input->survey_no : null) : null,
                    'lot_no'                =>  isset($input->lot_no) ? (!empty($input->lot_no) ? $input->lot_no : null) : null,
                    'block_no'              =>  isset($input->blk_no) ? (!empty($input->blk_no) ? $input->blk_no : null) : null,
                    'dated'                 =>  isset($input->dated) ? (!empty($input->dated) ? $this->formatDate($input->dated) : null) : null,
                    'boundaries'            =>  isset($input->boundaries->text) ? (!empty($input->boundaries->text) ? $input->boundaries->text : null) : null,
                    'boundaries_north'      =>  isset($input->boundaries->north) ? (!empty($input->boundaries->north) ? $input->boundaries->north : null) : null,
                    'boundaries_south'      =>  isset($input->boundaries->south) ? (!empty($input->boundaries->south) ? $input->boundaries->south : null) : null,
                    'boundaries_east'       =>  isset($input->boundaries->east) ? (!empty($input->boundaries->east) ? $input->boundaries->east : null) : null,
                    'boundaries_west'       =>  isset($input->boundaries->west) ? (!empty($input->boundaries->west) ? $input->boundaries->west : null) : null,
                    'property_kind'         =>  $input->type->name,
                    'description'           =>  isset($input->type->desc) ? (!empty($input->type->desc) ? $input->type->desc : null) : null,
                    'no_of_storey'          =>  isset($input->type->floors) ? (!empty($input->type->floors) ? $input->type->floors : null) : null,
                    'others_specified'      =>  isset($input->type->specify) ? (!empty($input->type->specify) ? $input->type->specify : null) : null,
                    'total_market_value'    =>  $input->total_market_value,
                    'total_assessed_value'  =>  $input->total_assessed_value,
                    'total_assessed_value_words'  =>  $input->assessed_val_words,
                    'is_taxable'            =>  ($input->tax_exempt == 'taxable') ? 1 : null,
                    'is_exempt'             =>  ($input->tax_exempt == 'exempt')  ? 1 : null,
                    'effectivity'           =>  $input->effectivity,
                    'canceled_td_id'        =>  isset($input->prev_declaration->td_no->id) ? $input->prev_declaration->td_no->id : null,
                    'ordinance_no'          =>  isset($input->ordinance_no) ? (!empty($input->ordinance_no) ? $input->ordinance_no : null) : null,
                    'ordinance_date'        =>  isset($input->ordinance_date) ? (!empty($input->ordinance_date) ? $this->formatDate($input->ordinance_date) : null) : null,
                    'approvers'             =>  json_encode($input->signatories),
                    'memoranda'             =>  isset($input->memoranda) ? (!empty($input->memoranda) ? $input->memoranda : null) : null,
                    'created_by'            =>  $_SESSION['user_id'],
                    'created_at'            =>  date('Y-m-d H:i:s'),
                    'updated_by'            =>  $_SESSION['user_id'],
                    'updated_at'            =>  date('Y-m-d H:i:s'),
                ];

                $insertTaxDec = $this->dbCon->prepare($this->queryHandler->insertTblData('tax_declarations', $tdEntryData));
                $insertTaxDec->execute($tdEntryData);
                $newTaxDecID = $this->dbCon->lastInsertId();
                $this->systemLogs($newTaxDecID, 'tax_declarations', 'Tax Declaration of Real Property - ADD', 'insert');

                // Cancel Tax Dec transaction
                if (isset($input->prev_declaration->td_no)) {
                    if (!empty($input->prev_declaration->td_no)) {
                        $updateTdEntryData = [
                            'status'        => 3,
                            'updated_by'    => $_SESSION['user_id'],
                            'updated_at'    => date('Y-m-d H:i:s')
                        ];

                        $updateTaxDec = $this->dbCon->prepare($this->queryHandler->updateTblData('tax_declarations', $input->prev_declaration->td_no->id, $updateTdEntryData));
                        $updateTaxDec->execute($updateTdEntryData);
                        $this->systemLogs($input->prev_declaration->td_no->id, 'tax_declarations', 'Tax Declaration of Real Property - ADD', 'update');
                    }
                }

                foreach ($input->details as $key => $value) {
                    $tdClassEntryData = [
                        'tax_declaration_id'    =>  $newTaxDecID,
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
                    $this->systemLogs($newTDClassId, 'tax_declaration_classifications', 'Tax Declaration of Real Property - ADD', 'insert');
                }
            
                $this->dbCon->commit();

                $output = [
                    'status'    => $status,
                    'rowData'   => $this->getTaxDeclarations($newTaxDecID)[0]
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