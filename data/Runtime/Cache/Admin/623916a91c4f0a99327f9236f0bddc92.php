<?php if (!defined('THINK_PATH')) exit();?><div class="dialog_box">
    <form action="<?php echo U('Resumeimport/Admin/imports');?>" method="post" name="form" id="form"  enctype="multipart/form-data">
        <table width="400" border="0" align="center" cellpadding="0" cellspacing="6" >
            <tr>
                <input type="file" name="wbmb"  id="wbmb" style="height:21px; width:210px; border:1px #999999 solid" />
            </tr>
            <tr>
                <td height="25">&nbsp;</td>
                <td><span style="border-top: 1px #A3C7DA solid;">
				 <input type="submit" name="set_audit" value="确定" class="admin_submit"/>
			  </span></td>
            </tr>
        </table>
    </form>
</div>