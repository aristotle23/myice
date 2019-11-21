<?php

require_once "vendor/ktamas77/firebase-php/src/firebaseLib.php";
require_once "script/config.php";
require_once "script/DbHandler.php";
require_once "script/Emergency.php";
require_once "script/User.php";

$db = new DbHandler();
$firebase = new Firebase\FirebaseLib(FIREBASEURL, FIREBASETOKEN);
if(isset($_REQUEST['type'])){
    if(!isset($_REQUEST['eid']) || !isset($_REQUEST['latlng']) || !isset($_REQUEST['date'])){
        $defaultURL = Helper::getDefaultEmergencyURL($_REQUEST['type']);
        if($defaultURL == null){
            $existingType = $db->getOne("select type_id from emergency limit 1");
            if($existingType) {
                $defaultURL = Helper::getDefaultEmergencyURL($existingType["type_id"]);
            }else{
                die("There is no EMERGENCY");
            }

        }
        header("location: ".$defaultURL);
    }

}else{
    header("location: index.php");
    exit;
}
$emergencyType = (!isset($_REQUEST['type'])) ? 1 : $_REQUEST['type'];
$emergency = new Emergency($emergencyType);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

</head>
<style>
  /* Always set the map height explicitly to define the size of the div
   * element that contains the map. */
  #map {
    height: 100%;
  }
  /* Optional: Makes the sample page fill the window. */
  html, body {
    height: 100%;
    margin: 0;
    padding: 0;
  }
  .flexcontent{
    display: flex;
    justify-content: space-between;
    font-weight: 400;
    border-bottom: 1px solid #c2bdbd; 
    padding-bottom: 10px;
  }
  .leftsidebarwrapper{
    background: white;
    width: 228px;
    padding: 9px 20px;
    position: absolute;
    top: 67px;
    left: 2rem;
    z-index: 1;
    opacity: .9;
    box-shadow: rgba(0, 0, 0, 0.15) 0px 5px 20px;
    height: 500px;
}
.leftsidetitle{
    font-weight: 700;
    font-size: 14px;
}
.leftsidename{
  font-size: 12px;
}
.dropdownwrapper{
    position: absolute;
    right: 1.4rem;
    z-index: 3;
    background: white;
    opacity: 0.9;
    font-weight: 500;
    width: 97%;
    padding: 10px 20px 10px 30px;
    box-shadow: rgba(0, 0, 0, 0.15) 0px 5px 20px;
    transition: 1s linear;
}
.infowrapper{
  display: flex;
}
.victiminfo{
  font-weight: 400;
  padding-bottom: 1.4rem;
  font-size: 14px;
}
.homeicon{
  position: absolute;
  cursor: pointer;
  z-index: 1;
  padding: 1px 5px 1px 5px;
  background: white;
  left: 50%;
  margin-top: 1.2rem;
  box-shadow: rgba(0, 0, 0, 0.15) 0px 5px 20px;
}
.paginations-wrapper{
  position: absolute;
  bottom: -3px;
  left: 38%;
  z-index: 2;
}
.pagination-sm .page-link{
  color: #444444;
}
.pagination-sm{
  box-shadow: rgba(0, 0, 0, 0.15) 0px 5px 20px;
  padding: .3rem .1rem .3rem .1rem;
}
.notificationbell{
  position: relative;
  top: -10px;
}
.numbernotification{
  font-size: 14px;
    background: rgb(215,2,6);
    width: 20px;
    text-align: center;
    border-radius: 50%;
    position: relative;
    top: 4px;
    left: -7px;
    color: white;
}
.page-link{
  border: 1px solid transparent;
}
.rightarrow{
  border-radius: 50%;
  position: absolute;
  bottom: 1.1rem;
  right: -3.4rem;
  box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 20px;
  width: 40px;
}
.leftarrow{
  border-radius: 50%;
  bottom: 1.1rem;
  position: absolute;
  left: -3.4rem;
  width: 40px;
  box-shadow: rgba(0, 0, 0, 0.20) 0px 5px 20px;
}
.notification{
    cursor: pointer;
    position: absolute;
    z-index: 2;
    left: 65rem;
    background: white;
    opacity: .8;
    top: 4rem;
    border-radius: 50%;
    width: 60px;
    text-align: center;
    box-shadow: rgba(0, 0, 0, 0.20) 0px 5px 20px;
}
.panicdropdown{
  position: absolute;
    z-index: 2;
    left: 70rem;
    background: white;
    opacity: .8;
    box-shadow: rgba(0, 0, 0, 0.20) 0px 5px 20px;
    top: 4.8rem;
    border-radius: 2rem;
    
}
</style>
<body >

    <!-- <div id="demo">
      </div> -->

    <div class="leftsidebarwrapper">
        <?php
        $ttlEmergency = $db->getOne("select count(id) as ttl from emergency where type_id = ?",array($emergencyType));
        $ttlEmergency = $ttlEmergency["ttl"];
        $ttlEmergencyPerPage = 10;
        $ttlPages = ceil($ttlEmergency / $ttlEmergencyPerPage);
        $currentPage = (isset($_REQUEST['page'])) ? $_REQUEST['page'] : 1;

        if($currentPage > $ttlPages){
            $currentPage = $ttlPages;
        }
        if($currentPage < 1){
            $currentPage = 1;
        }
        $offset = ($currentPage - 1) * $ttlEmergencyPerPage;

        $elist = $emergency->getEmergency(null,$offset,$ttlEmergencyPerPage);

        foreach ($elist as $key => $list) {
            $user = new User($list['user_id']);
            $emergencyLoc = $emergency->getLocation($list['id']);
            $originDate = $emergencyLoc['date'];
            $originLoc = $emergencyLoc['lat'] . "," . $emergencyLoc['lng'];

            //echo "<a href='?eid=" . $list['id'] . "&type=" . $emergencyType . "&latlng=" . $originLoc . "&date=" . $originDate . "' class='btn-emergency'>";

            ?>
            <a href="<?php echo "?eid=". $list['id'] . "&type=" . $emergencyType . "&latlng=" . $originLoc . "&date=" . $originDate."&page=".$currentPage   ?>">
                <div class="leftsidetitle">
                    <?php echo $user->getInfo("name")?>
                </div>
                <div class="flexcontent">
                    <div class="leftsidename">
                        <?php echo $emergency->getLabel()?>
                    </div>
                    <div class="leftsidename">
                        <?php echo Helper::timeAgo($originDate)?>
                    </div>
                </div>
            </a>
            <?php
        }
        ?>
    </div>

                  <!-- notification starts here -->
                  <div class="notification">
                      <div class="numbernotification">0</div>
                      <img src="images/notification.svg" class="notificationbell" alt="notification"/>
                  </div>
                  <div class="panicdropdown">
                    <div class="dropdown">
                        <select class="btn btn-secondary dropdown-toggle select-type" onclick="dropDownActive(e)"style="background:white;color: #444444;padding: 2px 20px;border: 2px solid transparent;width:150px" >
                            <?php
                            $types = $emergency->getAllType();
                            foreach ($types as $type){
                                $defaultURL = Helper::getDefaultEmergencyURL($type["id"]);
                                if($defaultURL == null){
                                    continue;
                                }
                                if($_REQUEST['type'] == $type['id']){
                                    echo '<option class="dropdown-item" selected value="'.$type["id"].'" data-href="'.$defaultURL.'">'.$type["text"].'</option>';
                                    continue;
                                }
                                echo '<option class="dropdown-item" value="'.$type["id"].'" data-href="'.$defaultURL.'">'.$type["text"].'</option>';
                                //echo "<a class='dropdown-item' href='".$defaultURL."' >".$type['text']."</a>";

                            }
                            ?>
                          <!--<option class="dropdown-item" value="value2" href="#">Action</option>
                          <option class="dropdown-item" value="value3" href="#">Another action</option>
                          <option class="dropdown-item" value="value4" href="#">Something else here</option>-->
                      </select>
                      </div>
                    </div>
                    <!-- notification ends here -->
                  
                <!-- top menu bar starts here -->
    <?php
    $data = array();
    $heading = array("Date","Name","Email","Address","Phone","Sex","Narration","Location","Media","Incident");
    $emergencyArr = $emergency->getEmergency($_REQUEST['eid']);
    $user = new User($emergencyArr['user_id']);
    $userArr = $user->getInfo();

    array_push($data, $emergencyArr['date'],$userArr['name'],$userArr['email'],$userArr['address'],$userArr['phone'],$userArr['sex']);

    array_push($data, $emergencyArr['narration'], $emergencyArr['location'],$emergencyArr['media'], $emergencyArr['incidence']);
    ?>
                  <div class="dropdownwrapper" style="top:-22rem" id='dropdownwrappermain'>

                      <div class="row" style="height: 21rem">
                          <?php
                          foreach($data as $key => $info) {
                              if($info == null){
                                  continue;
                              }
                              ?>
                              <div class="col-md-4  form-group">
                                  <div><?php echo $heading[$key]  ?></div>
                                  <div class="victiminfo">
                                      <?php
                                      if(strtolower($heading[$key]) == "media" ){
                                          $media = $info;
                                          $ext = explode(".",$media);
                                          $ext = $ext[count($ext)-1];
                                          echo '<button class="btn btn-primary mediaBtn" data-ext="'.$ext.'" data-media="'.$media.'" data-toggle="modal" data-target="#largeModal" >View</button>';
                                      }else{
                                          echo $info;
                                      }
                                      ?>
                                  </div>
                              </div>
                              <?php
                          }
                          ?>
                      </div>



                    <div class="homeicon" onclick="toggle()" >
                        <img src="images/menu.svg" alt="homeicon"/>
                      </div>
                  </div>
                  <!-- Pagination starts here -->
                  <div class="paginations-wrapper">
                      <?php
                      $range = 10;
                      if($currentPage > 1){
                          $prevPage = $currentPage - 1;
                          $urlQuery = $_GET;
                          $urlQuery["page"] = $prevPage;
                          $urlQuery = http_build_query($urlQuery);
                          echo '<a href="?'.$urlQuery.'"> <img src="images/leftarrow.jpg" class="leftarrow" alt="left-arrow"></a>';
                          //echo '<img src="images/leftarrow.jpg" class="leftarrow" alt="right-arrow">';
                      }
                      ?>
                    <!--<img src="images/leftarrow.jpg" class="leftarrow" alt="right-arrow">-->
                    <nav aria-label="...">
                        <ul class="pagination pagination-sm">
                            <?php
                            for($x = ($currentPage - $range) ; $x < (($currentPage + $range) + 1); $x++) {
                                if(($x > 0) && ($x <= $ttlPages)) {
                                    $urlQuery = $_GET;
                                    $urlQuery["page"] = $x;
                                    $urlQuery = http_build_query($urlQuery);
                                    if($x == $currentPage) {
                                        ?>

                                        <li class="page-item active" aria-current="page">
                                        <span class="page-link">
                                          <?php  echo $x ?>
                                          <span class="sr-only">(current)</span>
                                        </span>
                                        </li>
                                        <?php
                                    }else {
                                        ?>
                                        <li class="page-item"><a class="page-link" href="?<?php echo $urlQuery ?>"><?php  echo $x ?></a></li>
                                        <?php
                                    }
                                }
                            }
                            ?>
                        </ul>
                      </nav>
                      <?php
                      $range = 10;
                      if($currentPage != $ttlPages ){
                          $nextPage = $currentPage + 1;
                          $urlQuery = $_GET;
                          $urlQuery["page"] = $nextPage;
                          $urlQuery = http_build_query($urlQuery);
                          echo '<a href="?'.$urlQuery.'"> <img src="images/rightarrow.jpg" class="rightarrow" alt="right-arrow"></a>';
                      }
                      ?>
                      <!--<img src="images/rightarrow.jpg" class="rightarrow" alt="right-arrow">-->
                    </div>
                    <!-- Pagination ends here -->
                <div id="map" onclick="hideWhenBodyIsClicked()"></div>
    <!-- Modal for displaying uploads starts here -->
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
    <!-- Modal for displaying uploads ends here -->
 <script>
   const toggle=()=>{
     const el = document.querySelector('.dropdownwrapper')
     if(el.style.top==='0.5rem'){
        el.style.top = '-22rem'
     }
     else{
      el.style.top = '0.5rem'
     }
   }
   const hideWhenBodyIsClicked=()=>{
     const el = document.querySelector('.dropdownwrapper')
     if(el.style.top==='0.5rem'){
        el.style.top = '-22rem'
     }
   }
   

  /* var map;
  //  demo starts here
  var demoElement = document.getElementById('demo');
  const showPosition = async (position) =>{
    //demoElement.innerHTML = `latitude :` + position.coords.latitude + `longitude :` + position.coords.longitude
    var myLatitude = position.coords.latitude
    var myLongitude = position.coords.longitude
    console.log(myLatitude)
    map = await new google.maps.Map(document.getElementById('map'), {
      center: {lat: myLatitude, lng: myLongitude},
      zoom: 50,
      disableDefaultUI: true,
      disableDoubleClickZoom:true
    })
    var marker = await new google.maps.Marker({
    position: {lat: myLatitude, lng: myLongitude},
    map: map,
    title: 'Hello World!'
  });

    console.log(marker)
  }
  (()=>{
    console.log(navigator)
    if(navigator.geolocation){
      navigator.geolocation.getCurrentPosition(showPosition)
    }
  })()*/
  

  
