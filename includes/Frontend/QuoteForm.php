<?php
namespace BonzaQuote\Frontend;

/**
 * Class QuoteForm
 *
 * Handles the frontend quote form functionality for the Bonza Quote Management plugin.
 * Registers a shortcode to display the form and processes submissions to create custom quote posts.
 */
class QuoteForm {

    /**
     * Initializes the shortcode and submission handler.
     */
    public function init() {
        add_shortcode( 'bonza_quote_form', [ $this, 'render_form' ] );
        add_action( 'init', [ $this, 'handle_submission' ] );
    }

    /**
     * Renders the quote submission form via shortcode.
     *
     * @return string
     */
    public function render_form() {
        ob_start();
        ?>
       <form method="post">
            <p>
                <label for="bq_name">Name <span aria-hidden="true">*</span></label>
                <input type="text" id="bq_name" name="bq_name" required />
            </p>

            <p>
                <label for="bq_email">Email <span aria-hidden="true">*</span></label>
                <input type="email" id="bq_email" name="bq_email" required />
            </p>

            <p>
                <label for="bq_service">Service Type <span aria-hidden="true">*</span></label>
                <input type="text" id="bq_service" name="bq_service" required />
            </p>

            <p>
                <label for="bq_notes">Notes</label>
                <textarea id="bq_notes" name="bq_notes"></textarea>
            </p>

            <input type="hidden" name="bq_nonce" value="<?php echo esc_attr( wp_create_nonce( 'bq_form' ) ); ?>" />

            <p>
                <button type="submit" name="bq_submit">Submit Quote</button>
            </p>
        </form>

        <?php
        return ob_get_clean();
    }

    /**
     * Handles form submission, sanitizes input, and inserts a new quote post.
     * Displays a JavaScript alert upon successful submission.
     *
     * @return void
     */
    public function handle_submission() {
        if (
            isset( $_POST['bq_submit'], $_POST['bq_nonce'] ) &&
            wp_verify_nonce( $_POST['bq_nonce'], 'bq_form' )
        ) {
            $name    = sanitize_text_field( $_POST['bq_name'] );
            $email   = sanitize_email( $_POST['bq_email'] );
            $service = sanitize_text_field( $_POST['bq_service'] );
            $notes   = sanitize_textarea_field( $_POST['bq_notes'] );

            wp_insert_post( [
                'post_type'    => 'bonza_quote',
                'post_status'  => 'pending',
                'post_title'   => $name,
                'post_content' => $notes,
                'meta_input'   => [
                    'bq_email'   => $email,
                    'bq_service' => $service,
                ],
            ] );

            add_action( 'wp_footer', function () {
                echo "<script>alert('Your quote has been submitted.');</script>";
            } );
        }
    }
}
