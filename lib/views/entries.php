<div class="wrap">
    <pre>
    <?php
    // print_r($data);
    ?>
    </pre>
    <h2>Contact Forms Listing</h2>
    <div class="tablenav top">
        <form action="" method="get">
            <input type="hidden" name="page" value="manage-charity-orders">
            <div class="alignleft actions">
                <label for="filter-by-date" class="">From Date</label>
                <input type="date" name="from_date" id="" value="<?php echo (isset($_GET['from_date'])) ? $_GET['from_date'] : '' ?>">
                <label for="filter-by-date" class="">To Date</label>
                <input type="date" name="to_date" id="" value="<?php echo (isset($_GET['to_date'])) ? $_GET['to_date'] : '' ?>">
                <input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filter">		
            </div>
        </form>
        <div class="tablenav-pages one-page"><span class="displaying-num">Total Forms: <?php echo count($data['data']) ?></span>
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
                    ?>
                    <tr>
                        <td><?php echo $value->id ?></td>
                        <td><?php echo $value->post_title ?></td>
                        <td><?php echo $value->total_entries ?></td>
                        <td><a class="btn button button-primary" href="<?php echo site_url();?>/wp-admin/admin.php?page=manage-cf7-entries&from=<?php echo $value->id; ?>">See Entries</a></td>
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
