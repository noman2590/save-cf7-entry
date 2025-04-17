<?php
    if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="wrap cf7-entry-page">
    <?php 
        $from_date = (isset($_GET['from_date'])) ? sanitize_text_field($_GET['from_date']) : ''; 
        $to_date = (isset($_GET['to_date'])) ? sanitize_text_field($_GET['to_date']) : ''; 
    ?>
    <h2>Contact Forms Listing</h2>
    <div class="tablenav top">
        <form action="" method="get">
            <input type="hidden" name="page" value="manage-cf7-entries">
            <div class="alignleft actions">
                <label for="filter-by-date" class="">From Date</label>
                <input type="date" name="from_date" id="" value="<?php echo (isset($_GET['from_date'])) ? esc_attr(sanitize_text_field($_GET['from_date'])) : '' ?>">
            </div>
            <div class="alignleft actions">
                <label for="filter-by-date" class="">To Date</label>
                <input type="date" name="to_date" id="" value="<?php echo (isset($_GET['to_date'])) ? esc_attr(sanitize_text_field($_GET['to_date'])) : '' ?>">
            </div>
            <div class="alignleft actions">
                <input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filter">		
            </div>
        </form>
        <?php if($from_date || $to_date) { 
        $reset_filter_url = add_query_arg(array('page' => 'manage-cf7-entries'), admin_url('admin.php'));?>
        <div class="alignleft actions">
            <a type="button" id="post-query-submit" class="button" href="<?php echo esc_url($reset_filter_url); ?>">Clear Filters</a>
        </div>
        <?php } ?>
        <div class="tablenav-pages one-page"><span class="displaying-num">Total Forms: <?php echo esc_attr(count($data['data'])) ?></span>
    </div>
	<div class="bg-white">
		<div class="ai1wm-left">
            <table id="myTable" class="striped widefat">
                <thead>
                <tr>
                    <th>Form ID</th>
                    <th>Form Title</th>
                    <th>Total Saved Entries</th>
                    <th>Action</th>
                 </tr>
                </thead>
                <tbody>
                    <?php 
                    if(count($data['data'])){
                    foreach ($data['data'] as $key=>$value){ 
                        $entries_view_url = SCF7EMainController::scf7e_get_entries_url('form-entries', $value->id);
                    ?>
                    <tr>
                        <td><?php echo esc_attr($value->id) ?></td>
                        <td><?php echo esc_attr($value->post_title) ?></td>
                        <td><?php echo esc_attr($value->total_entries) ?></td>
                        <td><a class="btn button button-primary" href="<?php echo esc_url($entries_view_url); ?>">See Entries</a></td>
                    </tr>
                    <?php }}else { ?>
                    <tr>
                        <td colspan="4">
                            <p style="text-align:center">No form found</p>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
	</div>
</div>
