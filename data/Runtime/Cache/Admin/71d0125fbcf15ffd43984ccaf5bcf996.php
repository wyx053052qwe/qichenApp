<?php if (!defined('THINK_PATH')) exit();?><div class="mantit" style="margin-top:15px; margin-bottom: 8px;"><strong>工资详情</strong></div>
<div style="max-height:200px;overflow-y:auto">
    <table width="100%" border="0" cellpadding="10" cellspacing="1" bgcolor="#DFDFDF" class="link_lan">
        <?php if($config): ?><tr>
            <td bgcolor="#FFFFFF" align="center">工资日期</td>
            <?php if(is_array($config['config'])): $i = 0; $__LIST__ = $config['config'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><td bgcolor="#FFFFFF" align="center"><?php echo ($vo['cn']); ?></td><?php endforeach; endif; else: echo "" ;endif; ?>
            <td bgcolor="#FFFFFF" align="center">应发工资</td>
            <td bgcolor="#FFFFFF" align="center">实发工资</td>
            <td bgcolor="#FFFFFF" align="center">操作</td>
        </tr><?php endif; ?>

        <?php if($data): if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                    <td bgcolor="#FFFFFF" align="center"><?php echo ($vo['gongzi_time']); ?></td>
                    <?php if(is_array($vo['data'])): $i = 0; $__LIST__ = $vo['data'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$voo): $mod = ($i % 2 );++$i;?><td bgcolor="#FFFFFF" align="center"><?php echo ($voo['value']); ?></td><?php endforeach; endif; else: echo "" ;endif; ?>
                    <td bgcolor="#FFFFFF" align="center"><?php echo ($vo['yingfa']); ?></td>
                    <td bgcolor="#FFFFFF" align="center"><?php echo ($vo['shifa']); ?></td>
                        <td><a href="<?php echo U('gongzi_edit',array('id'=>$vo['id']));?>">编辑</a>
                        <a href="<?php echo U('gongzi_del',array('id'=>$vo['id']));?>">删除</a></td>
                        
                    
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" bgcolor="#FFFFFF">没有任何信息！</td>
            </tr><?php endif; ?>
    </table>
</div>