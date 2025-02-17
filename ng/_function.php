<?php

function getProcessingRecords($dbClient) {
        $r = $dbClient->query([
            'ExpressionAttributeValues' => [
                ':v1' => ['S' => 'prospects' ]
            ],
            'KeyConditionExpression' => 'pngname_pk = :v1',
            'TableName' => 'markup'
        ]);

    $f = [];
    if(sizeOf($r['Items']) > 0){ 
        $j = ['cities','zipcodes','phones','website'];
        foreach($r['Items'] as $data){
           foreach($j as $jout){
            $arr = $data['json_data']['M'][$jout]['L'];
            if(sizeOf($arr) > 0){
               if(isset($arr[0]['S'])){
                  $f[$jout][] = $arr[0]['S'];
               }else{
                  $f[$jout][] = 'none';
               }
            }
           }
        }
    }

    return $f;
}

?>
