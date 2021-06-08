![CF7 Hook 4 Amazon SQS cover](/img/cover.png)

# CF7 hook for sending data to Amazon SQS
A simple PHP hook for getting data from Contact Form 7 (CF7) and send it to Amazon Simple Queue Service (SQS)

## Description
[Amazon SQS](https://aws.amazon.com/sqs/) is a queue service where you can send messages to.
In this use case, we want to get the data entered by users on a [CF7 form](https://wordpress.org/plugins/contact-form-7/) - a popular WordPress plugin - and send that data to the SQS.
The code in this repo is basically a WordPress custom plugin that has to be uploaded in a dedicated folder like `/wp-content/plugins/cf7-amazon-sqs`.

## Requirements
In order to use this code you need:
* an active [WordPress](https://wordpress.org/download/) installation
* PHP version >=5.5.0
* PHP with [SimpleXML](https://www.php.net/manual/en/book.simplexml.php) extension enabled
* CF7 plugin
* [Composer](https://getcomposer.org/)

## Installation
* Create a dedicated plugin folder (e.g. `cf7-amazon-sqs`) containing the PHP source file inside `/wp-content/plugins/` folder
* Install the [Amazon SDK](https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/welcome.html) as a dependency using `Composer` inside the plugin folder: `composer require aws/aws-sdk-php`
* Activate the plugin through the `your-site/wp-admin/plugins.php` page

## Configurations
* The function `add_custom_field` is optional and can be used to assign a unique ID to the form submission.
* In function `cf7_amazon_sqs` there's a check about the form title. The hook will send data to the Amazon SQS only if the title contains a `TitleCustomString` (change this string with your desired value).
* In function `cf7_amazon_sqs` you can customize the posted data you are getting from CF7. Remember that the fields name (e.g. `surname`) have to be same in the CF7 dashboard.
* In function `cf7_amazon_sqs` there's the `$client` variable that holds all the client details:
	* Change the `region` parameter with your curernt Amazon SQS region.
	* Consider a better authentication with [environment variables](https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_credentials_environment.html) than the hardcoded (but faster for development) solution you can see in `credentials` parameter.
* In function `cf7_amazon_sqs` there's the `$params` variable that holds useful parameters like:
	* `MessageAttributes` - you can choose to use this parameter to send data to the Amazon SQS.
	* `MessageBody` - you can choose to use this parameter in addition (or as an alternative) to `MessageAttributes` to send data do the Amazon SQS.
	* `QueueUrl` - is the parameter that holds your Amazon SQS url (change it by replacing the example value `yourAmazonSQSURL` at the beginning of the `cf7_amazon_sqs` function).

## Troubleshooting
* To check if you are having some errors, activate WordPress debug mode and log in `/wp-config.php` adding the following lines of code:
	```
	define('WP_DEBUG', true);
	define('WP_DEBUG_LOG', true);
	```
	These 2 commands will write a `debug.log` inside the `/wp-content` folder anytime WordPress will face same warnings/errors.
* Common issues that you could encounter:
	* Problem: you have [freshly installed](https://www.php.net/manual/en/simplexml.installation.php) the SimpleXML extension for PHP, but you still get an error about missing extension in WordPress. Solution: try to restart the server (e.g. `sudo systemctl restart apache2`).
	* Problem: Composer is not globally installed. Solution: follow [these commands](https://www.digitalocean.com/community/tutorials/how-to-install-and-use-composer-on-ubuntu-20-04) if you are using Ubuntu 20.04 as server or refer to Composer's [official documentation](https://getcomposer.org/doc/00-intro.md#globally).
	* Problem: after installing Amazon SDK dependencies for PHP, WordPress cannot access the `/vendor` subfolder or related files, so permissions are messed up. Solution: go inside the plugin folder and change the permissions using `chown` with your WordPress instance couple `user:group` (generally it's `www-data:www-data` or `apache:apache`) running the command `sudo chown -R yourWPInstanceUser:yourWPInstanceUserGroup *`.
	* Problem: conflicts with other plugins' Composer dependencies. Solution: try to update/disable other plugins. Generally they use older versions of dependencies.