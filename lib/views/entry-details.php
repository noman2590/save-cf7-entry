<div class="wrap">
    <pre>
    <?php
    // print_r($data['data']);
    ?>
    </pre>
    <h2>Form Entries Listing</h2>
    <div class="tablenav top">
        <!-- <form action="" method="get">
            <input type="hidden" name="page" value="manage-charity-orders">
            <div class="alignleft actions">
                <label for="filter-by-date" class="">From Date</label>
                <input type="date" name="from_date" id="" value="<?php echo (isset($_GET['from_date'])) ? $_GET['from_date'] : '' ?>">
                <label for="filter-by-date" class="">To Date</label>
                <input type="date" name="to_date" id="" value="<?php echo (isset($_GET['to_date'])) ? $_GET['to_date'] : '' ?>">
                <input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filter">		
            </div>
        </form> -->
        <div class="tablenav-pages one-page"><span class="displaying-num">Total Entries: <?php echo count($data['row']) ?></span>
    </div>
	<div class="bg-white">
		<div class="ai1wm-left">
            <table id="myTable" class="striped widefat">
                <thead>
                <tr>
                    <?php
                    $firstEntryId = $data['data'][0]->cf7_entry_id;
                    $iterations = 0;
                    foreach($data['data'] as $entry) { 
                        if ($entry->cf7_entry_id == $firstEntryId) {
                        $iterations++;
                    ?>
                    <th><?php echo $entry->meta_key ?></th>
                    <?php }} ?>
                 </tr>
                </thead>
                <tbody>
                    <?php 
                    if(count($data['data'])){
                        $currentEntryId = null;
                        $iterations;
                        $entry_count = 0;
                        foreach ($data['data'] as $key => $value){
                            if ($entry_count == 0) {
                                echo '<tr>';
                            }
                            echo '<td>'.$value->meta_value.'</td>'; 
                            if ($entry_count == 4) {
                                echo '</tr>';
                            }
                            $entry_count++;
                            $entry_count = ($entry_count != $iterations) ? $entry_count++ : 0;
                        }
                    }else { ?>
                    <tr>
                        <td colspan="<?php echo $iterations ?>">
                            <p style="text-align:center">No entry found</p>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
	</div>
</div>
