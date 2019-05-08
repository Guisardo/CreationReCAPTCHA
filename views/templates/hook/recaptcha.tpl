{*
 * @author    Mirko Laruina
 * @copyright 2019 Mirko Laruina
 * @license   LICENSE.txt
 *}

{if $creationrecaptcha_subdisable}
<script>
    var unlockSubmit = function(){
        submitAccount.removeAttribute('disabled');
    }
    window.unlockSubmit = unlockSubmit;

    var lockSubmit = function(){
        var submitAccount = document.getElementById("submitAccount");
        submitAccount.setAttribute('disabled', true);
    }
    lockSubmit();
</script>
{/if}
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<div class="account_creation">
    <div class="form-group">
        <label>{$creationrecaptcha_label|escape:'htmlall':'UTF-8'}</label>
        <div class="g-recaptcha" {if $creationrecaptcha_subdisable}data-callback="unlockSubmit"{/if} data-sitekey='{$creationrecaptcha_key|escape:'htmlall':'UTF-8'}'></div>
    </div>
</div>  