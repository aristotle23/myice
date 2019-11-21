
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">

    <title>Track Me</title>
    <!-- Favicon-->

    <!-- Bootstrap Core Css -->
    <link href="plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="css/screen.css" rel="stylesheet" type="text/css">
    <script src="plugins/jquery/jquery.min.js"></script>

</head>
<body>

<div class="container-fluid" >
    <div class="row" style="margin-top: 40vh">
        <div class="col-md-6 col-md-offset-3 clearfix" >
            <h4>Latitude: <small id="latitude"></small></h4>
            <h4>Longitude: <small id="longitude"></small></h4>
            <h4>Time: <small id="time"></small></h4>
        </div>
        <div class="col-md-6 col-md-offset-3" >
            <button type="button" class="btn-primary btn btn-block btn-lg" data-user="12" data-active="0" id="track">Track Me</button>
        </div>
    </div>
</div>



<!-- Bootstrap Core Js -->
<script src="plugins/bootstrap/js/bootstrap.js"></script>
<!-- Select Plugin Js -->
<script src="plugins/bootstrap-select/js/bootstrap-select.js"></script>
<script>
    const key = "eccc90ab5ef44c294175d5196d606802051eb5f8";
    let userId, watchId,emergencyId;
    let timeDom = $("#time");
    let latDom = $("#latitude");
    let lngDom = $("#longitude")
    function getPostion(position) {
        let domTimeStamp = position.timestamp;
        let date = new Date(domTimeStamp);
        let month = date.getMonth() + 1;
        let lat = position.coords.latitude;
        let lng = position.coords.longitude;
        console.log(position.coords.accuracy);
        date = date.getFullYear()+"-"+month+"-"+date.getDate()+" "+date.getHours()+":"+date.getMinutes()+":"+date.getSeconds();
        latDom.text(lat);
        lngDom.text(lng);
        timeDom.text(date);
        return  lat+","+lng
    }
    function trackMe(position) {
        let latlng = getPostion(position);
        let param = {
            userid: userId,
            key: key,
            latlng: latlng,
            type: 5,
            emergencyid: emergencyId
        };
        $.post("http://mia.deciphertechltd.com/new/api/emergency.php",param,function (data) {
            console.log("Track Me Operational");
        },"json");
    }
    function error(error){
        alert(error.code + " : "+error.message)
    }

    $(document).on("click","#track",function(e){
        console.log("Btn Clicked");
        e.preventDefault();
        let $this = $(this);
        userId = $this.data("user");
        if(!navigator.geolocation){
            alert("Browser does not support location service");
        }
        if(parseInt($this.attr("data-active")) === 1){
            navigator.geolocation.clearWatch(watchId);
            $this.attr("data-active",0);
            $this.text("Track Me");
            return;
        }
        let param = {
            userid: userId,
            key: key,
            type: 5
        };
        $.post("http://mia.deciphertechltd.com/new/api/emergency.php",param,function (data) {
            console.log("Track Me Initialize");
            emergencyId = data.emergencyId;
            watchId = navigator.geolocation.watchPosition(trackMe,error,{
                maximumAge: 0
            })
            $this.attr("data-active",1);
            $this.text("STOP");
        },"json");

    })

</script>
</body>


</html>