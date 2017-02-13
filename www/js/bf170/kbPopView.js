var KBPopView = Class.create();

function myreplace(mystr, args) {
    var index = mystr.indexOf("%s");
    if (index > 0) {
        var str1 = mystr.substr(0, index + 2);
        var replaceIndex = args.indexOf(",");
        var args1 = args.substr(0, replaceIndex);
        var str = str1.replace("%s", args1);
        var str2 = mystr.substr(index + 2, mystr.length - index - 2);
        args = args.substr(replaceIndex + 1, args.length - replaceIndex - 1);
        mystr = str + str2;
        return myreplace(mystr, args);
    } else {
        return mystr;
    }
};

//添加成员
function addPerson(callID) {
    var add = document.getElementById("adduresid");
    add.style.display = "block";
};
//截止时间
function stopTime(callID) {

    alert("截止时间" + callID);
};

//添加附件
function addFile(callID) {
    document.getElementById("fileToUpload").click();
};

//工作量估算
function estimate(callID) {

    alert("工作量估算" + callID);
};
//保存
function save(callID) {
    var procid = jQuery('input.process_buzhou').val();
    var customerName = [];
    jQuery('input.ads_Checkbox:checked').each(function(i) {
        customerName[i] = jQuery(this).val();
    });
    var msStr = jQuery("#mypre_miaoshu").text();
    var pers = document.getElementById('preGroup');
    var plStr = "";
    for (var i = 0; i < pers.childNodes.length - 1; i++) {
        plStr += pers.childNodes[i].innerHTML;
    }
    var types = jQuery('select.tag_Select').val();
    var evaluate = jQuery("#commentPre").text();
    var endtime = jQuery("#datepicker").val();
    jQuery.ajax({
        url: "/pmtool/kanban/updatacardcomment",
        data: {
            "name": customerName,
            "description": msStr,
            "comment": plStr,
            "cardid": callID,
            "processid": procid,
            "type": types,
            "evaluate":evaluate,
            "endtime":endtime
        },
        type: "POST",
        dataType: "json",
        success: function(result) {
            alert(result.status);
            window.location.reload();
        }
    });
};
//管理员
function admin(callID) {

    alert("管理员项" + callID);
};

function saveInfo() {
    jQuery('#mydiv_1').hide();
    jQuery('#mypre_miaoshu').show();
    var valuetext = jQuery('#mytextarea_1').val();

    jQuery("#mypre_miaoshu").text(valuetext);
};

function cancleInfoEdit() {
    jQuery('#mydiv_1').hide();
    jQuery('#mypre_miaoshu').show();
}

function infoEdit() {
    var valuespan = jQuery("#mypre_miaoshu").text();
    var valuetext = jQuery('#mytextarea_1').text(valuespan);
    jQuery('#mydiv_1').show();
    jQuery('#mypre_miaoshu').hide();
};

function plExpress() {
    var valuespan = jQuery("#commentPre").text();
    var text = jQuery('#mytextarea_2').val();
    if (text != "添加评论" && text != "") {

        jQuery('#mytextarea_2').val("添加评论");
        jQuery("#commentPre").text(valuespan + text);
        console.log(valuespan + text);
    } else {
        alert("评论失败");
        jQuery('#mytextarea_2').val("添加评论");
    }
};

function plCancel() {
    jQuery('#mytextarea_2').val("添加评论");
}
String.prototype.format = String.prototype.f = function() {
    var s = this;
    var s2 = "";
    for (var i = 0; i < arguments.length; i++) {
        var temp = arguments[i] + ",";
        s2 += temp;
    }
    return myreplace(this, s2);
};

