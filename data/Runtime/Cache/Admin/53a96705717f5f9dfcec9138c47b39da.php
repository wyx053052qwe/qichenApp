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
			SELF = '/index.php?m=Admin&amp;c=Personal&amp;a=gongzi_add&amp;id=21',
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

<div class="toptit">添加在职员工工资</div>
<form id="form1" action="<?php echo U('zaizhi_add');?>" method="post">

    <div class="form_main width150">
        <div class="fl">工资日期:</div>
            <div class="fr">
                <input name="gongzi_time" type="text" class="input_text_default middle"  value=""/>
                
            </div>

            
        <?php if(is_array($config)): $i = 0; $__LIST__ = $config;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="fl"><?php echo ($vo['cn']); ?>:</div>
            <div class="fr">
                <input name="<?php echo ($vo['en']); ?>" type="text" class="input_text_default middle"  value=""/>
            </div><?php endforeach; endif; else: echo "" ;endif; ?>
        <div class="fl">应发工资:</div>
            <div class="fr">
                <input name="yingfa" type="text" class="input_text_default middle"  value=""/>
                
            </div>
        <div class="fl">实发工资:</div>
            <div class="fr">
                <input name="shifa" type="text" class="input_text_default middle"  value=""/>
                
            </div>
        
        
        <div class="fl"></div>
        <div class="fr">
            <input type="hidden" name="com_id" value="<?php echo ($com_id); ?>">
            <input type="hidden" name="zaizhi_id" value="<?php echo ($zaizhi_id); ?>">
            <input type="button" class="admin_submit" value="添加" id="J_submit"/>
            <input type="button" class="admin_submit" value="返回" onclick="window.location.href='<?php echo U('zaizhi');?>'"/>
        </div>
        <div class="clear"></div>
    </div>
</form>

</div>
<!-- public:footer 以下内容 -->
<div class="footer link_blue">
    Powered by <a href="" target="_blank"><span style="color:#009900">启辰</span><span
        style="color: #FF3300">cms</span></a> v<?php echo C('QSCMS_VERSION');?>
</div>
<!-- public:footer 以上内容 -->
</body>
<script type="text/javascript">
    $('#J_submit').click(function(){
            var that = $(this);
            if(that.hasClass('disabled')){
                return false;
            }
            that.val('正在保存...').addClass('disabled');
            $.post("<?php echo U('gongzi_add');?>",$('#form1').serialize(),function(result){
                if(result.status==1){
                    disapperTooltip("success", result.msg,function(){
                        location.reload();
                    });
                }else{
                    disapperTooltip("remind", result.msg,function(){
                        that.val('保存修改').removeClass('disabled');
                    });
                    return false;
                }
            },'json');
        });
</script>
</html>