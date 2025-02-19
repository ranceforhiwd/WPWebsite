<?php
include 'nextgendata.php';
include 'sdkObjects.php';
$r = getData($dbClient);
//print_r($r);
//exit();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1"> 
<title>NextGen</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<!-- Latest compiled and minified CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Latest compiled JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container"> 
         <form action="/action_page.php">

            <div class="container">
               <div class="row" id="title"><h2>NEXTGEN PANEL</h2></div>
               <div class="row">
                     <div class="col">            
                  <div class="form-floating">
                     <select class="form-select" id="cities" name="cities">
                       <option>None</option>
                     </select>
                     <label for="cities" class="form-label">Cities list (select one):</label>
                  </div>
                     </div>
               </div>
       

        <div class="row">‬
‭                  <div class="col">‬
                     <div class="form-floating">
  <select class="form-select" id="sel1" name="sellist">
    <option>Prospects</option>
  </select>
  <label for="sel1" class="form-label">Prospects list (select one):</label>
</div>
                  </div>‬
                  <div class="col">‬
                     <div class="form-floating">
  <select class="form-select" id="sel1" name="sellist">
    <option>Suppliers</option>
  </select>
  <label for="sel1" class="form-label">Suppliers list (select one):</label>
</div>
                  </div>‬

<div class="col">‬
                     <div class="form-floating">
  <select class="form-select" id="sel1" name="sellist">
    <option>Shippers</option>
  </select>
  <label for="sel1" class="form-label">Shippers list (select one):</label>
</div>
                  </div>‬
‭                   </div>‬

               <div  class="row">
                  <div class="col">
                     <div class="form-floating">
                        <input type="text" id="query" class="form-control" placeholder="Query">
                        <label for="query" class="form-label">Query</label>
                     </div>
                  </div>
               </div>


               <div class="row"><?php include 'map.html';?></div>
               <div style="margin-top:10px" class="row" id="map"></div>
               <div id="prospects" class="row"></div>
    ‭        </div>      

          </form>
</div>
</body>
</html>
