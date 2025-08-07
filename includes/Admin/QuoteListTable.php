<?php
namespace BonzaQuote\Admin;

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class QuoteListTable extends \WP_List_Table {
    /**
     * Stores column headers for the table.
     *
     * @var array
     */
    protected $_column_headers;

    public function __construct() {
        parent::__construct( [
            'singular' => 'Quote',
            'plural'   => 'Quotes',
            'ajax'     => false,
        ] );
    }

    public function get_columns() {
        return [
            'cb' => '<input type="checkbox" />',
            'title' => __( 'Quote Title', 'bonza-quote' ),
            'email' => __( 'Email', 'bonza-quote' ),
            'service' => __( 'Service Type', 'bonza-quote' ),
            'status' => __( 'Status', 'bonza-quote' ),
            'date' => __( 'Date', 'bonza-quote' ),
        ];
    }

    public function column_default( $item, $column_name ) {
        return isset( $item->$column_name ) ? esc_html( $item->$column_name ) : '<em>N/A</em>';
    }


    public function get_sortable_columns() {
        return [
            'title' => [ 'title', true ],
            'date'  => [ 'date', false ],
        ];
    }

    protected function get_column_info() {
        if ( ! isset( $this->_column_headers ) ) {
            $this->_column_headers = [ $this->get_columns(), [], $this->get_sortable_columns() ];
        }

        return $this->_column_headers;
    }

    public function column_cb( $item ) {
        return sprintf( '<input type="checkbox" name="quote_ids[]" value="%s" />', $item->ID );
    }

    public function column_title( $item ) {
        $edit_link = get_edit_post_link( $item->ID );
        $actions = [];
        $actions['edit'] = sprintf(
            '<a href="%s">%s</a>',
            esc_url( get_edit_post_link( $item->ID ) ),
            __( 'Edit', 'bonza-quote' )
        );

        $actions['trash'] = sprintf(
            '<a href="%s">%s</a>',
            esc_url( get_delete_post_link( $item->ID ) ),
            __( 'Trash', 'bonza-quote' )
        );

        return sprintf(
            '<a href="%s">%s</a> %s',
            esc_url( get_edit_post_link( $item->ID ) ),
            esc_html( $item->post_title ),
            $this->row_actions( $actions )
        );

    }

    public function column_email( $item ) {
        $email = get_post_meta( $item->ID, 'bq_email', true );
        return $email ? esc_html( $email ) : '<em>No email</em>';
    }

    public function column_service( $item ) {
        $service = get_post_meta( $item->ID, 'bq_service', true );
        return $service ? esc_html( $service ) : '<em>No service type</em>';
    }

    public function column_status( $item ) {
        $map = [
            'publish' => 'Approved',
            'draft'   => 'Rejected',
            'pending' => 'Pending',
            'trash'   => 'Trash'
        ];

        $status = $item->post_status;

        return esc_html( $map[ $status ] ?? ucfirst( $status ) );
    }

    public function column_date( $item ) {
        return esc_html( get_the_date( '', $item ) );
    }

    public function prepare_items() {
        $per_page     = $this->get_items_per_page( 'quotes_per_page', 10 );
        $current_page = $this->get_pagenum();
        $status = isset( $_REQUEST['post_status'] ) ? sanitize_key( $_REQUEST['post_status'] ) : '';
        $search       = !empty( $_REQUEST['s'] ) ? sanitize_text_field( $_REQUEST['s'] ) : '';

        $valid_statuses = [ 'publish', 'draft', 'pending', 'trash' ];

        $args         = [
            'post_type'      => 'bonza_quote',
            'post_status'    => in_array( $status, $valid_statuses, true ) ? $status : $valid_statuses,
            'posts_per_page' => $per_page,
            'paged'          => $current_page,
        ];

        if ( $search ) {
            $args['s'] = $search;
        }

        if ( ! empty( $_REQUEST['orderby'] ) && in_array( $_REQUEST['orderby'], [ 'title', 'date' ], true ) ) {
            $args['orderby'] = $_REQUEST['orderby'];
        }

        if ( ! empty( $_REQUEST['order'] ) && in_array( $_REQUEST['order'], ['asc', 'desc' ], true ) ) {
            $args['order'] = $_REQUEST['order'];
        }

        $query = new \WP_Query( $args );

        $this->items = $query->posts;

        $this->set_pagination_args( [
            'total_items' => $query->found_posts,
            'per_page'    => $per_page,
            'total_pages' => $query->max_num_pages,
        ] );
    }

    public function get_views() {
        $views  = [];
        $counts = wp_count_posts( 'bonza_quote' );
        $current = $_REQUEST['post_status'] ?? 'all';

        $total = (
            ( $counts->publish ?? 0 ) +
            ( $counts->draft ?? 0 ) +
            ( $counts->pending ?? 0 ) +
            ( $counts->trash ?? 0 ) 
        );

        $class = $current === 'all' ? 'current' : '';
        $url = remove_query_arg( 'post_status', admin_url( 'admin.php?page=bonza-quotes') );

        $views['all'] = "<a href='" . esc_url( $url ) . "' class='$class'>All <span class='count'>($total)</span></a>";


        $statuses = [
            'publish'  => 'Approved',
            'draft'    => 'Rejected',
            'pending'  => 'Pending',
            'trash'    => 'Trash',
        ];


        foreach ( $statuses as $status => $label ) {
            $count = $counts->$status ?? 0;

            if ( $count > 0 ) {
                $class = $current === $status ? 'current' : '';
                $url   = add_query_arg( 'post_status', $status, admin_url( 'admin.php?page=bonza-quotes' ) );
                $views[$status] = "<a href='" . esc_url( $url ) . "' class='$class'>$label <span class='count'>($count)</span></a>";
            }
        }

        return $views;
    }

    public function get_bulk_actions() {
        return [
            'approve' => 'Approve',
            'reject'  => 'Reject',
            'trash'   => 'Move to Trash',
        ];
    }

    public function process_bulk_action() {
        if ( isset( $_POST['quote_ids'], $_POST['_wpnonce'] ) ) {

            if ( ! check_admin_referer( 'bulk-quotes' ) ) {
                return;
            }

            $action = $this->current_action();

            $ids    = array_map( 'absint', $_POST['quote_ids'] );

            foreach ( $ids as $id ) {
                if ( $action === 'approve' ) {
                    wp_update_post( [ 'ID' => $id, 'post_status' => 'publish' ] );
                } elseif ( $action === 'reject' ) {
                    wp_update_post( [ 'ID' => $id, 'post_status' => 'draft' ] );
                } elseif ( $action === 'trash' ) {
                    wp_trash_post( $id );
                }
            }

            wp_redirect( admin_url( 'admin.php?page=bonza-quotes' ) );
            exit;
        }
    }
}
