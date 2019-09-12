

<div class="g-recaptcha" data-sitekey="<?php echo filter_var($this->getSitePublic(), FILTER_SANITIZE_STRING); ?>"></div>
<script type="text/javascript"
        src="https://www.google.com/recaptcha/api.js?hl=<?php echo filter_var($this->getLang(), FILTER_SANITIZE_STRING); ?>">
</script>