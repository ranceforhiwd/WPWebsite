<?php
/**Updated 2-19-2025 */
require 'vendor/autoload.php';
 
use Aws\S3\S3Client;
use Aws\DynamoDb\DynamoDbClient;

use Aws\Exception\AwsException;
 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
set_time_limit(0);


$s3client = new S3Client([
    'version'     => 'latest',
    'region'      => 'us-east-1', //Region of the bucket
]);

$dbClient = new DynamoDbClient([
    'version'     => 'latest',
    'region'      => 'us-east-1', //Region of the bucket
]);
