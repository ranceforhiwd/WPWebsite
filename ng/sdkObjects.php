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

$s3client = new S3Client([
    'version'     => 'latest',
    'region'      => 'us-east-1', //Region of the bucket
    'credentials' => array(
        'key'    => ${{ACCESS_KEY}},
        'secret' => ${{SECRET_KEY}},
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
