<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//require APPPATH . '/libraries/REST_Controller.php';

// use namespace
use Restserver\Libraries\REST_Controller;

/**
 * This is an code of a few basic user interaction methods
 * all done
 *
 * @package         CodeIgniter
 * @subpackage      aref24 Project
 * @category        Controller
 * @author          Mohammad Hoseini, Abolfazl Ganji
 * @license         MIT
 * @link            https://aref24.ir
 */
class Creatdoc extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
    }

    public function index_post()
    {
        if(isset($this->input->request_headers()['Authorization']))$employee_token_str = $this->input->request_headers()['Authorization'];
           $this->load->helper('my_helper');
        $this->load->model('B_user');
        $this->load->model('B_db');
        $this->load->helper('time_helper');
        $command = $this->post("command");

        if($this->B_user->checkrequestip('getrequestemployee',$command,get_client_ip(),50,50)) {
            if ($command == "create_paydoc_date") {
                $employeetoken = checkpermissionemployeetoken($employee_token_str, 'new', 'create_doc');
                if ($employeetoken[0] == 'ok') {
                    $min_date = $this->post("min_date");
                    $max_date = $this->post("max_date");
                    $datenow = strtotime($min_date);
                    $date = jdate('Y/m/d', $datenow, "", '', 'en');
                    $year = jdate('Y', $datenow, "", '', 'en');
                    $sanad_head='سند های پیش پرداختی تولید شده در تاریخ 1-'.$date ;
                    $sql1 = "select * from pey_tb,request_tb,company_tb,fieldinsurance_tb where pey_date>='$min_date' AND pey_date<'$max_date'  AND pey_request_id=request_id AND request_fieldinsurance=fieldinsurance AND request_company_id=company_id AND pey_backcode=0 AND  pey_id NOT IN (SELECT createdoc_foreign_id FROM createdoc1_tb WHERE createdoc_foreign_mode=1)  ";
                    $result = $this->B_db->run_query($sql1);
                    $output = array();
                    $doc_id=get_sanad_qroup()+1;
                    $radif=1;
                    foreach ($result as $row) {
                        $record = array();

                        $desc = "درخواست شماره " . $row['request_id'] . " در رشته -" . $row['fieldinsurance_fa'] . " - با شرکت " . $row['company_name'];
                        if ($row['pey_mode'] == 'pey') {
                            $desc .= " پرداخت اصلی با کد رهگیری " . $row['pey_refrenceid'];
                        } else {
                            $desc .= " پرداخت کسری واریزی با کد رهگیری " . $row['pey_refrenceid'];
                        }
                        $Records = '<Records Tarikh="' . $date . '" Code="' . $row['pey_refrenceid'] . '" Tedad="1" Fee="" Sharh="" />';

                        $query1 = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf, CodeTaf2, Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed                           , Bes, ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup,sanad_head)
                                                                VALUES( 1     , $year   , 0   , $radif  , '$date', 10       , 20     , 1      , 1       , ''    , ''           , 1            , '$desc'  ,  " . $row['pey_amount'] . ",    0,      0   ,     0, '$Records', 0  ,$doc_id  ,'$sanad_head' );";
                        $result1 = $this->B_db->run_query_put($query1);
                        $radif++;
                        $query2 = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                    , CodeTaf2, Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,    Bes                   , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup,sanad_head)
                                                             VALUES( 1     , $year   , 0   , $radif  , '$date', 34       , 1    , " . $row['request_user_id'] . " , ''      , ''    , 9            , ''            , '$desc'  ,    0,  " . $row['pey_amount'] . ",      0   ,     0, '$Records', 0    ,$doc_id,'$sanad_head');";
                        $result2 = $this->B_db->run_query_put($query2);
                        $radif++;
                        $migrate_id = $this->db->insert_id();

                        $query3 = "INSERT INTO createdoc1_tb
                                      ( createdoc_foreign_id, createdoc_foreign_mode, createdoc_foreign_date, createdoc_foreign_employee_id, createdoc_doc_id)
                                         VALUES (".$row['pey_id'].", 1, now(), ".$employeetoken[1].", $migrate_id );"  ;
                        $result3 = $this->B_db->run_query_put($query3);

                        $record['migrate_id'] = $migrate_id;
                        $output[] = $record;
                    }

                    echo json_encode(array('result' => "ok"
                    , "data" => $output
                    , 'desc' => 'سند اضافه شد'.$sql1), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                } else {
                    echo json_encode(array('result' => $employeetoken[0]
                    , "data" => $employeetoken[1]
                    , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }
            else
                if ($command == "create_doc_request_date") {
                    $employeetoken = checkpermissionemployeetoken($employee_token_str, 'new', 'create_doc');
                    if ($employeetoken[0] == 'ok') {
                        $min_date = $this->post("min_date");
                        $max_date = $this->post("max_date");
                        $datenow = strtotime($min_date);
                        $date = jdate('Y/m/d', $datenow, "", '', 'en');
                        $year = jdate('Y', $datenow, "", '', 'en');
                        $sanad_head='سند های درخواست های بیمه تولید شده در تاریخ 2-'.$date ;

                        $sql1 = "select * from request_financial_approval_tb,request_tb,company_tb,fieldinsurance_tb,requst_ready_tb where  request_financial_approval_date>='$min_date' AND request_financial_approval_date<'$max_date'  AND requst_ready_request_id=request_id AND request_financial_approval_request_id=request_id AND request_fieldinsurance=fieldinsurance AND request_company_id=company_id  AND request_financial_approval_id NOT IN (SELECT createdoc_foreign_id FROM createdoc1_tb WHERE createdoc_foreign_mode=2) ";
                        $result = $this->B_db->run_query($sql1);
                        $output = array();
                        $doc_id=get_sanad_qroup();
                        $doc_id=$doc_id+1;
                        $radif = 1;
                        foreach ($result as $row) {
                            $record = array();

                            $desc = "درخواست شماره " . $row['request_id'] . " در رشته -" . $row['fieldinsurance_fa'] . " - با شرکت " . $row['company_name'];
                            //*************************************************************************************************************
                            $Records = '<Records Tarikh="' . $date . '" Code="' . $row['pey_refrenceid'] . '" Tedad="1" Fee="123456" Sharh="fffffff" />';

                            $request_id = $row['request_id'];


                            $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh    , CodeKol, CodeMoin  , CodeTaf                             , CodeTaf2  , Ready   , CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed                                                 ,Bes, ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup,sanad_head)
                                                                 VALUES( 1     , $year   , 0   , $radif  , '$date', 34       , 1       , " . $row['request_user_id'] . "      , ''        ,  ''   , 9           ,  ''             , '$desc' ,     " . $row['request_financial_approval_price'] . ",0   ,      0   ,     0, '$Records', 0    ,$doc_id ,'$sanad_head');";
                            $result = $this->B_db->run_query_put($query);


                            $query01 = "select sum(user_pey_amount) AS discount_code from user_pey_tb where user_pey_mode='discount_code' AND user_pey_request_id=" . $request_id;
                            $result01 = $this->B_db->run_query($query01);
                            $user_pey01 = $result01[0];
                            if ($user_pey01['discount_code']) {
                                $radif=$radif+1;
                                $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                             , CodeTaf2, Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed                      , Bes, ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup ,sanad_head)
                                                                           VALUES( 1     , $year   , 0  , $radif , '$date', 84       , 12     , " . $row['fieldinsurance_id'] . "      , ''       , ''    , 14           , ''           , '$desc'  ,  " . $user_pey01['discount_code'] . ",    0,      0   ,     0, '$Records', 0    ,$doc_id  ,'$sanad_head');";
                                $result = $this->B_db->run_query_put($query);

                            }


                            $query03 = "select sum(user_pey_amount) AS managdiscount from user_pey_tb where user_pey_mode='managdiscount' AND user_pey_request_id=" . $request_id;
                            $result03 = $this->B_db->run_query($query03);
                            $user_pey03 = $result03[0];
                            if ($user_pey03['managdiscount']) {
                                $radif=$radif+1;
                                $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                             , CodeTaf2, Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed                      , Bes, ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup,sanad_head)
                                                                          VALUES( 1     , $year   , 0   , $radif  , '$date', 84       , 11     , " . $row['fieldinsurance_id'] . "     , ''       , ''    , 14           , ''           , '$desc'  ,  " . $user_pey01['managdiscount'] . ",    0,      0   ,     0, '$Records', 0    ,$doc_id  ,'$sanad_head');";
                                $result = $this->B_db->run_query_put($query);
                            }


//                            $query04 = "select sum(user_pey_amount) AS user_wallet from user_pey_tb where user_pey_mode='user_wallet' AND user_pey_request_id=" . $request_id;
//                            $result04 = $this->B_db->run_query($query04);
//                            $user_pey04 = $result04[0];
//                            if ($user_pey04['user_wallet']) {
//                                $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                    , CodeTaf2, Ready, CodeTaf_Group, CodeTaf2_Group, Sharh     , Bed                             , Bes, ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
//                                                                          VALUES( 1     , $year   , 0   , $radif  , '$date', 34       , 1    , " . $row['request_user_id'] . "  , ''      , ''    , 9            , ''            , '$desc'  ,  " . $user_pey04['user_wallet'] . " , 0,       0   ,     0, '$Records', 0     );";
//                                $result = $this->B_db->run_query_put($query);
//                                $radif=$radif+1;
//                            }


                            if ($row['request_financial_approval_difference_price'] > 0) {
                                $radif=$radif+1;
                                $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin    , CodeTaf                                 , CodeTaf2, Ready, CodeTaf_Group, CodeTaf2_Group         , Sharh     , Bed                                                   , Bes, ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup,sanad_head)
                                                                          VALUES( 1     , $year   , 0   , $radif  , '$date', 84       , 13    , " . $row['fieldinsurance_id'] . "  , ''      , ''    , 14            , ''            , '$desc'  ,  " . $row['request_financial_approval_difference_price'] . " , 0,       0   ,     0, '$Records', 0   ,$doc_id   ,'$sanad_head');";
                                $result = $this->B_db->run_query_put($query);

                            }

                            $query11 = "select * from user_pey_tb,instalment_check_tb,instalment_conditions_tb where instalment_conditions_id=instalment_check_condition_id AND user_pey_mode='instalment' AND instalment_check_user_pey_id=user_pey_id AND (instalment_conditions_mode_id=1 OR instalment_conditions_mode_id=3) AND user_pey_request_id=" . $request_id;
                            $result11 = $this->B_db->run_query($query11);
                            foreach ($result11 as $row11) {
                                $radif=$radif+1;
                                $desc2 = $desc . " با شماره چک " . $row11['instalment_check_num'] . "   بتاریخ " . $row11['instalment_check_date'] . "   بشرح " . $row11['instalment_check_desc'];
                                $query112 = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                    , CodeTaf2, Ready, CodeTaf_Group, CodeTaf2_Group, Sharh     , Bed                             , Bes, ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup,sanad_head)
                                                                          VALUES( 1     , $year   , 0   , $radif  , '$date', 14       , 3   , " . $row['request_user_id'] . "  , ''      , ''    , 9            , ''            , '$desc2'  ,  " . $row11['user_pey_amount'] . " , 0,       0   ,     0, '$Records', 0    ,$doc_id  ,'$sanad_head');";
                                $result = $this->B_db->run_query_put($query112);
                            }

                            $query12 = "select * from user_pey_tb,instalment_check_tb,instalment_conditions_tb,organ_contract_tb,organ_tb where organ_contract_organ_id=organ_id AND instalment_condition_contract_id=organ_contract_id AND instalment_conditions_id=instalment_check_condition_id AND user_pey_mode='instalment' AND instalment_check_user_pey_id=user_pey_id AND instalment_conditions_mode_id=2 AND user_pey_request_id=" . $request_id;
                            $result12 = $this->B_db->run_query($query12);
                            foreach ($result12 as $row12) {
                                $radif=$radif+1;
                                $desc12 = $desc . " کسر از حقوق  بتاریخ " . $row12['instalment_check_date'] . "   بشرح " . $row12['instalment_check_desc'] . " سازمان " . $row12['organ_name'];
                                $query122 = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif  , Tarikh , CodeKol  , CodeMoin, CodeTaf                     , CodeTaf2                       , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh     , Bed                                  , Bes, ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup,sanad_head)
                                                                            VALUES( 1     , $year   , 0   , $radif  , '$date', 14       , 2       , " . $row12['organ_id'] . "  ," . $row['request_user_id'] . " , ''    , ''            , 5            , '$desc12'  ,  " . $row12['user_pey_amount'] . " , 0,       0   ,     0, '$Records', 0  ,$doc_id    ,'$sanad_head');";
                                $result = $this->B_db->run_query_put($query122);

                            }

                            $radif=$radif+1;
                            $query1 = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh    , CodeKol, CodeMoin                           , CodeTaf                             , CodeTaf2                            , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,                 Bes                               , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup,sanad_head)
                                                                 VALUES( 1     , $year   , 0   , $radif  , '$date', 30       , " . $row['company_id'] . " , " . $row['request_agent_id'] . "      , " . $row['fieldinsurance_id'] . " , ''   , ''           , 2             , '$desc' ,    0 ,  " . $row['request_financial_approval_price'] . ",      0   ,     0, '$Records', 0   ,$doc_id  ,'$sanad_head' );";
                            $result1 = $this->B_db->run_query_put($query1);

                            $migrate_id = $this->db->insert_id();

                            $query3 = "INSERT INTO createdoc1_tb
                                      ( createdoc_foreign_id              , createdoc_foreign_mode, createdoc_foreign_date, createdoc_foreign_employee_id, createdoc_doc_id)
                         VALUES (".$row['request_financial_approval_id'].", 2                     , now()                 , ".$employeetoken[1].", $migrate_id );"  ;
                            $result3 = $this->B_db->run_query_put($query3);

                            //********************************

                            $query110 = "select sum(user_pey_amount) AS overpayment from user_pey_tb where user_pey_mode='overpayment' AND user_pey_request_id=" . $request_id;
                            $result110 = $this->B_db->run_query($query110);
                            $user_pey0 = $result110[0];
                            $overpayment = $user_pey0['overpayment'];

                            $query2 = "select sum(user_pey_amount) AS sumcash from user_pey_tb where not (user_pey_mode = 'instalment')  AND  not (user_pey_mode='overpayment') AND user_pey_request_id=" . $request_id;
                            $result2 = $this->B_db->run_query($query2);
                            $user_pey2 = $result2[0];
                            $user_pey_cash = ($user_pey2['sumcash'] - $overpayment - $row['requst_ready_code_penalty']) * 100 / 109;
                            $porsant = intval($user_pey_cash * $row['fieldinsurance_commission'] / 100);
                            $radif=$radif+1;

                            $desc12 = " کارمزد قسمت نقد  " . $desc;
                            $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin                    , CodeTaf                           , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,       Bes, ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup,sanad_head)
                                                                  VALUES( 1     , $year   , 0   , $radif  , '$date', 13       , " . $row['company_id'] . "   , " . $row['fieldinsurance_id'] . "      , ''       , ''   , 14          , ''            ,'$desc12',    $porsant ,  0,      0   ,     0, '$Records', 0   ,$doc_id   ,'$sanad_head');";
                            $result = $this->B_db->run_query_put($query);
                            $radif=$radif+1;

                            $daramad = intval($porsant * 0.917431);
                            $desc12 = "   درآمد از قسمت نقد بیمه نامه  " . $desc;
                            $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin , CodeTaf                    , CodeTaf2                      , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  , Bes         , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup,sanad_head)
                                                                 VALUES( 1     , $year   , 0   , $radif  , '$date', 60       , 1       , " . $row['company_id'] . "      ," . $row['fieldinsurance_id'] . " , ''   , ''          , 2             ,'$desc12',  0    ,    $daramad ,      0   ,     0, '$Records', 0 ,$doc_id    ,'$sanad_head' );";
                            $result = $this->B_db->run_query_put($query);
                            $radif=$radif+1;

                            $arzeshafzode = intval($porsant - $daramad);
                            $desc12 = "   ارزش افزوده کارمزد قسمت نقد  " . $desc;
                            $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                     , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,       Bes        , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup,sanad_head)
                                                                VALUES( 1     , $year   , 0   , $radif  , '$date', 34       ,3        , " . $row['company_id'] . "      , ''       , ''   , 7           ,  ''           ,'$desc12',  0   ,    $arzeshafzode ,      0   ,     0, '$Records', 0  ,$doc_id    ,'$sanad_head');";
                            $result = $this->B_db->run_query_put($query);

                            //*************************************************************************************************************


                            $migrate_id = $this->db->insert_id();


                            $query3 = "INSERT INTO createdoc1_tb
                                      ( createdoc_foreign_id              , createdoc_foreign_mode, createdoc_foreign_date, createdoc_foreign_employee_id, createdoc_doc_id)
                         VALUES (".$row['request_financial_approval_id'].", 2                     , now()                 , ".$employeetoken[1].", $migrate_id );"  ;
                            $result3 = $this->B_db->run_query_put($query3);

                            $record['migrate_id'] = $migrate_id;
                            $output[] = $record;
                        }

                        echo json_encode(array('result' => "ok"
                        , "data" => $output
                        , 'desc' => 'سند اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    } else {
                        echo json_encode(array('result' => $employeetoken[0]
                        , "data" => $employeetoken[1]
                        , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                }
                else
                    if ($command == "create_comission_doc_date") {
                        $employeetoken = checkpermissionemployeetoken($employee_token_str, 'new', 'create_doc');
                        if ($employeetoken[0] == 'ok') {
                            $output = array();

//*************************************************************************************************************************
                            $doc_id=get_sanad_qroup()+1;

                            $min_date = $this->post("min_date");
                            $max_date = $this->post("max_date");
                            $datenow = strtotime($min_date);
                            $date = jdate('Y/m/d', $datenow, "", '', 'en');
                            $year = jdate('Y', $datenow, "", '', 'en');
                            $sanad_head='سند های کمیسیون ها تولید شده در تاریخ 3-'.$date ;
                            $radif=1;

                            $query10 = "SELECT * FROM peycommision_leader_tb,user_wallet_tb,user_tb,request_tb,fieldinsurance_tb 
                                        WHERE user_wallet_timestamp>='$min_date' AND user_wallet_timestamp<'$max_date'  AND fieldinsurance=request_fieldinsurance AND request_id=peycommision_leader_request_id AND user_id=user_wallet_user_id AND user_wallet_id=peycommision_leader_user_wallet_id AND peycommision_leader_doc_id=0 AND peycommision_leader_id NOT IN (SELECT createdoc_foreign_id FROM createdoc1_tb WHERE createdoc_foreign_mode=3)";
                            $result = $this->B_db->run_query($query10);
                            foreach ($result as $row) {
                                $record = array();
                                $desc12 = " کارمزد پرداخت شده به هماهنگ کننده" . $row['user_wallet_detail'] . " در رشته -" . $row['fieldinsurance_fa'] . " - با شرکت " . $row['company_name'];
                                $Records = '<Records Tarikh="' . $date . '" Code="' . $row['pey_refrenceid'] . '" Tedad="1" Fee="123456" Sharh="fffffff" />';

                                $query1 = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                               , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh      , Bed                                 ,       Bes        , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup,sanad_head)
                                                                     VALUES( 1     , $year   , 0   , $radif  , '$date', 84       , 6     , " . $row['fieldinsurance_id'] . "      , ''       , ''   , 14            , ''            ,'$desc12',    " . $row['user_wallet_amount'] . ",  0 ,      0   ,     0, '$Records', 0     ,$doc_id,'$sanad_head');";
                                $result1 = $this->B_db->run_query_put($query1);
                                $radif=$radif+1;

                                $query2 = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                              , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,       Bes                 , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup,sanad_head)
                                                                      VALUES( 1     , $year   , 0   , $radif  , '$date', 34       , 1     , " . $row['request_user_id'] . "      , ''       , ''   , 9            , ''            ,'$desc12',    0 ,  " . $row['user_wallet_amount'] . ",      0   ,     0, '$Records', 0     ,$doc_id,'$sanad_head');";
                                $result = $this->B_db->run_query_put($query2);
                                $radif=$radif+1;


                                $migrate_id = $this->db->insert_id();

                                $query3 = "INSERT INTO createdoc1_tb
                                      ( createdoc_foreign_id              , createdoc_foreign_mode, createdoc_foreign_date, createdoc_foreign_employee_id, createdoc_doc_id)
                         VALUES (".$row['peycommision_leader_id'].", 3                     , now()                 , ".$employeetoken[1].", $migrate_id );"  ;
                                $result3 = $this->B_db->run_query_put($query3);


                                $record['migrate_id'] = $migrate_id;
                                $output[] = $record;
                            }
//*************************************************************************************************************************
                            $query11 = "SELECT * FROM peycommision_marketer_tb,user_wallet_tb,user_tb,request_tb,fieldinsurance_tb 
                                        WHERE  user_wallet_timestamp>='$min_date' AND user_wallet_timestamp<'$max_date'  AND fieldinsurance=request_fieldinsurance AND request_id=peycommision_marketer_request_id AND user_id=user_wallet_user_id AND user_wallet_id=peycommision_marketer_user_wallet_id AND  peycommision_marketer_id NOT IN (SELECT createdoc_foreign_id FROM createdoc1_tb WHERE createdoc_foreign_mode=4)";
                            $result11 = $this->B_db->run_query($query11);
                            foreach ($result11 as $row) {
                                $record = array();
                                $desc12 = " کارمزد پرداخت شده به همکار فروش " . $row['user_wallet_detail'] . " در رشته -" . $row['fieldinsurance_fa'] . " - با شرکت " . $row['company_name'];
                                $Records = '<Records Tarikh="' . $date . '" Code="' . $row['pey_refrenceid'] . '" Tedad="1" Fee="123456" Sharh="fffffff" />';


                                $query1 = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                              , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh      , Bed                ,       Bes        , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup,sanad_head)
                                                                       VALUES( 1     , $year   , 0   , $radif  , '$date', 84       , 6     , " . $row['fieldinsurance_id'] . "      , ''       , ''   , 14            , ''            ,'$desc12',    " . $row['user_wallet_amount'] . ",  0 ,      0   ,     0, '$Records', 0  ,$doc_id   ,'$sanad_head');";
                                $result1 = $this->B_db->run_query_put($query1);
                                $radif=$radif+1;

                                $query2 = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                              , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,       Bes                 , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup,sanad_head)
                                                                      VALUES( 1     , $year   , 0   , $radif  , '$date', 34       , 1     , " . $row['request_user_id'] . "      , ''       , ''   , 9            , ''            ,'$desc12',    0 ,  " . $row['user_wallet_amount'] . ",      0   ,     0, '$Records', 0  ,$doc_id  ,'$sanad_head' );";
                                $result2 = $this->B_db->run_query_put($query2);
                                $radif=$radif+1;

                                $migrate_id = $this->db->insert_id();



                                $query3 = "INSERT INTO createdoc1_tb
                                      ( createdoc_foreign_id              , createdoc_foreign_mode, createdoc_foreign_date, createdoc_foreign_employee_id, createdoc_doc_id)
                         VALUES (".$row['peycommision_marketer_id'].", 4                     , now()                 , ".$employeetoken[1].", $migrate_id );"  ;
                                $result3 = $this->B_db->run_query_put($query3);

                                $record['migrate_id'] = $migrate_id;
                                $output[] = $record;
                            }
//***************************************************************************************************************************
                            $query10 = "SELECT * FROM peybackcommision_leader_tb,user_wallet_tb,user_tb,request_tb,fieldinsurance_tb 
                                        WHERE  user_wallet_timestamp>='$min_date' AND user_wallet_timestamp<'$max_date'  AND fieldinsurance=request_fieldinsurance AND request_id=peybackcommision_leader_request_id AND user_id=user_wallet_user_id AND user_wallet_id=peybackcommision_leader_user_wallet_id AND  peybackcommision_leader_id NOT IN (SELECT createdoc_foreign_id FROM createdoc1_tb WHERE createdoc_foreign_mode=5)";
                            $result = $this->B_db->run_query($query10);
                            foreach ($result as $row) {
                                $record = array();
                                $desc12 = " بازگشت کارمزد پرداخت شده به هماهنگ کننده" . $row['user_wallet_detail'] . " در رشته -" . $row['fieldinsurance_fa'] . " - با شرکت " . $row['company_name'];
                                $Records = '<Records Tarikh="' . $date . '" Code="' . $row['pey_refrenceid'] . '" Tedad="1" Fee="123456" Sharh="fffffff" />';

                                $query1 = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                              , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,       Bes                 , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup,sanad_head)
                                                                       VALUES( 1     , $year   , 0   , $radif  , '$date', 34       , 1     , " . $row['request_user_id'] . "      , ''       , ''   , 9            , ''            ,'$desc12',  " . $row['user_wallet_amount'] . ",    0 ,      0   ,     0, '$Records', 0  ,$doc_id   ,'$sanad_head');";
                                $result1 = $this->B_db->run_query_put($query1);
                                $radif=$radif+1;

                                $query2 = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                              , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh      , Bed ,       Bes                          , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup,sanad_head)
                                                                       VALUES( 1     , $year   , 0   , $radif  , '$date', 84       , 6     , " . $row['fieldinsurance_id'] . "      , ''       , ''   , 14            , ''            ,'$desc12'     ,  0  ,    " . $row['user_wallet_amount'] . " ,      0   ,     0, '$Records', 0  ,$doc_id  ,'$sanad_head' );";
                                $result2 = $this->B_db->run_query_put($query2);
                                $radif=$radif+1;


                                $migrate_id = $this->db->insert_id();

                                $query3 = "INSERT INTO createdoc1_tb
                                      ( createdoc_foreign_id              , createdoc_foreign_mode, createdoc_foreign_date, createdoc_foreign_employee_id, createdoc_doc_id)
                         VALUES (".$row['peybackcommision_leader_id'].", 5                     , now()                 , ".$employeetoken[1].", $migrate_id );"  ;
                                $result3 = $this->B_db->run_query_put($query3);



                                $record['migrate_id'] = $migrate_id;
                                $output[] = $record;
                            }
