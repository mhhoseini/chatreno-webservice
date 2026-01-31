<?php

namespace Restserver\Libraries;
use Exception;
use stdClass;

defined('BASEPATH') OR exit('No direct script access allowed');

define('AWS_KEY', '00359daa-273f-4cd8-93f8-71674af6e879');
define('AWS_SECRET_KEY', '6319e80a6e1affba599996b4a0a97a90ece8df9801a537ac90589db1019c0202');
define('ENDPOINT', 'https://s3.ir-thr-at1.arvanstorage.ir');

if (is_php('5.4')) {
	require_once dirname(__FILE__).'/REST_Controller_54.php';
} else {
	require_once dirname(__FILE__).'/REST_Controller_53.php';
}

class REST_Controller extends \REST_Controller {


	public function __construct($config = 'rest') {

		parent::__construct($config);
	}

	/**
	 * Retrieve a value from the POST request arguments.
	 *
	 * @param string $key The key for the POST request argument to retrieve
	 * @param string $default_value
	 * @param boolean $xss_clean Whether the value should be XSS cleaned or not.
	 * @return array The POST argument value.
	 */
	public function post($key = NULL, $default_value="" , $xss_clean = TRUE)
	{
		if ($key === NULL)
		{
			return $this->_post_args;
		}
		$val =  $this->input->get_post($key, $xss_clean);
		if(!is_array($val))
		    $val =  $this->checkvar($val,'string','','')[1];
		if($val == NULL || $val == "" || !isset($val))
			return $default_value;
		else
			return $val;
	}

	function checkvar($var,$varmode,$varfilter,$defultvar)
	{
		if($var==null||$var==''){
			return [1,$defultvar];
		}else{
			$ttvar=$var;

			$var =strtolower($var);
			$tvar=$var;

			$var= preg_replace('~<script~', '<!--',$var);
			$var= preg_replace('~</script>~', '-->',$var);
			$var= preg_replace('~delete *from~', '',$var);
			$var= preg_replace('~drop *table~', '',$var);
			$var= preg_replace('~insert *into~', '',$var);
			$var= preg_replace('~or *1 *= *1~', '',$var);
			$var= preg_replace('~select *from~', '',$var);

			if($var!=$tvar){
				$query="INSERT INTO attack_tb( attack_ip, attack_text, attack_timestamp) VALUES
                            ( '".get_client_ip()."', '".$ttvar."', now() )";
				$CI = get_instance();
				$CI->load->model('B_db');
				$result=$CI->B_db->run_query($query);
			}else{
				$var=$ttvar;
			}
			$var=filter_var($var, FILTER_SANITIZE_STRING);

			if($varmode=='string')
			{
				if($varfilter==''){
					return [1,$var];
				}else if($varfilter=='email'){
					if(filter_var($var, FILTER_VALIDATE_EMAIL))
					{	return [1,$var];}else{	return [0,$var];}
				}else if($varfilter=='ip'){
					if(filter_var($var, FILTER_VALIDATE_IP))
					{return [1,$var];}else{	return [0,$var];}
				}
			}else if($varmode=='int'){
				if(is_numeric ($var)){
					return [1,$var];
				}else{
					return [0,$var];
				}
			}
		}
	}

	/**
	 * Determines if output compression is enabled
	 *
	 * @var boolean
	 */
	protected $_zlib_oc = FALSE;
	/**
	 * Response
	 *
	 * Takes pure data and optionally a status code, then creates the response.
	 *
	 * @param array $data
	 * @param null|int $http_code
	 */
	public function response($data = NULL, $http_code = NULL, $continue = false)
	{
		global $CFG;

		// If data is empty and not code provide, error and bail
		if (empty($data) && $http_code === null)
		{
			$http_code = 404;

			//create the output variable here in the case of $this->response(array());
			$output = $data;
		}

		// Otherwise (if no data but 200 provided) or some data, carry on camping!
		else
		{
			// Is compression requested?
			if ($CFG->item('compress_output') === TRUE && $this->_zlib_oc == FALSE)
			{
				if (extension_loaded('zlib'))
				{
					if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) AND strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE)
					{
						ob_start('ob_gzhandler');
					}
				}
			}

			is_numeric($http_code) OR $http_code = 200;

			// If the format method exists, call and return the output in that format
			if (method_exists($this, '_format_'.$this->response->format))
			{
				// Set the correct format header
				header('Content-Type: '.$this->_supported_formats[$this->response->format]);

				$output = $this->{'_format_'.$this->response->format}($data);
			}

			// If the format method exists, call and return the output in that format
			elseif (method_exists($this->format, 'to_'.$this->response->format))
			{
				// Set the correct format header
				header('Content-Type: '.$this->_supported_formats[$this->response->format]);

				$output = $this->format->factory($data)->{'to_'.$this->response->format}();
			}

			// Format not supported, output directly
			else
			{
				$output = $data;
			}
		}

		header('HTTP/1.1: ' . $http_code);
		header('Status: ' . $http_code);

		// If zlib.output_compression is enabled it will compress the output,
		// but it will not modify the content-length header to compensate for
		// the reduction, causing the browser to hang waiting for more data.
		// We'll just skip content-length in those cases.
		if ( ! $this->_zlib_oc && ! $CFG->item('compress_output'))
		{
			header('Content-Length: ' . strlen($output));
		}

		exit($output);
	}

}
