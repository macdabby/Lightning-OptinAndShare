test

<?= \Lightning\Tools\SocialDrivers\Facebook::renderShare('', ['callback' => 'OptinAndShare.complete']); ?>
<span onclick="
    lightning.social.facebook.init();
FB.ui({
  method: 'share',
  href: 'http://420dude.com/pipe-giveaway',
}, function(response){
console.log(response);
});
">Test</span>
<form id="shared" method="post">
    <?= \Lightning\Tools\Form::renderTokenInput(); ?>
    <input type="hidden" name="action" value="shared">
    <input type="hidden" name="t" value="<?= $token; ?>">
</form>
