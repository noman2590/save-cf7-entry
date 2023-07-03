<?php
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
            <input type="hidden" name="form" value="<?php echo $_GET['form']; ?>">
            <div class="alignleft actions">
                <label for="filter-by-date" class="">From Date</label>
                <input type="date" name="from_date" id="" value="<?php echo (isset($_GET['from_date'])) ? $_GET['from_date'] : '' ?>">		
            </div>
            <div class="alignleft actions">
                <label for="filter-by-date" class="">To Date</label>
                <input type="date" name="to_date" id="" value="<?php echo (isset($_GET['to_date'])) ? $_GET['to_date'] : '' ?>">
            </div>
            <div class="alignleft actions">
                <input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filter">
            </div>
        </form>
        <div class="tablenav-pages one-page"><span class="displaying-num"><a href="javascript:history.go(-1)">Go Back</a> | Total Entries: <?php echo count($formData) ?></span>
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
                            <td><?php echo $row['cf7_entry_id']; ?></td>
                            <td><?php echo $row['created_at']; ?></td>
                            <td>
                            <?php foreach ($row['meta'] as $metaKey => $metaValue): ?>
                                <?php echo $metaKey . ': ' . $metaValue; ?><br>
                            <?php endforeach; ?>
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
        $('#entryTable').DataTable();
        $('#toplevel_page_manage-cf7-entries').addClass('current');
        $('#toplevel_page_manage-cf7-entries a').addClass('current');
    });
</script>