<?php if (!isset($_COOKIE['cookie_accepted'])) { ?>
<script type="text/javascript">
  function setCookie(name, value, days) {
    let expires = "";
    if (days) {
      const date = new Date();
      date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
      expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (encodeURIComponent(value) || "") + expires + "; path=/";
  }

  $(function() {
    const $button = $('#setCookieAndHideButton');

    $button.on('click', function() {
      const $container = $('#cookieContainer');
      setCookie("cookie_accepted", "true", 365);
      $container.fadeOut(500);
    });
  });
</script>


<div class="cookie-banner" id="cookieContainer">
  <div class="cookie-banner-post">
    <div class="cookie-banner-text">
      <span>
        This site uses cookies to identify your user session for better user experience.
        <br>
        By continuing to browse, you agree the use of cookies according to our <a data-fancybox data-type="ajax" data-src="/ajax/privacy.php" href="javascript:;">privacy policy</a>.
      </span>
    </div>
    <button class="cookie-banner-button" id="setCookieAndHideButton">Ok</button>
  </div>
</div>
<?php } ?>
