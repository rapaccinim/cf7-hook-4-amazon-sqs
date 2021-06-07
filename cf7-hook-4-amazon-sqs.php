<?php 

/*
 
Plugin Name: CF7 hook for SQS
 
Plugin URI: https://www.marcorapaccini.it
 
Description: Amazon SQS Hook for Contact Form 7
 
Version: 1.0.0
 
Author: Marco Rapaccini
 
Author URI: https://www.marcorapaccini.it
 
License: MIT
 
Text Domain: prefix-plugin-name
 
*/

// we need composer stuff
require 'vendor/autoload.php';

// and Amazon SQS stuff
use Aws\Sqs\SqsClient; 
use Aws\Exception\AwsException;

// when user post data in the CF7 form...
add_filter('wpcf7_posted_data','add_custom_field');

// ...trigger the following function that adds a custom field with MD5 value
function add_custom_field($posted_data){

	// get current title
	$wpcf = WPCF7_ContactForm::get_current();
	$title = $wpcf->title();

	// trigger only if the CF7 form title is specified
	if (strpos($title, 'SpecificCF7Title')){

		// get the timestamp
		$timestamp_string = date("h:i:sa");

		// create a special string combining email and timestamp
		$mixed_string = $posted_data['email'] . $timestamp_string;

		// create and MD5 from the combined string
		$md5_string = md5($mixed_string);

		// pass the value in the final email that will be sent to the recipients
		$posted_data['custom_unique_id_4_SQS'] = $md5_string;

		// pass the form ID too
		$posted_data['form_unique_id'] = $wpcf->id();
	}

    return $posted_data;

};


// when email is sent to receipients...
add_action('wpcf7_mail_sent','cf7_amazon_sqs');

// ...trigger the following the function that sends message to Amazon SQS
function cf7_amazon_sqs($contact_form){

	// get the form title
	$title = $contact_form->title();

	// this is the url for the Amazon SQS
	$queueUrl = 'yourAmazonSQSURL';

	// if the form has the right title
	if (strpos($title, 'CustomSimecomForm4SQS')){

		// this is the submission instance
		$submission = WPCF7_Submission::get_instance();

		// if the user has submitted the form
		if ($submission) {

			// get all the submitted data
			$posted_data = $submission->get_posted_data();
	
			// fields coming from the form
			$name = $posted_data['name'];
			$surname = $posted_data['surname'];
			$email = $posted_data['email'];			
			$phone = $posted_data['phone'];
			$city = $posted_data['comune'];
			$county = $posted_data['provincia'];

			$md5_string = $posted_data['custom_unique_id_4_SQS'];

			// [OPTIONAL] you have to encode data before sending if you want to send data in the MessageBody
			$data_to_send = json_encode([
				'Name' => $name,
				'Surname' => $surname
				'Email' => $email,
				'Phone' => $phone,
				'City' => $city,
				'County' => $county,
				'Id' => $md5_string
			]);
			

			// create the SQS client - this is not safe, use environment variables instead!
			$client = new SqsClient([
    			'region' => 'eu-central-1',
    			'version' => 'latest',
				'credentials' => [
       				'key'    => 'yourKeyHere',
        			'secret' => 'yourSecretHere'
    			]		
			]);
			
			// parameters to send to SQS (supposing that you want to send data as MessageAttributes)
			$params = [
			    'DelaySeconds' => 1,
			    'MessageAttributes' => [
			        "Name" => [
			            'DataType' => "String",
			            'StringValue' => $name
			        ],
			        "Surname" => [
			            'DataType' => "String",
			            'StringValue' => $surname
			        ],
			        "Email" => [
			            'DataType' => "String",
			            'StringValue' => $email
			        ],
			       	"Phone" => [
			            'DataType' => "String",
			            'StringValue' => $phone
			        ], 
			    	"City" => [
			            'DataType' => "String",
			            'StringValue' => $city
			        ],
			    	"County" => [
			            'DataType' => "String",
			            'StringValue' => $county
			        ],
			        "Id" => [
			            'DataType' => "String",
			            'StringValue' => $md5_string
			        ]
			    ],
			    'MessageBody' => $data_to_send,	// change this value with something else if you send data through MessageAttributes
			    'QueueUrl' => $queueUrl
			];

			// send the message to the SQS
			try{
    			$result = $client->sendMessage($params);
			}catch (AwsException $e) { 
				error_log($e->getMessage());
			}
 
		}
	}

	return;

}

