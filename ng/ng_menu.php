<?php /** NextGen Custom Menu **/ ?>
 <!DOCTYPE html> <html lang="en">
 <head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<table class="table">
<thead><th>Prospect Panel</th></thead>
<body>
<tr><td><div class="form-check form-switch">
  <input class="form-check-input" type="checkbox" id="mySwitch" name="darkmode" value="yes" checked>
  <label class="form-check-label" for="mySwitch">Map Controls</label>
</div></td></tr>
<tr><td>Data Control</td></tr>
<tr><td></td></tr>
</tbody>
</table>
<form id="query" class="row">
<label for="query" class="form-label">Query</label>
<input class="form-control" type="text" />
<div class="d-grid">
<button id="import" type="button" class="button btn">Import</button>
<button class="button btn">Create Supply Chain</button>
<button class ="button btn">Create Proposal</button>
</div>
</form>
<script type="text/javascript">
$("document").ready(function(){
   $("#import").click(function(){
    alert('import function');
   });
});
</script>
</body>
</html>
