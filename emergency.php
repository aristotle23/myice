<?php
if(!isset($_REQUEST['typ'])){
    header("location: index.php");
    exit;
}
require_once "vendor/ktamas77/firebase-php/src/firebaseLib.php";
require_once "script/config.php";
require_once "script/DbHandler.php";
require_once "script/Emergency.php";
require_once "script/User.php";

$emergencyType = $_REQUEST['typ'];
$firebase = new Firebase\FirebaseLib(FIREBASEURL, FIREBASETOKEN);
$emergency = new Emergency($emergencyType);


require_once "static/header.php";
require_once "static/sidebar.php";
?>

    <section class="content">
        <div class="container-fluid">

            <!-- Basic Examples -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>
                                <?php echo strtoupper($emergency->getLabel() ) ?>
                            </h2>

                        </div>
                        <div class="body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover js-basic-example dataTable">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Name</th>
                                            <th>Phone</th>
                                            <th>Sex</th>
                                            <?php
                                            if($emergencyType != 1 && $emergencyType != 5 ) {
                                                ?>
                                                <th>Naration</th>
                                                <?php
                                            }
                                            ?>
                                            <th>Operation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $emergencies = $emergency->getEmergency();
                                    foreach ($emergencies as $info){

                                        $userId = $info['user_id'];
                                        $user = new User($userId);
                                        $userInfo = $user->getInfo();
                                        $infoLoc = $emergency->getLocation($info['id']);
                                        $originDate = $infoLoc["date"];
                                        $originLoc = $infoLoc['lat'] .",".$infoLoc['lng'];

                                        echo "<tr>";
                                        echo "<td>".$info['date']."</td>";
                                        echo "<td>".$userInfo['name']."</td>";
                                        echo "<td>".$userInfo['phone']."</td>";
                                        echo "<td>".$userInfo['sex']."</td>";
                                        if($emergencyType != 1 && $emergencyType != 5){
                                            echo "<td>".$info['narration']."</td>";
                                        }
                                        echo "<td>";
                                        if($info['media'] != null){
                                            $ext = explode(".",$info["media"]);
                                            end($ext);
                                            $ext = $ext[key($ext)];

                                            echo "<button type=\"button\" data-ext='".$ext."' data-media='".$info["media"]."' class=\"btn btn-danger btn-circle waves-effect mediaBtn ".
                                                "waves-circle waves-float\" data-toggle=\"modal\" data-target=\"#largeModal\" >".
                                                "<i class=\"material-icons\" >search</i> </button> ";
                                        }
                                        echo "<button type=\"button\" id='btnmap' data-type='".strtoupper($emergency->getLabel())."' class=\"btn btn-danger btn-circle waves-effect waves-circle waves-float mapBtn\" ".
                                            "data-toggle=\"modal\" data-eid='".$info['id']."' data-typeid='".$emergencyType."' data-narration='".$info['narration']."' 
                                            data-target=\"#largeModal\" data-usrname='".$userInfo['name']."' data-origin='".$originLoc."' data-date='".$originDate."'>".
                                            "<i class=\"material-icons\">room</i></button>";

                                        echo "</td>";


                                        echo "</tr>";
                                    }
                                    ?>


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- #END# Basic Examples -->
            <!-- Exportable Table -->

            <!-- #END# Exportable Table -->
        </div>
    </section>

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

    <!-- Jquery Core Js -->
    <script src="plugins/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core Js -->
    <script src="plugins/bootstrap/js/bootstrap.js"></script>

    <!-- Select Plugin Js -->
    <script src="plugins/bootstrap-select/js/bootstrap-select.js"></script>

    <!-- Slimscroll Plugin Js -->
    <script src="plugins/jquery-slimscroll/jquery.slimscroll.js"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="plugins/node-waves/waves.js"></script>

    <!-- Jquery DataTable Plugin Js -->
    <script src="plugins/jquery-datatable/jquery.dataTables.js"></script>
    <script src="plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/dataTables.buttons.min.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/buttons.flash.min.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/jszip.min.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/pdfmake.min.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/vfs_fonts.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/buttons.html5.min.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/buttons.print.min.js"></script>

    <!-- Custom Js -->
    <script src="js/admin.js"></script>
    <script src="js/script.js"></script>
    <script src="js/pages/tables/jquery-datatable.js"></script>

    <!-- Demo Js -->
    <script src="js/demo.js"></script>

