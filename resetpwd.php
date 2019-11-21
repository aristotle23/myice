<?php
require_once "script/config.php";
require_once "script/DbHandler.php";
require_once "script/Admin.php";

$Admin = new Admin();

if(isset($_REQUEST['submit']) && $_REQUEST['submit'] == 'next'){
    $valid = $Admin->login($_SESSION['email'],$_REQUEST['oldpwd']);
    if($valid != false){
        header("location: newpwd.php");
    }else{
        $message = "Incorrect Old Password";
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
                            <h2>Reset Password </h2>
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
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="password" class="form-control" name="oldpwd" required >
                                        <label class="form-label">Old Password</label>
                                    </div>
                                </div>
                                <button class="btn btn-primary waves-effect" name="submit" value="next" type="submit">Proceed</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

 <?php
require_once "static/footer.php";