
           const apiKey = "v1.public.eyJqdGkiOiI2ZjRlMGNkYy1jMTU5LTRjNGQtYjE3YS0wMTM3ZmNhYzZmM2MifXYPd4xXgu7FleOa9rviTr1McyDt4KXrbvhFazH8BG7eQM5lfz6bSWEMuL14syqAW0icQYIoLkBpEjSdrIkGXqxWiK6qlc7bBT0vT5Oz1Ac5Q9tV0W24Ef8ihW389_SjjzF-8gU7_To3_EqO0TWQERSoCv9NO14dHRwFvGIQFlXZ-6smngkzxgtX0Vsz-WzJ1S-JwQuGSuPUy2bHTvi9iJfE87-WWNNdti9GWJYIGnGHlKlTQ5p6o-geUKeTJmdqF4i5cne7wq-fO2-T2C0U6Py_Jrzv1Qf7_l4GPjfcZPpJK-GDxjGJROb09bHA6yzdzF6KWHltpO9FVe7bexmpNRE.ZWU0ZWIzMTktMWRhNi00Mzg0LTllMzYtNzlmMDU3MjRmYTkx"; // check how to create api key for Amazon Location
           const mapStyle = "Standard";  // eg. Standard, Monochrome, Hybrid, Satellite  
           const awsRegion = "us-east-1"; // eg. us-east-2, us-east-1, us-west-2, ap-south-1, ap-southeast-1, ap-southeast-2, ap-northeast-1, ca-central-1, eu-central-1, eu-west-1, eu-west-2, eu-south-2, eu-north-1, sa-east-1
           const styleUrl = `https://maps.geo.${awsRegion}.amazonaws.com/v2/styles/${mapStyle}/descriptor?key=${apiKey}`;


           function init(records){
             for(var index in records){
                if(records[index]["zipcodes"] == null){
                   $x = '**'+records[index]["cities"];
                }else{
                   $x = records[index]["cities"];
                }

                $y = JSON.stringify(records[index]);
                $('#cities').append(new Option($x, $y));
             }
           }

           $('select#cities').on('change', function() {
                 data = $('#cities option:selected').val();
                 d = JSON.parse(data);
                 create_map(d['longitude'], d['latitude'], d);
           });

           function create_map(ln, la, d){
                 
                //alert(JSON.stringify(d));
                const map = new maplibregl.Map({
                  container: 'map', // container id
                  style: styleUrl, // style URL
                  center: [ln, la], // starting position [lng, lat]
                  zoom: 13, // starting zoom
                });

               $('#map').show();
               $('div#prospects').empty();
               $('div#prospects').append('<a href="'+d["website"]+'" target="_blank">'+d["website"]+'</a>');
               $('div#prospects').append('<p>address:'+d["address"]+'</p>');
               $('div#prospects').append('<p>zipcode:'+d["zipcodes"]+'</p>');
           }

