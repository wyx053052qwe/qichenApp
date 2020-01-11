<?php if (!defined('THINK_PATH')) exit();?><!-- public:header 以下内容 -->
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>网站后台管理中心- Powered by 74cms.com</title>
	<link rel="shortcut icon" href="<?php echo C('qscms_site_dir');?>favicon.ico"/>
	<meta name="author" content="骑士CMS" />
	<meta name="copyright" content="74cms.com" />
	<link href="__ADMINPUBLIC__/css/common.css" rel="stylesheet" type="text/css">
	<script src="__ADMINPUBLIC__/js/jquery.min.js"></script>
	<script src="__ADMINPUBLIC__/js/jquery.highlight-3.js"></script>
	<script src="__ADMINPUBLIC__/js/jquery.vtip-min.js"></script>
	<script src="__ADMINPUBLIC__/js/jquery.modal.dialog.js"></script>
	<script type="text/javascript" src="__ADMINPUBLIC__/js/laydate/laydate.js"></script>
	<!--[if lt IE 9]>
	<script type="text/javascript" src="__ADMINPUBLIC__/js/PIE.js"></script>
	<script type="text/javascript">
		(function ($) {
			$.pie = function (name, v) {
				// 如果没有加载 PIE 则直接终止
				if (!PIE) return false;
				// 是否 jQuery 对象或者选择器名称
				var obj = typeof name == 'object' ? name : $(name);
				// 指定运行插件的 IE 浏览器版本
				var version = 9;
				// 未指定则默认使用 ie10 以下全兼容模式
				if (typeof v != 'number' && v < 9) {
					version = v;
				}
				// 可对指定的多个 jQuery 对象进行样式兼容
				if ($.browser.msie && obj.size() > 0) {
					if ($.browser.version * 1 <= version * 1) {
						obj.each(function () {
							PIE.attach(this);
						});
					}
				}
			}
		})(jQuery);
		if ($.browser.msie) {
			$.pie('.pie_about');
		};
	</script>
	<![endif]-->
	<script src="__ADMINPUBLIC__/js/jquery.disappear.tooltip.js"></script>
	<script src="__ADMINPUBLIC__/js/common.js"></script>
	<script>
		var URL = '/index.php/Admin/Personal',
			SELF = '/index.php?m=Admin&amp;c=Personal&amp;a=zaizhi',
			ROOT_PATH = '/index.php/Admin',
			APP	 =	 '/index.php';

		var qscms = {
			district_level : "<?php echo C('qscms_category_district_level');?>",
			default_district : "<?php echo C('qscms_default_district');?>"
		};
	</script>
	<?php echo ($synsitegroupunbindmobile); ?>
	<?php echo ($synsitegroupregister); ?>
	<?php echo ($synsitegroupedit); ?>
	<?php echo ($synsitegroup); ?>
</head>
<body>

<!-- public:header 以上内容 -->
<?php if(empty($menu_title)): ?><div class="allpagetop">后台管理中心<strong>/</strong>首页</div>
	<?php else: ?>
	<div class="allpagetop"><?php echo ($menu_title); ?><strong>/</strong><?php echo ($sub_menu_title); ?></div><?php endif; ?>
