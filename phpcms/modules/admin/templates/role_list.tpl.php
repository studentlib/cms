<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header'); ?>
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript">

    $(document).ready(function () {
        $('.updatePlatform').click(function () {
            var data = {};
            var idc = $(this).attr('id').split('_');
            data.roleid = $('.roleid_' + idc[1]).text();
            data.platform=$('.platform_' + idc[1]).text();
            data.pc_hash='<?php echo $_SESSION['pc_hash'];?>';
            $.post("?m=admin&c=role&a=updatePlatform", data, function (data) {
                alert(data.msg);
                //location.reload();
            },'json');
        });
    });
</script>
<div class="table-list pad-lr-10">
    <form name="myform" action="?m=admin&c=role&a=listorder" method="post">
        <table width="100%" cellspacing="0">
            <thead>
            <tr>
                <th width="10%"><?php echo L('listorder'); ?></th>
                <th width="10%">ID</th>
                <th width="15%" align="left"><?php echo L('role_name'); ?></th>
                <th width="265" align="left"><?php echo L('role_desc'); ?></th>
                <th width="5%" align="left"><?php echo L('role_status'); ?></th>
                <th class="text-c"><?php echo L('role_operation'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (is_array($infos)) {
                foreach ($infos as $info) {
                    ?>
                    <tr>
                        <td width="10%" align="center"><input name='listorders[<?php echo $info['roleid'] ?>]'
                                                              type='text' size='3'
                                                              value='<?php echo $info['listorder'] ?>'
                                                              class="input-text-c"></td>
                        <td width="10%" align="center"><?php echo $info['roleid'] ?></td>
                        <td width="15%"><?php echo $info['rolename'] ?></td>
                        <td width="265"><?php echo $info['description'] ?></td>
                        <td width="5%"><a
                                    href="?m=admin&c=role&a=change_status&roleid=<?php echo $info['roleid'] ?>&disabled=<?php echo($info['disabled'] == 1 ? 0 : 1) ?>"><?php echo $info['disabled'] ? L('icon_locked') : L('icon_unlock') ?></a>
                        </td>
                        <td class="text-c">
                            <?php if ($info['roleid'] > 1) { ?>
                                <a href="javascript:setting_role(<?php echo $info['roleid'] ?>, '<?php echo new_addslashes($info['rolename']) ?>')"><?php echo L('role_setting'); ?></a> |
                                <a href="javascript:void(0)"
                                   onclick="setting_cat_priv(<?php echo $info['roleid'] ?>, '<?php echo new_addslashes($info['rolename']) ?>')"><?php echo L('usersandmenus') ?></a> |
                            <?php } else { ?>
                                <font color="#cccccc"><?php echo L('role_setting'); ?></font> | <font
                                        color="#cccccc"><?php echo L('usersandmenus') ?></font> |
                            <?php } ?>
                            <a href="?m=admin&c=role&a=member_manage&roleid=<?php echo $info['roleid'] ?>&menuid=<?php echo $_GET['menuid'] ?>"><?php echo L('role_member_manage'); ?></a>
                            |
                            <?php if ($info['roleid'] > 1) { ?><a
                                href="?m=admin&c=role&a=edit&roleid=<?php echo $info['roleid'] ?>&menuid=<?php echo $_GET['menuid'] ?>"><?php echo L('edit') ?></a> |
                                <a href="javascript:confirmurl('?m=admin&c=role&a=delete&roleid=<?php echo $info['roleid'] ?>', '<?php echo L('posid_del_cofirm') ?>')"><?php echo L('delete') ?></a>
                            <?php } else { ?>
                                <font color="#cccccc"><?php echo L('edit') ?></font> | <font
                                        color="#cccccc"><?php echo L('delete') ?></font>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
        </table>
        <div class="btn"><input type="submit" class="button" name="dosubmit" value="<?php echo L('listorder') ?>"/>
        </div>
    </form>
</div>

<div align="center">
    <table align="center" width="100%" cellspacing="0"
           style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:14px;border:1px cadetblue solid;">
        <tr style="border:1px cadetblue solid;">
            <th width="10%" align="center">排序</th>
            <th width="10%">ID</th>
            <th width="15%" align="left">角色名</th>
            <th width="265" align="left">角色描述</th>
            <th width="50%" align="left">操作平台</th>
            <th width="120" align="left">管理操作</th>
        </tr>
        <?php
        foreach ($infos as $info) {
            ?>
            <tr>
                <th width="10%" align="center"><?php echo $info['listorder']; ?></th>
                <th width="10%" class="roleid_<?php echo $info['roleid']; ?>"><?php echo $info['roleid']; ?></th>
                <th width="15%" align="left"><?php echo $info['rolename']; ?></th>
                <th width="265" align="left"><?php echo $info['description']; ?></th>
                <th width="50%" align="left" contentEditable="true"
                    class="platform_<?php echo $info['roleid']; ?>"><?php echo $info['platform']; ?></th>
                <th width="200" align="center"><input type="submit" width="180px" class="updatePlatform"
                                                      id="updatePlatform_<?php echo trim($info['roleid']); ?>"
                                                      value="保存"></th>
            </tr>
            <?php
        }
        ?>
    </table>
</div>

</body>
<script type="text/javascript">
    <!--
    function setting_role(id, name) {

        window.top.art.dialog({
            title: '<?php echo L('sys_setting')?>《' + name + '》',
            id: 'edit',
            iframe: '?m=admin&c=role&a=priv_setting&roleid=' + id,
            width: '700',
            height: '500'
        });
    }

    function setting_cat_priv(id, name) {

        window.top.art.dialog({
            title: '<?php echo L('usersandmenus')?>《' + name + '》',
            id: 'edit',
            iframe: '?m=admin&c=role&a=setting_cat_priv&roleid=' + id,
            width: '700',
            height: '500'
        });
    }

    //-->

</script>
</html>