KBPopView.prototype = {

    initialize: function(popViewInfo, popViewInfos) {

        this.renderCard(popViewInfo, popViewInfos);
    },

    renderCard: function(popViewInfo) {
        var callstr = popViewInfo.currentId;
        var callures = popViewInfo.customer;
        var tag_info = popViewInfo.kapian_taginfos;
        var description = popViewInfo.bewrite;
        var due_at = popViewInfo.due_at;
        var comment_info = popViewInfo.comment_info;
        var priority = popViewInfo.priority;
        var template1 = '<div class = "popView-leftDiv" style="text-align:left">';       
        template1 += '<h1>%s</h1><h3>介绍:</h3>&nbsp<a class = "popView-aa" onclick="infoEdit()" href="#">点击编辑描述</a>';
        template1 += ' <div id = "mypre_miaoshu" style="width:100px;">%s</div>';
        template1 += '<div id="mydiv_1"  style="width:95%;height:110px;margin-left:10px; display:none">';
        template1 += '<textarea id = "mytextarea_1" name="name" rows="6" style="padding:0px;">描述...</textarea>';
        template1 += '<a class = "popView-a" onclick="saveInfo()" href="#" style="float:right">保存</a>';
        template1 += '<span style="float:right">&nbsp;</span>';
        template1 += '<a class = "popView-a" onclick="cancleInfoEdit()" href="#" style="float:right">取消</a>';
        template1 += '</div>';
        template1 += ' <div style="height:auto;margin-top:10px;text-align:left" class = "popView-imgDiv""><p>截止时间: <input type="text" id="datepicker" value=%s></p></div>';
        template1 += '<div><h5>附件:还没有弄</h5></div>'
        template1 += '<h3>成员评价</h3>';
        template1 += '<div>';
        template1 += '<div id="preGroup" style = "background-color:#CFCFDF;margin-left:10px;"></div>';
        template1 += '<div id="plDiv" style="margin-left:10px">';
        if(comment_info){
            for(var i = 0;i<comment_info.length;i++){
                var strs = '<h4>%s</h4>'.format(comment_info[i]);
                template1 += strs;
            }
        } 
        template1 += '<pre id="commentPre"></pre>';
        template1 += '<textarea id="mytextarea_2" name="name" rows="5"style="color:grey">添加评论(评论结束后请点个回车)</textarea>';
        template1 += '<a class="popView-a" onclick="plExpress()" href="#" style="float:right">保存</a>';
        template1 += '<form id= "uploadForm" enctype="multipart/form-data" action= "http://www.bf170.kanban.com/pmtool/kanban/updatafile" method="post" name="fileinfo"> ';
        template1 += '<input type="file" name="fileToUpload" class="popView-btn" class="popView-btn">';
        template1 += '<input type="hidden" name="card_id"  class="popView-btn" value="%s">';  
        template1 += '<input type="submit"  class="popView-btn" value="上传文件" >';
        template1 += '</form > ';
        template1 += '<span style="float:right">&nbsp;</span><a class="popView-a" onclick="plCancel()" href="#" style="float:right">取消</a>';
        template1 += '</div>';
        template1 += '</div>';
        template1 += '</div>';
        template1 += '<div id = "popView-right-id" class = "popView-rightDiv">';
        template1 += '<button type="button" name="button" class="popView-btn" onclick="addPerson(\'%s\')">添加成员</button>';
        template1 += '<button type="button" name="button" class="popView-btn" onclick="estimate(\'%s\')">工作量估算</button>';
        template1 += '<button type="button" name="button" class="popView-btn" onclick="save(\'%s\')">保存</button>';
        template1 += '<button type="button" name="button" class="popView-btn" onclick="admin(\'%s\')">管理员项</button>';
        template1 += '<div class = "customernames" id = "adduresid">';
        for (var idvalues in callures) {
            var str = '<input class="ads_Checkbox" type="checkbox" style = "width" value="%s"/>%s <br/>'.format(idvalues, callures[idvalues]);
            template1 += str;
        }
        template1 += '<br/>';
        template1 += '</div>';
        template1 += '<select class = "tag_Select" style="width:100%">';
        for (var tag_infoid in tag_info) {
            if(tag_infoid == priority){
                var str = '<option id = "option-0" value="%s" selected="selected">%s </option>'.format(tag_infoid, tag_info[tag_infoid]);                
            }else{
                var str = '<option id = "option-0" value="%s" >%s </option>'.format(tag_infoid, tag_info[tag_infoid])
            }
            template1 += str;
        }
        template1 += '</select>';
        template1 += '</div>';
        template1 += '</div>';
        var result = template1.format(popViewInfo.name, description,due_at,popViewInfo.currentId,popViewInfo.currentId,popViewInfo.currentId, popViewInfo.currentId, popViewInfo.currentId, popViewInfo.currentId, popViewInfo.currentId, popViewInfo.currentId);
        document.getElementById(popViewInfo.currentId).innerHTML = "";
        jQuery("#" + popViewInfo.currentId).append(result);
        jQuery(function() {
            jQuery("#datepicker").datepicker({
              dateFormat: "yy-mm-dd"
            });
        });

    },



};
