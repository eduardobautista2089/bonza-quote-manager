<?php
namespace BonzaQuote;

use BonzaQuote\Admin\QuoteAdmin;
use BonzaQuote\Frontend\QuoteForm;
use BonzaQuote\PostTypes\QuotePostType;

class Plugin {
    public function run() {
        ( new QuotePostType() )->register();
        ( new QuoteForm() )->init();
        ( new QuoteAdmin() )->init();
    }
}