<?php
namespace BonzaQuote\Frontend;

class QuoteForm {
    public function init() {
        add_shortcode( 'bonza_quote_form', [ $this, 'render_form' ] );
        add_action('init', [ $this, 'handle_submission' ] );
    }

    public function render_form() {
        ob_start();
        ?>

        <form method="post">
            <input type="text" name="bq_name" placeholder="name" required />
            <input type="email" name="bq_email" placeholder="Email" required />
            <input type="text" name="bq_service" placeholder="Service Type" />
            <textarea name="bq_notes" placeholder="Notes"></textarea>
            <input type="hidden" name="bq_nonce" value="<?php echo wp_create_nonce('bq_form'); ?>" />
            <button type="submit" name="bq_submit">Submit Quote</button>
        </form>

        <?php
        return ob_get_clean();
    }

    public function handle_submission() {
        if ( isset( $_POST['bq_submit'] ) && wp_verify_nonce( $_POST['bq_nonce'], 'bq_form' ) ) {
            $name = sanitize_text_field( $_POST['bq_name'] );
            $email = sanitize_email( $_POST['bq_email'] );
            $service = sanitize_text_field( $_POST['bq_service'] );
            $notes = sanitize_textarea_field( $_POST['bq_notes'] );

            wp_insert_post( [
                'post_type' => 'bonza_qoute',
                'post_status' => 'pending',
                'post_title' => $name,
                'post_content' => $notes,
                'meta_input' => [
                    'bq_email' => $email,
                    'bq_service' => $service,
                ]
            ] );

            add_action( 'wp_footer', function () {
                echo "<script>alert('Your quote has been submitted.');</script>";
            } );
        }
    }
}