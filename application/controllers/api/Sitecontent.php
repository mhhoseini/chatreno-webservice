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
class Sitecontent extends REST_Controller {

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
        //die;
        //if(isset($this->input->request_headers()['Authorization']))$employee_token_str = $this->input->request_headers()['Authorization'];
        $this->load->model('B_db');
        $this->load->helper('my_helper');
        $command = $this->post("command");
        if($command == "add_sitecontent"){
            $sitecontent_page=$this->post('sitecontent_page');
            $sitecontent_place=$this->post('sitecontent_place');
            $sitecontent_inplace=$this->post('sitecontent_inplace');
            $sitecontent_mode=$this->post('sitecontent_mode');
            $sitecontent_title=$this->post('sitecontent_title');
            $sitecontent_text=$this->post('sitecontent_text');
            $sitecontent_image=$this->post('sitecontent_image');
            $sitecontent_image_code=$this->post('sitecontent_image_code');
            $sitecontent_btntxt=$this->post('sitecontent_btntxt');
            $sitecontent_link=$this->post('sitecontent_link');
            
            $employeetoken=checkpermissionemployeetoken($employee_token_str,'new','sitecontent');
            if($employeetoken[0]=='ok')
            {
                $sitecontent= $this->B_db->add_sitecontent($sitecontent_page,$sitecontent_place, $sitecontent_inplace,$sitecontent_mode,$sitecontent_title,$sitecontent_text,$sitecontent_image,$sitecontent_image_code,$sitecontent_btntxt,$sitecontent_link);

                if ($sitecontent['new_sitecontent_id']!=='')
                {
                    $sitecontent_id = $sitecontent['new_sitecontent_id'];
                    echo json_encode(array('result'=>"ok"
                    ,"data"=>array('sitecontent_id'=>$sitecontent_id)
                    ,'desc'=>'محتوی سایت اضافه شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }elseif($sitecontent['sitecontent_id']!==''){
                    $sitecontent_id = $sitecontent['sitecontent_id'];
                    echo json_encode(array('result'=>"error"
                    ,"data"=>array('sitecontent_id'=>$sitecontent_id)
                    ,'desc'=>'محتوی سایت تکراری است'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            }
        }

        if ($command=="get_sitecontent")
        {
            $query="select * from sitecontent_tb where ";
            $filter1 = $this->post('filter1');
            if(isset($filter1)){
                $query.=$filter1;}else{$query.=" 1=1 ";}
            $query.=" AND ";
            $filter2 = $this->post('filter2');
            if(isset($filter2)){
                $query.=$filter2;}else{$query.=" 1=1 ";}
            $query.=" AND ";
            $filter3 = $this->post('filter2');
            if(isset($filter3)){
                $query.=$filter3;}else{$query.=" 1=1 ";}
            $query.=" AND ";
            $filter4 = $this->post('filter2');
            if(isset($filter4)){
                $query.=$filter4;}else{$query.=" 1=1 ";}
            $query.=" ORDER BY sitecontent_id ASC";
            $result = $this->B_db->run_query_put($query);
            $output =array();
            foreach($result as $row)
            {
                $record=array();
                $record['sitecontent_id']=$row['sitecontent_id'];
                $record['sitecontent_page']=$row['sitecontent_page'];
                $record['sitecontent_place']=$row['sitecontent_place'];
                $record['sitecontent_inplace']=$row['sitecontent_inplace'];
                $record['sitecontent_mode']=$row['sitecontent_mode'];
                $record['sitecontent_title']=$row['sitecontent_title'];
                $record['sitecontent_text']=$row['sitecontent_text'];
                $record['sitecontent_image']=$row['sitecontent_image'];
                $record['sitecontent_btntxt']=$row['sitecontent_btntxt'];
                $record['sitecontent_link']=$row['sitecontent_link'];
                $output[]=$record;
            }
            echo json_encode(array('result'=>"ok"
            ,"data"=>$output
            ,'desc'=>'مشحصات محتوی سایت با  موفقیت ارسال شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }

        if ($command=="delete_sitecontent")
        {
            $sitecontent_id=$this->post('sitecontent_id');
            
            $employeetoken=checkpermissionemployeetoken($employee_token_str,'delete','sitecontent');
            if($employeetoken[0]=='ok')
            {
                $result = $this->B_db->del_sitecontent($sitecontent_id);

                if($result){
                    echo json_encode(array('result'=>"ok"
                ,"data"=>$output
                ,'desc'=>'محتوی سایت مورد نظر حذف شد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }else{
                    echo json_encode(array('result'=>"error"
                    ,"data"=>$output
                    ,'desc'=>'محتوی سایت مورد نظر حذف نشد'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }
            }else{
                echo json_encode(array('result'=>$employeetoken[0]
                ,"data"=>$employeetoken[1]
                ,'desc'=>$employeetoken[2]),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            }
        }
        if ($command=="modify_sitecontent")
        {
            //later
        }
        if ($command=="gethedear_footer")
        {
            $hedear=array(
                'logo'=>array(
                    'image'=>IMGADD.'sitecontent/4.png',
                    'alt'=>'لوگو',
                    'url'=>''
                ),
                'caption'=>array(
                    'text'=>'با وارد کردن کد GIFT از تخفیف 30%  محصوص خرید بیمه بدنه خودرو برخوردار شوید',
                    'url'=>''
                ),
                'gift_image'=>array(
                    'image'=>IMGADD.'sitecontent/6.png',
                    'alt'=>'لوگو',
                    'url'=>''
                )
            );

            $footerlist=array(
                array('url'=>'www.aref24.com','title'=>'صفحه نخست'),
                array('url'=>'www.aref24.com','title'=>'خدمات'),
                array('url'=>'www.aref24.com','title'=>'درباره ما'),
                array('url'=>'www.aref24.com','title'=>'مطالب')
            );

            $footer=array(
                'footertitle'=>'عارف۲۴',
                'footeraddress'=>'تهران خیابان نلسون ماندلا،روبروی بیمه مرکزی ، ساختمان ترژر، واحد 303',
                'footertell'=>'021-22015219',
                'footerlist'=>$footerlist,
                'footerimg'=>array(
                    'image'=>IMGADD.'sitecontent/white-logo.png',
                    'alt'=>'لوگو',
                    'url'=>''
                )
            );
            echo json_encode(array(
                'header'=>$hedear,
                'footer'=>$footer),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
        if ($command=="getmain_page")
        {
            $result = $this->B_db->getmain_page();
            $output =array();
            foreach($result as $row)
            {
                $record['title']=$row['fieldinsurance_fa'];
                $record['image']=IMGADD.$row['fieldinsurance_logo_url'];
                $record['link']=$row['fieldinsurance_link'];
                $output[]=$record;
            }
            $insurancefield=$output;
            $cards=array(
                array('title'=>"مشاوره تخصصی بیمه",'text'=>"ارائه مشاوره تخصصی، کاملا بی طرفانه و رایگان توسط کارشناسان بیمه ای عارف۲۴",'image'=>IMGADD.'sitecontent/moshavere bime icon.png'),
                array('title'=>"مقایسه قیمت و شرایط بیمه",'text'=>"مقایسه قیمت، خدمات و پوشش ها برای انتخاب بهترین بیمه",'image'=>IMGADD.'sitecontent/moqayese qeymat icon.png'),
                array('title'=>"خرید سریع و آسان",'text'=>"عارف۲۴ ارسال فوری در تهران و مراکز استان ها بصورت کاملا رایگان دارد",'image'=>IMGADD.'sitecontent/kharid sari icon.png'),
                array('title'=>"پرداخت آسان و مطمئن",'text'=>"امکان پرداخت حق بیمه از طریق درگاه بانکی و بصورت چک در رشته های منتخب",'image'=>IMGADD.'sitecontent/pardakht asan icon.png'),
            );
            $content_box=array(
                'title'=>"معرفی خدمات عارف۲۴",
                'content'=>"عارف۲۴ سامانه مشاوره، مقایسه، استعلام قیمت ؛و خرید آنلاین بیمه است.
در وبسایت عارف۲۴ میتوانید قیمت انواع بیمه را از تمامی شرکتها استعلام بگیرید، شرایط و پوشش‌ها را با هم مقایسه کنید و بیمه مورد نظر خود را آنلاین سفارش دهید همچنین میتوانید برای مشاوره تخصصی و رایگان در مورد خرید بیمه نامه با کارشناسان ما تماس بگیرید و یا از مشاوره آنلاین استفاده کنید و با فاصله چند روز از سفارش میتوانید بیمه نامه خود را در محل تحویل بگیرید.
تا رسیدن به آخرین نقطه در فرایند خرید بیمه‌نامه موردنظرتان، عارف۲۴ با شماست.",
                'more_title'=>"بیشتر بخوانید",
                'more_url'=>'www.aref24.com'

            );
            echo json_encode(array(
                'insurancefield'=>array(
                    'title'=>"سامانه آنلاین بیمه",
                    'tellsuport_text'=>"مشاوره بیمه ای و پیگیری خسارت 24 ساعته به صورت تلفنی:",
                    'sub_title'=>"فقط کافی است بیمه مورد نظر خود را انتخاب کنید:",
                    'insurancefield'=>$insurancefield),
                'content_box'=>$content_box,
                'cards'=>$cards
                ),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
    }

}
