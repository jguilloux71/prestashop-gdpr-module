<!--
  In BODY tag
-->




<!-- Block gdpr module -->

<div id="mymodule_block_left" class="block">
</div>

<!--
  cookieconsent: method in views/js/cookies-content.js
-->

<script>
    window.addEventListener(
        "load",
        function() {
            window.wpcc.init({
                "border": "thin",
                "colors": {
                    "popup": {
                        "background": "{$gdpr_cookies_popup_background_color}",
                        "text": "{$gdpr_cookies_popup_text_color}",
                        "border": "{$gdpr_cookies_popup_background_color}"
                    },
                    "button": {
                        "background": "{$gdpr_cookies_button_background_color}",
                        "text": "{$gdpr_cookies_button_text_color}"
                    }
                },
                "position": "bottom",
                "cookie": {
                    expiryDays: {$gdpr_cookies_delay_before_new_popup}
                },
                "content": {
                    "message": "{$gdpr_cookies_consent_text}",
                    "link": "{l s='Learn more' mod='gdpr'}",
                    "href": "{$gdpr_privacy_data_link}",
                    "button": "{l s='Accept' mod='gdpr'}"
                },
                "corners": "large",
                "transparency": "{$gdpr_cookies_popup_background_transparency}"
            })
        }
    );
</script>

<!-- /Block gdpr module -->
