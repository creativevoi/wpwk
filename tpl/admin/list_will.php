<div class="wrap">

    <div class="icon32" id="icon-will"><br /></div>

    <h2>Manage Will Kit</h2>
    
    <p>Below is list of created wills.</p>
    
    <form method="get" action="<?php echo admin_url('admin.php');?>" id="filterform">
        <input type="hidden" name="page" value="<?php echo $this->pluginPrefix;?>_manage_will" />
        <input type="hidden" name="p" value="<?php echo $pagination['current_page']?>" />
        <table>
            <tr>
                <td>
                    <label>Search by keywords: <input type="text" name="s" value="<?php echo isset($_GET['s']) ? $_GET['s'] : ''?>" /></label>
                </td>
                <td>
                    <select name="completed">
                        <option value="-1" <?php echo (!isset($_GET['completed']) OR $_GET['completed']=='-1') ? 'selected="selected"' : ''?>>Is completed?</option>
                        <option value="1" <?php echo (isset($_GET['completed']) AND $_GET['completed']=='1') ? 'selected="selected"' : ''?>>Yes</option>
                        <option value="0" <?php echo (isset($_GET['completed']) AND $_GET['completed']=='0') ? 'selected="selected"' : ''?>>No</option>
                    </select>
                </td>
                <!--<td>
                    <select name="is_paid">
                        <option value="-1" <?php echo (!isset($_GET['is_paid']) OR $_GET['is_paid']=='-1') ? 'selected="selected"' : ''?>>Is purchased?</option>
                        <option value="1" <?php echo (isset($_GET['is_paid']) AND $_GET['is_paid']=='1') ? 'selected="selected"' : ''?>>Yes</option>
                        <option value="0" <?php echo (isset($_GET['is_paid']) AND $_GET['is_paid']=='0') ? 'selected="selected"' : ''?>>No</option>
                    </select>
                </td>-->
                <td><input type="submit" value="Search" class="button" /></td>
            </tr>
        </table>
    </form>
    
    <form method="post" action="<?php echo admin_url('admin-ajax.php');?>">
        <input type="hidden" name="action" value="wk_export_customer" />
        <input type="hidden" name="s" value="<?php echo isset($_GET['s']) ? trim($_GET['s']) : ''?>" />
        <input type="hidden" name="completed" value="<?php echo isset($_GET['completed']) ? trim($_GET['completed']) : '-1'?>" />
        <input type="hidden" name="is_paid" value="<?php echo isset($_GET['is_paid']) ? trim($_GET['is_paid']) : '-1'?>" />
        <input type="submit" value="Export To CSV" class="button alignright" />
        <p class="cvpaging">
            <strong>Page:</strong> <?php echo $pagination['goto_slb']?> of <?php echo $pagination['total_page']?> page(s) |
            <strong><?php echo $pagination['text']?> will(s)</strong>
        </p>
    </form>
    
    <table class="cvlist" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th class="first" width="50">Will ID</th>
                <th align="left">Customer</th>
                <th width="20%" align="left">Occupation</th>
                <th width="20%" align="left">Email</th>
                <th width="50" align="left">Completed</th>
                <!--<th width="50" align="left">Purchased</th>-->
            </tr>
        </thead>
        <tbody>
    <?php if (sizeof($items)) : ?>
        <?php foreach ( $items as $index=>$item ) : ?>
            <tr <?php echo $index%2 ? 'class="alt"' : ''?>>
                <td align="center"><?php echo $item->id?></td>
                <td>
                    <strong><?php echo stripslashes(trim("$item->firstname $item->lastname"))?></strong>
                </td>
                <td><em><?php echo stripslashes($item->occupation)?></em></td>
                <td><a href="mailto:<?php echo $item->email?>"><?php echo $item->email?></a></td>
                <td>
                <?php if ($item->completed) : ?>
                    <strong style="color:#090">YES</strong>
                <?php else : ?>
                    <strong style="color:#999">NO</strong>
                <?php endif; ?>
                </td>
                <!--<td>
                <?php if ($item->is_paid) : ?>
                    <strong style="color:#090">YES</strong>
                <?php else : ?>
                    <strong style="color:#999">NO</strong>
                <?php endif; ?>
                </td>-->
            </tr>
        <?php endforeach; ?>
    <?php else : ?>
            <tr>
                <td colspan="6">No wills found.</td>
            </tr>
    <?php endif; ?>
        </tbody>
    </table>
    
    <p class="cvpaging">
        <strong>Page:</strong> <?php echo $pagination['goto_slb']?> of <?php echo $pagination['total_page']?> page(s) |
        <strong><?php echo $pagination['text']?> will(s)</strong>
    </p>
    
</div>