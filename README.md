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
TBD

## Usage
TBD

## Troubleshooting
TBD