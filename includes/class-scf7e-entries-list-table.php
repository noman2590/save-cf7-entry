<?php

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class SCF7E_Entries_List_Table extends WP_List_Table {

    private $form_id;

    public function __construct($form_id) {
        $this->form_id = absint($form_id);

        parent::__construct([
            'singular' => 'entry',
            'plural'   => 'entries',
            'ajax'     => false
        ]);
    }

    public function get_columns() {
        $columns = [
            'created_at' => __('Date/Time', 'scf7e'),
        ];
    
        // Get dynamic field columns from cache (or fallback to DB scan)
        $meta_keys = get_transient("scf7e_meta_keys_{$this->form_id}");
        
        if (!$meta_keys || !is_array($meta_keys)) {
            $meta_keys = get_transient("scf7e_meta_keys_{$this->form_id}");
        }
    
        if ($meta_keys) {
            foreach ($meta_keys as $key) {
                $columns["meta_$key"] = SCF7EMainController::scf7e_convert_to_title_case($key);
            }
        }
    
        $columns['actions'] = __('Actions', 'scf7e');
        return $columns;
    }

    public function get_hidden_columns() {
        return get_hidden_columns($this->screen);
    }

    public function get_sortable_columns() {
        return [
            'created_at' => ['created_at', true]
        ];
    }

    public function column_default($item, $column_name) {
        if (strpos($column_name, 'meta_') === 0) {
            $key = substr($column_name, 5);
            return isset($item['meta'][$key]) ? esc_html($item['meta'][$key]) : '—';
        }
        return '';
    }

    public function column_created_at($item) {
        return esc_html($item['created_at']);
    }

    public function column_actions( $item ) {
        return '<button type="submit"
                       name="scf7e_delete"
                       value="' . esc_attr( $item['cf7_entry_id'] ) . '"
                       style="background:none;border:none;cursor:pointer;color:#a00;padding:0;"
                       onclick="return confirm(\'Delete this entry permanently?\');">
                    <span class="dashicons dashicons-trash" style="font-size:18px;"></span>
                </button>';
    }

    public function prepare_items() {
        global $wpdb;
        $table      = $wpdb->prefix . 'cf7_entries';      // main table
        $meta_table = $wpdb->prefix . 'cf7_entry_meta';   // meta table
        
        $per_page     = 20;
        $current_page = $this->get_pagenum();
        $offset       = ($current_page - 1) * $per_page;
    
        // Filters
        $from_date = !empty($_GET['from_date']) ? sanitize_text_field($_GET['from_date']) : '';
        $to_date   = !empty($_GET['to_date']) ? sanitize_text_field($_GET['to_date']) : '';
    
        $where = ['e.post_id = %d'];
        $params = [$this->form_id];
    
        if ($from_date) {
            $where[] = "DATE(e.created_at) >= %s";
            $params[] = $from_date;
        }
        if ($to_date) {
            $where[] = "DATE(e.created_at) <= %s";
            $params[] = $to_date;
        }
        $where_sql = 'WHERE ' . implode(' AND ', $where);
    
        // Count total
        $total_items = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table e $where_sql",
            $params
        ));
    
        // MAIN QUERY — JOIN: e.id → m.cf7_entry_id
        $sql = $wpdb->prepare("
            SELECT 
                e.id AS entry_id,
                e.created_at,
                GROUP_CONCAT(
                    CONCAT(m.meta_key, '||', COALESCE(m.meta_value, ''))
                    SEPARATOR '~~'
                ) AS meta_data
            FROM $table e
            LEFT JOIN $meta_table m ON e.id = m.cf7_entry_id
            $where_sql
            GROUP BY e.id, e.created_at
            ORDER BY e.created_at DESC
            LIMIT %d, %d
        ", array_merge($params, [$offset, $per_page]));
    
        $results = $wpdb->get_results($sql);
    
        // Transform results
        $items = [];
        $all_meta_keys = [];
    
        foreach ($results as $row) {
            $entry = [
                'cf7_entry_id' => $row->entry_id,
                'created_at'   => $row->created_at,
                'meta'         => []
            ];
    
            if ($row->meta_data) {
                foreach (explode('~~', $row->meta_data) as $pair) {
                    if (strpos($pair, '||') !== false) {
                        [$key, $value] = explode('||', $pair, 2);
                        $entry['meta'][$key] = $value;
                        $all_meta_keys[$key] = true;
                    }
                }
            }
    
            $items[] = $entry;
        }
    
        // Cache columns
        set_transient("scf7e_meta_keys_{$this->form_id}", array_keys($all_meta_keys), HOUR_IN_SECONDS);
    
        $this->items = $items;
    
        // Pagination
        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ]);
    
        $this->_column_headers = [$this->get_columns(), $this->get_hidden_columns(), $this->get_sortable_columns()];
    }

    protected function get_table_classes() {
        return ['widefat', 'fixed', 'striped', 'scf7e-entries-table'];
    }
}