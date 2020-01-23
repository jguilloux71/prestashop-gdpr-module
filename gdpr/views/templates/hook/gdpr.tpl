<!--
  In BODY tag
-->




<!-- Block gdpr module -->

<div id="mymodule_block_left" class="block">
</div>

<!--
  cookieconsent: method in views/js/cookies-conent.js
-->

<script>
    window.cookieconsent.initialise({
        "palette": {
            "popup": {
            "background": "{$gdpr_cookies_palette_background_color}"
        },
        "button": {
            "background": "{$gdpr_cookies_button_background_color}"
        }
    },
    "content": {
        "dismiss": "{l s='Accept' mod='gdpr'}",
        "message": "{$gdpr_cookies_consent_text}",
        "link": "{l s='Learn more' mod='gdpr'}",
        "href": "{$gdpr_privacy_data_link}"
    }
});
</script>

<!-- /Block gdpr module -->