</script>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<!--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDWw4493zr0Ja1zWOoe49OGeuf7eVZ9J5Q&callback=initMap" async defer></script>-->
    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="js/moment.js"></script>

<script>
    $(".select-type").on("change",function (e) {
        let $this = $(this);
        let option = $this.find("option:selected");
        let href = option.data("href");
        location = href;
    })
</script>
    <script>
        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            var results = regex.exec(location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        };
        function diff_hours(dt2, dt1)
        {
            let diff = dt2.diff(dt1,"hours");
            //console.log(diff)
            //console.log(dt2,dt1)
            //let diff =(dt2.getTime() - dt1.getTime()) / 1000;
            //diff /= (60 * 60);
            //let hr = Math.abs(Math.round(diff));
            return diff +"hr";

        }
        function initMap() {
            let emergencyId = getUrlParameter("eid");
            let emergencyType = getUrlParameter("type");
            let date = getUrlParameter("date");
            let iniDate = date;
            let realtimeInfoWindow = new google.maps.InfoWindow();

            let latLng = getUrlParameter("latlng");
            latLng = latLng.split(",");
            let origin = {lat: Number(latLng[0]), lng: Number(latLng[1])};
            let mapCon = document.getElementById("map");

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
                        //console.log(data[i]["date"], date);
                        let dt2 = moment(data[i]["date"],"YYYY-MM-DD HH:mm:ss");
                        let dt1 = moment(iniDate,"YYYY-MM-DD HH:mm:ss");
                        date = data[i]["date"];
                        //console.log(moment(data[i]["date"],"YYYY-MM-DD HH:mm:ss"))
                        dur = diff_hours(dt2,dt1);

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

            if(type === "mp4" || type === "3gpp" || type === "quicktime"){

                mediaElement = $('<video width="100%" height="100%" autoplay controls>');
                source = $('<source type="video/'+type+'">');
                source.attr("src",media);
                mediaElement.append(source);
            }else if(type === "mp3" || type === "amr" || type === "wav" ){
                mainDiv.addClass("modal-col-red ")
                conDiv.css("background-image","url(images/music.png)");
                conDiv.css("background-size","cover")
                mediaElement = $('<video width="100%" height="100%" controls autoplay>');
                source = $('<source type="audio/'+type+'">');
                source.attr("src",media);
                mediaElement.append(source);
            }
            //console.log(media);
            conDiv.append(mediaElement);
        })
        var notification = () => {

            setInterval(()=>{
                let etype = getUrlParameter("type");
                let notifyDiv = $(".numbernotification");
                //console.log("arrLiId",arrLiId);
                let param = {
                    state : "notifycountdown",
                    etype : etype
                };
                $.post("script/ajax.php",param,function (data) {
                    notifyDiv.text(data);
                },"json")

            },3000);
        };
        notification();
    </script>
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBkm2crXjtqMhOiWozr-Os03NUJB9BPPMo&callback=initMap">
    </script>
</body>
  </body>
</html>