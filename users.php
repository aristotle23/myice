<?php
require_once "script/config.php";
require_once "script/DbHandler.php";
require_once "script/User.php";

$User = new User();
if(isset($_REQUEST["act"]) && isset($_REQUEST['uid'])){
    $userId = $_REQUEST['uid'];
    $act = $_REQUEST['act'];
    $User->deactiave($userId,$act);
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
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Address</th>
                                            <th>Sex</th>
                                            <th>Operation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $contacts = $User->getUsers();

                                    foreach ($contacts as $info){

                                        echo "<tr>";
                                        echo "<td>".$info['name']."</td>";
                                        echo "<td>".$info['email']."</td>";
                                        echo "<td>".$info['phone']."</td>";
                                        echo "<td>".$info['address']."</td>";
                                        echo "<td>".$info['sex']."</td>";
                                        echo "<td>";
                                        echo "<a   class=\"btn btn-danger btn-circle waves-effect eBtn ".
                                            "waves-circle waves-float\" title='Emergency details' href='uemergency.php?uid=".$info['id']."'>".
                                            "<i class=\"material-icons\" >work</i> </a> ";
                                        if($info['deactivate'] == 0) {
                                            echo "<a   class=\"btn btn-danger btn-circle waves-effect delBtn " .
                                                "waves-circle waves-float\" title='Deactivate User' href='?act=1&uid=" . $info['id'] . "'>" .
                                                "<i class=\"material-icons\" >remove</i> </a> ";
                                        }else{
                                            echo "<a   class=\"btn btn-primary btn-circle waves-effect " .
                                                "waves-circle waves-float\" title='Reactivate User' href='?act=0&uid=" . $info['id'] . "'>" .
                                                "<i class=\"material-icons\" >add</i> </a> ";
                                        }

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
    $(".delBtn").on("click",function (e) {
        let choice = confirm("Are you sure you want to delete this user");
        if(choice === false){
            return false;
        }
    })


</script>

</body>

</html>
