<?php if ( ! defined( 'ABSPATH' ) ) exit; 
extract( $data ); // $list_table and $form_id are available
?>

<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php _e( 'Form Entries', 'scf7e' ); ?>
    </h1>

    <!-- Back to Forms Button -->
    <a href="<?php echo esc_url( SCF7EMainController::scf7e_get_entries_url( 'manage-cf7-entries' ) ); ?>" 
       class="page-title-action">
        <?php _e( 'Back to Forms', 'scf7e' ); ?>
    </a>

    <hr class="wp-header-end">

    <!-- SUCCESS MESSAGE — 100% reliable via transient -->
    <?php if ( get_transient( 'scf7e_entry_deleted' ) ): 
        delete_transient( 'scf7e_entry_deleted' ); ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e( 'Entry deleted successfully.', 'scf7e' ); ?></p>
        </div>
    <?php endif; ?>

    <!-- DATE FILTER FORM (GET) — separate so it doesn't interfere with delete -->
    <form method="get" id="scf7e-date-filter" class="tablenav top">
        <input type="hidden" name="page" value="form-entries">
        <input type="hidden" name="form" value="<?php echo esc_attr( $form_id ); ?>">

        <div class="alignleft actions">
            <label for="from_date"><?php _e( 'From Date' ); ?></label>
            <input type="date" name="from_date" id="from_date" 
                   value="<?php echo esc_attr( $_GET['from_date'] ?? '' ); ?>">

            <label for="to_date"><?php _e( 'To Date' ); ?></label>
            <input type="date" name="to_date" id="to_date" 
                   value="<?php echo esc_attr( $_GET['to_date'] ?? '' ); ?>">

            <?php submit_button( __( 'Filter', 'scf7e' ), 'button', false, false ); ?>

            <?php if ( ! empty( $_GET['from_date'] ) || ! empty( $_GET['to_date'] ) ): ?>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=form-entries&form=' . $form_id ) ); ?>" 
                   class="button">
                    <?php _e( 'Clear Filters', 'scf7e' ); ?>
                </a>
            <?php endif; ?>
        </div>
        <br class="clear">
    </form>

    <!-- MAIN DELETE FORM — submits to admin-post.php -->
    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <input type="hidden" name="action" value="scf7e_delete_entry">
        <input type="hidden" name="form" value="<?php echo esc_attr( $form_id ); ?>">
        <?php wp_nonce_field( 'scf7e_delete_entry', 'scf7e_delete_nonce' ); ?>

        <!-- Preserve context after delete -->
        <input type="hidden" name="form_id"   value="<?php echo esc_attr( $form_id ); ?>">
        <input type="hidden" name="from_date" value="<?php echo esc_attr( $_GET['from_date'] ?? '' ); ?>">
        <input type="hidden" name="to_date"   value="<?php echo esc_attr( $_GET['to_date'] ?? '' ); ?>">
        <input type="hidden" name="paged"     value="<?php echo esc_attr( $list_table->get_pagenum() ); ?>">

        <?php $list_table->display(); ?>
    </form>
</div>

<!-- Optional: nice trash icon hover -->
<style>
    .column-actions button { 
        background: none; 
        border: none; 
        cursor: pointer; 
        color: #a00; 
        padding: 0; 
    }
    .column-actions button:hover { opacity: 0.7; }
    .column-actions .dashicons-trash { font-size: 18px; }
</style>