

<div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars($this->getSitePublic()); ?>"></div>
<script type="text/javascript"
        src="https://www.google.com/recaptcha/api.js?hl=<?php echo htmlspecialchars($this->getLang(), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8'); ?>">
</script>