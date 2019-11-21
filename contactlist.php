<?php
require_once "script/config.php";
require_once "script/DbHandler.php";
$db = new DbHandler();
if(isset($_REQUEST['del']) && $_REQUEST['del'] == 'true'){
    if(isset($_REQUEST['cid'])){
        $db->execute("delete from contacts where id = ?",array($_REQUEST['cid']));
        header("location: contactlist.php");
    }

}
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
                                Available Public Emergency Numbers
                            </h2>

                        </div>
                        <div class="body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover js-basic-example dataTable">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Phone</th>
                                            <th>Location</th>
                                            <th>Operation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $contacts = $db->getAll("select * from contacts");

                                    foreach ($contacts as $info){

                                        echo "<tr>";
                                        echo "<td>".$info['name']."</td>";
                                        echo "<td>".$info['phone']."</td>";
                                        echo "<td>".$info['location']."</td>";
                                        echo "<td>";
                                        echo "<a   class=\"btn btn-danger btn-circle waves-effect mediaBtn ".
                                            "waves-circle waves-float\" href='addcontact.php?cid=".$info['id']."'>".
                                            "<i class=\"material-icons\" >mode_edit</i> </a> ";
                                        echo "<button type=\"button\" id='btnmap' class=\"btn btn-danger btn-circle waves-effect waves-circle waves-float mapBtn\" ".
                                            "data-toggle=\"modal\" data-target=\"#largeModal\" data-lng='".$info['lng']."' data-lat='".$info['lat']."'>".
                                            "<i class=\"material-icons\">room</i></button> ";
                                        echo "<a   class=\"btn btn-danger btn-circle waves-effect mediaBtn ".
                                            "waves-circle waves-float\" href='?del=true&cid=".$info['id']."'>".
                                            "<i class=\"material-icons\" >remove</i> </a> ";

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
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
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
    $(document).on("click",".mapBtn",function (e) {
        var $this = $(this);
        var lng = $this.data("lng");
        var lat = $this.data("lat");
        initMap(lat,lng,$this)
    });
    function initMap(lat,lng,$this) {
        var myLatLng = {lat: lat, lng: lng};
        var mapDiv = document.getElementById("popup-container");


        var map = new google.maps.Map(mapDiv, {
            zoom: 17,
            center: myLatLng
        });
        var marker = new google.maps.Marker({
            position: myLatLng,
            map: map
        });

        map.addListener('center_changed', function() {
            window.setTimeout(function() {
                map.panTo(marker.getPosition());
            }, 3000);
        });
        map.addListener('zoom_changed', function() {

            map.panTo(marker.getPosition());

        });

    }
    $("#largeModal").on('hidden.bs.modal', function(){
        var popupcontainer = $("#popup-container");
        popupcontainer.empty();
    });
</script>

</body>

</html>
