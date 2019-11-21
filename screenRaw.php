<?php
require_once "script/DbHandler.php";
require_once "script/Emergency.php";
$emergencyType = (!isset($_REQUEST['type'])) ? 1 : $_REQUEST['type'];
$emergency = new Emergency($emergencyType);

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">

    <title>MyICE</title>
    <!-- Favicon-->

    <!-- Bootstrap Core Css -->
    <link href="plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="css/screen.css" rel="stylesheet" type="text/css">
    <!--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBkm2crXjtqMhOiWozr-Os03NUJB9BPPMo"></script>-->
    <script src="plugins/jquery/jquery.min.js"></script>

</head>
<body>

<div class="container-fluid" >
    <div class="row">
        <div class="col-md-1 ">
                <ul>
                    <?php
                    $elist = $emergency->getEmergency();
                    foreach ($elist as $key => $list){
                        $emergencyLoc = $emergency->getLocation($list['id']);
                        $originDate = $emergencyLoc['date'];
                        $originLoc = $emergencyLoc['lat'] .",".$emergencyLoc['lng'];



                        echo "<li>";
                        echo "<a href='?eid=".$list['id']."&type=".$emergencyType."&latlng=".$originLoc."&date=".$originDate."' class='btn-emergency'>".$key."</a>";
                        echo "</li>";
                    }
                    ?>
                </ul>

        </div>
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-12" >
                    <div class="row" id="infowindow">

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12" id="mapcon" >

                </div>
            </div>



        </div>
        <div class="col-md-3"  >
            <ul>
                <?php
                $types = $emergency->getAllType();
                foreach ($types as $type){
                    echo "<li>";
                    echo "<a href='?type=".$type['id']."' >".$type['text']."</a>";
                    echo "</li>";
                }
                ?>
            </ul>

        </div>
    </div>
</div>


<div class="modal fade" id="largeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content ">
            <div class="modal-header">

            </div>
            <div class="modal-body" >
                <div id="popup-container" style="height: 400px; width: 100%; position: relative ">


                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap Core Js -->
