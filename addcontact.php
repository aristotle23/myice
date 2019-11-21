<?php
require_once "script/config.php";
require_once "script/DbHandler.php";

$db = new DbHandler();
$message = null;
$name = null;
$phone = null;
$location = null;
$lat = null;
$lng = null;
if(isset($_REQUEST['submit']) && $_REQUEST['submit'] == 'save'){
    $name = $_REQUEST['name'];
    $phone = $_REQUEST['phone'];
    $location = $_REQUEST['location'];
    $lat = $_REQUEST['lat'];
    $lng = $_REQUEST['lng'];
    ;
    $cid = $db->executeGetId("insert into contacts (name, lat, lng, phone, location) VALUES (?,?,?,?,?)",
        array($name,$lat,$lng,$phone,$location));
    if($cid){
        $name = null;
        $phone = null;
        $location = null;
        $lat = null;
        $lng = null;
        $message = "Public emergency number saved successfully";
    }else{
        $message = "Unable to save public Emergency number";
    }

}
if(isset($_REQUEST['cid']) ){
    if(isset($_REQUEST['submit']) && $_REQUEST['submit'] == 'save') {
        $name = $_REQUEST['name'];
        $phone = $_REQUEST['phone'];
        $location = $_REQUEST['location'];
        $lat = $_REQUEST['lat'];
        $lng = $_REQUEST['lng'];
        $db->execute("update contacts set name = ?,lat = ?, lng = ?, phone = ?, location = ? where id = ?",
            array($name,$lat,$lng,$phone,$location,$_REQUEST['cid']));
        $message = "save successfully";
    }else{
        $contact = $db->getOne("select * from contacts where id = ?",array($_REQUEST['cid']));
        $name = $contact['name'];
        $phone = $contact['phone'];
        $location = $contact['location'];
        $lat = $contact['lat'];
        $lng = $contact['lng'];
    }
}

require_once "static/header.php";
require_once "static/sidebar.php";
?>
    <!-- #Top Bar -->

    <section class="content">
        <div class="container-fluid">

            <!-- Basic Validation -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>Add Public Emergency Contact</h2>
                            <ul class="header-dropdown m-r--5">
                                <li class="dropdown">
                                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                        <i class="material-icons">more_vert</i>
                                    </a>
                                    <ul class="dropdown-menu pull-right">
                                        <li><a href="javascript:void(0);">Action</a></li>
                                        <li><a href="javascript:void(0);">Another action</a></li>
                                        <li><a href="javascript:void(0);">Something else here</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <div class="body">
                            <form id="form_validation" method="POST">
                                <?php
                                if(isset($_REQUEST['cid'])){
                                    echo "<input type='hidden' name='cid' required value='". $_REQUEST["cid"]."'>";
                                }
                                ?>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="name" required value="<?php echo $name ?>">
                                        <label class="form-label">Name</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="phone" required value="<?php echo $phone ?>">
                                        <label class="form-label">Phone</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="location" required value="<?php echo $location ?>">
                                        <label class="form-label">Location</label>
                                    </div>
                                </div>

                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="lat" required value="<?php echo $lat ?>">
                                        <label class="form-label">Latitude</label>
                                    </div>
                                </div>

                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="lng" required value="<?php echo $lng ?>">
                                        <label class="form-label">Logitude</label>
                                    </div>
                                </div>

                                <button class="btn btn-primary waves-effect" name="submit" value="save" type="submit">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- #END# Basic Validation -->
            <!-- #END# Validation Stats -->
        </div>
    </section>

 <?php
require_once "static/footer.php";