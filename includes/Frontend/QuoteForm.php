<?php
namespace BonzaQuote\Frontend;

/**
 * Handles rendering and processing of the frontend quote submission form.
 */
class QuoteForm {

    /**
     * Initialize hooks and shortcodes.
     *
     * @return void
     */
    public function init() {
        add_shortcode( 'bonza_quote_form', [ $this, 'render_form' ] );
        add_action( 'init', [ $this, 'handle_submission' ] );
    }

    /**
     * Render the quote submission form.
     *
     * Includes accessible markup, validation attributes,
     * and nonce protection for security.
     *
     * @return string HTML output for the form.
     */
    public function render_form() {
        ob_start();
        ?>
        <form method="post" role="form" aria-labelledby="bq_form_title" novalidate>
            <h2 id="bq_form_title" class="screen-reader-text">
                <?php esc_html_e( 'Quote Request Form', 'bonza-quote' ); ?>
            </h2>

            <p>
                <label for="bq_name">
                    <?php esc_html_e( 'Name', 'bonza-quote' ); ?>
                    <span class="required" aria-hidden="true">*</span>
                </label>
                <input type="text" id="bq_name" name="bq_name" required aria-required="true" />
            </p>

            <p>
                <label for="bq_email">
                    <?php esc_html_e( 'Email', 'bonza-quote' ); ?>
                    <span class="required" aria-hidden="true">*</span>
                </label>
                <input type="email" id="bq_email" name="bq_email" required aria-required="true" />
            </p>

            <p>
                <label for="bq_service">
                    <?php esc_html_e( 'Service Type', 'bonza-quote' ); ?>
                    <span class="required" aria-hidden="true">*</span>
                </label>
                <input type="text" id="bq_service" name="bq_service" required aria-required="true" />
            </p>

            <p>
                <label for="bq_notes">
                    <?php esc_html_e( 'Notes', 'bonza-quote' ); ?>
                </label>
                <textarea id="bq_notes" name="bq_notes" aria-describedby="bq_notes_desc"></textarea>
                <span id="bq_notes_desc" class="screen-reader-text">
                    <?php esc_html_e( 'Optional: Provide additional details about your request.', 'bonza-quote' ); ?>
                </span>
            </p>

            <?php wp_nonce_field( 'bq_form', 'bq_nonce' ); ?>

            <p>
                <button type="submit" name="bq_submit">
                    <?php esc_html_e( 'Submit Quote', 'bonza-quote' ); ?>
                </button>
            </p>
        </form>
        <?php
        return ob_get_clean();
    }

    /**
     * Handle form submission, validate inputs, save the quote, and send notification.
     *
     * @return void
     */
    public function handle_submission() {
        if (
            isset( $_POST['bq_submit'], $_POST['bq_nonce'] ) &&
            wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bq_nonce'] ) ), 'bq_form' )
        ) {
            $name    = sanitize_text_field( wp_unslash( $_POST['bq_name'] ?? '' ) );
            $email   = sanitize_email( wp_unslash( $_POST['bq_email'] ?? '' ) );
            $service = sanitize_text_field( wp_unslash( $_POST['bq_service'] ?? '' ) );
            $notes   = sanitize_textarea_field( wp_unslash( $_POST['bq_notes'] ?? '' ) );

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

            // Send admin notification email.
            $mailer = new QuoteMailer();
            $mailer->send_admin_notification( $name, $email, $service, $notes );

            // Redirect with success flag for flash message display.
            wp_safe_redirect( add_query_arg( 'bq_submitted', '1', wp_get_referer() ) );
            exit;
        }
    }
}
