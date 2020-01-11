<?php if (!defined('THINK_PATH')) exit();?>
<div class="admin_business link_blue">
        <div style="height:30px;">用户名 <strong style="color:#FF6600"><?php echo ($userinfo['username']); ?></strong>(uid：<?php echo ($userinfo['uid']); ?>) &nbsp;&nbsp;&nbsp;&nbsp;
            <a target="_blank" href="<?php echo ($manage_url); ?>">进入会员中心>></a><br/>
        </div>
        <?php if(is_array($return_config)): $i = 0; $__LIST__ = $return_config;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="manitem active" url="<?php echo U('gongzi_log',array('com_id'=>$vo['com_id'],'zaizhi_id'=>$zaizhi_id));?>"><?php echo ($vo['companyname']); ?></div><?php endforeach; endif; else: echo "" ;endif; ?>
        
        
        
        <div class="clear"></div>
        <div class="show_box" style="margin-top:10px;"></div>
</div>

<script type="text/javascript">
<?php if(!empty($return_config)): ?>$.getJSON("<?php echo U('gongzi_log',array('com_id'=>$return_config[0]['com_id'],'zaizhi_id'=>$zaizhi_id));?>", function (result) {
        $('.show_box').html(result.data);
        afreshDialogPosition();
    });<?php endif; ?>


    $(".manitem").click(function () {
        $('.manitem').removeClass('active');
        $(this).addClass('active');
        var url = $(this).attr('url');
        $.getJSON(url, function (result) {
            $('.show_box').html(result.data);
        });
    });
</script>