<?php
namespace BonzaQuote\Frontend;

class QuoteForm {

    public function init() {
        add_shortcode( 'bonza_quote_form', [ $this, 'render_form' ] );
        add_action( 'init', [ $this, 'handle_submission' ] );
    }

    public function render_form() {
        ob_start();

        // Flash message
        if ( isset( $_GET['bq_submitted'] ) && $_GET['bq_submitted'] === '1' ) {

            ?>

                <div class="bonza-quote-flash" style="
                    position:relative;
                    padding:10px 15px;
                    margin-bottom:15px;
                    background:#dff0d8;
                    color:#3c763d;
                    border-radius:4px;
                    transition:opacity 0.5s ease-out;
                ">
                    Thank you! Your quote has been submitted.
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const flash = document.querySelector('.bonza-quote-flash');
                        if (flash) {
                            setTimeout(() => {
                                flash.style.opacity = '0';
                                setTimeout(() => flash.remove(), 500);
                            }, 3000);
                        }
                    });
                </script>

            <?php
        }

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

            // Send email
            $mailer = new QuoteMailer();
            $mailer->send_admin_notification( $name, $email, $service, $notes );

            // Redirect with flash flag
            wp_safe_redirect( add_query_arg( 'bq_submitted', '1', wp_get_referer() ) );
            exit;
        }
    }
}