//*************************************************************************************************************************
                            $query11 = "SELECT * FROM peybackcommision_marketer_tb,user_wallet_tb,user_tb,request_tb,fieldinsurance_tb 
                                        WHERE  user_wallet_timestamp>='$min_date' AND user_wallet_timestamp<'$max_date'  AND fieldinsurance=request_fieldinsurance AND request_id=peybackcommision_marketer_request_id AND user_id=user_wallet_user_id AND user_wallet_id=peybackcommision_marketer_user_wallet_id AND  peybackcommision_marketer_id NOT IN (SELECT createdoc_foreign_id FROM createdoc1_tb WHERE createdoc_foreign_mode=6)";
                            $result11 = $this->B_db->run_query($query11);
                            foreach ($result11 as $row) {
                                $record = array();
                                $desc12 = " بازگشت کارمزد پرداخت شده به همکار فروش " . $row['user_wallet_detail'] . " در رشته -" . $row['fieldinsurance_fa'] . " - با شرکت " . $row['company_name'];
                                $Records = '<Records Tarikh="' . $date . '" Code="' . $row['pey_refrenceid'] . '" Tedad="1" Fee="123456" Sharh="fffffff" />';


                                $query1 = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                              , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,       Bes                 , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup,sanad_head)
                                                                       VALUES( 1     , $year   , 0   , $radif  , '$date', 34       , 1     , " . $row['request_user_id'] . "      , ''       , ''   , 9            , ''            ,'$desc12',  " . $row['user_wallet_amount'] . ",    0 ,      0   ,     0, '$Records', 0   ,$doc_id ,'$sanad_head' );";
                                $result1 = $this->B_db->run_query_put($query1);
                                $radif=$radif+1;

                                $query2 = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                              , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh      , Bed ,       Bes                          , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup,sanad_head)
                                                                       VALUES( 1     , $year   , 0   , $radif  , '$date', 84       , 6     , " . $row['fieldinsurance_id'] . "      , ''       , ''   , 14            , ''            ,'$desc12'     ,  0  ,    " . $row['user_wallet_amount'] . " ,      0   ,     0, '$Records', 0  ,$doc_id  ,'$sanad_head' );";
                                $result2 = $this->B_db->run_query_put($query2);
                                $radif=$radif+1;


                                $migrate_id = $this->db->insert_id();

                                $query3 = "INSERT INTO createdoc1_tb
                                      ( createdoc_foreign_id              , createdoc_foreign_mode, createdoc_foreign_date, createdoc_foreign_employee_id, createdoc_doc_id)
                         VALUES (".$row['peybackcommision_marketer_id'].", 6                     , now()                 , ".$employeetoken[1].", $migrate_id );"  ;


                                $result3 = $this->B_db->run_query_put($query3);
                                $record['migrate_id'] = $migrate_id;
                                $output[] = $record;
                            }
                            //*************************************************************************************************************************

                            echo json_encode(array('result' => "ok"
                            , "data" => $output
                            , 'desc' => 'سند اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
//*************************************************************************************************************************
                        } else {
                            echo json_encode(array('result' => $employeetoken[0]
                            , "data" => $employeetoken[1]
                            , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    }
                    else
                        if ($command == "create_doc_installment_date") {
                            $employeetoken = checkpermissionemployeetoken($employee_token_str, 'new', 'create_doc');
                            if ($employeetoken[0] == 'ok') {
                                $output = array();

//*************************************************************************************************************************
                                $min_date = $this->post("min_date");
                                $max_date = $this->post("max_date");
                                $datenow = strtotime($min_date);
                                $date = jdate('Y/m/d', $datenow, "", '', 'en');
                                $year = jdate('Y', $datenow, "", '', 'en');
                                $sanad_head='سند های تعهد های پاس شده در تاریخ 4-'.$date ;
                                $radif = 1;

                                $query = "select * from instalment_check_tb,instalment_conditions_tb,user_tb,agent_tb,request_tb,company_tb,fieldinsurance_tb,request_state where 
                                         instalment_check_date_pass>='$min_date' AND instalment_check_date_pass<'$max_date'  AND
                                            instalment_conditions_id=instalment_check_condition_id AND  request_state_id=request_last_state_id AND user_id=request_user_id AND agent_id=request_agent_id AND fieldinsurance=request_fieldinsurance  AND  company_id=request_company_id AND instalment_check_request_id=request_id 
                                         AND( instalment_conditions_mode_id=1 OR  instalment_conditions_mode_id=3) AND instalment_check_pass=1 AND instalment_check_id NOT IN (SELECT createdoc_foreign_id FROM createdoc1_tb WHERE createdoc_foreign_mode=7)";
                                $result = $this->B_db->run_query($query);
                                $doc_id=get_sanad_qroup()+1;
                                foreach ($result as $row) {
                                    $record = array();
                                    $desc12 = "  سند پاس شدن تعهد کاربر " . $row['user_name'] . " " . $row['user_family'] . " در رشته -" . $row['fieldinsurance_fa'] . " - با شرکت " . $row['company_name'] . " با شماره چک " . $row['instalment_check_num'] . " و سند پاس شدن " . $row['instalment_check_doc'];
                                    $Records = '<Records Tarikh="' . $date . '" Code="' . $row['request_id'] . '" Tedad="1" Fee="123456" Sharh="fffffff" />';

                                    $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                             , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh      , Bed                ,       Bes        , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup ,sanad_head)
                                                                          VALUES( 1     , $year   , 0   , $radif  , '$date', 10       , 20     , 1                                    , 4        , ''   , ''            , 1            ,'$desc12',    " . $row['instalment_check_amount'] . ",  0 ,      0   ,     0, '$Records', 0,$doc_id   ,'$sanad_head'  );";
                                    $result = $this->B_db->run_query_put($query);
                                    $radif=$radif+1;

                                    $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                              , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,       Bes                 , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup ,sanad_head)
                                                                       VALUES( 1     , $year   , 0   , $radif  , '$date', 14       , 3     , " . $row['request_user_id'] . "      , ''       , ''   , 9            , ''            ,'$desc12',    0 ,  " . $row['instalment_check_amount'] . ",      0   ,     0, '$Records', 0 ,$doc_id   ,'$sanad_head'  );";
                                    $result = $this->B_db->run_query_put($query);
                                    $radif=$radif+1;

                                    $porsant = intval($row['instalment_check_amount'] * $row['fieldinsurance_commission'] / 100);

                                    $desc12 = " کارمزد قسمت نقد  " . $desc12;
                                    $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin                    , CodeTaf                           , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,       Bes, ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup ,sanad_head)
                                                                         VALUES( 1     , $year   , 0   , $radif  , '$date', 13       , " . $row['company_id'] . "   , " . $row['fieldinsurance_id'] . "      , ''       , ''   , 14          , ''            ,'$desc12',    $porsant ,  0,      0   ,     0, '$Records', 0   ,$doc_id  ,'$sanad_head'  );";
                                    $result = $this->B_db->run_query_put($query);
                                    $radif=$radif+1;

                                    $daramad = intval($porsant * 0.917431);
                                    $desc12 = "   درآمد از قسمت نقد بیمه نامه  " . $desc12;
                                    $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin , CodeTaf                    , CodeTaf2                      , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  , Bes         , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup ,sanad_head)
                                                                          VALUES( 1     , $year   , 0   , $radif  , '$date', 60       , 1       , " . $row['company_id'] . "      ," . $row['fieldinsurance_id'] . " , ''   , ''          , 2             ,'$desc12',  0    ,    $daramad ,      0   ,     0, '$Records', 0  ,$doc_id    ,'$sanad_head');";
                                    $result = $this->B_db->run_query_put($query);
                                    $radif=$radif+1;

                                    $arzeshafzode = intval($porsant * 0.082569);
                                    $desc12 = "   ارزش افزوده کارمزد قسمت نقد  " . $desc12;
                                    $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                     , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,       Bes        , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup,sanad_head)
                                                                        VALUES( 1     , $year   , 0   , $radif  , '$date', 34       ,3        , " . $row['company_id'] . "      , ''       , ''   , 7           , ''         ,'$desc12',  0   ,    $arzeshafzode ,      0   ,     0, '$Records', 0  ,$doc_id  ,'$sanad_head' );";
                                    $result = $this->B_db->run_query_put($query);
                                    $radif=$radif+1;


                                    $migrate_id = $this->db->insert_id();



                                    $query3 = "INSERT INTO createdoc1_tb
                                      ( createdoc_foreign_id              , createdoc_foreign_mode, createdoc_foreign_date, createdoc_foreign_employee_id, createdoc_doc_id)
                         VALUES (".$row['instalment_check_id'].", 7                     , now()                 , ".$employeetoken[1].", $migrate_id );"  ;
                                    $result3 = $this->B_db->run_query_put($query3);

                                    $record['migrate_id'] = $migrate_id;
                                    $output[] = $record;
                                }

                                $query = "select * from instalment_check_tb,instalment_conditions_tb,user_tb,agent_tb,request_tb,company_tb,fieldinsurance_tb,request_state,organ_contract_tb,organ_tb where 
                                         instalment_check_date_pass>='$min_date' AND instalment_check_date_pass<'$max_date'  AND
                                         organ_contract_organ_id=organ_id AND instalment_condition_contract_id=organ_contract_id AND instalment_conditions_id=instalment_check_condition_id AND  request_state_id=request_last_state_id AND user_id=request_user_id AND agent_id=request_agent_id AND fieldinsurance=request_fieldinsurance  AND  company_id=request_company_id AND instalment_check_request_id=request_id 
                                         AND instalment_conditions_mode_id=2 AND instalment_check_pass=1 AND  instalment_check_id NOT IN (SELECT createdoc_foreign_id FROM createdoc1_tb WHERE createdoc_foreign_mode=8)";

                                $result = $this->B_db->run_query($query);
                                foreach ($result as $row) {
                                    $record = array();
                                    $desc12 = "  سند پاس شدن تعهد سازمان " . $row['user_name'] . " " . $row['user_family'] . " در رشته -" . $row['fieldinsurance_fa'] . " - با شرکت " . $row['company_name'] . " و در سازمان " . $row['organ_name'] . " و شماره قرارداد " . $row["organ_contract_num"] . " و سند پاس شدن " . $row['instalment_check_doc'];
                                    $Records = '<Records Tarikh="' . $date . '" Code="' . $row['request_id'] . '" Tedad="1" Fee="123456" Sharh="fffffff" />';


                                    $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                , CodeTaf2                  , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh      , Bed                ,       Bes        , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup,sanad_head)
                                                                             VALUES( 1     , $year   , 0   , $radif  , '$date', 10       , 20     , 1                       , 4                         , ''   , ''            , 1            ,'$desc12',    " . $row['instalment_check_amount'] . ",  0 ,      0   ,     0, '$Records', 0  ,$doc_id     ,'$sanad_head');";
                                    $result = $this->B_db->run_query_put($query);
                                    $radif=$radif+1;

                                    $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                 , CodeTaf2                   , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,       Bes                 , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup,sanad_head)
                                                                               VALUES( 1     , $year   , 0   , $radif  , '$date', 14       , 2     , " . $row['organ_id'] . "," . $row['user_id'] . "      , ''   , ''           , 5            ,'$desc12',    0 ,  " . $row['instalment_check_amount'] . ",      0   ,     0, '$Records', 0  ,$doc_id   ,'$sanad_head'  );";
                                    $result = $this->B_db->run_query_put($query);
                                    $radif=$radif+1;

                                    $porsant = intval($row['instalment_check_amount'] * $row['fieldinsurance_commission'] / 100);

                                    $desc12 = " کارمزد قسمت نقد  " . $desc12;
                                    $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin                    , CodeTaf                           , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,       Bes, ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup,sanad_head)
                                                                               VALUES( 1     , $year   , 0   , $radif  , '$date', 13       , " . $row['company_id'] . "   , " . $row['fieldinsurance_id'] . "      , ''       , ''   , 14          , ''            ,'$desc12',    $porsant ,  0,      0   ,     0, '$Records', 0  ,$doc_id  ,'$sanad_head'  );";
                                    $result = $this->B_db->run_query_put($query);
                                    $radif=$radif+1;

                                    $daramad = intval($porsant * 0.917431);
                                    $desc12 = "   درآمد از قسمت نقد بیمه نامه  " . $desc12;
                                    $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin , CodeTaf                    , CodeTaf2                      , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  , Bes         , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup,sanad_head)
                                                                               VALUES( 1     , $year   , 0   , $radif  , '$date', 60       , 1       , " . $row['company_id'] . "      ," . $row['fieldinsurance_id'] . " , ''   , ''          , 2             ,'$desc12',  0    ,    $daramad ,      0   ,     0, '$Records', 0  ,$doc_id   ,'$sanad_head'  );";
                                    $result = $this->B_db->run_query_put($query);
                                    $radif=$radif+1;

                                    $arzeshafzode = intval($porsant * 0.082569);
                                    $desc12 = "   ارزش افزوده کارمزد قسمت نقد  " . $desc12;
                                    $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                     , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,       Bes        , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup,sanad_head)
                                                                               VALUES( 1     , $year   , 0   , $radif  , '$date', 34       ,3        , " . $row['company_id'] . "      , ''       , ''   , 7           ,  2            ,'$desc12',  0   ,    $arzeshafzode ,      0   ,     0, '$Records', 0   ,$doc_id   ,'$sanad_head' );";
                                    $result = $this->B_db->run_query_put($query);
                                    $radif=$radif+1;

                                    $migrate_id = $this->db->insert_id();


                                    $query3 = "INSERT INTO createdoc1_tb
                                      ( createdoc_foreign_id              , createdoc_foreign_mode, createdoc_foreign_date, createdoc_foreign_employee_id, createdoc_doc_id)
                         VALUES (".$row['instalment_check_id'].", 8                     , now()                 , ".$employeetoken[1].", $migrate_id );"  ;
                                    $result3 = $this->B_db->run_query_put($query3);

                                    $record['migrate_id'] = $migrate_id;
                                    $output[] = $record;
                                }


                                echo json_encode(array('result' => "ok"
                                , "data" => $output
                                , 'desc' => 'سند اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
//*************************************************************************************************************************

                            } else {
                                echo json_encode(array('result' => $employeetoken[0]
                                , "data" => $employeetoken[1]
                                , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            }
                        }
                        else
                            if ($command == "create_doc_clearing_date") {
                                $employeetoken = checkpermissionemployeetoken($employee_token_str, 'new', 'create_doc');
                                if ($employeetoken[0] == 'ok') {
                                    $output = array();

//*************************************************************************************************************************
                                    $year = jdate('Y', '', "", '', 'en');
                                    $date = jdate('Y/m/d', '', "", '', 'en');
                                    $query = "SELECT * FROM request_financial_approval_tb,request_financial_doc_tb,request_financial_paying_tb,requst_ready_tb,user_tb,agent_tb,request_tb,company_tb,fieldinsurance_tb
WHERE  request_financial_approval_request_id=requst_ready_request_id AND request_financial_doc=1 AND requst_ready_request_id=request_id AND requst_ready_request_id=request_financial_paying_request_id
  AND   request_financial_paying_doc_id=request_financial_doc_id AND user_id=request_user_id AND agent_id=request_agent_id AND fieldinsurance=request_fieldinsurance  AND  company_id=request_company_id
  AND request_financial_paying_id NOT IN (SELECT createdoc_foreign_id FROM createdoc1_tb WHERE createdoc_foreign_mode=9)";
                                    $result = $this->B_db->run_query($query);
                                    $doc_id=get_sanad_qroup();
                                    foreach ($result as $row) {
                                        $record = array();
                                        if ($row['request_ready_clearing_id'] == 1) {
                                            $doc_id=$doc_id+1;
                                            $query2 = "select * from employee_tb where employee_id=" . $row['requst_ready_employee_id'];
                                            $result2 = $this->B_db->run_query($query2);
                                            $employee = $result2[0];

                                            $desc12 = "  سند تسویه تنخواه کارمند " . $employee['employee_name'] . " " . $employee['employee_family'] . " در رشته -" . $row['fieldinsurance_fa'] . " - با شرکت " . $row['company_name'] . " با نمایندگی " . $row['agent_name'] . ' ' . $row['agent_family'];
                                            $Records = '<Records Tarikh="' . $date . '" Code="' . $row['request_id'] . '" Tedad="1" Fee="123456" Sharh="fffffff" />';

                                            $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin                         , CodeTaf                 , CodeTaf2                          , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh      , Bed                                             , Bes , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                                      VALUES( 1     , $year   , 0   , 1  , '$date', 30       , " . $row['company_id'] . "  ,  " . $row['agent_id'] . "   ,  " . $row['fieldinsurance_id'] . ", ''   , ''            , 2            ,'$desc12',    " . $row['request_financial_approval_price'] . ",  0 ,      0   ,     0, '$Records', 0   ,$doc_id  );";
                                            $result = $this->B_db->run_query_put($query);

                                            $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin                         , CodeTaf                , CodeTaf2                         , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,       Bes                 , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                                       VALUES( 1    , $year   , 0   , 2  , '$date', 10       , 30                             ,  2                      , " . $row['requst_ready_employee_id'] . "      , ''   , ''            , 6            ,'$desc12',    0 ,  " . $row['request_financial_approval_price'] . ",      0   ,     0, '$Records', 0   ,$doc_id   );";
                                            $result = $this->B_db->run_query_put($query);

                                            $migrate_id = $this->db->insert_id();

                                        } else if ($row['request_ready_clearing_id'] == 2) {
                                            $desc12 = "  سند تسویه نقدی نماینده " . $row['agent_name'] . " " . $row['agent_family'] . " در رشته -" . $row['fieldinsurance_fa'] . " - با شرکت " . $row['company_name'];
                                            $Records = '<Records Tarikh="' . $date . '" Code="' . $row['request_id'] . '" Tedad="1" Fee="123456" Sharh="fffffff" />';

                                            $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin                         , CodeTaf                 , CodeTaf2                          , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh      , Bed                ,       Bes        , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                                      VALUES( 1     , $year   , 0   , 1  , '$date', 30       , " . $row['company_id'] . "  ,  " . $row['agent_id'] . "   ,  " . $row['fieldinsurance_id'] . ", ''   , ''            , 2            ,'$desc12',    " . $row['request_financial_approval_price'] . ",  0 ,      0   ,     0, '$Records', 0    ,$doc_id  );";
                                            $result = $this->B_db->run_query_put($query);

                                            $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin                         , CodeTaf                 , CodeTaf2                         , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,       Bes                 , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                                       VALUES( 1     , $year   , 0   , 2  , '$date', 10       , 20                             ,  1                      , 2                                , ''   , ''            , 1            ,'$desc12',    0 ,  " . $row['request_financial_approval_price'] . ",      0   ,     0, '$Records', 0   ,$doc_id   );";
                                            $result = $this->B_db->run_query_put($query);

                                            $migrate_id = $this->db->insert_id();

                                        } else if ($row['request_ready_clearing_id'] == 3) {
                                            $desc12 = "  سند تسویه صندوق شرکت بیمه " . $row['company_name'] . " در رشته -" . $row['fieldinsurance_fa'];
                                            $Records = '<Records Tarikh="' . $date . '" Code="' . $row['request_id'] . '" Tedad="1" Fee="123456" Sharh="fffffff" />';

                                            $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin                         , CodeTaf                 , CodeTaf2                          , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh      , Bed                ,       Bes        , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                                      VALUES( 1     , $year   , 0   , 1  , '$date', 30       , " . $row['company_id'] . "  ,  " . $row['agent_id'] . "   ,  " . $row['fieldinsurance_id'] . ", ''   , ''            , 2            ,'$desc12',    " . $row['request_financial_approval_price'] . ",  0 ,      0   ,     0, '$Records', 0   ,$doc_id   );";
                                            $result = $this->B_db->run_query_put($query);

                                            $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin                         , CodeTaf                 , CodeTaf2                         , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,       Bes                 , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                                       VALUES( 1     , $year   , 0   , 2  , '$date', 10       , 20                             ,  1                      , 2                                , ''   , ''            , 1            ,'$desc12',    0 ,  " . $row['request_financial_approval_price'] . ",      0   ,     0, '$Records', 0   ,$doc_id   );";
                                            $result = $this->B_db->run_query_put($query);

                                            $migrate_id = $this->db->insert_id();


                                        }

                                        $query3 = "INSERT INTO createdoc1_tb
                                      ( createdoc_foreign_id              , createdoc_foreign_mode, createdoc_foreign_date, createdoc_foreign_employee_id, createdoc_doc_id)
                         VALUES (".$row['request_financial_paying_id']." , 9                     , now()                 , ".$employeetoken[1].", $migrate_id );"  ;
                                        $result3 = $this->B_db->run_query_put($query3);


                                        $record['migrate_id'] = $migrate_id;
                                        $output[] = $record;
                                    }
                                    echo json_encode(array('result' => "ok"
                                    , "data" => $output
                                    , 'desc' => 'سند اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                } else {
                                    echo json_encode(array('result' => $employeetoken[0]
                                    , "data" => $employeetoken[1]
                                    , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                }
                            }
                            else

                                if ($command == "create_doc_refund_user_date") {
                                    $employeetoken = checkpermissionemployeetoken($employee_token_str, 'new', 'create_doc');
                                    if ($employeetoken[0] == 'ok') {
                                        $output = array();
                                        $doc_id=get_sanad_qroup()+1;
                                        $radif=1;
//*************************************************************************************************************************
                                        $min_date = $this->post("min_date");
                                        $max_date = $this->post("max_date");
                                        $datenow = strtotime($min_date);
                                        $date = jdate('Y/m/d', $datenow, "", '', 'en');
                                        $year = jdate('Y', $datenow, "", '', 'en');
                                        $sanad_head='سند های بازگشت وجه تولید شده در تاریخ 5-'.$date ;

                                        $query = "select * FROM refund_user_tb,user_tb,useracbank_tb
WHERE refund_user_datepeyed>='$min_date' AND refund_user_datepeyed<'$max_date'  AND refund_user_user_id=user_id AND refund_user_useracbank_id=useracbank_id AND refund_user_pey=1  AND refund_user_id NOT IN (SELECT createdoc_foreign_id FROM createdoc1_tb WHERE createdoc_foreign_mode=10) ";
                                        $result = $this->B_db->run_query($query);
                                        foreach ($result as $row) {
                                            $record = array();


                                            $desc12 = "  سند  پرداخت بازگشت وجه کاربر " . $row['user_name'] . " " . $row['user_family'] . " به شماره شبای  -" . $row['useracbank_sheba'] . " - با شماره پیگیری " . $row['refund_user_code'];
                                            $Records = '<Records Tarikh="' . $date . '" Code="' . $row['request_id'] . '" Tedad="1" Fee="123456" Sharh="' . $row['request_id'] . '" />';

                                            $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin     , CodeTaf                 , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh      , Bed                ,       Bes        , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup,sanad_head)
                                                                                VALUES( 1     , $year   , 0   , $radif  , '$date', 34       , 1         ,  " . $row['user_id'] . "   ,  ''   , ''   , 9            , ''            ,'$desc12',    " . $row['refund_user_amount'] . ",  0 ,      0   ,     0, '$Records', 0   ,$doc_id ,'$sanad_head' );";
                                            $result = $this->B_db->run_query_put($query);
                                            $radif=$radif+1;
                                            $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin     , CodeTaf                 , CodeTaf2    , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed      ,       Bes                          , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup,sanad_head)
                                                                                  VALUES( 1     , $year   , 0  ,$radif, '$date', 10       , 20         ,  1                      ,3            , ''   , ''            , 1            ,'$desc12',    0 ,  " . $row['refund_user_amount'] . ",      0   ,     0, '$Records', 0   ,$doc_id  ,'$sanad_head');";
                                            $result = $this->B_db->run_query_put($query);
                                            $radif=$radif+1;

                                            $migrate_id = $this->db->insert_id();


                                            $query3 = "INSERT INTO createdoc1_tb
                                      ( createdoc_foreign_id              , createdoc_foreign_mode, createdoc_foreign_date, createdoc_foreign_employee_id, createdoc_doc_id)
                         VALUES (".$row['refund_user_id']." , 10                     , now()                 , ".$employeetoken[1].", $migrate_id );"  ;
                                            $result3 = $this->B_db->run_query_put($query3);

                                            $record['migrate_id'] = $migrate_id;
                                            $output[] = $record;
                                        }
                                        echo json_encode(array('result' => "ok"
                                        , "data" => $output
                                        , 'desc' => 'سند اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


                                    } else {
                                        echo json_encode(array('result' => $employeetoken[0]
                                        , "data" => $employeetoken[1]
                                        , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                    }
                                }
                                else


                if ($command == "create_paydoc") {
                $employeetoken = checkpermissionemployeetoken($employee_token_str, 'new', 'create_doc');
                if ($employeetoken[0] == 'ok') {
                    $date = jdate('Y/m/d', '', "", '', 'en');
                    $year = jdate('Y', '', "", '', 'en');

                    $sql1 = "select * from pey_tb,request_tb,company_tb,fieldinsurance_tb where pey_request_id=request_id AND request_fieldinsurance=fieldinsurance AND request_company_id=company_id AND pey_backcode=0 AND  pey_id NOT IN (SELECT createdoc_foreign_id FROM createdoc1_tb WHERE createdoc_foreign_mode=1)  ";
                    $result = $this->B_db->run_query($sql1);
                    $output = array();
                    $doc_id=get_sanad_qroup();
                    foreach ($result as $row) {
                        $record = array();

                        $desc = "درخواست شماره " . $row['request_id'] . " در رشته -" . $row['fieldinsurance_fa'] . " - با شرکت " . $row['company_name'];
                        if ($row['pey_mode'] == 'pey') {
                            $desc .= " پرداخت اصلی با کد رهگیری " . $row['pey_refrenceid'];
                        } else {
                            $desc .= " پرداخت کسری واریزی با کد رهگیری " . $row['pey_refrenceid'];
                        }
                        $Records = '<Records Tarikh="' . $date . '" Code="' . $row['pey_refrenceid'] . '" Tedad="1" Fee="" Sharh="" />';
                        $doc_id=$doc_id+1;

                        $query1 = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf, CodeTaf2, Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed                           , Bes, ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                    VALUES( 1     , $year   , 0   , 1  , '$date', 10       , 20     , 1      , 1       , ''    , ''           , 1            , '$desc'  ,  " . $row['pey_amount'] . ",    0,      0   ,     0, '$Records', 0  ,$doc_id   );";
                        $result1 = $this->B_db->run_query_put($query1);

                        $query2 = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                    , CodeTaf2, Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,    Bes                   , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                    VALUES( 1     , $year   , 0   , 2  , '$date', 34       , 1    , " . $row['request_user_id'] . " , ''      , ''    , 9            , ''            , '$desc'  ,    0,  " . $row['pey_amount'] . ",      0   ,     0, '$Records', 0    ,$doc_id);";

                       $result2 = $this->B_db->run_query_put($query2);
                       $migrate_id = $this->db->insert_id();

                        $query3 = "INSERT INTO createdoc1_tb
                                      ( createdoc_foreign_id, createdoc_foreign_mode, createdoc_foreign_date, createdoc_foreign_employee_id, createdoc_doc_id)
                                         VALUES (".$row['pey_id'].", 1, now(), ".$employeetoken[1].", $migrate_id );"  ;
                        $result3 = $this->B_db->run_query_put($query3);

                          $record['migrate_id'] = $migrate_id;
                        $output[] = $record;
                    }

                    echo json_encode(array('result' => "ok"
                    , "data" => $output
                    , 'desc' => 'سند اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                } else {
                    echo json_encode(array('result' => $employeetoken[0]
                    , "data" => $employeetoken[1]
                    , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }
            else
                if ($command == "delete_paydoc") {
                $employeetoken = checkpermissionemployeetoken($employee_token_str, 'delete', 'create_doc');
                if ($employeetoken[0] == 'ok') {


                    $query="DELETE FROM migrate1_sql WHERE sanad_qroup IN (SELECT sanad_qroup FROM migrate1_sql where IsMigrated=0 AND  ID  IN(SELECT createdoc_doc_id FROM createdoc1_tb WHERE createdoc_foreign_mode=1))";
                    $result = $this->B_db->run_query_put($query);

                    $query="DELETE  FROM createdoc1_tb WHERE createdoc_foreign_mode=1 AND  createdoc_doc_id NOT IN (SELECT ID FROM migrate1_sql where IsMigrated=1 )";
                    $result = $this->B_db->run_query_put($query);


                    echo json_encode(array('result' => "ok"
                    , "data" => ''
                    , 'desc' => 'سند اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                } else {
                    echo json_encode(array('result' => $employeetoken[0]
                    , "data" => $employeetoken[1]
                    , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }

            else
                if ($command == "create_doc_request") {
                    $employeetoken = checkpermissionemployeetoken($employee_token_str, 'new', 'create_doc');
                    if ($employeetoken[0] == 'ok') {
                        $date = jdate('Y/m/d', '', "", '', 'en');
                        $year = jdate('Y', '', "", '', 'en');

                        $sql1 = "select * from request_financial_approval_tb,request_tb,company_tb,fieldinsurance_tb,requst_ready_tb where requst_ready_request_id=request_id AND request_financial_approval_request_id=request_id AND request_fieldinsurance=fieldinsurance AND request_company_id=company_id  AND request_financial_approval_id NOT IN (SELECT createdoc_foreign_id FROM createdoc1_tb WHERE createdoc_foreign_mode=2) ";
                        $result = $this->B_db->run_query($sql1);
                        $output = array();
                        $doc_id=get_sanad_qroup();
                        foreach ($result as $row) {
                            $record = array();
                            $doc_id=$doc_id+1;

                            $desc = "درخواست شماره " . $row['request_id'] . " در رشته -" . $row['fieldinsurance_fa'] . " - با شرکت " . $row['company_name'];
                            //*************************************************************************************************************
                            $Records = '<Records Tarikh="' . $date . '" Code="' . $row['pey_refrenceid'] . '" Tedad="1" Fee="123456" Sharh="fffffff" />';

                            $request_id = $row['request_id'];
                            $radif = 1;


                            $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh    , CodeKol, CodeMoin  , CodeTaf                             , CodeTaf2  , Ready   , CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed                                                 ,Bes, ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                 VALUES( 1     , $year   , 0   , $radif  , '$date', 34       , 1       , " . $row['request_user_id'] . "      , ''        ,  ''   , 9           ,  ''             , '$desc' ,     " . $row['request_financial_approval_price'] . ",0   ,      0   ,     0, '$Records', 0    ,$doc_id );";
                            $result = $this->B_db->run_query_put($query);


                            $query01 = "select sum(user_pey_amount) AS discount_code from user_pey_tb where user_pey_mode='discount_code' AND user_pey_request_id=" . $request_id;
                            $result01 = $this->B_db->run_query($query01);
                            $user_pey01 = $result01[0];
                            if ($user_pey01['discount_code']) {
                                $radif=$radif+1;
                                $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                             , CodeTaf2, Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed                      , Bes, ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                           VALUES( 1     , $year   , 0  , $radif , '$date', 84       , 12     , " . $row['fieldinsurance_id'] . "      , ''       , ''    , 14           , ''           , '$desc'  ,  " . $user_pey01['discount_code'] . ",    0,      0   ,     0, '$Records', 0    ,$doc_id );";
                                $result = $this->B_db->run_query_put($query);

                            }


                            $query03 = "select sum(user_pey_amount) AS managdiscount from user_pey_tb where user_pey_mode='managdiscount' AND user_pey_request_id=" . $request_id;
                            $result03 = $this->B_db->run_query($query03);
                            $user_pey03 = $result03[0];
                            if ($user_pey03['managdiscount']) {
                                $radif=$radif+1;
                                $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                             , CodeTaf2, Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed                      , Bes, ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                          VALUES( 1     , $year   , 0   , $radif  , '$date', 84       , 11     , " . $row['fieldinsurance_id'] . "     , ''       , ''    , 14           , ''           , '$desc'  ,  " . $user_pey01['managdiscount'] . ",    0,      0   ,     0, '$Records', 0    ,$doc_id );";
                                $result = $this->B_db->run_query_put($query);
                            }


//                            $query04 = "select sum(user_pey_amount) AS user_wallet from user_pey_tb where user_pey_mode='user_wallet' AND user_pey_request_id=" . $request_id;
//                            $result04 = $this->B_db->run_query($query04);
//                            $user_pey04 = $result04[0];
//                            if ($user_pey04['user_wallet']) {
//                                $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                    , CodeTaf2, Ready, CodeTaf_Group, CodeTaf2_Group, Sharh     , Bed                             , Bes, ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
//                                                                          VALUES( 1     , $year   , 0   , $radif  , '$date', 34       , 1    , " . $row['request_user_id'] . "  , ''      , ''    , 9            , ''            , '$desc'  ,  " . $user_pey04['user_wallet'] . " , 0,       0   ,     0, '$Records', 0     );";
//                                $result = $this->B_db->run_query_put($query);
//                                $radif=$radif+1;
//                            }


                            if ($row['request_financial_approval_difference_price'] > 0) {
                                $radif=$radif+1;
                                $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin    , CodeTaf                                 , CodeTaf2, Ready, CodeTaf_Group, CodeTaf2_Group         , Sharh     , Bed                                                   , Bes, ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                          VALUES( 1     , $year   , 0   , $radif  , '$date', 84       , 13    , " . $row['fieldinsurance_id'] . "  , ''      , ''    , 14            , ''            , '$desc'  ,  " . $row['request_financial_approval_difference_price'] . " , 0,       0   ,     0, '$Records', 0   ,$doc_id  );";
                                $result = $this->B_db->run_query_put($query);

                            }

                            $query11 = "select * from user_pey_tb,instalment_check_tb,instalment_conditions_tb where instalment_conditions_id=instalment_check_condition_id AND user_pey_mode='instalment' AND instalment_check_user_pey_id=user_pey_id AND (instalment_conditions_mode_id=1 OR instalment_conditions_mode_id=3) AND user_pey_request_id=" . $request_id;
                            $result11 = $this->B_db->run_query($query11);
                            foreach ($result11 as $row11) {
                                $radif=$radif+1;
                                $desc2 = $desc . " با شماره چک " . $row11['instalment_check_num'] . "   بتاریخ " . $row11['instalment_check_date'] . "   بشرح " . $row11['instalment_check_desc'];
                                $query112 = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                    , CodeTaf2, Ready, CodeTaf_Group, CodeTaf2_Group, Sharh     , Bed                             , Bes, ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                          VALUES( 1     , $year   , 0   , $radif  , '$date', 14       , 3   , " . $row['request_user_id'] . "  , ''      , ''    , 9            , ''            , '$desc2'  ,  " . $row11['user_pey_amount'] . " , 0,       0   ,     0, '$Records', 0    ,$doc_id );";
                                $result = $this->B_db->run_query_put($query112);
                            }

                            $query12 = "select * from user_pey_tb,instalment_check_tb,instalment_conditions_tb,organ_contract_tb,organ_tb where organ_contract_organ_id=organ_id AND instalment_condition_contract_id=organ_contract_id AND instalment_conditions_id=instalment_check_condition_id AND user_pey_mode='instalment' AND instalment_check_user_pey_id=user_pey_id AND instalment_conditions_mode_id=2 AND user_pey_request_id=" . $request_id;
                            $result12 = $this->B_db->run_query($query12);
                            foreach ($result12 as $row12) {
                                $radif=$radif+1;
                                $desc12 = $desc . " کسر از حقوق  بتاریخ " . $row12['instalment_check_date'] . "   بشرح " . $row12['instalment_check_desc'] . " سازمان " . $row12['organ_name'];
                                $query122 = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif  , Tarikh , CodeKol  , CodeMoin, CodeTaf                     , CodeTaf2                       , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh     , Bed                                  , Bes, ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                            VALUES( 1     , $year   , 0   , $radif  , '$date', 14       , 2       , " . $row12['organ_id'] . "  ," . $row['request_user_id'] . " , ''    , ''            , 5            , '$desc12'  ,  " . $row12['user_pey_amount'] . " , 0,       0   ,     0, '$Records', 0  ,$doc_id   );";
                                $result = $this->B_db->run_query_put($query122);

                            }

                            $radif=$radif+1;
                            $query1 = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh    , CodeKol, CodeMoin                           , CodeTaf                             , CodeTaf2                            , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,                 Bes                               , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                 VALUES( 1     , $year   , 0   , $radif  , '$date', 30       , " . $row['company_id'] . " , " . $row['request_agent_id'] . "      , " . $row['fieldinsurance_id'] . " , ''   , ''           , 2             , '$desc' ,    0 ,  " . $row['request_financial_approval_price'] . ",      0   ,     0, '$Records', 0   ,$doc_id  );";
                            $result1 = $this->B_db->run_query_put($query1);

                            $migrate_id = $this->db->insert_id();

                            $query3 = "INSERT INTO createdoc1_tb
                                      ( createdoc_foreign_id              , createdoc_foreign_mode, createdoc_foreign_date, createdoc_foreign_employee_id, createdoc_doc_id)
                         VALUES (".$row['request_financial_approval_id'].", 2                     , now()                 , ".$employeetoken[1].", $migrate_id );"  ;
                            $result3 = $this->B_db->run_query_put($query3);

                            //********************************
                            $doc_id=$doc_id+1;

                            $query110 = "select sum(user_pey_amount) AS overpayment from user_pey_tb where user_pey_mode='overpayment' AND user_pey_request_id=" . $request_id;
                            $result110 = $this->B_db->run_query($query110);
                            $user_pey0 = $result110[0];
                            $overpayment = $user_pey0['overpayment'];

                            $query2 = "select sum(user_pey_amount) AS sumcash from user_pey_tb where not (user_pey_mode = 'instalment')  AND  not (user_pey_mode='overpayment') AND user_pey_request_id=" . $request_id;
                            $result2 = $this->B_db->run_query($query2);
                            $user_pey2 = $result2[0];
                            $user_pey_cash = ($user_pey2['sumcash'] - $overpayment - $row['requst_ready_code_penalty']) * 100 / 109;
                            $porsant = intval($user_pey_cash * $row['fieldinsurance_commission'] / 100);

                            $desc12 = " کارمزد قسمت نقد  " . $desc;
                            $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin                    , CodeTaf                           , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,       Bes, ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                      VALUES( 1     , $year   , 0   , 1  , '$date', 13       , " . $row['company_id'] . "   , " . $row['fieldinsurance_id'] . "      , ''       , ''   , 14          , ''            ,'$desc12',    $porsant ,  0,      0   ,     0, '$Records', 0   ,$doc_id  );";
                            $result = $this->B_db->run_query_put($query);

                            $daramad = intval($porsant * 0.917431);
                            $desc12 = "   درآمد از قسمت نقد بیمه نامه  " . $desc;
                            $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin , CodeTaf                    , CodeTaf2                      , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  , Bes         , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                      VALUES( 1     , $year   , 0   , 2  , '$date', 60       , 1       , " . $row['company_id'] . "      ," . $row['fieldinsurance_id'] . " , ''   , ''          , 2             ,'$desc12',  0    ,    $daramad ,      0   ,     0, '$Records', 0 ,$doc_id    );";
                            $result = $this->B_db->run_query_put($query);

                            $arzeshafzode = intval($porsant - $daramad);
                            $desc12 = "   ارزش افزوده کارمزد قسمت نقد  " . $desc;
                            $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                     , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,       Bes        , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                      VALUES( 1     , $year   , 0   , 3  , '$date', 34       ,3        , " . $row['company_id'] . "      , ''       , ''   , 7           ,  ''           ,'$desc12',  0   ,    $arzeshafzode ,      0   ,     0, '$Records', 0  ,$doc_id   );";
                            $result = $this->B_db->run_query_put($query);

                            //*************************************************************************************************************


                            $migrate_id = $this->db->insert_id();


                            $query3 = "INSERT INTO createdoc1_tb
                                      ( createdoc_foreign_id              , createdoc_foreign_mode, createdoc_foreign_date, createdoc_foreign_employee_id, createdoc_doc_id)
                         VALUES (".$row['request_financial_approval_id'].", 2                     , now()                 , ".$employeetoken[1].", $migrate_id );"  ;
                            $result3 = $this->B_db->run_query_put($query3);

                            $record['query1'] = $query1;
                            $record['migrate_id'] = $migrate_id;
                            $output[] = $record;
                        }

                        echo json_encode(array('result' => "ok"
                        , "data" => $output
                        , 'desc' => 'سند اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    } else {
                        echo json_encode(array('result' => $employeetoken[0]
                        , "data" => $employeetoken[1]
                        , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                }
                else
                    if ($command == "delete_doc_request") {
                        $employeetoken = checkpermissionemployeetoken($employee_token_str, 'delete', 'create_doc');
                        if ($employeetoken[0] == 'ok') {

                            $query="DELETE FROM migrate1_sql WHERE sanad_qroup IN (SELECT sanad_qroup FROM migrate1_sql where IsMigrated=0 AND  ID  IN(SELECT createdoc_doc_id FROM createdoc1_tb WHERE createdoc_foreign_mode=2))";
                            $result = $this->B_db->run_query_put($query);

                            $query="DELETE  FROM createdoc1_tb WHERE createdoc_foreign_mode=2 AND  createdoc_doc_id NOT IN (SELECT ID FROM migrate1_sql where IsMigrated=1 )";
                            $result = $this->B_db->run_query_put($query);



                            echo json_encode(array('result' => "ok"
                            , "data" => ''
                            , 'desc' => 'سند اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                        } else {
                            echo json_encode(array('result' => $employeetoken[0]
                            , "data" => $employeetoken[1]
                            , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    }
                else

                    if ($command == "create_comission_doc") {
                        $employeetoken = checkpermissionemployeetoken($employee_token_str, 'new', 'create_doc');
                        if ($employeetoken[0] == 'ok') {
                            $output = array();

//*************************************************************************************************************************
                            $doc_id=get_sanad_qroup();
                            $date = jdate('Y/m/d', '', "", '', 'en');
                            $year = jdate('Y', '', "", '', 'en');
                            $query10 = "SELECT * FROM peycommision_leader_tb,user_wallet_tb,user_tb,request_tb,fieldinsurance_tb 
                                        WHERE fieldinsurance=request_fieldinsurance AND request_id=peycommision_leader_request_id AND user_id=user_wallet_user_id AND user_wallet_id=peycommision_leader_user_wallet_id AND peycommision_leader_doc_id=0 AND peycommision_leader_id NOT IN (SELECT createdoc_foreign_id FROM createdoc1_tb WHERE createdoc_foreign_mode=3)";
                            $result = $this->B_db->run_query($query10);
                            foreach ($result as $row) {
                                $record = array();
                                $desc12 = " کارمزد پرداخت شده به هماهنگ کننده" . $row['user_wallet_detail'] . " در رشته -" . $row['fieldinsurance_fa'] . " - با شرکت " . $row['company_name'];
                                $Records = '<Records Tarikh="' . $date . '" Code="' . $row['pey_refrenceid'] . '" Tedad="1" Fee="123456" Sharh="fffffff" />';
                                $doc_id=$doc_id+1;

                                $query1 = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                               , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh      , Bed                                 ,       Bes        , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                           VALUES( 1     , $year   , 0   , 1  , '$date', 84       , 6     , " . $row['fieldinsurance_id'] . "      , ''       , ''   , 14            , ''            ,'$desc12',    " . $row['user_wallet_amount'] . ",  0 ,      0   ,     0, '$Records', 0     ,$doc_id);";
                                $result1 = $this->B_db->run_query_put($query1);

                                $query2 = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                              , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,       Bes                 , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                           VALUES( 1     , $year   , 0   , 2  , '$date', 34       , 1     , " . $row['request_user_id'] . "      , ''       , ''   , 9            , ''            ,'$desc12',    0 ,  " . $row['user_wallet_amount'] . ",      0   ,     0, '$Records', 0     ,$doc_id);";
                                $result = $this->B_db->run_query_put($query2);


                                $migrate_id = $this->db->insert_id();

                                $query3 = "INSERT INTO createdoc1_tb
                                      ( createdoc_foreign_id              , createdoc_foreign_mode, createdoc_foreign_date, createdoc_foreign_employee_id, createdoc_doc_id)
                         VALUES (".$row['peycommision_leader_id'].", 3                     , now()                 , ".$employeetoken[1].", $migrate_id );"  ;
                                $result3 = $this->B_db->run_query_put($query3);


                                $record['migrate_id'] = $migrate_id;
                                $output[] = $record;
                            }
//*************************************************************************************************************************
                            $query11 = "SELECT * FROM peycommision_marketer_tb,user_wallet_tb,user_tb,request_tb,fieldinsurance_tb 
                                        WHERE fieldinsurance=request_fieldinsurance AND request_id=peycommision_marketer_request_id AND user_id=user_wallet_user_id AND user_wallet_id=peycommision_marketer_user_wallet_id AND  peycommision_marketer_id NOT IN (SELECT createdoc_foreign_id FROM createdoc1_tb WHERE createdoc_foreign_mode=4)";
                            $result11 = $this->B_db->run_query($query11);
                            $doc_id=get_sanad_qroup();
                            foreach ($result11 as $row) {
                                $record = array();
                                $desc12 = " کارمزد پرداخت شده به همکار فروش " . $row['user_wallet_detail'] . " در رشته -" . $row['fieldinsurance_fa'] . " - با شرکت " . $row['company_name'];
                                $Records = '<Records Tarikh="' . $date . '" Code="' . $row['pey_refrenceid'] . '" Tedad="1" Fee="123456" Sharh="fffffff" />';
                                $doc_id=$doc_id+1;


                                $query1 = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                              , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh      , Bed                ,       Bes        , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                       VALUES( 1     , $year   , 0   , 1  , '$date', 84       , 6     , " . $row['fieldinsurance_id'] . "      , ''       , ''   , 14            , ''            ,'$desc12',    " . $row['user_wallet_amount'] . ",  0 ,      0   ,     0, '$Records', 0  ,$doc_id   );";
                                $result1 = $this->B_db->run_query_put($query1);

                                $query2 = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                              , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,       Bes                 , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                       VALUES( 1     , $year   , 0   , 2  , '$date', 34       , 1     , " . $row['request_user_id'] . "      , ''       , ''   , 9            , ''            ,'$desc12',    0 ,  " . $row['user_wallet_amount'] . ",      0   ,     0, '$Records', 0  ,$doc_id   );";
                                $result2 = $this->B_db->run_query_put($query2);

                                $migrate_id = $this->db->insert_id();



                                $query3 = "INSERT INTO createdoc1_tb
                                      ( createdoc_foreign_id              , createdoc_foreign_mode, createdoc_foreign_date, createdoc_foreign_employee_id, createdoc_doc_id)
                         VALUES (".$row['peycommision_marketer_id'].", 4                     , now()                 , ".$employeetoken[1].", $migrate_id );"  ;
                                $result3 = $this->B_db->run_query_put($query3);

                                $record['migrate_id'] = $migrate_id;
                                $output[] = $record;
                            }
//***************************************************************************************************************************
                            $query10 = "SELECT * FROM peybackcommision_leader_tb,user_wallet_tb,user_tb,request_tb,fieldinsurance_tb 
                                        WHERE fieldinsurance=request_fieldinsurance AND request_id=peybackcommision_leader_request_id AND user_id=user_wallet_user_id AND user_wallet_id=peybackcommision_leader_user_wallet_id AND  peybackcommision_leader_id NOT IN (SELECT createdoc_foreign_id FROM createdoc1_tb WHERE createdoc_foreign_mode=5)";
                            $result = $this->B_db->run_query($query10);
                            $doc_id=get_sanad_qroup();
                            foreach ($result as $row) {
                                $record = array();
                                $desc12 = " بازگشت کارمزد پرداخت شده به هماهنگ کننده" . $row['user_wallet_detail'] . " در رشته -" . $row['fieldinsurance_fa'] . " - با شرکت " . $row['company_name'];
                                $Records = '<Records Tarikh="' . $date . '" Code="' . $row['pey_refrenceid'] . '" Tedad="1" Fee="123456" Sharh="fffffff" />';
                                $doc_id=$doc_id+1;

                                $query1 = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                              , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,       Bes                 , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                       VALUES( 1     , $year   , 0   , 1  , '$date', 34       , 1     , " . $row['request_user_id'] . "      , ''       , ''   , 9            , ''            ,'$desc12',  " . $row['user_wallet_amount'] . ",    0 ,      0   ,     0, '$Records', 0  ,$doc_id   );";
                                $result1 = $this->B_db->run_query_put($query1);

                                $query2 = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                              , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh      , Bed ,       Bes                          , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                       VALUES( 1     , $year   , 0   , 2  , '$date', 84       , 6     , " . $row['fieldinsurance_id'] . "      , ''       , ''   , 14            , ''            ,'$desc12'     ,  0  ,    " . $row['user_wallet_amount'] . " ,      0   ,     0, '$Records', 0  ,$doc_id   );";
                                $result2 = $this->B_db->run_query_put($query2);


                                $migrate_id = $this->db->insert_id();

                                $query3 = "INSERT INTO createdoc1_tb
                                      ( createdoc_foreign_id              , createdoc_foreign_mode, createdoc_foreign_date, createdoc_foreign_employee_id, createdoc_doc_id)
                         VALUES (".$row['peybackcommision_leader_id'].", 5                     , now()                 , ".$employeetoken[1].", $migrate_id );"  ;
                                $result3 = $this->B_db->run_query_put($query3);



                                $record['migrate_id'] = $migrate_id;
                                $output[] = $record;
                            }
//*************************************************************************************************************************
                            $query11 = "SELECT * FROM peybackcommision_marketer_tb,user_wallet_tb,user_tb,request_tb,fieldinsurance_tb 
                                        WHERE fieldinsurance=request_fieldinsurance AND request_id=peybackcommision_marketer_request_id AND user_id=user_wallet_user_id AND user_wallet_id=peybackcommision_marketer_user_wallet_id AND  peybackcommision_marketer_id NOT IN (SELECT createdoc_foreign_id FROM createdoc1_tb WHERE createdoc_foreign_mode=6)";
                            $result11 = $this->B_db->run_query($query11);
                            $doc_id=get_sanad_qroup();
                            foreach ($result11 as $row) {
                                $record = array();
                                $desc12 = " بازگشت کارمزد پرداخت شده به همکار فروش " . $row['user_wallet_detail'] . " در رشته -" . $row['fieldinsurance_fa'] . " - با شرکت " . $row['company_name'];
                                $Records = '<Records Tarikh="' . $date . '" Code="' . $row['pey_refrenceid'] . '" Tedad="1" Fee="123456" Sharh="fffffff" />';

                                $doc_id=$doc_id+1;

                                $query1 = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                              , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,       Bes                 , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                       VALUES( 1     , $year   , 0   , 1  , '$date', 34       , 1     , " . $row['request_user_id'] . "      , ''       , ''   , 9            , ''            ,'$desc12',  " . $row['user_wallet_amount'] . ",    0 ,      0   ,     0, '$Records', 0   ,$doc_id  );";
                                $result1 = $this->B_db->run_query_put($query1);

                                $query2 = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                              , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh      , Bed ,       Bes                          , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                       VALUES( 1     , $year   , 0   , 2  , '$date', 84       , 6     , " . $row['fieldinsurance_id'] . "      , ''       , ''   , 14            , ''            ,'$desc12'     ,  0  ,    " . $row['user_wallet_amount'] . " ,      0   ,     0, '$Records', 0  ,$doc_id   );";
                                $result2 = $this->B_db->run_query_put($query2);


                                $migrate_id = $this->db->insert_id();

                                $query3 = "INSERT INTO createdoc1_tb
                                      ( createdoc_foreign_id              , createdoc_foreign_mode, createdoc_foreign_date, createdoc_foreign_employee_id, createdoc_doc_id)
                         VALUES (".$row['peybackcommision_marketer_id'].", 6                     , now()                 , ".$employeetoken[1].", $migrate_id );"  ;


                                $result3 = $this->B_db->run_query_put($query3);
                                $record['migrate_id'] = $migrate_id;
                                $output[] = $record;
                            }
                            //*************************************************************************************************************************

                            echo json_encode(array('result' => "ok"
                            , "data" => $output
                            , 'desc' => 'سند اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
//*************************************************************************************************************************
                        } else {
                            echo json_encode(array('result' => $employeetoken[0]
                            , "data" => $employeetoken[1]
                            , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    }
                    else
                        if ($command == "delete_comission_doc") {
                            $employeetoken = checkpermissionemployeetoken($employee_token_str, 'delete', 'create_doc');
                            if ($employeetoken[0] == 'ok') {

                                $query="DELETE FROM migrate1_sql WHERE sanad_qroup IN (SELECT sanad_qroup FROM migrate1_sql where IsMigrated=0 AND  ID  IN(SELECT createdoc_doc_id FROM createdoc1_tb WHERE createdoc_foreign_mode=3))";
                                $result = $this->B_db->run_query_put($query);

                                $query="DELETE  FROM createdoc1_tb WHERE createdoc_foreign_mode=3 AND  createdoc_doc_id NOT IN (SELECT ID FROM migrate1_sql where IsMigrated=1 )";
                                $result = $this->B_db->run_query_put($query);

                                $query="DELETE FROM migrate1_sql WHERE sanad_qroup IN (SELECT sanad_qroup FROM migrate1_sql where IsMigrated=0 AND  ID  IN(SELECT createdoc_doc_id FROM createdoc1_tb WHERE createdoc_foreign_mode=4))";
                                $result = $this->B_db->run_query_put($query);

                                $query="DELETE  FROM createdoc1_tb WHERE createdoc_foreign_mode=4 AND  createdoc_doc_id NOT IN (SELECT ID FROM migrate1_sql where IsMigrated=1 )";
                                $result = $this->B_db->run_query_put($query);

                                $query="DELETE FROM migrate1_sql WHERE sanad_qroup IN (SELECT sanad_qroup FROM migrate1_sql where IsMigrated=0 AND  ID  IN(SELECT createdoc_doc_id FROM createdoc1_tb WHERE createdoc_foreign_mode=5))";
                                $result = $this->B_db->run_query_put($query);

                                $query="DELETE  FROM createdoc1_tb WHERE createdoc_foreign_mode=5 AND  createdoc_doc_id NOT IN (SELECT ID FROM migrate1_sql where IsMigrated=1 )";
                                $result = $this->B_db->run_query_put($query);


                                $query="DELETE FROM migrate1_sql WHERE sanad_qroup IN (SELECT sanad_qroup FROM migrate1_sql where IsMigrated=0 AND  ID  IN(SELECT createdoc_doc_id FROM createdoc1_tb WHERE createdoc_foreign_mode=6))";
                                $result = $this->B_db->run_query_put($query);

                                $query="DELETE  FROM createdoc1_tb WHERE createdoc_foreign_mode=6 AND  createdoc_doc_id NOT IN (SELECT ID FROM migrate1_sql where IsMigrated=1 )";
                                $result = $this->B_db->run_query_put($query);


                                echo json_encode(array('result' => "ok"
                                , "data" => ''
                                , 'desc' => 'سند اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                            } else {
                                echo json_encode(array('result' => $employeetoken[0]
                                , "data" => $employeetoken[1]
                                , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            }
                        }
                        else
                        if ($command == "create_doc_installment") {
                            $employeetoken = checkpermissionemployeetoken($employee_token_str, 'new', 'create_doc');
                            if ($employeetoken[0] == 'ok') {
                                $output = array();

//*************************************************************************************************************************
                                $year = jdate('Y', '', "", '', 'en');
                                $date = jdate('Y/m/d', '', "", '', 'en');
                                $query = "select * from instalment_check_tb,instalment_conditions_tb,user_tb,agent_tb,request_tb,company_tb,fieldinsurance_tb,request_state where instalment_conditions_id=instalment_check_condition_id AND  request_state_id=request_last_state_id AND user_id=request_user_id AND agent_id=request_agent_id AND fieldinsurance=request_fieldinsurance  AND  company_id=request_company_id AND instalment_check_request_id=request_id 
                                         AND( instalment_conditions_mode_id=1 OR  instalment_conditions_mode_id=3) AND instalment_check_pass=1 AND instalment_check_id NOT IN (SELECT createdoc_foreign_id FROM createdoc1_tb WHERE createdoc_foreign_mode=7)";
                                $result = $this->B_db->run_query($query);
                                $doc_id=get_sanad_qroup();
                                foreach ($result as $row) {
                                    $record = array();
                                    $desc12 = "  سند پاس شدن تعهد کاربر " . $row['user_name'] . " " . $row['user_family'] . " در رشته -" . $row['fieldinsurance_fa'] . " - با شرکت " . $row['company_name'] . " با شماره چک " . $row['instalment_check_num'] . " و سند پاس شدن " . $row['instalment_check_doc'];
                                    $Records = '<Records Tarikh="' . $date . '" Code="' . $row['request_id'] . '" Tedad="1" Fee="123456" Sharh="fffffff" />';

                                    $doc_id=$doc_id+1;
                                    $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                             , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh      , Bed                ,       Bes        , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                             VALUES( 1     , $year   , 0   , 1  , '$date', 10       , 20     , 1                                    , 4        , ''   , ''            , 1            ,'$desc12',    " . $row['instalment_check_amount'] . ",  0 ,      0   ,     0, '$Records', 0,$doc_id     );";
                                    $result = $this->B_db->run_query_put($query);

                                    $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                              , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,       Bes                 , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                               VALUES( 1     , $year   , 0   , 2  , '$date', 14       , 3     , " . $row['request_user_id'] . "      , ''       , ''   , 9            , ''            ,'$desc12',    0 ,  " . $row['instalment_check_amount'] . ",      0   ,     0, '$Records', 0 ,$doc_id    );";
                                    $result = $this->B_db->run_query_put($query);

                                    $porsant = intval($row['instalment_check_amount'] * $row['fieldinsurance_commission'] / 100);

                                    $desc12 = " کارمزد قسمت نقد  " . $desc12;
                                    $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin                    , CodeTaf                           , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,       Bes, ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                               VALUES( 1     , $year   , 0   , 1  , '$date', 13       , " . $row['company_id'] . "   , " . $row['fieldinsurance_id'] . "      , ''       , ''   , 14          , ''            ,'$desc12',    $porsant ,  0,      0   ,     0, '$Records', 0   ,$doc_id  );";
                                    $result = $this->B_db->run_query_put($query);

                                    $daramad = intval($porsant * 0.917431);
                                    $desc12 = "   درآمد از قسمت نقد بیمه نامه  " . $desc12;
                                    $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin , CodeTaf                    , CodeTaf2                      , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  , Bes         , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                              VALUES( 1     , $year   , 0   , 2  , '$date', 60       , 1       , " . $row['company_id'] . "      ," . $row['fieldinsurance_id'] . " , ''   , ''          , 2             ,'$desc12',  0    ,    $daramad ,      0   ,     0, '$Records', 0  ,$doc_id   );";
                                    $result = $this->B_db->run_query_put($query);

                                    $arzeshafzode = intval($porsant * 0.082569);
                                    $desc12 = "   ارزش افزوده کارمزد قسمت نقد  " . $desc12;
                                    $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                     , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,       Bes        , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                               VALUES( 1     , $year   , 0   , 3  , '$date', 34       ,3        , " . $row['company_id'] . "      , ''       , ''   , 7           , ''         ,'$desc12',  0   ,    $arzeshafzode ,      0   ,     0, '$Records', 0  ,$doc_id   );";
                                    $result = $this->B_db->run_query_put($query);


                                    $migrate_id = $this->db->insert_id();



                                    $query3 = "INSERT INTO createdoc1_tb
                                      ( createdoc_foreign_id              , createdoc_foreign_mode, createdoc_foreign_date, createdoc_foreign_employee_id, createdoc_doc_id)
                         VALUES (".$row['instalment_check_id'].", 7                     , now()                 , ".$employeetoken[1].", $migrate_id );"  ;
                                    $result3 = $this->B_db->run_query_put($query3);

                                    $record['migrate_id'] = $migrate_id;
                                    $output[] = $record;
                                }

                                $query = "select * from instalment_check_tb,instalment_conditions_tb,user_tb,agent_tb,request_tb,company_tb,fieldinsurance_tb,request_state,organ_contract_tb,organ_tb where organ_contract_organ_id=organ_id AND instalment_condition_contract_id=organ_contract_id AND instalment_conditions_id=instalment_check_condition_id AND  request_state_id=request_last_state_id AND user_id=request_user_id AND agent_id=request_agent_id AND fieldinsurance=request_fieldinsurance  AND  company_id=request_company_id AND instalment_check_request_id=request_id 
                                         AND instalment_conditions_mode_id=2 AND instalment_check_pass=1 AND  instalment_check_id NOT IN (SELECT createdoc_foreign_id FROM createdoc1_tb WHERE createdoc_foreign_mode=8)";

                                $result = $this->B_db->run_query($query);
                                foreach ($result as $row) {
                                    $record = array();
                                    $desc12 = "  سند پاس شدن تعهد سازمان " . $row['user_name'] . " " . $row['user_family'] . " در رشته -" . $row['fieldinsurance_fa'] . " - با شرکت " . $row['company_name'] . " و در سازمان " . $row['organ_name'] . " و شماره قرارداد " . $row["organ_contract_num"] . " و سند پاس شدن " . $row['instalment_check_doc'];
                                    $Records = '<Records Tarikh="' . $date . '" Code="' . $row['request_id'] . '" Tedad="1" Fee="123456" Sharh="fffffff" />';
                                    $doc_id=$doc_id+1;


                                    $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                , CodeTaf2                  , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh      , Bed                ,       Bes        , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                             VALUES( 1     , $year   , 0   , 1  , '$date', 10       , 20     , 1                       , 4                         , ''   , ''            , 1            ,'$desc12',    " . $row['instalment_check_amount'] . ",  0 ,      0   ,     0, '$Records', 0  ,$doc_id    );";
                                    $result = $this->B_db->run_query_put($query);

                                    $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                 , CodeTaf2                   , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,       Bes                 , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                               VALUES( 1     , $year   , 0   , 2  , '$date', 14       , 2     , " . $row['organ_id'] . "," . $row['user_id'] . "      , ''   , ''           , 5            ,'$desc12',    0 ,  " . $row['instalment_check_amount'] . ",      0   ,     0, '$Records', 0  ,$doc_id    );";
                                    $result = $this->B_db->run_query_put($query);

                                    $porsant = intval($row['instalment_check_amount'] * $row['fieldinsurance_commission'] / 100);

                                    $desc12 = " کارمزد قسمت نقد  " . $desc12;
                                    $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin                    , CodeTaf                           , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,       Bes, ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                               VALUES( 1     , $year   , 0   , 1  , '$date', 13       , " . $row['company_id'] . "   , " . $row['fieldinsurance_id'] . "      , ''       , ''   , 14          , ''            ,'$desc12',    $porsant ,  0,      0   ,     0, '$Records', 0  ,$doc_id    );";
                                    $result = $this->B_db->run_query_put($query);

                                    $daramad = intval($porsant * 0.917431);
                                    $desc12 = "   درآمد از قسمت نقد بیمه نامه  " . $desc12;
                                    $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin , CodeTaf                    , CodeTaf2                      , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  , Bes         , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                               VALUES( 1     , $year   , 0   , 2  , '$date', 60       , 1       , " . $row['company_id'] . "      ," . $row['fieldinsurance_id'] . " , ''   , ''          , 2             ,'$desc12',  0    ,    $daramad ,      0   ,     0, '$Records', 0  ,$doc_id    );";
                                    $result = $this->B_db->run_query_put($query);

                                    $arzeshafzode = intval($porsant * 0.082569);
                                    $desc12 = "   ارزش افزوده کارمزد قسمت نقد  " . $desc12;
                                    $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin, CodeTaf                     , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,       Bes        , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                               VALUES( 1     , $year   , 0   , 3  , '$date', 34       ,3        , " . $row['company_id'] . "      , ''       , ''   , 7           ,  2            ,'$desc12',  0   ,    $arzeshafzode ,      0   ,     0, '$Records', 0   ,$doc_id   );";
                                    $result = $this->B_db->run_query_put($query);

                                    $migrate_id = $this->db->insert_id();


                                    $query3 = "INSERT INTO createdoc1_tb
                                      ( createdoc_foreign_id              , createdoc_foreign_mode, createdoc_foreign_date, createdoc_foreign_employee_id, createdoc_doc_id)
                         VALUES (".$row['instalment_check_id'].", 8                     , now()                 , ".$employeetoken[1].", $migrate_id );"  ;
                                    $result3 = $this->B_db->run_query_put($query3);

                                    $record['migrate_id'] = $migrate_id;
                                    $output[] = $record;
                                }


                                echo json_encode(array('result' => "ok"
                                , "data" => $output
                                , 'desc' => 'سند اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
//*************************************************************************************************************************

                            } else {
                                echo json_encode(array('result' => $employeetoken[0]
                                , "data" => $employeetoken[1]
                                , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            }
                        } else
                            if ($command == "delete_doc_installment") {
                                $employeetoken = checkpermissionemployeetoken($employee_token_str, 'delete', 'create_doc');
                                if ($employeetoken[0] == 'ok') {

                                    $query="DELETE FROM migrate1_sql WHERE sanad_qroup IN (SELECT sanad_qroup FROM migrate1_sql where IsMigrated=0 AND  ID  IN(SELECT createdoc_doc_id FROM createdoc1_tb WHERE createdoc_foreign_mode=7))";
                                    $result = $this->B_db->run_query_put($query);

                                    $query="DELETE  FROM createdoc1_tb WHERE createdoc_foreign_mode=7 AND  createdoc_doc_id NOT IN (SELECT ID FROM migrate1_sql where IsMigrated=1 )";
                                    $result = $this->B_db->run_query_put($query);

                                    $query="DELETE FROM migrate1_sql WHERE sanad_qroup IN (SELECT sanad_qroup FROM migrate1_sql where IsMigrated=0 AND  ID  IN(SELECT createdoc_doc_id FROM createdoc1_tb WHERE createdoc_foreign_mode=8))";
                                    $result = $this->B_db->run_query_put($query);

                                    $query="DELETE  FROM createdoc1_tb WHERE createdoc_foreign_mode=8 AND  createdoc_doc_id NOT IN (SELECT ID FROM migrate1_sql where IsMigrated=1 )";
                                    $result = $this->B_db->run_query_put($query);


                                    echo json_encode(array('result' => "ok"
                                    , "data" => ''
                                    , 'desc' => 'سند اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                } else {
                                    echo json_encode(array('result' => $employeetoken[0]
                                    , "data" => $employeetoken[1]
                                    , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                }
                            }
                            else


                                if ($command == "create_doc_clearing") {
                                $employeetoken = checkpermissionemployeetoken($employee_token_str, 'new', 'create_doc');
                                if ($employeetoken[0] == 'ok') {
                                    $output = array();

//*************************************************************************************************************************
                                    $year = jdate('Y', '', "", '', 'en');
                                    $date = jdate('Y/m/d', '', "", '', 'en');
                                    $query = "SELECT * FROM request_financial_approval_tb,request_financial_doc_tb,request_financial_paying_tb,requst_ready_tb,user_tb,agent_tb,request_tb,company_tb,fieldinsurance_tb
WHERE  request_financial_approval_request_id=requst_ready_request_id AND request_financial_doc=1 AND requst_ready_request_id=request_id AND requst_ready_request_id=request_financial_paying_request_id
  AND   request_financial_paying_doc_id=request_financial_doc_id AND user_id=request_user_id AND agent_id=request_agent_id AND fieldinsurance=request_fieldinsurance  AND  company_id=request_company_id
  AND request_financial_paying_id NOT IN (SELECT createdoc_foreign_id FROM createdoc1_tb WHERE createdoc_foreign_mode=9)";
                                    $result = $this->B_db->run_query($query);
                                    $doc_id=get_sanad_qroup();
                                    foreach ($result as $row) {
                                        $record = array();
                                        if ($row['request_ready_clearing_id'] == 1) {
                                            $doc_id=$doc_id+1;
                                            $query2 = "select * from employee_tb where employee_id=" . $row['requst_ready_employee_id'];
                                            $result2 = $this->B_db->run_query($query2);
                                            $employee = $result2[0];

                                            $desc12 = "  سند تسویه تنخواه کارمند " . $employee['employee_name'] . " " . $employee['employee_family'] . " در رشته -" . $row['fieldinsurance_fa'] . " - با شرکت " . $row['company_name'] . " با نمایندگی " . $row['agent_name'] . ' ' . $row['agent_family'];
                                            $Records = '<Records Tarikh="' . $date . '" Code="' . $row['request_id'] . '" Tedad="1" Fee="123456" Sharh="fffffff" />';

                                            $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin                         , CodeTaf                 , CodeTaf2                          , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh      , Bed                                             , Bes , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                                      VALUES( 1     , $year   , 0   , 1  , '$date', 30       , " . $row['company_id'] . "  ,  " . $row['agent_id'] . "   ,  " . $row['fieldinsurance_id'] . ", ''   , ''            , 2            ,'$desc12',    " . $row['request_financial_approval_price'] . ",  0 ,      0   ,     0, '$Records', 0   ,$doc_id  );";
                                            $result = $this->B_db->run_query_put($query);

                                            $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin                         , CodeTaf                , CodeTaf2                         , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,       Bes                 , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                                       VALUES( 1    , $year   , 0   , 2  , '$date', 10       , 30                             ,  2                      , " . $row['requst_ready_employee_id'] . "      , ''   , ''            , 6            ,'$desc12',    0 ,  " . $row['request_financial_approval_price'] . ",      0   ,     0, '$Records', 0   ,$doc_id   );";
                                            $result = $this->B_db->run_query_put($query);

                                            $migrate_id = $this->db->insert_id();

                                            } else if ($row['request_ready_clearing_id'] == 2) {
                                            $desc12 = "  سند تسویه نقدی نماینده " . $row['agent_name'] . " " . $row['agent_family'] . " در رشته -" . $row['fieldinsurance_fa'] . " - با شرکت " . $row['company_name'];
                                            $Records = '<Records Tarikh="' . $date . '" Code="' . $row['request_id'] . '" Tedad="1" Fee="123456" Sharh="fffffff" />';

                                            $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin                         , CodeTaf                 , CodeTaf2                          , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh      , Bed                ,       Bes        , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                                      VALUES( 1     , $year   , 0   , 1  , '$date', 30       , " . $row['company_id'] . "  ,  " . $row['agent_id'] . "   ,  " . $row['fieldinsurance_id'] . ", ''   , ''            , 2            ,'$desc12',    " . $row['request_financial_approval_price'] . ",  0 ,      0   ,     0, '$Records', 0    ,$doc_id  );";
                                            $result = $this->B_db->run_query_put($query);

                                            $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin                         , CodeTaf                 , CodeTaf2                         , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,       Bes                 , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                                       VALUES( 1     , $year   , 0   , 2  , '$date', 10       , 20                             ,  1                      , 2                                , ''   , ''            , 1            ,'$desc12',    0 ,  " . $row['request_financial_approval_price'] . ",      0   ,     0, '$Records', 0   ,$doc_id   );";
                                            $result = $this->B_db->run_query_put($query);

                                            $migrate_id = $this->db->insert_id();

                                               } else if ($row['request_ready_clearing_id'] == 3) {
                                            $desc12 = "  سند تسویه صندوق شرکت بیمه " . $row['company_name'] . " در رشته -" . $row['fieldinsurance_fa'];
                                            $Records = '<Records Tarikh="' . $date . '" Code="' . $row['request_id'] . '" Tedad="1" Fee="123456" Sharh="fffffff" />';

                                            $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin                         , CodeTaf                 , CodeTaf2                          , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh      , Bed                ,       Bes        , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                                      VALUES( 1     , $year   , 0   , 1  , '$date', 30       , " . $row['company_id'] . "  ,  " . $row['agent_id'] . "   ,  " . $row['fieldinsurance_id'] . ", ''   , ''            , 2            ,'$desc12',    " . $row['request_financial_approval_price'] . ",  0 ,      0   ,     0, '$Records', 0   ,$doc_id   );";
                                            $result = $this->B_db->run_query_put($query);

                                            $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin                         , CodeTaf                 , CodeTaf2                         , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed  ,       Bes                 , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                                       VALUES( 1     , $year   , 0   , 2  , '$date', 10       , 20                             ,  1                      , 2                                , ''   , ''            , 1            ,'$desc12',    0 ,  " . $row['request_financial_approval_price'] . ",      0   ,     0, '$Records', 0   ,$doc_id   );";
                                            $result = $this->B_db->run_query_put($query);

                                            $migrate_id = $this->db->insert_id();


                                        }

                                        $query3 = "INSERT INTO createdoc1_tb
                                      ( createdoc_foreign_id              , createdoc_foreign_mode, createdoc_foreign_date, createdoc_foreign_employee_id, createdoc_doc_id)
                         VALUES (".$row['request_financial_paying_id']." , 9                     , now()                 , ".$employeetoken[1].", $migrate_id );"  ;
                                        $result3 = $this->B_db->run_query_put($query3);


                                        $record['migrate_id'] = $migrate_id;
                                        $output[] = $record;
                                    }
                                    echo json_encode(array('result' => "ok"
                                    , "data" => $output
                                    , 'desc' => 'سند اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                } else {
                                    echo json_encode(array('result' => $employeetoken[0]
                                    , "data" => $employeetoken[1]
                                    , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                }
                            }
                                else
                                    if ($command == "delete_doc_clearing") {
                                        $employeetoken = checkpermissionemployeetoken($employee_token_str, 'delete', 'create_doc');
                                        if ($employeetoken[0] == 'ok') {

                                            $query="DELETE FROM migrate1_sql WHERE sanad_qroup IN (SELECT sanad_qroup FROM migrate1_sql where IsMigrated=0 AND  ID  IN(SELECT createdoc_doc_id FROM createdoc1_tb WHERE createdoc_foreign_mode=9))";
                                            $result = $this->B_db->run_query_put($query);

                                            $query="DELETE  FROM createdoc1_tb WHERE createdoc_foreign_mode=9 AND  createdoc_doc_id NOT IN (SELECT ID FROM migrate1_sql where IsMigrated=1 )";
                                            $result = $this->B_db->run_query_put($query);


                                            echo json_encode(array('result' => "ok"
                                            , "data" => ''
                                            , 'desc' => 'سند اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                        } else {
                                            echo json_encode(array('result' => $employeetoken[0]
                                            , "data" => $employeetoken[1]
                                            , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                        }
                                    }
                                else

                                if ($command == "create_doc_refund_user") {
                                    $employeetoken = checkpermissionemployeetoken($employee_token_str, 'new', 'create_doc');
                                    if ($employeetoken[0] == 'ok') {
                                        $output = array();
                                        $doc_id=get_sanad_qroup();

//*************************************************************************************************************************
                                        $date = jdate('Y/m/d', '', "", '', 'en');
                                        $year = jdate('Y', '', "", '', 'en');
                                        $query = "select * FROM refund_user_tb,user_tb,useracbank_tb
WHERE refund_user_user_id=user_id AND refund_user_useracbank_id=useracbank_id AND refund_user_pey=1  AND refund_user_id NOT IN (SELECT createdoc_foreign_id FROM createdoc1_tb WHERE createdoc_foreign_mode=10) ";
                                        $result = $this->B_db->run_query($query);
                                        foreach ($result as $row) {
                                            $record = array();
                                            $doc_id=$doc_id+1;


                                            $desc12 = "  سند  پرداخت بازگشت وجه کاربر " . $row['user_name'] . " " . $row['user_family'] . " به شماره شبای  -" . $row['useracbank_sheba'] . " - با شماره پیگیری " . $row['refund_user_code'];
                                            $Records = '<Records Tarikh="' . $date . '" Code="' . $row['request_id'] . '" Tedad="1" Fee="123456" Sharh="' . $row['request_id'] . '" />';

                                            $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin     , CodeTaf                 , CodeTaf2 , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh      , Bed                ,       Bes        , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                                            VALUES( 1     , $year   , 0   , 1  , '$date', 34       , 1         ,  " . $row['user_id'] . "   ,  ''   , ''   , 9            , ''            ,'$desc12',    " . $row['refund_user_amount'] . ",  0 ,      0   ,     0, '$Records', 0   ,$doc_id  );";
                                            $result = $this->B_db->run_query_put($query);

                                            $query = "INSERT INTO migrate1_sql ( Shobe, SalMali, Code, Radif, Tarikh, CodeKol, CodeMoin     , CodeTaf                 , CodeTaf2    , Ready, CodeTaf_Group, CodeTaf2_Group, Sharh   , Bed      ,       Bes                          , ArzValue, ArzFee, RowDetXML, Pages,sanad_qroup)
                                                                                  VALUES( 1     , $year   , 0  , 1 , '$date', 10       , 20         ,  1                      ,3            , ''   , ''            , 1            ,'$desc12',    0 ,  " . $row['refund_user_amount'] . ",      0   ,     0, '$Records', 0   ,$doc_id  );";
                                            $result = $this->B_db->run_query_put($query);

                                            $migrate_id = $this->db->insert_id();


                                            $query3 = "INSERT INTO createdoc1_tb
                                      ( createdoc_foreign_id              , createdoc_foreign_mode, createdoc_foreign_date, createdoc_foreign_employee_id, createdoc_doc_id)
                         VALUES (".$row['refund_user_id']." , 10                     , now()                 , ".$employeetoken[1].", $migrate_id );"  ;
                                            $result3 = $this->B_db->run_query_put($query3);

                                            $record['migrate_id'] = $migrate_id;
                                            $output[] = $record;
                                        }
                                        echo json_encode(array('result' => "ok"
                                        , "data" => $output
                                        , 'desc' => 'سند اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


                                    } else {
                                        echo json_encode(array('result' => $employeetoken[0]
                                        , "data" => $employeetoken[1]
                                        , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                    }
                                } else
                                    if ($command == "delete_doc_refund_user") {
                                        $employeetoken = checkpermissionemployeetoken($employee_token_str, 'delete', 'create_doc');
                                        if ($employeetoken[0] == 'ok') {

                                            $query="DELETE FROM migrate1_sql WHERE sanad_qroup IN (SELECT sanad_qroup FROM migrate1_sql where IsMigrated=0 AND  ID  IN(SELECT createdoc_doc_id FROM createdoc1_tb WHERE createdoc_foreign_mode=10))";
                                            $result = $this->B_db->run_query_put($query);

                                            $query="DELETE  FROM createdoc1_tb WHERE createdoc_foreign_mode=10 AND  createdoc_doc_id NOT IN (SELECT ID FROM migrate1_sql where IsMigrated=1 )";
                                            $result = $this->B_db->run_query_put($query);


                                            echo json_encode(array('result' => "ok"
                                            , "data" => ''
                                            , 'desc' => 'سند اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                        } else {
                                            echo json_encode(array('result' => $employeetoken[0]
                                            , "data" => $employeetoken[1]
                                            , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                        }
                                    }
                                else

                                    if ($command == "send_doc_nosend") {
                                        $employeetoken = checkpermissionemployeetoken($employee_token_str, 'view', 'create_doc');
                                        if ($employeetoken[0] == 'ok') {

                                            $sql1 = "select * from migrate1_sql  where IsMigrated =0 ORDER BY ID DESC ";
                                            $sql2 = "select  count(*) AS cnt from migrate1_sql  where IsMigrated =0 ORDER BY ID DESC ";

                                            $limit = $this->post("limit");
                                            $offset = $this->post("offset");
                                            $limit_state ="";
                                            if($limit!="" & $offset!="") {
                                                $limit_state = " LIMIT " . $offset . "," . $limit;
                                            }

                                            $result = $this->B_db->run_query($sql1.$limit_state);
                                            $count  = $this->B_db->run_query($sql2);


                                            echo json_encode(array('result' => "ok"
                                            , "data" => $result
                                            ,"cnt"=>$count[0]['cnt']
                                            , 'desc' => 'سند اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


                                        } else {
                                            echo json_encode(array('result' => $employeetoken[0]
                                            , "data" => $employeetoken[1]
                                            , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                        }
                                    } else
                                        if ($command == "send_doc_send") {
                                            $employeetoken = checkpermissionemployeetoken($employee_token_str, 'view', 'create_doc');
                                            if ($employeetoken[0] == 'ok') {

                                                $sql1 = "select * from migrate1_sql  where IsMigrated =1  ORDER BY ID DESC ";
                                                $sql2= "select  count(*) AS cnt  from migrate1_sql  where IsMigrated =1  ORDER BY ID DESC ";

                                                $limit = $this->post("limit");
                                                $offset = $this->post("offset");
                                                $limit_state ="";
                                                if($limit!="" & $offset!="") {
                                                    $limit_state = " LIMIT " . $offset . "," . $limit;
                                                }

                                                $result = $this->B_db->run_query($sql1.$limit_state);
                                                $count  = $this->B_db->run_query($sql2);

                                                echo json_encode(array('result' => "ok"
                                                , "data" => $result
                                                ,"cnt"=>$count[0]['cnt']
                                                , 'desc' => 'سند اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


                                            } else {
                                                echo json_encode(array('result' => $employeetoken[0]
                                                , "data" => $employeetoken[1]
                                                , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                            }
                                        } else
                                            if ($command == "send_tafzili_nosend") {
                                                $employeetoken = checkpermissionemployeetoken($employee_token_str, 'view', 'create_doc');
                                                if ($employeetoken[0] == 'ok') {

                                                    $sql1 = "select * from sarfasl_taffs where IsMigrated =0  ORDER BY SarfaslTaffs_ID DESC ";
                                                    $sql2 = "select count(*) AS cnt from sarfasl_taffs where IsMigrated =0  ORDER BY SarfaslTaffs_ID DESC ";

                                                    $limit = $this->post("limit");
                                                    $offset = $this->post("offset");
                                                    $limit_state ="";
                                                    if($limit!="" & $offset!="") {
                                                        $limit_state = " LIMIT " . $offset . "," . $limit;
                                                    }

                                                    $result = $this->B_db->run_query($sql1.$limit_state);
                                                    $count  = $this->B_db->run_query($sql2);

                                                    echo json_encode(array('result' => "ok"
                                                    , "data" => $result
                                                    ,"cnt"=>$count[0]['cnt']
                                                    , 'desc' => 'سند اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


                                                } else {
                                                    echo json_encode(array('result' => $employeetoken[0]
                                                    , "data" => $employeetoken[1]
                                                    , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                }
                                            } else
                                                if ($command == "send_tafzili_send") {
                                                    $employeetoken = checkpermissionemployeetoken($employee_token_str, 'view', 'create_doc');
                                                    if ($employeetoken[0] == 'ok') {

                                                        $sql1 = "select * from sarfasl_taffs where IsMigrated =1  ORDER BY SarfaslTaffs_ID DESC ";
                                                        $sql2 = "select  count(*) AS cnt from sarfasl_taffs where IsMigrated =1  ORDER BY SarfaslTaffs_ID DESC ";

                                                        $limit = $this->post("limit");
                                                        $offset = $this->post("offset");
                                                        $limit_state ="";
                                                        if($limit!="" & $offset!="") {
                                                            $limit_state = " LIMIT " . $offset . "," . $limit;
                                                        }

                                                        $result = $this->B_db->run_query($sql1.$limit_state);
                                                        $count  = $this->B_db->run_query($sql2);

                                                        echo json_encode(array('result' => "ok"
                                                        , "data" => $result
                                                        ,"cnt"=>$count[0]['cnt']
                                                        , 'desc' => 'سند اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


                                                    } else {
                                                        echo json_encode(array('result' => $employeetoken[0]
                                                        , "data" => $employeetoken[1]
                                                        , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                    }
                                                }else
                                                    if ($command == "send_sarfasl_nosend") {
                                                        $employeetoken = checkpermissionemployeetoken($employee_token_str, 'view', 'create_doc');
                                                        if ($employeetoken[0] == 'ok') {

                                                            $sql1 = "select * from SarFasl where IsMigrated =0  ORDER BY Sarfasl_ID DESC ";
                                                            $sql2 = "select  count(*) AS cnt from SarFasl where IsMigrated =0  ORDER BY Sarfasl_ID DESC ";

                                                            $limit = $this->post("limit");
                                                            $offset = $this->post("offset");
                                                            $limit_state ="";
                                                            if($limit!="" & $offset!="") {
                                                                $limit_state = " LIMIT " . $offset . "," . $limit;
                                                            }

                                                            $result = $this->B_db->run_query($sql1.$limit_state);
                                                            $count  = $this->B_db->run_query($sql2);

                                                            echo json_encode(array('result' => "ok"
                                                            , "data" => $result
                                                            ,"cnt"=>$count[0]['cnt']
                                                            , 'desc' => 'سند اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


                                                        } else {
                                                            echo json_encode(array('result' => $employeetoken[0]
                                                            , "data" => $employeetoken[1]
                                                            , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                        }
                                                    } else
                                                        if ($command == "send_sarfasl_send") {
                                                            $employeetoken = checkpermissionemployeetoken($employee_token_str, 'view', 'create_doc');
                                                            if ($employeetoken[0] == 'ok') {

                                                                $sql1 = "select * from SarFasl where IsMigrated =1  ORDER BY Sarfasl_ID DESC ";
                                                                $sql2 = "select count(*) AS cnt from SarFasl where IsMigrated =1  ORDER BY Sarfasl_ID DESC ";

                                                                $limit = $this->post("limit");
                                                                $offset = $this->post("offset");
                                                                $limit_state ="";
                                                                if($limit!="" & $offset!="") {
                                                                    $limit_state = " LIMIT " . $offset . "," . $limit;
                                                                }

                                                                $result = $this->B_db->run_query($sql1.$limit_state);
                                                                $count  = $this->B_db->run_query($sql2);

                                                                echo json_encode(array('result' => "ok"
                                                                , "data" => $result
                                                                ,"cnt"=>$count[0]['cnt']
                                                                , 'desc' => 'سند اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


                                                            } else {
                                                                echo json_encode(array('result' => $employeetoken[0]
                                                                , "data" => $employeetoken[1]
                                                                , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                            }
                                                        }else
                                                            if ($command == "create_tafzili_user") {
                                                        $employeetoken = checkpermissionemployeetoken($employee_token_str, 'new', 'create_doc');
                                                        if ($employeetoken[0] == 'ok') {
                                                            $output = array();

//*************************************************************************************************************************
                                                            $query = "select * FROM user_tb
                                                                   WHERE   user_id NOT IN (SELECT createtaff_foreign_id FROM createtaff1_tb WHERE createtaff_foreign_mode=1) ";
                                                            $result = $this->B_db->run_query($query);
                                                            foreach ($result as $row) {
                                                                $record = array();


                                                                $desc =  $row['user_name'] . " " . $row['user_family'] . " به شماره همراه  -" . $row['user_mobile'] . " - کد ملی " . $row['user_national_code'];

                                                                $query = "INSERT INTO sarfasl_taffs
         ( RowID, Shobe , CodeTaf_Group, CodeTaf                  , CodeTaf2_Group, CodeTaf2, Sharh           , Mahiat, Arz , Disable, IsMeghdari)
VALUES( 0        , 1    , 9            , " . $row['user_id'] . "  , 0             , 0        , '" . $desc . "'  , 1     , 0   , 0      , 0         );";
                                                                $result = $this->B_db->run_query_put($query);




                                                                $migrate_id = $this->db->insert_id();


                                                                $query3 = "INSERT INTO createtaff1_tb
                                      ( createtaff_foreign_id, createtaff_foreign_mode, createtaff_foreign_date, createtaff_foreign_employee_id, createtaff_doc_id)
                                         VALUES (".$row['user_id'].", 1, now(), ".$employeetoken[1].", $migrate_id );"  ;
                                                                $result3 = $this->B_db->run_query_put($query3);



                                                                $record['migrate_id'] = $query;
                                                                $output[] = $record;
                                                            }
                                                            echo json_encode(array('result' => "ok"
                                                            , "data" => $output
                                                            , 'desc' => 'سند اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


                                                        } else {
                                                            echo json_encode(array('result' => $employeetoken[0]
                                                            , "data" => $employeetoken[1]
                                                            , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                        }
                                                    }else
                                                        if ($command == "create_tafzili_user_organ") {
                                                            $employeetoken = checkpermissionemployeetoken($employee_token_str, 'new', 'create_doc');
                                                            if ($employeetoken[0] == 'ok') {
                                                                $output = array();

//*************************************************************************************************************************
                                                                $query = "SELECT  organ_user_id ,user_id,organ_user_user_id,user_name,user_family,user_mobile,user_national_code,organ_name,organ_id,organ_user_create_doc_id 
FROM organ_user_tb,user_tb,organ_tb 
WHERE organ_user_user_id=user_id AND organ_user_organ_id=organ_id AND 
      organ_user_id NOT IN (SELECT createtaff_foreign_id FROM createtaff1_tb WHERE createtaff_foreign_mode=2)
      AND organ_user_id IN(
SELECT  MIN(organ_user_id) AS organ_user_id
FROM organ_user_tb
GROUP BY organ_user_user_id
) ORDER BY organ_user_id ";
                                                                $result = $this->B_db->run_query($query);
                                                                foreach ($result as $row) {
                                                                    $record = array();

                                                                    $query = "select * FROM user_tb
                                                                   WHERE   user_id NOT IN (SELECT createtaff_foreign_id FROM createtaff1_tb WHERE createtaff_foreign_mode=1) ";

    $desc = $row['user_name'] . " " . $row['user_family'] . " به شماره همراه  -" . $row['user_mobile'] . " - کد ملی " . $row['user_national_code'] . " -سازمان " . $row['organ_name'];


    // karbar sazeman bayad ijad shavad
    //
    $query2 = "INSERT INTO sarfasl_taffs
                                        ( RowID, Shobe , CodeTaf_Group, CodeTaf                 , CodeTaf2_Group, CodeTaf2                 , Sharh           , Mahiat, Arz , Disable, IsMeghdari)
                                  VALUES( 0        , 1    , ''          , " . $row['organ_id'] . "  , 5             , " . $row['user_id'] . ", '" . $desc . "'  , 1     , 0   , 0   , 0         );";
    $result = $this->B_db->run_query_put($query2);


    $migrate_id = $this->db->insert_id();


    $query3 = "INSERT INTO createtaff1_tb
                                      ( createtaff_foreign_id, createtaff_foreign_mode, createtaff_foreign_date, createtaff_foreign_employee_id, createtaff_doc_id)
                                         VALUES (".$row['organ_user_id'].", 2, now(), ".$employeetoken[1].", $migrate_id );"  ;
    $result3 = $this->B_db->run_query_put($query3);

    $record['migrate_id'] = $query2;
    $output[] = $record;

                                                                }
                                                                echo json_encode(array('result' => "ok"
                                                                , "data" => $output
                                                                , 'desc' => 'سند اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


                                                            } else {
                                                                echo json_encode(array('result' => $employeetoken[0]
                                                                , "data" => $employeetoken[1]
                                                                , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                            }
                                                        }else
                                                        if ($command == "create_tafzili_agent") {//sarfasl jabeja shavad
                                                            $employeetoken = checkpermissionemployeetoken($employee_token_str, 'new', 'create_doc');
                                                            if ($employeetoken[0] == 'ok') {
                                                                $output = array();

//*************************************************************************************************************************
                                                                $query = "select * FROM agent_tb
                                                                   WHERE  agent_id NOT IN (SELECT createtaff_foreign_id FROM createtaff1_tb WHERE createtaff_foreign_mode=3)";
                                                                $result = $this->B_db->run_query($query);
                                                                foreach ($result as $row) {
                                                                    $record = array();

                                                                    $desc =  $row['agent_name'] . " " . $row['agent_family'] . " به شماره همراه  -" . $row['agent_mobile'] . " -  کد نماینده " . $row['agent_code'];

                                                                    $query2 = "INSERT INTO SarFasl
        ( Shobe, Grp, CodeKol   , CodeMoin                  , CodeTaf                           , CodeTaf2, Sharh            , Ready, Mahiat, Arz, Daem      )
VALUES  ( 1     , 1   , 30      , " . $row['agent_company_id'] . " , " . $row['agent_id'] . "  , 0        , '" . $desc . "'  , 0     , 0   , 0      , 0      );";
                                                                    $result = $this->B_db->run_query_put($query2);


                                                                    $migrate_id = $this->db->insert_id();


                                                                    $query3 = "INSERT INTO createtaff1_tb
                                      ( createtaff_foreign_id, createtaff_foreign_mode, createtaff_foreign_date, createtaff_foreign_employee_id, createtaff_doc_id)
                                         VALUES (".$row['agent_id'].", 3, now(), ".$employeetoken[1].", $migrate_id );"  ;
                                                                    $result3 = $this->B_db->run_query_put($query3);

                                                                    $record['migrate_id'] = $query;
                                                                    $output[] = $record;
                                                                }
                                                                echo json_encode(array('result' => "ok"
                                                                , "data" => $output
                                                                , 'desc' => 'سند اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


                                                            } else {
                                                                echo json_encode(array('result' => $employeetoken[0]
                                                                , "data" => $employeetoken[1]
                                                                , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                            }
                                                        }else
                                                            if ($command == "create_tafzili_fieldinsurance") {
                                                                $employeetoken = checkpermissionemployeetoken($employee_token_str, 'new', 'create_doc');
                                                                if ($employeetoken[0] == 'ok') {
                                                                    $output = array();

//*************************************************************************************************************************
                                                                    $query = "select * FROM fieldinsurance_tb
                                                                   WHERE  fieldinsurance_id NOT IN (SELECT createtaff_foreign_id FROM createtaff1_tb WHERE createtaff_foreign_mode=4)";
                                                                    $result = $this->B_db->run_query($query);
                                                                    foreach ($result as $row) {
                                                                        $record = array();


                                                                        $desc =  $row['fieldinsurance_fa'] ;

                                                                        $query = "INSERT INTO sarfasl_taffs
         ( RowID, Shobe , CodeTaf_Group, CodeTaf                         , CodeTaf2_Group, CodeTaf2, Sharh           , Mahiat, Arz , Disable, IsMeghdari)
VALUES( 0        , 1    , 14            , " . $row['fieldinsurance_id'] . "  , 0             , 0        , '" . $desc . "'  , 1     , 0   , 0      , 0         );";
                                                                        $result = $this->B_db->run_query_put($query);

                                                                        //sarfasl jabeja shavad
                                                                        $query1 = "INSERT INTO sarfasl_taffs
         ( RowID, Shobe , CodeTaf_Group, CodeTaf       , CodeTaf2_Group  , CodeTaf2                         , Sharh           , Mahiat, Arz , Disable, IsMeghdari)
VALUES( 0        , 1    , ''            ,''            , 2               ," . $row['fieldinsurance_id'] . " , '" . $desc . "'  , 1     , 0   , 0      , 0         );";
                                                                        $result1 = $this->B_db->run_query_put($query1);


                                                                        $migrate_id = $this->db->insert_id();



                                                                        $query3 = "INSERT INTO createtaff1_tb
                                      ( createtaff_foreign_id, createtaff_foreign_mode, createtaff_foreign_date, createtaff_foreign_employee_id, createtaff_doc_id)
                                         VALUES (".$row['fieldinsurance_id'].", 4, now(), ".$employeetoken[1].", $migrate_id );"  ;
                                                                        $result3 = $this->B_db->run_query_put($query3);

                                                                        $record['migrate_id'] = $query;
                                                                        $output[] = $record;
                                                                    }
                                                                    echo json_encode(array('result' => "ok"
                                                                    , "data" => $output
                                                                    , 'desc' => 'سند اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


                                                                } else {
                                                                    echo json_encode(array('result' => $employeetoken[0]
                                                                    , "data" => $employeetoken[1]
                                                                    , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                                }
                                                            }else
                                                                if ($command == "create_tafzili_employee") {
                                                                    $employeetoken = checkpermissionemployeetoken($employee_token_str, 'new', 'create_doc');
                                                                    if ($employeetoken[0] == 'ok') {
                                                                        $output = array();

//*************************************************************************************************************************
                                                                        $query = "select * FROM employee_tb
                                                                   WHERE   employee_id NOT IN (SELECT createtaff_foreign_id FROM createtaff1_tb WHERE createtaff_foreign_mode=5)";
                                                                        $result = $this->B_db->run_query($query);
                                                                        foreach ($result as $row) {
                                                                            $record = array();


                                                                            $desc =  $row['employee_name'] . " " . $row['employee_family'] . " به شماره همراه  -" . $row['employee_mobile'] ;

                                                                            $query = "INSERT INTO sarfasl_taffs
         ( RowID, Shobe , CodeTaf_Group, CodeTaf         , CodeTaf2_Group, CodeTaf2                 , Sharh           , Mahiat, Arz , Disable, IsMeghdari)
VALUES( 0        , 1    , ''            , ''             , 5             , ".$row['employee_id'] .", '" . $desc . "'  , 1     , 0   , 0      , 0         );";
                                                                            $result = $this->B_db->run_query_put($query);

                                                                            $migrate_id = $this->db->insert_id();

                                                                            $query3 = "INSERT INTO createtaff1_tb
                                      ( createtaff_foreign_id, createtaff_foreign_mode, createtaff_foreign_date, createtaff_foreign_employee_id, createtaff_doc_id)
                                         VALUES (".$row['employee_id'].", 5, now(), ".$employeetoken[1].", $migrate_id );"  ;
                                                                            $result3 = $this->B_db->run_query_put($query3);

                                                                            $record['migrate_id'] = $query;
                                                                            $output[] = $record;
                                                                        }
                                                                        echo json_encode(array('result' => "ok"
                                                                        , "data" => $output
                                                                        , 'desc' => 'سند اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


                                                                    } else {
                                                                        echo json_encode(array('result' => $employeetoken[0]
                                                                        , "data" => $employeetoken[1]
                                                                        , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                                    }
                                                                }else
                                                                    if ($command == "create_tafzili_organ") {
                                                                        $employeetoken = checkpermissionemployeetoken($employee_token_str, 'new', 'create_doc');
                                                                        if ($employeetoken[0] == 'ok') {
                                                                            $output = array();

//*************************************************************************************************************************
                                                                            $query = "select * FROM organ_tb
                                                                   WHERE  organ_id NOT IN (SELECT createtaff_foreign_id FROM createtaff1_tb WHERE createtaff_foreign_mode=6)";
                                                                            $result = $this->B_db->run_query($query);
                                                                            foreach ($result as $row) {
                                                                                $record = array();


                                                                                $desc =  $row['organ_name'] ;
                                                                                $query2 = "INSERT INTO SarFasl
        ( Shobe, Grp, CodeKol, CodeMoin   , CodeTaf                           , CodeTaf2, Sharh            , Ready, Mahiat, Arz, Daem      )
VALUES( 1     , 1   , 14      ,2          , " . $row['organ_id'] . "  , 0       , '" . $desc . "'  , 0     , 0   , 0      , 0      );";
                                                                                $result = $this->B_db->run_query_put($query2);



 //*****************************************************************

                                                                                $migrate_id = $this->db->insert_id();



                                                                                $query3 = "INSERT INTO createtaff1_tb
                                      ( createtaff_foreign_id, createtaff_foreign_mode, createtaff_foreign_date, createtaff_foreign_employee_id, createtaff_doc_id)
                                         VALUES (".$row['organ_id'].", 6, now(), ".$employeetoken[1].", $migrate_id );"  ;
                                                                                $result3 = $this->B_db->run_query_put($query3);

                                                                                $record['migrate_id'] = $query;
                                                                                $output[] = $record;
                                                                            }
                                                                            echo json_encode(array('result' => "ok"
                                                                            , "data" => $output
                                                                            , 'desc' => 'سند اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


                                                                        } else {
                                                                            echo json_encode(array('result' => $employeetoken[0]
                                                                            , "data" => $employeetoken[1]
                                                                            , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                                        }
                                                                    }else
                                                                    if ($command == "create_tafzili_company") {
                                                                        $employeetoken = checkpermissionemployeetoken($employee_token_str, 'new', 'create_doc');
                                                                        if ($employeetoken[0] == 'ok') {
                                                                            $output = array();

//*************************************************************************************************************************
                                                                            $query = "select * FROM company_tb
                                                                   WHERE  company_id NOT IN (SELECT createtaff_foreign_id FROM createtaff1_tb WHERE createtaff_foreign_mode=7)";
                                                                            $result = $this->B_db->run_query($query);
                                                                            foreach ($result as $row) {
                                                                                $record = array();


                                                                                $desc =  $row['company_name'] ;
                                                                                $query2 = "INSERT INTO SarFasl
        ( Shobe, Grp, CodeKol, CodeMoin                  , CodeTaf , CodeTaf2, Sharh            , Ready, Mahiat, Arz, Daem      )
VALUES( 1     , 1   , 30      ," . $row['company_id'] . ", ''      , 0       , '" . $desc . "'  , 0     , 0   , 0      , 0      );";
                                                                                $result = $this->B_db->run_query_put($query2);

                                                                                $query3 = "INSERT INTO SarFasl
        ( Shobe, Grp, CodeKol, CodeMoin , CodeTaf                         , CodeTaf2, Sharh            , Ready, Mahiat, Arz, Daem      )
VALUES( 1     , 1   , 60      ,1        ," . $row['company_id'] . "       , ''      , '" . $desc . "'  , 0     , 0   , 0      , 0      );";
                                                                                $result = $this->B_db->run_query_put($query3);

                                                                                $query4 = "INSERT INTO SarFasl
        ( Shobe, Grp, CodeKol, CodeMoin                  , CodeTaf , CodeTaf2, Sharh            , Ready, Mahiat, Arz, Daem      )
VALUES( 1     , 1   , 13      ," . $row['company_id'] . ", ''      , 0       , '" . $desc . "'  , 0     , 0   , 0      , 0      );";
                                                                                $result = $this->B_db->run_query_put($query4);


                                                                                $query = "INSERT INTO sarfasl_taffs
         ( RowID, Shobe , CodeTaf_Group, CodeTaf                 , CodeTaf2_Group, CodeTaf2                 , Sharh           , Mahiat, Arz , Disable, IsMeghdari)
VALUES( 0        , 1    , 7            , ".$row['company_id'] ." , ''             , ''                  , '" . $desc . "'      , 1     , 0   , 0      , 0         );";
                                                                                $result = $this->B_db->run_query_put($query);


 //*****************************************************************

                                                                                $migrate_id = $this->db->insert_id();


                                                                                $query3 = "INSERT INTO createtaff1_tb
                                      ( createtaff_foreign_id, createtaff_foreign_mode, createtaff_foreign_date, createtaff_foreign_employee_id, createtaff_doc_id)
                                         VALUES (".$row['company_id'].", 7, now(), ".$employeetoken[1].", $migrate_id );"  ;
                                                                                $result3 = $this->B_db->run_query_put($query3);

                                                                                $record['migrate_id'] = $query;
                                                                                $output[] = $record;
                                                                            }
                                                                            echo json_encode(array('result' => "ok"
                                                                            , "data" => $output
                                                                            , 'desc' => 'سند اضافه شد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


                                                                        } else {
                                                                            echo json_encode(array('result' => $employeetoken[0]
                                                                            , "data" => $employeetoken[1]
                                                                            , 'desc' => $employeetoken[2]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                                                        }
                                                                    }else
                                                                        if($command == "sendsms"){
                                                                        $query2="select * from request_visit_tb";
                                                                        $result2=$this->B_db->run_query($query2);
                                                                        echo json_encode( $result2, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                                                                    }
        }
    }
}
