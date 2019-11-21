<!-- #Top Bar -->
<section>
    <!-- Left Sidebar -->
    <aside id="leftsidebar" class="sidebar">
        <!-- User Info -->
        <div class="user-info">
            <div class="image">
                <img src="images/user.png" width="48" height="48" alt="User" />
            </div>
            <div class="info-container">
                <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $_SESSION['name'] ?></div>
                <div class="email"><?php echo $_SESSION['email'] ?></div>
                <div class="btn-group user-helper-dropdown">
                    <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>
                    <ul class="dropdown-menu pull-right">
                        <li><a href="resetpwd.php"><i class="material-icons">person</i>Reset Password</a></li>
                        <?php
                        if($_SESSION['right'] > 1) {
                            ?>
                            <li role="separator" class="divider"></li>
                            <li><a href="addadmin.php"><i class="material-icons">group</i>New Admin</a></li>
                            <li><a href="adminlist.php"><i class="material-icons">group</i>Admin List</a></li>
                            <?php
                        }
                        ?>

                        <li role="separator" class="divider"></li>
                        <li><a href="?logout=true"><i class="material-icons">input</i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- #User Info -->
        <!-- Menu -->
        <div class="menu">
            <ul class="list">
                <li class="header">MAIN NAVIGATION</li>
                <li class="active">
                    <a href="index.php">
                        <i class="material-icons">home</i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="emergency.php?typ=1">
                        <i class="material-icons">view_list</i>
                        <span>Panic Signals</span>
                    </a>
                </li>
                <li>
                    <a href="emergency.php?typ=2">
                        <i class="material-icons">view_list</i>
                        <span>Distress</span>
                    </a>
                </li>
                <li>
                    <a href="emergency.php?typ=3">
                        <i class="material-icons">view_list</i>
                        <span>Whistle Blower</span>
                    </a>
                </li>
                <li>
                    <a href="emergency.php?typ=4">
                        <i class="material-icons">view_list</i>
                        <span>Eye Witness</span>
                    </a>
                </li>
                <li>
                    <a href="emergency.php?typ=5">
                        <i class="material-icons">view_list</i>
                        <span>Track Me</span>
                    </a>
                </li>
                <li>
                    <a href="users.php">
                        <i class="material-icons">view_list</i>
                        <span>Users</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);" class="menu-toggle">
                        <i class="material-icons">trending_down</i>
                        <span>Public Emergency Contact</span>
                    </a>
                    <ul class="ml-menu">
                        <li>
                            <a href="addcontact.php">
                                <span>Add Contact</span>
                            </a>
                        </li>
                        <li>
                            <a href="contactlist.php">
                                <span>Contact List</span>
                            </a>
                        </li>

                    </ul>
                </li>

            </ul>
        </div>
        <!-- #Menu -->
        <!-- Footer -->
        <div class="legal">
            <div class="copyright">
                &copy; <?php echo date("Y") ?> <a href="javascript:void(0);">MyICE</a>.
            </div>
            <div class="version">
                <b>Version: </b> 1.0.5
            </div>
        </div>
        <!-- #Footer -->
    </aside>
    <!-- #END# Left Sidebar -->
</section>