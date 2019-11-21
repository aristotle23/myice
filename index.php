<?php
require_once "script/config.php";
require_once "static/header.php";
require_once "static/sidebar.php";
?>


    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <h2>DASHBOARD </h2>
            </div>

            <!-- Widgets -->
            <div class="row clearfix">
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-pink hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">PANIC SIGNALS</div>
                            <div class="number count-to" data-from="0" data-to="0" data-speed="1000" id="panicbox" data-fresh-interval="20"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-cyan hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">help</i>
                        </div>
                        <div class="content">
                            <div class="text">DISTRESS</div>
                            <div class="number count-to" data-from="0" data-to="0" data-speed="1000" id="distressbox" data-fresh-interval="20"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-light-green hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">forum</i>
                        </div>
                        <div class="content">
                            <div class="text">W. BLOWER</div>
                            <div class="number count-to" data-from="0" data-to="0" data-speed="1000" id="whistlebox" data-fresh-interval="20"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-orange hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">person_add</i>
                        </div>
                        <div class="content">
                            <div class="text">EYE WITNESS</div>
                            <div class="number count-to" data-from="0" data-to="0" data-speed="1000" id="witnessbox" data-fresh-interval="20"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-orange hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">person_add</i>
                        </div>
                        <div class="content">
                            <div class="text">TRACK ME</div>
                            <div class="number count-to" data-from="0" data-to="0" data-speed="1000" id="trackbox" data-fresh-interval="20"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- #END# Widgets -->
            <!-- CPU Usage -->
            <!--<div class="row clearfix">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="header">
                            <div class="row clearfix">
                                <div class="col-xs-12 col-sm-6">
                                    <h2>CPU USAGE (%)</h2>
                                </div>
                                <div class="col-xs-12 col-sm-6 align-right">
                                    <div class="switch panel-switch-btn">
                                        <span class="m-r-10 font-12">REAL TIME</span>
                                        <label>OFF<input type="checkbox" id="realtime" checked><span class="lever switch-col-cyan"></span>ON</label>
                                    </div>
                                </div>
                            </div>
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
                            <div id="real_time_chart" class="dashboard-flot-chart"></div>
                        </div>
                    </div>
                </div>
            </div>-->
            <!-- #END# CPU Usage -->



        </div>
    </section>
<script type="text/javascript">
    setInterval(function () {
        var panicbox = $("#panicbox");
        var whistle = $("#whistlebox");
        var distress = $("#distressbox");
        var witness = $("#witnessbox");
        var track = $("#trackbox");
        var param = {
            state : "indexcountdown",
        };
        $.post("script/ajax.php",param,function (data) {
                //console.log(data);
                for (var i = 0; i <= data.length; i++) {
                    var emergency = data[i];
                    if(emergency !== undefined){
                        if(emergency['whistle'] !== undefined ){
                            whistle.text(emergency["whistle"]);
                        }
                        if(emergency['witness'] !== undefined ){
                            witness.text(emergency["witness"]);
                        }
                        if(emergency['panic'] !== undefined ){
                            panicbox.text(emergency["panic"]);
                        }
                        if(emergency['distress'] !== undefined ){
                            distress.text(emergency["distress"]);
                        }
                        if(emergency['track'] !== undefined){
                            track.text(emergency["track"]);
                        }
                        //console.log(emergency['witness']);
                    }


                }

        },"json")
    },3000)
</script>

<?php
require_once "static/footer.php";
