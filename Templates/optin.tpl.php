<h1>We're giving something away!</h1>
<h2>To enter to win, enter your name and email.</h2>
<form method="post" data-abide>
    <?= \Lightning\Tools\Form::renderTokenInput(); ?>
    <div>
        <label>Your Name:
            <input type="text" name="name" id='name' value="<?=\Lightning\View\Field::defaultValue('name');?>" required />
        </label>
        <small class="error">Please enter your name.</small>
    </div>
    <div>
        <label>Your Email:
            <input type="email" name="email" id='email' value="<?=\Lightning\View\Field::defaultValue('email');?>" required />
        </label>
        <small class="error">Please enter your email.</small>
    </div>
    <input type="hidden" name="action" value="register" />
    <input type="hidden" name="redirect" value="<?=!empty($redirect) ? $redirect : '';?>" />
    <input type="submit" name="submit" value="Register" class="button" />
</form>
