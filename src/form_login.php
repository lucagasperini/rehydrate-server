<body>
        <div class="main-login">
                <h1 style="text-align:center">
                        <?php echo _('header_login'); ?>
                </h1>
                <input name="u" class="input-login" id="login_user" placeholder="<?php echo _('login_user'); ?>" />
                <input name="p" class="input-login" id="login_pass" type="password"
                        placeholder="<?php echo _('login_pass'); ?>" />
                <button id="login_button" onclick="auth_web()">
                        <?php echo _('login_button'); ?>
                </button>
        </div>
</body>