<div class="mains">
	<div class="h1tit">
		<div class="h1">
            <?php if($sub_menu['pageheader']): echo ($sub_menu["pageheader"]); ?>
                <?php else: ?>
                <!--欢迎登录 <?php echo C('qscms_site_name');?> 管理中心！--><?php endif; ?>
        </div>
        <?php if(!empty($sub_menu['menu'])): ?><div class="topnav">
                <?php if(is_array($sub_menu['menu'])): $i = 0; $__LIST__ = $sub_menu['menu'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><a href="<?php echo U($val['module_name'].'/'.$val['controller_name'].'/'.$val['action_name']); echo ($val["data"]); if($isget and $_GET): echo get_first(); endif; if($_GET['_k_v']): ?>&_k_v=<?php echo (_I($_GET['_k_v'])); endif; ?>" class="<?php echo ($val["class"]); ?>"><?php echo ($val["name"]); ?></a><?php endforeach; endif; else: echo "" ;endif; ?>
                <div class="clear"></div>
            </div><?php endif; ?>
		<div class="clear"></div>
	</div>
<div class="seltpye_x">
    <div class="left">绑定时间</div>
    <div class="right">
        <a href="<?php echo P(array('settr'=>''));?>" <?php if(($_GET['settr']) == ""): ?>class="select"<?php endif; ?>>不限</a>
        <a href="<?php echo P(array('settr'=>'3'));?>" <?php if(($_GET['settr']) == "3"): ?>class="select"<?php endif; ?>>三天内</a>
        <a href="<?php echo P(array('settr'=>'7'));?>" <?php if(($_GET['settr']) == "7"): ?>class="select"<?php endif; ?>>一周内</a>
        <a href="<?php echo P(array('settr'=>'30'));?>" <?php if(($_GET['settr']) == "30"): ?>class="select"<?php endif; ?>>一月内</a>
        <a href="<?php echo P(array('settr'=>'180'));?>" <?php if(($_GET['settr']) == "180"): ?>class="select"<?php endif; ?>>半年内</a>
        <a href="<?php echo P(array('settr'=>'360'));?>" <?php if(($_GET['settr']) == "360"): ?>class="select"<?php endif; ?>>一年内</a>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>
<div class="seltpye_x">
    <div class="left">在职绑定状态</div>
    <div class="right">
        <a href="<?php echo P(array('is_bind'=>''));?>" <?php if(($_GET['is_bind']) == ""): ?>class="select"<?php endif; ?>>不限</a>
        <a href="<?php echo P(array('is_bind'=>'1'));?>" <?php if(($_GET['is_bind']) == "1"): ?>class="select"<?php endif; ?>>绑定</a>
        <a href="<?php echo P(array('is_bind'=>'0'));?>" <?php if(($_GET['is_bind']) == "0"): ?>class="select"<?php endif; ?>>未绑定</a>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>
<form id="form1" name="form1" method="post" action="<?php echo U('zaizhi_delete');?>">
    <div class="list_th">
        
        <div class="td" style=" width:10%;">
            <label id="chkAll" class="left_padding">
                <input type="checkbox" name="chkAll" id="chk" title="全选/反选"/>姓名
            </label>
        </div>
       
        <div class="td" style=" width:10%;">身份证</div>
        <div class="td" style=" width:4%;">绑定用户</div>
        <div class="td center" style=" width:10%;">绑定时间</div>
        <div class="td center" style=" width:5%;">合同到期时间</div>
        
        <div class="td center" style=" width:10%;">在职企业</div>
        <div class="td " style=" width:10%;">工资卡号</div>
        <div class="td " style=" width:10%;">联系方式</div>
        <div class="td" style=" width:30%;">操作</div>
        <div class="clear"></div>
    </div>

    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="list_tr link_black">
            <div class="td" style=" width:10%;">
                <div class="left_padding striking">
                    <input name="tuid[]" type="checkbox" id="id" value="<?php echo ($vo['id']); ?>"/><?php echo ($vo['name']); ?>
                    <?php if(!empty($vo['lizhi_time'])): ?>(已离职)
                        <?php else: ?>
                        <?php if($vo['is_bind']): ?>(在职人员)
                        <?php else: ?>
                        (未绑定)<?php endif; endif; ?>



                    
                </div>
            </div>
            
            <div class="td" style=" width: 10%;">
                <span><?php echo (($vo['id_card'] != "")?($vo['id_card']):"未填写"); ?></span>
            </div>
            <div class="td" style=" width: 4%;">
                <span>
                <?php if(empty($vo['uid_name'])): ?>未绑定
                <?php else: ?>
                    <?php echo (($vo['uid_name'] != "")?($vo['uid_name']):"未绑定"); endif; ?>
                </span>
            </div>
            <div class="td center" style=" width:10%;">
                <?php if(empty($vo['dengji_time'])): ?>未绑定
                    <?php else: ?>
                    <?php echo date("Y-m-d",$vo['dengji_time']); endif; ?>
            </div>

            <div class="td center" style=" width:6%;">
                <?php if(empty($vo['dengji_time'])): ?>未设置
                    <?php else: ?>
                    <?php echo date("Y-m-d",$vo['ht_time']); endif; ?>
            </div>
            
            <div class="td center" style=" width:10%;"><?php if(empty($vo['companyname'])): ?>未绑定
                <?php else: ?>
                    <?php echo (($vo['companyname'] != "")?($vo['companyname']):"未绑定"); endif; ?></div>
            <div class="td" style=" width: 10%;">
                <span><?php echo (($vo['gongzi_card'] != "")?($vo['gongzi_card']):"未填写"); ?></span>
            </div>
            <div class="td" style=" width: 10%;">
                <span><?php echo (($vo['mobile'] != "")?($vo['mobile']):"未填写"); ?></span>
            </div>
            <div class="td edit" style=" width:30%;">
               
                <?php if($vo['is_bind'] == 1): if($vo['is_f'] == 1): ?><a href="<?php echo U('send_zaizhi',array('id'=>$vo['uid']));?>" style="background-color:red">发送</a><?php endif; ?>
                     <a href="javascript:void(0);" class="business" parameter="zaizhi_id=<?php echo ($vo['id']); ?>" hideFocus="true">工资表</a>
                     <a href="javascript:void(0);" class="tuijian" parameter="uid=<?php echo ($vo['uid']); ?>" hideFocus="true">推荐入职列表</a>
                     <a href="javascript:void(0);" class="jifen" parameter="uid=<?php echo ($vo['uid']); ?>" hideFocus="true">送积分</a>
                    <a href="<?php echo U('gongzi_add',array('id'=>$vo['id']));?>">添加工资表</a> 
                    <a href="<?php echo U('lizhi',array('id'=>$vo['id']));?>">离职</a><?php endif; ?>

                <!-- <?php if(!empty($vo['lizhi_time'])): ?><a href="<?php echo U('lizhi',array('id'=>$vo['id']));?>">未绑定</a><?php endif; ?> -->
                <a href="<?php echo U('zaizhi_edit',array('id'=>$vo['id']));?>">编辑</a>
                
                
            </div>
            <div class="clear"></div>
        </div><?php endforeach; endif; else: echo "" ;endif; ?>
</form>

<?php if(empty($list)): ?><div class="list_empty">没有任何信息！</div><?php endif; ?>

<div class="list_foot">

    <div class="btnbox">
        <input type="button" class="admin_submit" id="ButAdd" value="添加在职人员" onclick="window.location.href='<?php echo U('zaizhi_add');?>'"/>
        <input type="button" class="admin_submit" id="ButDel" value="删除在职人员"/>

        <input type="button" class="admin_submit" id="yijian_f" style="background-color:red" value="一键发送合同到期提醒"/>
        <?php if($apply['Export']): ?><input type="button" class="admin_submit" id="ExPort" value="导出"/><?php endif; ?>
        <?php if(!empty($apply['Resumeimport'])): ?><input type="button" class="admin_submit xianshi" id="ButImportresume" value="导入工资表"/><?php endif; ?>
    </div>
    </div>

    
</div>
<div class="pages"><?php echo ($page); ?></div>

</div>
<!-- public:footer 以下内容 -->
<div class="footer link_blue">
    Powered by <a href="" target="_blank"><span style="color:#009900">启辰</span><span
        style="color: #FF3300">cms</span></a> v<?php echo C('QSCMS_VERSION');?>
</div>
<!-- public:footer 以上内容 -->

</body>
<script type="text/javascript" src="__ADMINPUBLIC__/js/jquery.dropdown.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        //登录日志
        $(".login_log").click(function () {
            var qsDialog = $(this).dialog({
                title: '登录日志',
                loading: true,
                footer : false
            });
            var param = $(this).attr('parameter');
            var url = "<?php echo U('Ajax/login_log');?>&" + param;
            $.getJSON(url, function (result) {
                qsDialog.setContent(result.data);
            });
        });
        //会员日志
        $(".login_log").click(function () {
            var qsDialog = $(this).dialog({
                title: '登陆日志',
                loading: true,
                footer : false
            });
            var param = $(this).attr('parameter');
            var url = "<?php echo U('Ajax/login_log');?>&" + param;
            $.getJSON(url, function (result) {
                qsDialog.setContent(result.data);
            });
        });
         //会员日志
        $(".personal_log").click(function () { 
            var qsDialog = $(this).dialog({
                title: '会员日志',
                loading: true,
                footer : false
            });
            var param = $(this).attr('parameter');
            var url = "<?php echo U('Ajax/personal_log');?>&" + param;
            $.getJSON(url, function (result) {
                qsDialog.setContent(result.data);
            });
        });
        $(".J_message").click(function () {
            $('.modal_backdrop').remove();
            $('.modal').remove();
            var qsDialog = $(this).dialog({
                title: '发消息',
                loading: true,
                footer : false
            });
            var param = $(this).attr('parameter');
            var url = "<?php echo U('Ajax/ajax_message');?>&" + param;
            $.getJSON(url, function (result) {
                qsDialog.setContent(result.data);
            });
        });

        $(".jifen").click(function () {
            $('.modal_backdrop').remove();
            $('.modal').remove();
            var qsDialog = $(this).dialog({
                title: '赠送积分',
                loading: true,
                footer : false
            });
            var param = $(this).attr('parameter');
            var url = "<?php echo U('jifen');?>&" + param;
            $.getJSON(url, function (result) {
                qsDialog.setContent(result.data);
            });
        });
        //业务
        $(".business").click(function () {
            var qsDialog = $(this).dialog({
                title: '业务',
                loading: true,
                footer : false
            });
            var param = $(this).attr('parameter');
            var url = "<?php echo U('gongzi');?>&" + param;
            $.getJSON(url, function (result) {
                qsDialog.setContent(result.data);
            });
        });
        //推荐入职
        $(".tuijian").click(function () {
            var qsDialog = $(this).dialog({
                title: '业务',
                loading: true,
                footer : false
            });
            var param = $(this).attr('parameter');
            var url = "<?php echo U('son_list');?>&" + param;
            $.getJSON(url, function (result) {
                qsDialog.setContent(result.data);
            });
        });
        //批量删除
        $("#ButDel").click(function () {
            if (confirm('删除在职人员后，缺少部分功能')) {
                $("form[name=form1]").attr("action", "<?php echo U('zaizhi_delete');?>");
                $("form[name=form1]").submit();
            }
        });

        $("#yijian_f").click(function(){
            var url = "<?php echo U('yijian_f');?>";
            window.location.href=url;
        });

            <?php if($apply['Export']): ?>//点击批量导出
            $("#ExPort").click(function () {
                var data = $("form[name=form1]").serialize();
                if(data.length == 0){
                    disapperTooltip('remind','请选择员工！');
                    qsDialog.hide();
                }
                $("form[name=form1]").attr("action", "<?php echo U('Export/Admin/export_personal1');?>");
                $("form[name=form1]").submit();
            });<?php endif; ?>
        <?php if(!empty($apply['Resumeimport'])): ?>//导入简历
            $("#ButImportresume").click(function () {
                var qsDialog = $(this).dialog({
                    title: '导入简历',
                    loading: true,
                    footer : false
                });
                var url = "<?php echo U('Ajax/resume_imports');?>";
                $.getJSON(url, function (result) {
                    qsDialog.setContent(result.data);
                });
            });<?php endif; ?>
    });
</script>
</html>