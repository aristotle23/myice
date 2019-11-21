<?php
require_once "script/config.php";
require_once "script/DbHandler.php";
require_once "script/Admin.php";
$Admin = new Admin();
$db = new DbHandler();
if(isset($_REQUEST['del']) && $_REQUEST['del'] == 'true'){
    if(isset($_REQUEST['aid'])){
        if($Admin->delete($_REQUEST['aid'])){
            header("location: adminlist.php");
        }
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
                                Admin List
                            </h2>

                        </div>
                        <div class="body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover js-basic-example dataTable">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Access Right</th>
                                            <th>Operation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $Admins = $Admin->getAdmin();

                                    foreach ($Admins as $info){

                                        echo "<tr>";
                                        echo "<td>".$info['name']."</td>";
                                        echo "<td>".$info['email']."</td>";
                                        echo "<td>".$info['label']."</td>";
                                        echo "<td>";
                                        echo "<a   class=\"btn btn-danger btn-circle waves-effect delBtn ".
                                            "waves-circle waves-float\" href='?del=true&aid=".$info['id']."'>".
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
