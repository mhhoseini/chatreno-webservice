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
class Exam extends REST_Controller
{

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
    }

    public function index_post()
    {
        phpinfo();
        $this->load->helper('my_helper');

    }

    public function migrate_post()
    {
        $server = "91.92.127.79";
        $db = "Fasih2";
        $pass = "302050";
        $user = "sa";
        $conn_array = array(
            "UID" => "sa",
            "PWD" => "302050",
            "Database" => "Fasih2",
        );
        $conn = sqlsrv_connect('91.92.127.79\SQLEXPRESS,1433', $conn_array);
        $last_code = 33;
        $sal_mali = 1400;

        if (($result = sqlsrv_query($conn, "SELECT TOP 1 [Code] FROM [Accounting].[SanadHead] Where [SalMali]=" . $sal_mali . " Order By [Code] DESC")) !== false) {
            while ($obj = sqlsrv_fetch_object($result)) {
                $last_code = $obj->Code;
            }
        } else
            $last_code = 0;
        $last_code = $last_code + 1;

        $sand_group_from = $this->post('sanad_qroup_from', 0);
        $sand_group_to = $this->post('sanad_qroup_to', 15);

        $this->load->model('B_db');
        $sql = "select DISTINCT(sanad_qroup),count(sanad_qroup) as ct from migrate1_sql where IsMigrated=0 AND sanad_qroup BETWEEN $sand_group_from AND $sand_group_to group by sanad_qroup order by sanad_qroup  asc";
        $sanads = $this->B_db->run_query($sql);
        $deneid_sanad_group[] = 8888888;
        foreach ($sanads as $sanad) {
            $sql = "Select * from migrate1_sql Where IsMigrated=0 AND sanad_qroup IN (" . $sanad['sanad_qroup'] . ") Order By Tarikh,Radif ASC";
            $records = ${'list_' . $sanad['sanad_qroup']}[][] = $this->B_db->run_query($sql);
            //line base query string builder
            $k = $z = 0;
            //array_search($rec['sanad_qroup'], $deneid_sanad_group);
            foreach ($records as $rec) {
                $sql_check = "SELECT [CodeTaf] FROM [Fasih2].[Accounting].[SarFasl_TafFs] Where [CodeTaf_Group]=" . $rec['CodeTaf_Group'] . " AND [CodeTaf]=" . $rec['CodeTaf'] . " AND [CodeTaf2_Group]=" . $rec['CodeTaf2_Group'] . " AND [CodeTaf2]=" . $rec['CodeTaf2'] . " Order By [CodeTaf2] DESC";
                $sql_mysql = "SELECT CodeTaf FROM sarfasl_taffs Where CodeTaf_Group=" . $rec['CodeTaf_Group'] . " AND CodeTaf=" . $rec['CodeTaf'] . " AND CodeTaf2_Group=" . $rec['CodeTaf2_Group'] . " AND CodeTaf2=" . $rec['CodeTaf2'] . " Order By CodeTaf2 DESC";
                $res_taf = $this->B_db->run_query($sql_mysql);
                //if (($result = sqlsrv_query($conn, $sql_check)) !== false) {
                //$obj = sqlsrv_fetch_object($result);
                if ($res_taf[0]['CodeTaf'] != '') {
                    $res_taf[0]['CodeTaf'] . '@@' . $sanad['sanad_qroup'] . '@';
                    $str_procedure[$sanad['sanad_qroup']][$k][$z] = $rec['SalMali'] . "," . $rec['Shobe'] . "," . $last_code . ",'" . $rec['Sharh'] . "','" . $rec['sanad_head'] . "','" . $rec['Tarikh'] . "'," . $rec['CodeKol'] . ",
                        " . $rec['CodeMoin'] . "," . $rec['CodeTaf_Group'] . "," . $rec['CodeTaf'] . "," . $rec['CodeTaf2_Group'] . "," . $rec['CodeTaf2'] . "," . $rec['Ready'] . ",'" . $rec['Sharh'] . "'," . $rec['Bed'] . "," . $rec['Bes'] . ",
                        " . $rec['Radif'] . "," . $rec['Pages'] . "," . $rec['ArzValue'] . "," . $rec['ArzFee'] . ",'" . $rec['RowDetXML'] . "'";
                    $IDS[$sanad['sanad_qroup']][][] = $rec['ID'];
                    if ($z >= 1) {
                        $z = 0;
                        $k++;
                    } else $z++;
                } else {
                    $res_taf[0]['CodeTaf'] . '&&' . $sanad['sanad_qroup'] . '&';
                    $arr[$rec['CodeTaf_Group']][] = array('CodeTaf_Group' => $rec['CodeTaf_Group'], 'CodeTaf' => $rec['CodeTaf'], 'CodeTaf2_Group' => $rec['CodeTaf2_Group'], 'CodeTaf2' => $rec['CodeTaf2']);
                    $deneid_sanad_group[] = $rec['sanad_qroup'];
                }
            }
            $arr = array_map("unserialize", array_unique(array_map("serialize", $arr)));
            $last_code = $last_code + 1;
        }
        echo 'کدهای تفصیلی تعریف نشده';
        print_r($arr);
        echo 'پایان لیست ردیف های تعریف نشده';
        implode('#', $deneid_sanad_group);
        //print_r($str_procedure);
        foreach ($str_procedure as $key => $st) {
            if (array_search($key, $deneid_sanad_group) > 0){
                unset($str_procedure[$key]);
            }
        }
        //print_r($str_procedure);
        if (!empty($str_procedure)) {
            $i = $j = 0;
            $dbh = new PDO("sqlsrv:Server=$server;Database=$db", $user, $pass);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            foreach ($str_procedure as $key => $mini_procedure) {
                foreach ($mini_procedure as $query_string) {
                    //try {
                    $arr_name = array_fill(0, count($query_string), 'A');
                    $procedure_name = implode('', $arr_name);
                    echo "EXEC '[Fasih2].dbo.'.$procedure_name " . implode(',', $query_string) . '***********';
                    die;
                    $stmt = $dbh->prepare("EXEC [Fasih2].dbo." . $procedure_name . " " . implode(',', $query_string));
                    if (!$stmt) {
                        echo "Statement could not be prepared.\n";
                        die(print_r(sqlsrv_errors(), true));
                    }
                    $res = $stmt->execute();
                    if ($res === true) {
                        //print_r(sqlsrv_fetch_array($stmt));
                        echo "@";
                        //$data = $stmt->fetchAll();
                        //$response = array('sanad_qroup'=>$key,'sqlserver_response'=>$data[0][0]);
                        $response = 'ss';
                        //print_r($response['sqlserver_response']);
                        echo "$";
                        //echo $pos  = strpos($response['sqlserver_response'], 'قابل');
                        $pos = false;
                        echo "$";

                        foreach ($IDS[$key] as $id)
                            $ids[] = $id[0];

                        if ($pos !== false) {
                            echo $query = "Update migrate1_sql set SqlServerResponse='" . implode('-', $response) . "' where ID IN(" . implode(',', $ids) . ")";
                            $j++;
                        } else {
                            echo $query = "Update migrate1_sql set Code=$last_code,IsMigrated=1,Migrate_Date=now(), SqlServerResponse='" . implode('-', $response) . "#done' where ID IN(" . implode(',', $ids) . ")";
                            $i++;
                        }
                        $this->B_db->run_query_put($query);
                    } else {
                        echo "sanad_qroup: " . $key . " failure";
                        die(print_r(sqlsrv_errors(), true));
                    }
                    //} catch (PDOException $e) {
                    //  echo "@".implode(',',$IDS[$key]);
                    //print_r($IDS);
                    //echo "EXEC '[Fasih2].dbo.'.$procedure_name ".implode(',',$records);
                    //print "Error!: " . $e->getMessage() . "<br/>";
                    //}
                }
            }
            $dbh = null;
        } else
            echo "رکوردی برای انتقال یافت نشد.";
        echo "True:" . $i . "-VS-False:" . $j;
    }

    public function find_post()
    {
        $server = "91.92.127.79";
        $db = "Fasih2";
        $pass = "302050";
        $user = "sa";
        $conn_array = array(
            "UID" => "sa",
            "PWD" => "302050",
            "Database" => "Fasih2",
        );
        $conn = sqlsrv_connect('91.92.127.79\SQLEXPRESS,1433', $conn_array);

        $sand_group_from = $this->post('sanad_qroup_from', 0);
        $sand_group_to = $this->post('sanad_qroup_to', 15);

        $this->load->model('B_db');
        $sql = "select DISTINCT(sanad_qroup),count(sanad_qroup) as ct from migrate1_sql where IsMigrated=0 AND sanad_qroup BETWEEN $sand_group_from AND $sand_group_to group by sanad_qroup order by ct  desc";
        $sanads = $this->B_db->run_query($sql);
        $arr = array();
        foreach ($sanads as $sanad) {
            $sql = "Select * from migrate1_sql Where IsMigrated=0 AND sanad_qroup IN (" . $sanad['sanad_qroup'] . ") Order By Tarikh,Radif ASC";
            $records = ${'list_' . $sanad['sanad_qroup']}[][] = $this->B_db->run_query($sql);
            //line base query string builder
            $k = $z = 0;
            foreach ($records as $rec) {
                $sql_check = "SELECT [CodeTaf] FROM [Fasih2].[Accounting].[SarFasl_TafFs] Where [CodeTaf_Group]=" . $rec['CodeTaf_Group'] . " AND [CodeTaf]=" . $rec['CodeTaf'] . " AND [CodeTaf2_Group]=" . $rec['CodeTaf2_Group'] . " AND [CodeTaf2]=" . $rec['CodeTaf2'] . " Order By [CodeTaf2] DESC";
                $sql_mysql = "SELECT CodeTaf FROM sarfasl_taffs Where CodeTaf_Group=" . $rec['CodeTaf_Group'] . " AND CodeTaf=" . $rec['CodeTaf'] . " AND CodeTaf2_Group=" . $rec['CodeTaf2_Group'] . " AND CodeTaf2=" . $rec['CodeTaf2'] . " Order By CodeTaf2 DESC";
                $res_taf = $this->B_db->run_query($sql_mysql);
                //if (($result = sqlsrv_query($conn, $sql_check)) !== false) {
                //$obj = sqlsrv_fetch_object($result);
                $last_code = 1;
                if ($res_taf[0]['CodeTaf'] != '') {
                    $res_taf[0]['CodeTaf'] . '@@' . $sanad['sanad_qroup'] . '@';
                    $str_procedure[$sanad['sanad_qroup']][$k][$z] = $rec['SalMali'] . "," . $rec['Shobe'] . "," . $last_code . ",'" . $rec['Sharh'] . "','" . $rec['sanad_head'] . "','" . $rec['Tarikh'] . "'," . $rec['CodeKol'] . ",
                        " . $rec['CodeMoin'] . "," . $rec['CodeTaf_Group'] . "," . $rec['CodeTaf'] . "," . $rec['CodeTaf2_Group'] . "," . $rec['CodeTaf2'] . "," . $rec['Ready'] . ",'" . $rec['Sharh'] . "'," . $rec['Bed'] . "," . $rec['Bes'] . ",
                        " . $rec['Radif'] . "," . $rec['Pages'] . "," . $rec['ArzValue'] . "," . $rec['ArzFee'] . ",'" . $rec['RowDetXML'] . "'";
                    $IDS[$sanad['sanad_qroup']][][] = $rec['ID'];
                    if ($z >= 1) {
                        $z = 0;
                        $k++;
                    } else $z++;
                } else {
                    $res_taf[0]['CodeTaf'] . '&&' . $sanad['sanad_qroup'] . '&';
                    $find = False;
                    if(!empty($arr[1]))
                    foreach($arr[1] as $_){
                        if($_['CodeTaf_Group'] == $rec['CodeTaf_Group'] AND $_['CodeTaf'] == $rec['CodeTaf'] AND $_['CodeTaf2_Group'] == $rec['CodeTaf2_Group'] AND $_['CodeTaf2'] == $rec['CodeTaf2']){
                            $find = True;
                        }
                    }
                    if(!$find)
                        $arr[$rec['CodeTaf_Group']][] = array('CodeTaf_Group' => $rec['CodeTaf_Group'], 'CodeTaf' => $rec['CodeTaf'], 'CodeTaf2_Group' => $rec['CodeTaf2_Group'], 'CodeTaf2' => $rec['CodeTaf2']);
                    $deneid_sanad_group[] = $rec['sanad_qroup'];
                }
            }
            $last_code = $last_code + 1;
        }
        $arr = array_map("unserialize", array_unique(array_map("serialize", $arr[0])));
        //print_r($arr);
        foreach($arr as $item){
            if(($this->gmp_sign($item['CodeTaf'])!= $this->gmp_sign($item['CodeTaf2'])) OR ($item['CodeTaf']+$item['CodeTaf2'])==(0))
            {
                if(($this->gmp_sign($item['CodeTaf_Group'])== $this->gmp_sign($item['CodeTaf'])))
                {
                    if(($this->gmp_sign($item['CodeTaf2_Group'])==$this->gmp_sign($item['CodeTaf2'])))
                        $rule = true;
                    else
                    {
                        $rule = false;
                        print_r($item);
                        echo 'این کدها در کنار هم نمی توانند بکار روند و غیر مجازند';
                        echo 'نقض قانون دوم:';
                        echo 'CodeTaf_Group AND CodeTaf --- CodeTaf2_Group AND CodeTaf2 یا هردو باید صفر باشن یا هردو بزرگتر از صفر' ;
                    }
                }else
                {
                    $rule = false;
                    print_r($item);
                    echo 'این کدها در کنار هم نمی توانند بکار روند و غیر مجازند';
                    echo 'نقض قانون دوم:';
                    echo 'CodeTaf_Group AND CodeTaf --- CodeTaf2_Group AND CodeTaf2 یا هردو باید صفر باشن یا هردو بزرگتر از صفر' ;
                }
            } else
            {
                $rule = false;
                print_r($item);
                echo 'این کدها در کنار هم نمی توانند بکار روند و غیر مجازند';
                echo 'نقض قانون اول در این دو کد صورت گرفته است.';
                echo 'نباید CodeTaf و CodeTaf2 هردو بزرگتر از صفر باشن.';
            }
            //echo "#######".$rule."##";
            //print_r($item);
            if($rule == true){
                echo "^^^^^^^^^^^^^^^^^^^";
                echo 'This Record does not exist in sarfasl_taffs. Run this query to add. Fill the -Sharh- Field before!';
                print_r($item);
                echo $sql_insert = "INSERT INTO sarfasl_taffs
                (RowID, Shobe, CodeTaf_Group, CodeTaf, CodeTaf2_Group, CodeTaf2, Sharh, Mahiat, Arz, Disable, IsMeghdari, Migrate_Date, IsMigrated)
                VALUES(0, 1, ".$item['CodeTaf_Group'].", ".$item['CodeTaf'].", ".$item['CodeTaf2_Group'].", ".$item['CodeTaf2'].", '".$item['Sharh']."', 0, 0, 0, 0, NULL, 0)";
                echo "--\\\\\\\\\\\\\\\\\\\\---";
            }
        }
            //$this->B_db->run_query_put($sql_insert);
    }

    public function gmp_sign($num){
        if($num>0)
            return 1;
        elseif($num==0)
            return 0;
        elseif($num<0)
            return -1;
    }

    public function migrate2_post()
    {
        $server = "91.92.127.79";
        $db = "Fasih2";
        $pass = "302050";
        $user = "sa";
        $conn_array = array(
            "UID" => "sa",
            "PWD" => "302050",
            "Database" => "Fasih2",
        );
        $conn = sqlsrv_connect('91.92.127.79\SQLEXPRESS,1433', $conn_array);
        $last_code = 33;
        $sal_mali = 1400;

        if (($result = sqlsrv_query($conn, "SELECT TOP 1 [Code] FROM [Accounting].[SanadHead] Where [SalMali]=" . $sal_mali . " Order By [Code] DESC")) !== false) {
            while ($obj = sqlsrv_fetch_object($result)) {
                $last_code = $obj->Code;
            }
        }
        $last_code = $last_code + 1;

        $sand_group_from = $this->post('sanad_qroup_from', 0);
        $sand_group_to = $this->post('sanad_qroup_to', 15);

        $this->load->model('B_db');
        $sql = "select DISTINCT(sanad_qroup),count(sanad_qroup) as ct from migrate1_sql where IsMigrated=0 AND sanad_qroup BETWEEN $sand_group_from AND $sand_group_to group by sanad_qroup order by ct  desc";
        $sanads = $this->B_db->run_query($sql);

        foreach ($sanads as $sanad) {
            $sql = "Select * from migrate1_sql Where IsMigrated=0 AND sanad_qroup IN (" . $sanad['sanad_qroup'] . ") Order By Tarikh,Radif ASC";
            $records = ${'list_' . $sanad['sanad_qroup']}[] = $this->B_db->run_query($sql);

            //line base query string builder
            foreach ($records as $rec) {

                $str_procedure = array();
                $str_procedure[] = $rec['SalMali'] . "," . $rec['Shobe'] . "," . $last_code . ",'" . $rec['Sharh'] . "','" . $rec['Sharh'] . "','" . $rec['Tarikh'] . "'," . $rec['CodeKol'] . ",
                " . $rec['CodeMoin'] . "," . $rec['CodeTaf_Group'] . "," . $rec['CodeTaf'] . "," . $rec['CodeTaf2_Group'] . "," . $rec['CodeTaf2'] . "," . $rec['Ready'] . ",'" . $rec['Sharh'] . "'," . $rec['Bed'] . "," . $rec['Bes'] . ",
                " . $rec['Radif'] . "," . $rec['Pages'] . "," . $rec['ArzValue'] . "," . $rec['ArzFee'] . ",'" . $rec['RowDetXML'] . "'";
                $IDS[$sanad['sanad_qroup']][] = $rec['ID'];
                //print_r($str_procedure);
                if (!empty($str_procedure)) {
                    $i = $j = 0;
                    $dbh = new PDO("sqlsrv:Server=$server;Database=$db", $user, $pass);
                    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    foreach ($str_procedure as $key => $query_string) {
                        $arr_name = array_fill(0, count($query_string), 'A');
                        $procedure_name = implode('', $arr_name);
                        echo "EXEC '[Fasih2].dbo.'.$procedure_name " . $str_procedure[0];
                        $stmt = $dbh->prepare("EXEC [Fasih2].dbo." . $procedure_name . " " . $str_procedure[0]);
                        if (!$stmt) {
                            echo "Statement could not be prepared.\n";
                            die(print_r(sqlsrv_errors(), true));
                        }
                        $res = $stmt->execute();
                        if ($res === true) {
                            echo "@";
                            $response = 'ss';
                            echo "$";
                            $pos = false;
                            echo "$";
                            if ($pos !== false) {
                                echo $query = "Update migrate1_sql set SqlServerResponse='" . implode('-', $response) . "' where ID IN(" . implode(',', $IDS[$key]) . ")";
                                $j++;
                            } else {
                                echo $query = "Update migrate1_sql set Code=$last_code,IsMigrated=1,Migrate_Date=now(), SqlServerResponse='" . implode('-', $response) . "#done' where ID IN(" . implode(',', $IDS[$key]) . ")";
                                $i++;
                            }
                            $this->B_db->run_query_put($query);
                        } else {
                            echo "sanad_qroup: " . $key . " failure";
                            die(print_r(sqlsrv_errors(), true));
                        }
                    }
                    $dbh = null;
                } else
                    echo "رکوردی برای انتقال یافت نشد.";
            }
            $last_code = $last_code + 1;
        }
        echo "True:" . $i . "-VS-False:" . $j;
    }

    public function sarfasl_taf_post()
    {
        $conn_array = array(
            "UID" => "sa",
            "PWD" => "302050",
            "Database" => "Fasih2",
        );
        $this->load->model('B_db');
        $conn = sqlsrv_connect('91.92.127.79\SQLEXPRESS,1433', $conn_array);
        $query = "select * from sarfasl_taffs where IsMigrated=0 Order By SarfaslTaffs_ID";
        $result = $this->B_db->run_query($query);
        if (!empty($result)) {
            foreach ($result as $obj) {
                    if(($this->gmp_sign($obj['CodeTaf'])!= $this->gmp_sign($obj['CodeTaf2'])) OR ($obj['CodeTaf']+$obj['CodeTaf2'])==(0))
                    {
                        if(($this->gmp_sign($obj['CodeTaf_Group'])== $this->gmp_sign($obj['CodeTaf'])))
                        {
                            if(($this->gmp_sign($obj['CodeTaf2_Group'])==$this->gmp_sign($obj['CodeTaf2'])))
                                $rule = true;
                            else
                            {
                                $rule = false;
                                print_r($obj);
                                echo 'این کدها در کنار هم نمی توانند بکار روند و غیر مجازند';
                                echo 'نقض قانون دوم:';
                                echo 'CodeTaf_Group AND CodeTaf --- CodeTaf2_Group AND CodeTaf2 یا هردو باید صفر باشن یا هردو بزرگتر از صفر' ;
                            }
                        }else
                        {
                            $rule = false;
                            print_r($obj);
                            echo 'این کدها در کنار هم نمی توانند بکار روند و غیر مجازند';
                            echo 'نقض قانون دوم:';
                            echo 'CodeTaf_Group AND CodeTaf --- CodeTaf2_Group AND CodeTaf2 یا هردو باید صفر باشن یا هردو بزرگتر از صفر' ;
                        }
                    } else
                    {
                        $rule = false;
                        print_r($obj);
                        echo 'این کدها در کنار هم نمی توانند بکار روند و غیر مجازند';
                        echo 'نقض قانون اول در این دو کد صورت گرفته است.';
                        echo 'نباید CodeTaf و CodeTaf2 هردو بزرگتر از صفر باشن.';
                    }
                if(!$rule)
                    exit;

                $query = 'INSERT INTO [Accounting].[SarFasl_TafFs] (Shobe , CodeTaf_Group, CodeTaf, CodeTaf2_Group, CodeTaf2, Sharh, Mahiat, Arz, Disable, IsMeghdari)
                    VALUES (' . $obj['Shobe'] . ', ' . $obj['CodeTaf_Group'] . ', ' . $obj['CodeTaf'] . ', ' . $obj['CodeTaf2_Group'] . ',' . $obj['CodeTaf2'] . ', \'' . $obj['Sharh'] . '\',
                     ' . $obj['Mahiat'] . ', ' . $obj['Arz'] . ', ' . $obj['Disable'] . ', ' . $obj['IsMeghdari'] . ') ; SELECT SCOPE_IDENTITY()';

                if ($conn) {
                    try {
                        if (($result = sqlsrv_query($conn, $query)) !== false) {

                            sqlsrv_next_result($result);
                            sqlsrv_fetch($result);
                            $last_id = sqlsrv_get_field($result, 0);

                            //echo $last_id."@Executed<br/>";
                            echo $query = "Update sarfasl_taffs set RowID=" . $last_id . ",IsMigrated=1,Migrate_Date=now() where SarfaslTaffs_ID IN(" . $obj['SarfaslTaffs_ID'] . ")";
                            $response = $this->B_db->run_query_put($query);

                            if ($response)
                                echo "تغییرات با موفقیت انتقال یافت";
                            else {
                                echo "مشکلی در انتقال وجود دارد";
                                print_r(sqlsrv_errors(), true);
                            }
                        } else {
                            echo $obj['RowID'] . "NOT@Executed<br/>";
                            print_r(sqlsrv_errors(), true);
                        }
                    } catch (PDOException $e) {
                        echo "SqlServer Bug in some IDS " . $obj['SerialID'];
                        //print_r($IDS);
                        //print "Error!: " . $e->getMessage() . "<br/>";
                    }
                    //update "sarfasl_taffs";
                } else {
                    print_r(sqlsrv_errors());
                }

            }
        } else {
            die("There is nothing to transfer!");
        }
    }


    public function sarfasl_post()
    {
        $conn_array = array(
            "UID" => "sa",
            "PWD" => "302050",
            "Database" => "Fasih2",
        );
        $this->load->model('B_db');
        $conn = sqlsrv_connect('91.92.127.79\SQLEXPRESS,1433', $conn_array);

        $query = "select * from SarFasl where IsMigrated=0 Order By Sarfasl_ID";
        $result = $this->B_db->run_query($query);

        if (!empty($result)) {
            $i = 0;
            foreach ($result as $obj) {
                $sql1 = "Select * From [Fasih2].[Accounting].[SarFasl] Where [Shobe]=" . $obj['Shobe'] . " AND [Grp]=" . $obj['Grp'] . " AND [CodeKol]=" . $obj['CodeKol'] . "
                AND [CodeMoin]=" . $obj['CodeMoin'] . " AND [CodeTaf]=" . $obj['CodeTaf'] . " AND [CodeTaf2]=" . $obj['CodeTaf2'];
                if (($result = sqlsrv_query($conn, $sql1)) !== false) {
                    $objSQL = sqlsrv_fetch_object($result);
                    if (!empty($objSQL)) {
                        //echo $objSQL->CodeMoin." @";
                    } else {
                        $i++;
                        $query = 'INSERT INTO [Fasih2].[Accounting].[SarFasl] ([Shobe],[Grp],[CodeKol],[CodeMoin],[CodeTaf],[CodeTaf2],[Sharh])
                        VALUES (
                        ' . $obj['Shobe'] . ', ' . $obj['Grp'] . ', ' . $obj['CodeKol'] . ', ' . $obj['CodeMoin'] . ',
                        ' . $obj['CodeTaf'] . ',' . $obj['CodeTaf2'] . ', \'' . $obj['Sharh'] . '\')';
                        if ($conn) {
                            if (($result = sqlsrv_query($conn, $query)) !== false) {
                                $query = "Update SarFasl set IsMigrated=1,Migrate_Date=now() where Sarfasl_ID IN(" . $obj['Sarfasl_ID'] . ")";
                                $response = $this->B_db->run_query_put($query);
                                if ($response)
                                    echo "تغییرات با موفقیت انتقال یافت";
                                else {
                                    echo "مشکلی در انتقال وجود دارد";
                                    die(print_r(sqlsrv_errors(), true));
                                }
                            } else {
                                echo "NOT@Executed<br/>";
                                $query = "Update SarFasl set IsMigrated=1,Migrate_Date=now() where Sarfasl_ID IN(" . $obj['Sarfasl_ID'] . ")";
                                $response = $this->B_db->run_query_put($query);
                                if ($response)
                                    echo "تغییرات با موفقیت انتقال یافت";
                                else {
                                    echo "مشکلی در انتقال وجود دارد";
                                    die(print_r(sqlsrv_errors(), true));
                                }
                            }
                        } else {
                            //die(print_r(sqlsrv_errors(), true));
                        }
                    }
                }
            }
            echo "Count=" . $i;
        }
    }

    public function sql_post()
    {
        $server = "91.92.127.79";
        $db = "Fasih2";
        $pass = "302050";
        $user = "sa";

        try {
            $dbh = new PDO("sqlsrv:Server=$server;Database=$db", $user, $pass);
            $stmt = $dbh->prepare("EXEC AAA 1399,1,34,'1399/10/30',34, 1,9,1,0,0,0,'sharh satre avval' ,1000,0,1,0,0,0,'@@@',1399,1,34,'1399/10/30',10,20,0,1,2,1,0,'sharh satre dovm',0,1000,2,0,0,0,'###' ");

            $stmt->execute();
            do {
                $data = $stmt->fetchAll();
                var_dump($data);
            } while ($stmt->nextRowset() && $stmt->columnCount());

            $dbh = null;
        } catch (PDOException $e) {
            echo "not connected@@@@";
            print "Error!: " . $e->getMessage() . "<br/>";
        }
        die;


        $conn_array = array(
            "UID" => "sa",
            "PWD" => "302050",
            "Database" => "Fasih2",
        );
        $conn = sqlsrv_connect('91.92.127.79,1433', $conn_array);
        if ($conn) {
            echo "connected";
            if (($result = sqlsrv_query($conn, "SELECT TOP 1000 * FROM [Fasih2].[Accounting].[sarfasl_taffs]")) !== false) {
                while ($obj = sqlsrv_fetch_object($result)) {
                    echo $obj->Sharh . '<br />';
                }
            }
        } else {

            print_r(sqlsrv_errors());
            echo "not connected!!!!!!!";
        }


        $conn_array1 = array(
            "UID" => "sa",
            "PWD" => "aA123456@#",
            "Database" => "maskan",
        );
        $conn1 = sqlsrv_connect('91.92.127.79,1433', $conn_array1);
        if ($conn1) {
            echo "connected########";
        } else {
            echo "not connected33############!!!!!!!";
            print_r(sqlsrv_errors());
        }


        $conn_array1 = array(
            "UID" => "SA",
            "PWD" => "aA123456",
            "Database" => "sales",
        );
        $conn1 = sqlsrv_connect('88.198.51.206,1433', $conn_array1);
        if ($conn1) {
            // echo "connected";
            if (($result = sqlsrv_query($conn1, "SELECT TOP 1000 [id],[name] ,[price] FROM [sales].[dbo].[Test]")) !== false) {
                while ($obj = sqlsrv_fetch_object($result)) {
                    echo $obj->colName . '@' . $obj->name . '<br />';
                }
            }
        } else {

            print_r(sqlsrv_errors());
            echo "not connected!!!!!!!";
        }
        die;


        if ($conn) {
            echo "connected";
            if (($result = sqlsrv_query($conn, "SELECT * FROM [Fasih2].[Accounting].[sarfasl_taffs] ")) !== false) {
                while ($obj = sqlsrv_fetch_object($result)) {
                    echo $obj->colName . '@' . $obj->Sharh . '<br />';
                    echo $query = 'INSERT INTO [Accounting].[SarFasl_TafFs] (RowID, Shobe, CodeTaf_Group, CodeTaf, CodeTaf2_Group, CodeTaf2, Sharh, Mahiat, Arz, Disable, IsMeghdari, SendDate)
                    VALUES (' . $obj->RowID . ', ' . $obj->Shobe . ', ' . $obj->CodeTaf_Group . ', ' . $obj->CodeTaf . ', ' . $obj->CodeTaf2_Group . ',' . $obj->CodeTaf2 . ', "' . $obj->Sharh . '", ' . $obj->Mahiat . ', ' . $obj->Arz . ', ' . $obj->Disable . ', ' . $obj->IsMeghdari . ', GETDATE())';
                    echo "@@@";
                    /*
                     if(($result = sqlsrv_query($conn,$query)) !== false){
                        echo $obj->RowID.'@Ececuted<br/>';
                        die( print_r( sqlsrv_errors(), true));
                    }
                    else{
                        echo $obj->RowID.'@NOT-Ececuted<br/>';
                        die( print_r( sqlsrv_errors(), true));
                    }
                     * */

                }
            }
        } else {
            die(print_r(sqlsrv_errors(), true));
        }
    }

    public function sqlpdo_post()
    {
        $server = "93.113.239.33\SQLEXPRESS,1433";
        $db = "Fasih2";
        $pass = "302050";
        $user = "sa";

        try {
            $dbh = new PDO('sqlsrv:Server=$server;Database=$db', $user, $pass);
            foreach ($dbh->query('SELECT * from [Fasih2].[Accounting].[sarfasl_taffs]') as $row) {
                print_r($row);
            }
            $dbh = null;
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
        }
    }

    public function sqlsrv_post($operate = "select")
    {

        //phpinfo();
        //print_r(sqlsrv_errors(), true);
        //die();
        //if($operate =='select'){
        $db = $this->load->database("sqldb", TRUE);


        try {
            if ($db->call_function('error') !== 0) {
                // Failed to connect
                echo "Failed to connect";
            }
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }

        //echo $connected = $db->initialize();

        $sql = "Select * from [Fasih2].[Accounting].[sarfasl_taffs]";
        $res = $db->query($sql)->result_array();
        foreach ($res as $row)
            print_r($row);

        /*
         *
         * $dsn = 'odbc:sql2';
        $dbUser = 'sa';
        $pw = '302050';
        $db = new PDO($dsn, $dbUser, $pw);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'utf8'");

        $sql = "Select * from [Fasih2].[Accounting].[sarfasl_taffs]";
        $statement = $db->prepare($sql);
        $statement->execute();
        $res =  $statement->fetch(PDO::FETCH_ASSOC);
        //$res = $db->query($sql)->result_array();

         * */
        //}
        die;

        $pdo = null;
        $dsn = 'mysql:host=localhost;dbname=varankho_deliwash_aref';
        $dbUser = 'root';
        $pw = '';
        try {
            $pdo = new PDO($dsn, $dbUser, $pw);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'utf8'");
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
        $this->db = $pdo;
        foreach ($res as $row) {
            try {
                $statement = $this->db->prepare('INSERT INTO sarfasl_taffs (RowID, Shobe, CodeTaf_Group, CodeTaf, CodeTaf2_Group, CodeTaf2, Sharh, Mahiat, Arz, Disable, IsMeghdari)
                VALUES (:RowID, :Shobe, :CodeTaf_Group, :CodeTaf, :CodeTaf2_Group, :CodeTaf2, :Sharh, :Mahiat, :Arz, :Disable, :IsMeghdari)');
                $statement->execute($row);
            } catch (PDOException $e) {
                echo $e->getMessage();
            }

            /*$statement = $this->db->prepare("INSERT INTO sarfasl_taffs
            (RowID) VALUES
            (:RowID)");
            $statement->execute(['RowID'=>1]);
            */
            //$statement->fetch();

        }

    }

    public function ping_post()
    {
        $host = '91.92.127.79';
        $host1 = "88.198.51.206";
        $output = shell_exec("ping $host1");
        var_dump($output);
        die;

        $port = 1433;
        $waitTimeoutInSeconds = 1;
        if ($fp = fsockopen($host, $port, $errCode, $errStr, $waitTimeoutInSeconds)) {
            // It worked
            var_dump($errStr);
            echo "It worked";
        } else {
            // It didn't work
            var_dump($errStr);
            echo "It didn't worked";
        }
        fclose($fp);
    }

    //Copy Data from sqlserver to mysql
    public function copy_to_mysql_post()
    {
        $db = $this->load->database("sqldb", TRUE);
        if ($db->call_function('error') !== 0) {
            // Failed to connect
            echo "Failed to connect";
        }
        $sql = "Select * from [Fasih2].[Accounting].[sarfasl_taffs]";
        $res = $db->query($sql)->result_array();

        $dsn = 'mysql:host=localhost;dbname=varankho_deliwash_aref';
        $dbUser = 'root';
        $pw = '';
        try {
            $pdo = new PDO($dsn, $dbUser, $pw);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'utf8'");
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
        foreach ($res as $row) {
            try {
                $statement = $pdo->prepare('INSERT INTO sarfasl_taffs (RowID, Shobe, CodeTaf_Group, CodeTaf, CodeTaf2_Group, CodeTaf2, Sharh, Mahiat, Arz, Disable, IsMeghdari, SendDate)
                VALUES (:RowID, :Shobe, :CodeTaf_Group, :CodeTaf, :CodeTaf2_Group, :CodeTaf2, :Sharh, :Mahiat, :Arz, :Disable, :IsMeghdari, now())');
                $statement->execute($row);
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
    }

    public function insert_to_sqlserver_post()
    {
        $sql = "Select * from sarfasl_taffs where SarfaslTaffs_ID=1";
        $res = $this->db->query($sql)->result_array();

        $dsn = 'odbc:sql2';
        $dbUser = 'sa';
        $pw = '302050';
        try {
            $pdo = new PDO($dsn, $dbUser, $pw);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'utf8'");

        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
        foreach ($res as $row) {
            unset($row["RowID"]);
            unset($row["SendDate"]);
            unset($row["SarfaslTaffs_ID"]);
            print_r($row);
            $row = Array
            (
                'Shobe' => 1,
                'CodeTaf_Group' => 0,
                'CodeTaf' => 0,
                'CodeTaf2_Group' => 100,
                'CodeTaf2' => 2,
                'Sharh' => '',
                'Mahiat' => 1,
                'Arz' => 0,
                'Disable' => 0,
                'IsMeghdari' => 0
            );

            try {

                $statement = $pdo->prepare('INSERT INTO [Fasih2].[Accounting].[SarFasl_TafFs] ( Shobe, CodeTaf_Group, CodeTaf, CodeTaf2_Group, CodeTaf2, Sharh, Mahiat, Arz, Disable, IsMeghdari)
                VALUES ( :Shobe, :CodeTaf_Group, :CodeTaf, :CodeTaf2_Group, :CodeTaf2, :Sharh, :Mahiat, :Arz, :Disable, :IsMeghdari)');
                //$pdo->query('SET IDENTITY_INSERT [Fasih2].[Accounting].[SarFasl_TafFs] ON');
                $statement->execute($row);
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
    }

    public function pdo_test_post()
    {

        //$this->db = $this->load->database('pdodb',TRUE);

        $pdo = null;
        $dsn = 'mysql:host=localhost;dbname=varankho_deliwash_aref';
        $dbUser = 'root';
        $pw = '';
        try {
            $pdo = new PDO($dsn, $dbUser, $pw);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'utf8'");
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
        $this->db = $pdo;

        $statement = $this->db->prepare("select * from user_tb where user_name like '%' :username '%' ");
        $statement->execute(array(':username' => "a"));
        $row = $statement->fetch();
        var_dump($row);
        die;


        $stmt = $this->db->prepare("select * from user_tb where user_name like '%' :username '%' ");
        $username = "محمد";
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $get_user_data = $stmt->fetch(PDO::FETCH_ASSOC);

        var_dump($get_user_data);
        echo $data_exists = ($get_user_data !== false);
        die;


        $pdo = null;
        $dsn = 'mysql:host=localhost;dbname=varankho_deliwash_aref';
        $dbUser = 'root';
        $pw = '';
        try {
            $pdo = new PDO($dsn, $dbUser, $pw);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo 'Connection established.<br/>';
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }


        $db = $pdo;
        /*
        $query = $this->db->get('user_tb');
        foreach ($query->result() as $row)
        {
            var_dump($row->user_name); //testing purpose
        }
        die;
        */
        //$query = $this->db->get('user_tb');
        //var_dump($query->result());
        $pQuery = $db->prepare(function ($db) {
            return $db->table('user_db')
                ->insert([
                    'user_name' => 'x',
                    'user_family' => 'yyyy',
                    'user_mobile' => 'tt'
                ]);
        });

// Collect the Data
        $user_name = 'John Doe';
        $user_family = 'j.doe@example.com';
        $user_mobile = '09124518130';
// Run the Query
        echo $results = $pQuery->conn_id->execute($user_name, $user_family, $user_mobile);

        $query = $this->db->get('user_tb');
        foreach ($query->result() as $row) {
            var_dump($row->user_name); //testing purpose
        }
    }

    public function clean_requestip_tb_post()
    {
        $sql = "Delete from requestip_tb where requestip_timestamp <= (NOW() - INTERVAL 7 Day)";
        return $this->db->query($sql);
    }

    public function uuid_post()
    {

        $this->load->helper('string');
        echo random_string('alnum', 20);
        die;
    }

    function getVariavleName($var)
    {
        foreach ($GLOBALS as $varName => $value) {
            if ($value === $var) {
                return $varName;
            }
        }
        return;
    }

    public function generate_filter_post()
    {
        $this->load->model('B_db');
        $tbl = $this->post('tbl');
        $first_field = $this->post('first_field');
        $sql = "SHOW COLUMNS FROM ".$tbl;
        $result = $this->B_db->run_query($sql);
        $str = '$filter="";'."\r\n";
        foreach($result as $key=>$row){
            $str .= '$'.$row["Field"].' = $this->post("'.$row["Field"].'");'."\r\n";
        }
        $str .= ''."\r\n";
        $str .= 'if($'.$first_field.' !=\'\') {'."\r\n";
        $str .= '$filter .= "  '.$first_field.'=\'" . $'.$first_field.' . "\'";'."\r\n";
        $str .= '}else{$filter .=" 1=1 "; }'."\r\n";
        $str .= ''."\r\n";
        foreach($result as $key=>$row){
            if($row['Field']!=$first_field){
                if('varchar' == substr($row['Type'],0,7) AND strpos($row["Field"], 'timestamp'))
                {
                    $str .= 'if($'.$row["Field"].'_start !=\'\') {'."\r\n";
                    $str .= '$filter .= " And '.$row["Field"].'>=\'" . $'.$row["Field"].'_start . "\'";'."\r\n";
                    $str .= '}else{$filter .=" AND 1=1 "; }'."\r\n";
                    $str .= ''."\r\n";
                    $str .= 'if($'.$row["Field"].'_end !=\'\') {'."\r\n";
                    $str .= '$filter .= " And '.$row["Field"].'<=\'" . '.$row["Field"].'_end . "\'";'."\r\n";
                    $str .= '}else{$filter .=" AND 1=1 "; }'."\r\n";
                    $str .= ''."\r\n";
                }
                elseif('varchar' == substr($row['Type'],0,7))
                {
                    $str .= 'if($'.$row["Field"].' !=\'\') {'."\r\n";
                    $str .= '$filter .= "  '.$row["Field"].' LIKE \'" . $'.$row["Field"].' . "\'";'."\r\n";
                    $str .= '}else{$filter .=" AND 1=1 "; }'."\r\n";
                    $str .= ''."\r\n";
                }
                else
                {
                    $str .= 'if($'.$row["Field"].' !=\'\') {'."\r\n";
                    $str .= '$filter .= "  '.$row["Field"].'=\'" . $'.$row["Field"].' . "\'";'."\r\n";
                    $str .= '}else{$filter .=" AND 1=1 "; }'."\r\n";
                    $str .= ''."\r\n";
                }

            }
        }
        $str .= '$limit = $this->post("limit");'."\r\n";
        $str .= '$offset = $this->post("offset");'."\r\n";
        $str .= '$limit_state ="";'."\r\n";
        $str .= 'if($limit!="" & $offset!="")'."\r\n";
        $str .= '$limit_state = "LIMIT ".$offset.",".$limit;'."\r\n";

        $myfile = fopen("abc/".$tbl.".txt", "w") or die("Unable to open file!");
        fwrite($myfile, $str);
        fclose($myfile);
    }

}

/*
       $ID=$data[0][0]['ID'];
       $SalMali=$data[0][0]['SalMali'];
       $Shobe=$data[0][0]['Shobe'];
       $Tarikh=$data[0][0]['Tarikh'];
       $CodeKol=$data[0][0]['CodeKol'];
       $CodeMoin=$data[0][0]['CodeMoin'];
       $CodeTaf_Group=$data[0][0]['CodeTaf_Group'];
       $CodeTaf=$data[0][0]['CodeTaf'];
       $CodeTaf2_Group=$data[0][0]['CodeTaf2_Group'];
       $CodeTaf2=$data[0][0]['CodeTaf2'];
       $Ready=$data[0][0]['Ready'];
       $Sharh=$data[0][0]['Sharh'];
       $Bed=$data[0][0]['Bed'];
       $Bes=$data[0][0]['Bes'];
       $Radif=$data[0][0]['Radif'];
       $Pages=$data[0][0]['Pages'];
       $ArzValue=$data[0][0]['ArzValue'];
       $ArzFee=$data[0][0]['ArzFee'];
       $RowDetXML= $data[0][0]['RowDetXML'];

       $ID1=$data[1][0]['ID'];
       $SalMali1=$data[1][0]['SalMali'];
       $Shobe1=$data[1][0]['Shobe'];
       $Tarikh1=$data[1][0]['Tarikh'];
       $CodeKol1=$data[1][0]['CodeKol'];
       $CodeMoin1=$data[1][0]['CodeMoin'];
       $CodeTaf_Group1=$data[1][0]['CodeTaf_Group'];
       $CodeTaf1=$data[1][0]['CodeTaf'];
       $CodeTaf2_Group1=$data[1][0]['CodeTaf2_Group'];
       $CodeTaf21=$data[1][0]['CodeTaf2'];
       $Ready1=$data[1][0]['Ready'];
       $Sharh1=$data[1][0]['Sharh'];
       $Bed1=$data[1][0]['Bed'];
       $Bes1=$data[1][0]['Bes'];
       $Radif1=$data[1][0]['Radif'];
       $Pages1=$data[1][0]['Pages'];
       $ArzValue1=$data[1][0]['ArzValue'];
       $ArzFee1=$data[1][0]['ArzFee'];
       $RowDetXML1= $data[1][0]['RowDetXML'];
       //echo "$SalMali,$Shobe,$last_code,'$Tarikh',$CodeKol, $CodeMoin,$CodeTaf_Group,$CodeTaf,$CodeTaf2_Group,$CodeTaf2,$Ready,'$Sharh' ,$Bed,$Bes,$Radif,$Pages,$ArzValue,$ArzFee,'$RowDetXML',$SalMali1,$Shobe1,$last_code,'$Tarikh1',$CodeKol1, $CodeMoin1,$CodeTaf_Group1,$CodeTaf1,$CodeTaf2_Group1,$CodeTaf21,$Ready1,'$Sharh1' ,$Bed1,$Bes1,$Radif1,$Pages1,$ArzValue1,$ArzFee1,'$RowDetXML1' ";
       */

/*
if (($result = sqlsrv_query($conn, "SELECT TOP 1 [CodeTaf_Group] FROM [Accounting].[SarFasl_TafFs]
                                  Where [CodeTaf]= " . $Code_taf . " Order By [CodeTaf_Group] DESC")) !== false) {
        while ($obj = sqlsrv_fetch_object($result)) {
            $last_codetaf_group = $obj->CodeTaf_Group;
        }
    }
*/