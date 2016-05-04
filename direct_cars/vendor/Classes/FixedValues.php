<?php
Use Zend\Mime, Zend\Mail\Message;
use Zend\Mail;
class Fixedvalues
{
	public function __construct()
	{
		// Constructor of the class
	}
	public function get_month_array()
	{
		return array("01" => "Jan",
					 "02" => "Feb",
					 "03" => "Mar",
					 "04" => "Apr",
					 "05" => "May",
					 "06" => "Jun",
					 "07" => "Jul",
					 "08" => "Aug",
					 "09" => "Sep",
					 "10" => "Oct",
					 "11" => "Nov",
					 "12" => "Dec");
	}
	public function get_owner_array()
	{
		return array("1" => "1",
					"2" => "2",
					"3" => "3",
					"4" => "4");
	}
	public function get_color_array()
	{
		return array(	"Beige" => "Beige",
						"Black" => "Black",
						"Blue" => "Blue",
						"Bluish Silver" => "Bluish Silver",
						"Brown" => "Brown",
						"Charcoal" => "Charcoal",
						"Gold" => "Gold",
						"Gray" => "Gray",
						"Green" => "Green",
						"Greenish Silver" => "Greenish Silver",
						"Maroon Red" => "Maroon Red",
						"Orange" => "Orange",
						"Pink" => "Pink",
						"Purple" => "Purple",
						"Red" => "Red",
						"Silver" => "Silver",
						"White" => "White",
						"Yellow" => "Yellow",
					);
	}
	public function get_fuel_type()
	{
		return array("Petrol" => "Petrol",
					  "Diesel" => "Diesel",
					  "CNG" => "CNG",
					  "LPG" => "LPG",
					  "Hybrid" => "Hybrid",
					  "Electric" => "Electric");
	}
	public function get_tranmission_type()
	{
		return array("Automatic" => "Automatic",
					  "Manual" => "Manual");
	}
	public function get_reg_city()
	{
		return array("Gurgaon" => "Gurgaon",
					 "Faridabad" => "Faridabad",
					 "New Delhi" => "New Delhi",
					 "Noida" => "Noida",
					 "Ghaziabad" => "Ghaziabad");
	}
	public function get_car_status()
	{
		return array("Fresh Arrival" => "Fresh Arrival",
					 "Available" => "Available",
					 "Sold Out" => "Sold Out",
					 "Coming Soon" => "Coming Soon");
	}
	public function get_insurance()
	{
		return array("0" => "No Insurance",
					 "1" => "Comprehensive",
					 "2" => "Third Party");
	}
	
	public function get_certified_by()
	{
		return array("Carnation Certified" => "Carnation Certified",
					 "Direct Cars Certified" => "Direct Cars Certified");
	}
	public function get_warranty()
	{
		return array("6 Months warranty" => "6 Months warranty",
					 "12 Months warranty" => "12 Months warranty");
	}
	public function get_free_services()
	{
		return array("1"=>"1","2"=>"2","3"=>"3");
	}
	public function get_service_history()
	{
		return array("Verified OK" => "Verified OK",
					"Not Available" => "Not Available");
	}
	public function get_onraod_assistance()
	{
		return array("6 Months" => "6 Months",
					 "12 Months" => "12 Months");
	}
	public function get_km_driven()
	{
		return array("10000" => "10000",
					 "20000" => "20000",
					 "30000" => "30000",
					 "40000" => "40000",
					 "50000" => "50000",
					 "60000" => "60000",
					 "70000" => "70000",
					 "80000" => "80000",
					 "90000" => "90000",
					 "100000" => "100000");
	}
	public function min_price_range()
	{
		return array("50000"  => "50000",
					 "100000" => "1 lakh",
					 "200000" => "2 lakh",
					 "300000" => "3 lakh",
					 "400000" => "4 lakh",
					 "500000" => "5 lakh",
					 "600000" => "6 lakh",
					 "700000" => "7 lakh",
					 "800000" => "8 lakh",
					 "900000" => "9 lakh",
					 "1000000" => "10 lakh",
					 "1500000" => "15 lakh",
					 "2000000" => "20 lakh",
					 "2500000" => "25 lakh",
					 "3000000" => "30 lakh");
	}
	public function max_price_range()
	{
		return array("50000"  => "50000",
					 "100000" => "1 lakh",
					 "200000" => "2 lakh",
					 "300000" => "3 lakh",
					 "400000" => "4 lakh",
					 "500000" => "5 lakh",
					 "600000" => "6 lakh",
					 "700000" => "7 lakh",
					 "800000" => "8 lakh",
					 "900000" => "9 lakh",
					 "1000000" => "10 lakh",
					 "1500000" => "15 lakh",
					 "2000000" => "20 lakh",
					 "2500000" => "25 lakh",
					 "3000000" => "30 lakh",
					 "4000000" => "40 lakh",
					 "5000000" => "50 lakh",
					 "6000000" => "60 lakh",
					 "7000000" => "70 lakh",
					 "8000000" => "80 lakh",
					 "9000000" => "90 lakh",
					 "10000000" => "1 Crore",);
	}
	
