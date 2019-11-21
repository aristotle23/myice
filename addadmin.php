<?php
require_once "script/config.php";
require_once "script/DbHandler.php";
require_once "script/Admin.php";
Helper::logOut();
$Admin = new Admin();
$message = null;
if(isset($_REQUEST['submit']) && $_REQUEST['submit'] == 'save'){
    $name = $_REQUEST['name'];
    $email = $_REQUEST['email'];
    $right = $_REQUEST['right'];
    if($Admin->setAdmin($name,$email,$right)){
        $message = "New Admin saved successfully";
    }else{
        $message = "Email already exist";
    };

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
                            <h2>Add Admin </h2>
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
                                        <input type="text" class="form-control" name="name" required >
                                        <label class="form-label">Name</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="email" required >
                                        <label class="form-label">Email</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <select class="form-control show-tick" name="right" required>
                                        <option value="">-- Access Right --</option>
                                        <?php
                                        $rights = $Admin->getRight();
                                        foreach ($rights as $right){
                                            echo '<option value="'.$right["right"].'">'.$right['label'].'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>


                                <button class="btn btn-primary waves-effect" name="submit" value="save" type="submit">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

 <?php
require_once "static/footer.php";