const notifyHtml = '<li class="notify-li" >\n' +
    '                                    <a href="" target="_blank" class="waves-effect waves-block">\n' +
    '                                        <div class="icon-circle bg-light-green">\n' +
    '                                            <i class="material-icons">person_add</i>\n' +
    '                                        </div>\n' +
    '                                        <div class="menu-info">\n' +
    '                                            <h4></h4>\n' +
    '                                            <p>\n' +
    '                                                <i class="material-icons">access_time</i> \n' +
    '                                            </p>\n' +
    '                                        </div>\n' +
    '                                    </a>\n' +
    '                                </li>';
const notificationLimit = 7;
let lastId = 0;
function rearrange(){

}
function notificationClicked() {
    const $this = $(this);
    const eid = $this.data("id");
    let param = {
        state : "notifyclicked",
        eid : eid
    };
    $.post("script/ajax.php",param, function (data) {
        $this.remove()
    })

}

var notification = () => {

    setInterval(()=>{
        let notifyUl = $(".notify-ul");
        let notifyLiArr = notifyUl.find(".notify-li");
        let arrLiId = [];
        let arrLiTag = [];
        for (let i = 0 ; i < notifyLiArr.length; i++){
            let Li = $(notifyLiArr[i]);
            arrLiId.push(Li.data("id"));
            arrLiTag.push(Li);
        }
        //console.log("arrLiId",arrLiId);
        let param = {
            state : "notification",
            "notifyIdx[]": arrLiId,
            notificationLimit: notificationLimit
        };
        $.post("script/ajax.php",param,function (data) {
            //console.log("data =>",data);
            for(let i = 0; i < arrLiId.length; i++){
                let LiId = arrLiId[i];
                let LiTag = arrLiTag[i];
                let dataArr = data.existing;
                if(dataArr.indexOf(LiId) === -1){
                    LiTag.remove();
                }
            }
            let recentNotify = data.recent;
            for(let i =0 ; i < recentNotify.length ; i++){
                let info = recentNotify[i];
                let notifyLi = $(notifyHtml);
                notifyLi.data("id",parseInt(info.eid));
                notifyLi.on("click",notificationClicked);
                notifyLi.find("a").attr("href",info.href);
                notifyLi.find("h4").text(info.h4);
                notifyLi.find("p").append(info.time);
                notifyUl.prepend(notifyLi);
            }
            let pastNotify = data.past;
            for(let i =0 ; i < pastNotify.length ; i++){
                let info = pastNotify[i];
                let notifyLi = $(notifyHtml);
                notifyLi.data("id",parseInt(info.eid));
                notifyLi.on("click",notificationClicked);
                notifyLi.find("a").attr("href",info.href);
                notifyLi.find("h4").text(info.h4);
                notifyLi.find("p").append(info.time);
                notifyUl.append(notifyLi);
            }


        },"json")

    },3000);
};
notification();
var notificationCount = () => {

    setInterval(()=>{
        let notifyDiv = $("#notificationcount");
        //console.log("arrLiId",arrLiId);
        let param = {
            state : "notifycountdown",
        };
        $.post("script/ajax.php",param,function (data) {
            notifyDiv.text(data);
        },"json")

    },3000);
};
notificationCount();
var popupnotification = () =>{
    var hist = [0];
    setInterval(()=>{
        let param = {
            state : "popupNotify",
            "hist[]": hist,
        };
        $.post("script/ajax.php",param,function (data) {
            console.log(data);
        },"json")

    },3000)

}
//popupnotification();
