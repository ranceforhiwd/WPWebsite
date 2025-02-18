<?php
/**NextGen App Functions rla 2-18-25 */
function clean($x){
   $n = [];
   $blank =  sizeOf($x);

   foreach($x as $y){
      $z = array_merge($y);
      $n[] = array_shift($z);
   }

   return array_shift($n);
}

function getData($dbClient) {
        $r = $dbClient->query([
            'ExpressionAttributeValues' => [
                ':v1' => ['S' => 'prospects' ]
            ],
            'KeyConditionExpression' => 'pngname_pk = :v1',
            'TableName' => 'markup'
        ]);

        $f = [];
        $test = [];
        $b = $r['Items'];

 foreach($b as $x){
    $output = [];
    foreach($x as $y){
       if(isset($y['M'])){
          foreach($y['M'] as $w=>$z){
               if(gettype($z) != 'string'){
                  $k = array_shift($z);
                  $test[$w] = $k;
               }
          }
       }
    }

    foreach($test as $title=>$t){
       if(gettype($t) != 'string'){
         $s = sizeOf($t);
         if($s == 1){
            $s = array_shift($t);
            $output[$title] = array_shift($s);
         }elseif($s ==0){
            $output[$title] = null;
         }else{
            $output[$title] = clean($t);
         }
       }
    }

    $f[] = $output;
 }

$data = [];

foreach($f as $y=>$x){
   if(!empty($x['website'])){
      $data[] = $x;
   }
}

return $data;
}
?>
