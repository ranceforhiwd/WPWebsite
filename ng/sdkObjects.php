<?php
require 'vendor/autoload.php';
 
use Aws\S3\S3Client;
use Aws\DynamoDb\DynamoDbClient;
use Aws\Ecs\EcsClient;
use Aws\Exception\AwsException;
use Aws\Sqs\SqsClient;
 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
set_time_limit(0);

$key = $AWS_ACCESS_KEY;
$secret = $SECRET_KEY;

$s3client = new S3Client([
    'version'     => 'latest',
    'region'      => 'us-east-1', //Region of the bucket
    'credentials' => array(
        'key'    => $key,
        'secret' => $secret,
    )
]);

$dbClient = new DynamoDbClient([
    'version'     => 'latest',
    'region'      => 'us-east-1', //Region of the bucket
    'credentials' => array(
        'key'    => $key,
        'secret' => $secret,
    )
]);
