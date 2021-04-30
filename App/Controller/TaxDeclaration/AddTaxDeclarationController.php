<?php
    namespace App\Controller\TaxDeclaration;

    require_once("TaxDeclarationController.php");

    use App\Controller\TaxDeclaration\TaxDeclarationController as ModuleController;
    use Exception;

    class AddTaxDeclarationController extends ModuleController {
        
        public function saveNewTaxDeclaration($input)
        {
            print_r($input);
            die();
            try {
                $this->dbCon->beginTransaction();

                $tdEntryData = [
                    'td_no'                 =>  '',
                    'pin'                   =>  '',
                    'owner'                 =>  '',
                    'owner_tin'             =>  '',
                    'owner_address'         =>  '',
                    'beneficiary'           =>  '',
                    'beneficiary_tin'       =>  '',
                    'beneficiary_address'   =>  '',
                    'beneficiary_tel_no'    =>  '',
                    'property_location'     =>  '',
                    'oct_tct_cloa_no'       =>  '',
                    'cct'                   =>  '',
                    'survey_no'             =>  '',
                    'lot_no'                =>  '',
                    'block_no'              =>  '',
                    'dated'                 =>  '',
                    'boundaries'            =>  '',
                    'property_kind'         =>  '',
                    'description'           =>  '',
                    'no_of_storey'          =>  '',
                    'others_specified'      =>  '',
                    'total_market_value'    =>  '',
                    'total_assessed_value'  =>  '',
                    'is_taxable'            =>  '',
                    'is_exempt'             =>  '',
                    'effectivity'           =>  '',
                    'canceled_td_id'        =>  '',
                    'ordinance_no'          =>  '',
                    'ordinance_date'        =>  '',
                    'approvers'             =>  '',
                    'memoranda'             =>  '',
                    'created_by'            =>  $_SESSION['user_id'],
                    'created_at'            =>  date('Y-m-d H:i:s'),
                    'updated_by'            =>  $_SESSION['user_id'],
                    'updated_at'            =>  date('Y-m-d H:i:s'),
                ];

                $insertTaxDec = $this->dbCon->prepare($this->queryHandler->insertTblData('tax_declarations', $tdEntryData));
                $insertTaxDec->execute($tdEntryData);
                $newTaxDecID = $this->dbCon->lastInsertId();
                $this->systemLogs($newTaxDecID, 'tax_declarations', 'Tax Declaration of Real Property', 'insert');

                foreach ($input->classifications as $key => $value) {
                    $tdClassEntryData = [
                        'tax_declaration_id'    =>  '',
                        'classification_id'     =>  '',
                        'area'                  =>  '',
                        'unit_measurement'      =>  '',
                        'area_in_sqm'           =>  '',
                        'area_in_ha'            =>  '',
                        'market_value'          =>  '',
                        'actual_use'            =>  '',
                        'assessment_level'      =>  '',
                        'assessed_value'        =>  '',
                        'created_by'            =>  $_SESSION['user_id'],
                        'created_at'            =>  date('Y-m-d H:i:s'),
                        'updated_by'            =>  $_SESSION['user_id'],
                        'updated_at'            =>  date('Y-m-d H:i:s'),
                    ];

                    $insertTaxDecClass = $this->dbCon->prepare($this->queryHandler->insertTblData('tax_declaration_classifications', $tdClassEntryData));
                    $status = $insertTaxDecClass->execute($tdClassEntryData);
                    $newTDClassId = $this->dbCon->lastInsertId();
                    $this->systemLogs($newTDClassId, 'tax_declaration_classifications', 'Tax Declaration of Real Property', 'insert');
                }
            
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
    }