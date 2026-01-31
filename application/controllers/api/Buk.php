<?php

require APPPATH . '../vendor/autoload.php';
require FCPATH . '/vendor/autoload.php';

use Aws\S3\S3Client;
use Restserver\Libraries\REST_Controller;

class Buk extends REST_Controller
{
    public $s3Client;

    public function __construct()
    {
        parent::__construct();
        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region' => 'eu-west-1',
            'credentials' => [
                'key' => AWS_KEY,
                'secret' => AWS_SECRET_KEY
            ]
        ]);
    }

    public function index_post()
    {
        phpinfo();
        die;
        //echo nl2br(file_get_contents( "./vendor/autoload.php" ));
        //echo nl2br(file_get_contents( APPPATH."../vendor/autoload.php" ));
        //echo APPPATH .'../vendor/autoload.php';
        //echo DIR .'../vendor/autoload.php';
        //echo APPPATH;echo $this->config->item('composer_autoload');die;
        //return $this->load->view('form');
        /*
                APPPATH:D:\xampp\htdocs\aref24-backend\application\###<br>
                __DIR__:D:\xampp\htdocs\aref24-backend\application\controllers\api###<br>
                SYSDIR:system###<br>
                FCPATH:D:\xampp\htdocs\aref24-backend\###<br>
                SELF:index.php###<br>
                BASEPATH:D:\xampp\htdocs\aref24-backend\application\vendor\codeigniter\framework\system\###<br>
        */

        $this->load->model('B_db');
        $image_code = $this->post('image_code');
        $t = $this->B_db->get_image($image_code, 3600);
        var_dump($t);
        die;
        $listResponse = $this->s3Client->listBuckets();
        $buckets = $listResponse['Buckets'];
        foreach ($buckets as $bucket) {
            echo $bucket['Name'] . "\t" . $bucket['CreationDate'] . "\n";

            die;
            //$s3Client->createBucket(array('Bucket' => 'mybucket'));
            // Upload an object to Amazon S3
            $result = $s3Client->putObject(array(
                'Bucket' => 'folderupload',
                'Key' => 'data.txt',
                'Body' => 'Hello!'
            ));

            print_r($result);
            die;
// Access parts of the result object
            echo $result['Expiration'] . "\n";
            echo $result['ServerSideEncryption'] . "\n";
            echo $result['ETag'] . "\n";
            echo $result['VersionId'] . "\n";
            echo $result['RequestId'] . "\n";
// Get the URL the object can be downloaded from
            echo $result['ObjectURL'] . "\n";
        }
    }

    public function delete_garbage_post()
    {
        $this->load->Model('B_db');
        $result = $this->B_db->run_query('SELECT COUNT(*) FROM  image_tb where
                    image_id NOT IN (SELECT image_id FROM user_tb,image_tb it where user_back_national_image_code=image_code) AND
                    image_id NOT IN (SELECT image_id FROM user_tb,image_tb it where user_national_image_code=image_code) AND
                    image_id NOT IN (SELECT image_id FROM requst_ready_image_tb,image_tb it where requst_ready_image_code=image_code)   AND
                    image_id  IN (SELECT request_img_image_code FROM request_img_tb,request_tb where request_img_request_id=request_id AND request_last_state_id=1)  AND
                    image_id NOT IN (SELECT image_id FROM instalment_check_tb,image_tb where instalment_check_image_code=image_code) AND
                    image_id NOT IN (SELECT image_id FROM agent_tb,image_tb where agent_image_code=image_code)  AND
                    image_id NOT IN (SELECT image_id FROM damagefile_ready_image_tb,image_tb where damagefile_ready_image_code=image_code) AND
                    image_id NOT IN (SELECT damagefile_img_image_code FROM damagefile_img_tb where 1) AND
                    image_id NOT IN (SELECT image_id FROM damagefile_ready_image_tb,image_tb where damagefile_ready_image_code=image_code)  AND
                    image_id NOT IN (SELECT image_id FROM employee_tb,image_tb where employee_image_code=image_code)  AND
                    image_id NOT IN (SELECT image_id FROM expert_tb,image_tb where expert_image_code=image_code)  AND
                    image_id NOT IN (SELECT image_id FROM organ_tb,image_tb where organ_logo=image_code)  AND
                    image_id NOT IN (SELECT image_id FROM organ_tb,usermarketer_tb where organ_logo=image_code)');
        if (count($result) > 0) {
            foreach ($result as $item) {
                if (file_exists($item['image_url']))
                    unlink($item['image_url']);
                if (file_exists($item['image_tumb_url']))
                    unlink($item['image_tumb_url']);
                $this->B_db->run_query_put('Update image_tb set image_deleted=1 where image_id =' . $item['image_id']);
            }
            echo "Files delete completed!";
        } else
            echo "There is nothing to delete!";

    }

    public function listobj_post()
    {
        $bucket = 'folderupload';
        $client = $this->s3Client;
        $objectsListResponse = $client->listObjects(['Bucket' => $bucket]);

        $objects = $objectsListResponse['Contents'] ?? [];
        foreach ($objects as $object) {
            print_r($object) . "\n";
            echo $object['Key'] . "\t" . $object['Size'] . "\t" . $object['LastModified'] . "\n";
        }
        die;
        $iterator = $this->s3Client->getIterator('ListObjects', array(
            'Bucket' => $bucket
        ));
        foreach ($iterator as $object) {
            echo $object['Key'] . "\n";
        }
    }

    public function getAcl_post()
    {
        $bucket = 'folderupload';
        $client = $this->s3Client;
        // Gets the access control policy for a bucket
        try {
            $resp = $client->getBucketAcl([
                'Bucket' => $bucket
            ]);
            echo "Succeed in retrieving bucket ACL as follows: \n";
            var_dump($resp);
        } catch (AwsException $e) {
            // output error message if fails
            echo $e->getMessage();
            echo "\n";
        }
    }

    public function createbuk_post()
    {
        $bucket = 'folderupload';
        $result = $this->s3Client->createBucket(array(
            'Bucket' => $bucket,
            'LocationConstraint' => 'eu-west-1',
        ));

// Get the Location header of the response
        echo $result['Location'] . "\n";

// Get the request ID
        echo $result['RequestId'] . "\n";
        $result = $this->s3Client->createBucket(array('Bucket' => 'mybucket01'));
        var_dump($result);
    }

    public function dwnfile_post()
    {
        $bucket = 'folderupload';
        $client = $this->s3Client;
        $object = $client->getObject([
            'Bucket' => $bucket,
            'Key' => '12.jpg']);
        file_put_contents(__DIR__ . '/../files/122.jpg', $object['Body']->getContents());
    }

    public function dwnlink_post()
    {
        $bucket = 'folderupload';
        $client = $this->s3Client;
        $file_name = '12.jpg';

        $url = $client->getObjectUrl($bucket, $file_name);
        echo "<a href='$url' target='_blank'>Download</a>";
    }

    public function dwnpresigned_post()
    {
        $bucket = 'folderupload';
        $client = $this->s3Client;
        $expire_time = 3600;
        $_time = $this->post('expire_time');
        if ($_time != '')
            $expire_time = $_time;
        $file_name = $this->post('image_key');
        $keyExists = $this->s3Client->doesObjectExist($bucket, $file_name);
        if ($keyExists) {
            $cmd = $client->getCommand('GetObject', [
                'Bucket' => $bucket,
                'Key' => $file_name
            ]);
            $request = $client->createPresignedRequest($cmd, '+' . $expire_time . ' seconds');
            $presignedUrl = (string)$request->getUri();
            echo json_encode(array('result' => "ok", "data" => array('object_url' => $presignedUrl)), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } else
            echo json_encode(array('result' => "error", 'desc' => 'فایل مورد نظر یافت نشد'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function upfile_post()
    {
        $bucket = 'folderupload';
        $client = $this->s3Client;
        $file_name = $this->post('image_key');
        try {
            $result = $client->putObject([
                'Bucket' => $bucket,
                'Key' => $file_name,
                'SourceFile' => __DIR__ . '\..\files\12.jpg',
            ]);
        } catch (S3Exception $e) {
            echo $e->getMessage() . "\n";
        }
    }

    public function store()
    {
        $this->load->helper('form');
        $validated = $this->validate([
            'file' => [
                'uploaded[file]',
                'mime_in[file,image/jpg,image/jpeg,image/gif,image/png]',
                'max_size[file,4096]',
            ],
        ]);
        $msg = 'Please select a valid file';
        if ($validated) {
            $avatar = $this->request->getFile('file');
            $avatar->move(WRITEPATH . 'uploads');
            $data = [
                'name' => $avatar->getClientName(),
                'type' => $avatar->getClientMimeType()
            ];
            // Instantiate an Amazon S3 client.
            $s3Client = new S3Client([
                'version' => 'latest',
                'region' => 'eu-west-1',
                'credentials' => [
                    'key' => AWS_KEY,
                    'secret' => AWS_SECRET_KEY
                ]
            ]);

            $filename = '';
            $bucket = 'YOUR_BUCKET_NAME';
            $file_Path = __DIR__ . '/uploads/' . $filename;
            $key = basename($file_Path);

            try {
                $result = $s3Client->putObject([
                    'Bucket' => $bucket,
                    'Key' => $key,
                    'Body' => fopen($file_Path, 'r'),
                    'ACL' => 'public-read', // make file 'public'
                ]);
                $msg = 'File has been uploaded';
            } catch (Aws\S3\Exception\S3Exception $e) {
                //$msg = 'File has been uploaded';
                echo $e->getMessage();
            }
            $msg = 'File has been uploaded';
        }

        return redirect()->to(base_url('public/index.php/form'))->with('msg', $msg);
    }
}