<script src="plugins/bootstrap/js/bootstrap.js"></script>
<!-- Select Plugin Js -->
<script src="plugins/bootstrap-select/js/bootstrap-select.js"></script>
<script>
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    };
    function diff_hours(dt2, dt1)
    {

        let diff =(dt2.getTime() - dt1.getTime()) / 1000;
        diff /= (60 * 60);
        let hr = Math.abs(Math.round(diff));
        return hr+"hr";

    }
    function displayInfo(emergencyId, emergencyType,date) {

        var heading = ["Date","Name","Email","Address","Phone","Sex","Narration","Location","Media","Incident"];
        var html = '<div class="col-md-4 form-group" ><label class="control-label heading"></label><p class="value"></p></div>';
        var mediahtml = '<button class="btn btn-danger mediaBtn" data-ext="" data-media="" data-toggle="modal" data-target="#largeModal" >View</button>';
        var infoWindow = $("#infowindow").empty();
        var param = {
            state : "screen",
            task : "info",
            eid : emergencyId,
            etype : emergencyType,
            date: date
        };

        $.post("script/ajax.php",param,function (data) {
            for(var i = 0; i <= heading.length; i++){
                var con = $(html);
                var mediaBtn = $(mediahtml);
                var lbl_heading = heading[i];
                var lbl_value = data[i];
                if(lbl_value == null){
                    continue;
                }
                con.find(".heading").text(lbl_heading)
                if(lbl_heading === "Media"){
                    mediaBtn.data("media",lbl_value);
                    var ext = lbl_value.split(".");
                    ext = ext[ext.length - 1];
                    mediaBtn.data("ext",ext);
                    con.find(".value").append(mediaBtn);
                }else{
                    con.find(".value").text(lbl_value);
                }

                infoWindow.append(con);

            }
        },"json")
    }
    function initMap() {
        let emergencyId = getUrlParameter("eid");
        let emergencyType = getUrlParameter("type");
        let date = getUrlParameter("date");
        displayInfo(emergencyId,emergencyType,date);
        let realtimeInfoWindow = new google.maps.InfoWindow();

        let latLng = getUrlParameter("latlng");
        latLng = latLng.split(",");
        let origin = {lat: Number(latLng[0]), lng: Number(latLng[1])};
        let mapCon = document.getElementById("mapcon");

        let map = new google.maps.Map(mapCon, {
            zoom: 18,
            center: origin,
            mapTypeControl: false
        });
        var lineSymbol = {
            path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW
        };
        let poly = new google.maps.Polyline({
            path: [origin],
            geodesic: true,
            strokeColor: '#FF0000',
            strokeOpacity: 1.0,
            strokeWeight: 2,
            icons: [{
                icon: lineSymbol,
                offset: '100%'
            }],
        });
        let originMarker = new google.maps.Marker({
            position: origin,
            map: map,
            icon: "images/apoint.png"
        });
        let destMarker = new google.maps.Marker({
            map: map
        });

        poly.setMap(map);
        map.addListener('center_changed', function() {
            realtimeInfoWindow.setOptions({
                disableAutoPan : true
            })
        });
        poly.addListener('click', function() {
            map.panTo(destMarker.getPosition())
        });

        setInterval(function(){
            var param = {
                state : "screen",
                task : "destLoc",
                eid : emergencyId,
                etype : emergencyType,
                date: date
            };
            $.post("script/ajax.php",param,function (data) {
                let lat = null;
                let lng = null;
                let spd = null;
                let dur = null;
                let alt = null;
                for(let i = 0 ; i < data.length; i++){
                    let path = poly.getPath();
                    lat = Number(data[i]["lat"]);
                    lng = Number(data[i]["lng"]);
                    alt = data[i]["altitude"] + "m";
                    spd = data[i]["speed"] +"m/s";
                    dur = diff_hours(new Date(data[i]["date"]),new Date(date));
                    path.push(new google.maps.LatLng( lat,lng))
                }
                if(lat !== null || lng !== null){
                    realtimeInfoWindow.close();
                    let realtimeContent = '<div id="content">'+
                        '<div id="bodyContent">'+
                        '<p><b>DUR : </b>'+dur+'</p>'+
                        '<p><b>SPD : </b>'+spd+'</p>'+
                        '<p><b>ALT : </b>'+alt+'</p>'+
                        '</div></div>';
                    destMarker.setPosition({lat: lat, lng: lng});
                    realtimeInfoWindow.setContent(realtimeContent);
                    realtimeInfoWindow.open(map,destMarker);
                    //map.setCenter({lat: lat, lng: lng});

                }

            },"json")

        },3000)
    }

    $("#largeModal").on('hidden.bs.modal', function(){
        var popupcontainer = $("#popup-container");
        popupcontainer.css("background", "none")
        popupcontainer.empty();
    });
    $(document).on("click",".mediaBtn",function (e) {
        var $this = $(this);
        var mainDiv = $("#largeModal .modal-content");
        var media = $this.data("media");
        var conDiv = $("#popup-container");
        var type = $this.data("ext");

        mainDiv.removeClass("modal-col-red ")
        conDiv.css("overflow","auto");
        var source = null;
        var mediaElement = $('<img width="100%" />');
        mediaElement.attr("src",media);

        if(type === "mp4"){

            mediaElement = $('<video width="100%" height="100%" autoplay controls>')
            source = $('<source type="video/mp4">')
            source.attr("src",media);
            mediaElement.append(source);
        }else if(type === "mp3"){
            mainDiv.addClass("modal-col-red ")
            conDiv.css("background-image","url(images/music.png)");
            conDiv.css("background-size","cover")
            mediaElement = $('<video width="100%" height="100%" controls autoplay>')
            source = $('<source type="audio/mpeg">')
            source.attr("src",media);
            mediaElement.append(source);
        }
        //console.log(media);
        conDiv.append(mediaElement);
    })

</script>
<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBkm2crXjtqMhOiWozr-Os03NUJB9BPPMo&callback=initMap">
</script>
</body>


</html>