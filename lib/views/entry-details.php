<?php
    if ( ! defined( 'ABSPATH' ) ) exit;

    $from_date = (isset($_GET['from_date'])) ? sanitize_text_field($_GET['from_date']) : ''; 
    $to_date = (isset($_GET['to_date'])) ? sanitize_text_field($_GET['to_date']) : ''; 

    $formData = array();
    foreach ($data['data'] as $row) {
        $parentId = $row->cf7_entry_id;
        if (!isset($formData[$parentId])) {
            $formData[$parentId] = array(
            'id' => $row->id,
            'created_at' => $row->created_at,
            'cf7_entry_id' => $parentId,
            'meta' => array()
            );
        }
        $formData[$parentId]['meta'][$row->meta_key] = $row->meta_value;
    }
?>
<div class="wrap entry-detail-page cf7-entry-page"> 
    <h2>Form Entries Listing</h2>
    <div class="tablenav top">
        <form action="" method="get">
            <input type="hidden" name="page" value="form-entries">
            <input type="hidden" name="form" value="<?php echo esc_attr(sanitize_text_field($_GET['form'])); ?>">
            <div class="alignleft actions">
                <label for="filter-by-date" class="">From Date</label>
                <input type="date" name="from_date" id="" value="<?php echo esc_attr($from_date)?>">		
            </div>
            <div class="alignleft actions">
                <label for="filter-by-date" class="">To Date</label>
                <input type="date" name="to_date" id="" value="<?php echo esc_attr($to_date);?>">
            </div>
            <div class="alignleft actions">
                <input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filter">
            </div>
        </form>
        <?php if($from_date || $to_date) { 
        $reset_filter_url = add_query_arg(array('page' => 'form-entries', 'form' => $_GET['form']), admin_url('admin.php'));?>
        <div class="alignleft actions">
            <a type="button" id="post-query-submit" class="button" href="<?php echo esc_url($reset_filter_url); ?>">Clear Filters</a>
        </div>
        <?php }
            $entries_goback_url = SCF7EMainController::scf7e_get_entries_url('manage-cf7-entries');
        ?>
        <div class="tablenav-pages one-page"><span class="displaying-num"><a href="<?php echo esc_url($entries_goback_url); ?>">Go Back</a> | Total Entries: <?php echo esc_attr(count($formData)) ?></span>
    </div>
	<div class="bg-white">
		<div class="ai1wm-left">
        <table id="entryTable" class="striped widefat">
                <thead>
                <tr>
                    <th>Entry Date/Time</th>
                    <?php $allMetaKeys = []; ?>
                    <?php if (count($formData) > 0): ?>
                        <?php foreach ($formData as $row): ?>
                            <?php $allMetaKeys = array_merge($allMetaKeys, array_keys($row['meta'])); ?>
                        <?php endforeach; ?>
                        <?php $allMetaKeys = array_unique($allMetaKeys); ?>
                        <?php foreach ($allMetaKeys as $metaKey): ?>
                            <th><?php echo esc_attr(SCF7EMainController::scf7e_convert_to_title_case($metaKey)); ?></th>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <th>Action</th>
                 </tr>
                </thead>
                <tbody>
                    <?php 
                    if(count($data['data'])){
                    foreach ($formData as $row): ?>
                        <tr>
                            <td><?php echo $row['created_at']; ?></td>
                            <?php foreach ($allMetaKeys as $metaKey): ?>
                                <td><?php echo isset($row['meta'][$metaKey]) ? esc_attr($row['meta'][$metaKey]) : ''; ?></td>
                            <?php endforeach; ?>
                            <td>
                                <form action="<?php echo admin_url('admin-post.php'); ?>" method="POST">
                                    <input type="hidden" name="entry_id" value="<?php echo $row['cf7_entry_id']; ?>">
                                    <input type="hidden" name="action" value="scf7e_delete_entry">
                                    <?php wp_nonce_field('scf7e_delete_entry_nonce_action', 'scf7e_delete_entry_nonce'); ?>
                                    <button type="submit" class="dashicon-button">
                                        <span class="dashicons dashicons-trash"></span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php 
                    endforeach;
                    } else { 
                    ?>
                    <tr>
                        <td colspan="3">
                            <p style="text-align:center">No entry found</p>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
	</div>
</div>

<script>
    jQuery(document).ready( function ($) {
        var table = $('#entryTable').DataTable({
           "autoWidth": true
        });
        $('#toplevel_page_manage-cf7-entries').addClass('current');
        $('#toplevel_page_manage-cf7-entries a').addClass('current');
    });
</script>