<script>
    var updateInterval = null;
    $(document).on("click",".mapBtn",function (e) {
        var $this = $(this);
        var mainDiv = $("#largeModal .modal-content");
        mainDiv.removeClass("modal-col-red ")
        var origin = $this.data("origin");
        var date = $this.data("date");
        initMap(origin,date,$this)
    });
    function initMap(origin,date,$this) {
        clearInterval(updateInterval);
        if(origin === ","){
            return false;
        }
        var usrname = $this.data("usrname");
        var narration = $this.data("narration");
        var emergencyId = $this.data('eid');
        var emergencyType = $this.data('typeid');
        console.log(emergencyType);
        var type = $this.data("type");
        var contentString = '<div id="content">'+
            '<div id="siteNotice">'+
            '</div>'+
            '<h6 id="firstHeading" class="firstHeading">'+type+'</h6>'+
            '<div id="bodyContent">'+
            '<p><b>User Name : </b>'+usrname+'</p><p>'+
            '<b>Narration :</b><br/>'+
            narration +'</p>';

        var mapDiv = document.getElementById("popup-container");
        var latlng = origin.split(",");

        var myLatLng = {lat: Number(latlng[0]), lng: Number(latlng[1])};

        var coordInfoWindow = new google.maps.InfoWindow({
            content: contentString,
            position: myLatLng
        });
        var map = new google.maps.Map(mapDiv, {
            zoom: 17,
            center: myLatLng,
            mapTypeControl: false
        });
        var lineSymbol = {
            path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW
        };
        let poly = new google.maps.Polyline({
            path: [myLatLng],
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
            position: myLatLng,
            map: map,
            icon: "images/apoint.png"
        });
        let destMarker = new google.maps.Marker({
            map: map
        });
        poly.setMap(map);

        originMarker.addListener('click', function() {
            coordInfoWindow.close();

            coordInfoWindow.open(map,originMarker);
            map.setZoom(17);
            map.setCenter(originMarker.getPosition());
        });
        map.addListener('center_changed', function() {
            window.setTimeout(function() {
                map.panTo(originMarker.getPosition());
            }, 3000);
        });
        map.addListener('zoom_changed', function() {
            map.panTo(originMarker.getPosition());
        });


        updateInterval = setInterval(function(){
            var param = {
                state : "screen",
                task : "destLoc",
                eid : emergencyId,
                etype : emergencyType,
                date: date
            };
            console.log(param);
            $.post("script/ajax.php",param,function (data) {
                console.log(data);
                let lat = null;
                let lng = null;
                for(let i = 0 ; i < data.length; i++){
                    let path = poly.getPath();
                    lat = Number(data[i]["lat"]);
                    lng = Number(data[i]["lng"]);
                    date = data[i]["date"];
                    path.push(new google.maps.LatLng( lat,lng))
                }
                if(lat !== null || lng !== null){
                    destMarker.setPosition({lat: lat, lng: lng})
                    map.setZoom(8);
                }

            },"json")

        },3000);


    }
    $("#largeModal").on('hidden.bs.modal', function(){
        var popupcontainer = $("#popup-container");
        popupcontainer.css("background", "none")
        popupcontainer.empty();
        clearInterval(updateInterval);
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

        if(type === "mp4" || type === "3gpp" || type === "quicktime"){

            mediaElement = $('<video width="100%" height="100%" autoplay controls>')
            source = $('<source type="video/'+type+'">');
            source.attr("src",media);
            mediaElement.append(source);
        }else if(type === "mp3" || type === "amr" || type === "wav"){
            mainDiv.addClass("modal-col-red ")
            conDiv.css("background-image","url(images/music.png)");
            conDiv.css("background-size","cover")
            mediaElement = $('<video width="100%" height="100%" controls autoplay>')
            source = $('<source type="audio/'+type+'">')
            source.attr("src",media);
            mediaElement.append(source);
        }
        console.log(media);
        conDiv.append(mediaElement);
    })
</script>

</body>

</html>
