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
            <input type="hidden" name="form" value="<?php echo sanitize_text_field($_GET['form']); ?>">
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
        <div class="tablenav-pages one-page"><span class="displaying-num"><a href="javascript:history.go(-1)">Go Back</a> | Total Entries: <?php echo esc_attr(count($formData)) ?></span>
    </div>
	<div class="bg-white">
		<div class="ai1wm-left">
            <table id="entryTable" class="striped widefat">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Entry Date/Time</th>
                    <th>Form Data</th>
                 </tr>
                </thead>
                <tbody>
                    <?php 
                    if(count($data['data'])){
                    foreach ($formData as $row): ?>
                        <tr>
                            <td><?php echo esc_attr($row['cf7_entry_id']); ?></td>
                            <td><?php echo esc_attr($row['created_at']); ?></td>
                            <td>
                            <?php foreach ($row['meta'] as $metaKey => $metaValue): ?>
                                <?php echo wp_kses('<b>' . $metaKey . ':</b> ' . $metaValue, array('b' => array())); ?><br>
                            <?php endforeach; ?>
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
        // table.columns.adjust().draw();
        $('#toplevel_page_manage-cf7-entries').addClass('current');
        $('#toplevel_page_manage-cf7-entries a').addClass('current');
    });
</script>