	public function sendSMS($message,$mobilenumbers)
	{
		//Please Enter Your Details
		$user="directcars"; //your username
		$password="Direct12#"; //your password
		
		$senderid="SMSCountry"; //Your senderid
		$messagetype="N"; //Type Of Your Message
		$DReports="Y"; //Delivery Reports
		$url="http://www.smscountry.com/SMSCwebservice_Bulk.aspx";
		$message = str_replace("<br>", "\n", $message);
		$message = urlencode($message);
		$ch = curl_init();
		if (!$ch){die("Couldn't initialize a cURL handle");}
		$ret = curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt ($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt ($ch, CURLOPT_POSTFIELDS,
		"User=$user&passwd=$password&mobilenumber=$mobilenumbers&message=$message&sid=$senderid&mtype=$messagetype&DR=$DReports");
		$ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//If you are behind proxy then please uncomment below line and provide your proxy ip with port.
		// $ret = curl_setopt($ch, CURLOPT_PROXY, "PROXY IP ADDRESS:PORT");
		$curlresponse = curl_exec($ch); // execute
		if(curl_errno($ch))
		echo 'curl error : '. curl_error($ch);
		if (empty($ret)) {
		// some kind of an error happened
		die(curl_error($ch));
		curl_close($ch); // close cURL handler
		} else {
		$info = curl_getinfo($ch);
		//print_r($info);
		curl_close($ch); // close cURL handler
		//echo "******************";
		echo $curlresponse; //echo "Message Sent Succesfully" ;
		}
	}
	
	public function sendmail($message,$subject,$email)
	{
		$text = new Mime\Part($message);
			$text->type = Mime\Mime::TYPE_HTML;
			$text->charset = 'utf-8';
			// then add them to a MIME message
			$mimeMessage = new Mime\Message();
			$mimeMessage->setParts(array($text));

			// and finally we create the actual email
			$message = new Message();
			$message->setBody($mimeMessage);

			$from = "support@directcars.in";

			$message->setFrom($from);
			$message->addTo($email);
			$message->setSubject($subject);
			
			$transport = new Mail\Transport\Sendmail();
			$result = $transport->send($message);
			
			return $result;
	}
	
	public function sendmailattachment($message,$subject,$email,$attachmentname)
	{
			$config = $this->getServiceLocator()->get('Config');
			$text = new Mime\Part($message);
			$text->type = Mime\Mime::TYPE_HTML;
			$text->charset = 'utf-8';
			$somefilePath = $config['public_folder_path']."images/rctransfer/".$attachmentname['name'];
			$fileContent = fopen($somefilePath, 'r');
			$attachment = new Mime\Part($fileContent);
			
			$attachment->type = $attachmentname['type'];
			$attachment->filename = $attachmentname['name'];
			$attachment->disposition = Mime\Mime::DISPOSITION_ATTACHMENT;
			// Setting the encoding is recommended for binary data
			$attachment->encoding = Mime\Mime::ENCODING_BASE64;
			
			// then add them to a MIME message
			$mimeMessage = new Mime\Message();
			$mimeMessage->setParts(array($text,$attachment));

			// and finally we create the actual email
			$message = new Message();
			$message->setBody($mimeMessage);
			
			$from = "support@directcars.in";

			$message->setFrom($from);
			$message->addTo($email);
			$message->setSubject($subject);
			
			$transport = new Mail\Transport\Sendmail();
			$result = $transport->send($message);
			
			return $result;
	}
}
?>