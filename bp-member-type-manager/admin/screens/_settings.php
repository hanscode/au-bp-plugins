<?php
global $bmtm;
$bmtm_options = $bmtm->get_options();

?>
<div class="wrap bmtm-wrap" style="background: #fff; padding: 20px">



    <div id="ajax-response"></div>

    <br class="clear">

    <div id="col-container">

        <div id="col-right" style="float: left">



        <div class="col-wrap">
            <h2><?php _e( 'Member Type Manager Settings', 'bmtm' ); ?> </h2>

            <div class="form-wrap">
                <form method="post">
                    <input type="hidden" name="bmtm_action" value="bmtm_save_member_type_settings" />
                    <input type="hidden" name="screen" value="<?php echo $_REQUEST['page'] ?>">

                    <?php wp_nonce_field( 'bmtm_save_member_type_settings' ) ?>


                    <div class="form-field form-required member-type-separate-registration-page">
                        <label for="member-type-separate-registration-page">Enable Separate Registration Page?</label>
                        <select name="member-type-separate-registration-page" id="member-type-separate-registration-page" aria-required="true">
                            <option value="1" <?php echo !empty($bmtm_options['settings']["separate_registration"]) ? ' selected ' : '' ?> >Enabled</option>
                            <option value="0" <?php echo empty($bmtm_options['settings']["separate_registration"]) ? ' selected ' : '' ?>>Disabled</option>
                        </select>
                        <p><br/></p>
                        <p>Enable separate registration page for each member type?<br>
                            If Enabled, Separate registration link will be created for each member type.<br>
                            BuddyPress default register slug will be overwritten by default member type <br>
                            <b>e.g. www.example.com/register/student</b>
                        </p>
                    </div>


                    <div class="form-field form-required member-type-user-role-sync">
                        <label for="member-type-directory">Synchronize User Role with Member Types?</label>
                        <select name="member-type-user-role-sync" id="member-type-user-role-sync" aria-required="false">
                            <option value="1" <?php echo !empty($bmtm_options['settings']["sync_user_role"]) ? ' selected ' : '' ?> >Enabled</option>
                            <option value="0" <?php echo empty($bmtm_options['settings']["sync_user_role"]) ? ' selected ' : '' ?>>Disabled</option>
                        </select>
                        <p><br/></p>
                        <p>
                            If Enabled, Associated User role for each member type will work as alice.<br>
                            If Member type is changed, Role will be change too.<br>
                            WordPress default user role will be overridden on new registration based on <br>
                            new registered user member type and associated role of that member type .<br>

                        </p>
                    </div>
<?php /*

                    <div class="form-field form-required member-type-enable-directory-tab">
                        <label for="member-type-directory">Add member type tabs in member directory?</label>
                        <select name="member-type-enable-directory-tab" id="member-type-enable-directory-tab" aria-required="false">
                            <option value="1" <?php echo !empty($bmtm_options['settings']["enable-directory-tab"]) ? ' selected ' : '' ?> >Enabled</option>
                            <option value="0" <?php echo empty($bmtm_options['settings']["enable-directory-tab"]) ? ' selected ' : '' ?>>Disabled</option>
                        </select>
                        <p><br/></p>
                        <p>
                            If Enabled, In BuddyPress member directory page, separate tabs will be added for each member type<br>
                            Separate member directory page will be created and linked at tabs for each member type.<br>

                        </p>
                    </div>

*/ ?>

                    <p class="submit">
                        <input type="submit" name="submit" id="submit" class="button button-primary" value="Update Settings">
                    </p>

                </form>
            </div>



        </div>

        </div>

        <div id="col-left" style="float: right;  border-left: 1px dashed #bbb; margin-left: -40px; padding-left: 30px; height: 100%; text-align: center;">



            <div class="col-wrap">
                <h2><?php _e( 'Donate to Support The Plugin (paypal)', 'bmtm' ); ?> </h2>
                <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                    <input type="hidden" name="cmd" value="_xclick">
                    <input type="hidden" name="business" value="rimon989@gmail.com">
                    <input type="hidden" name="lc" value="BH">
                    <input type="hidden" name="item_name" value="BuddyPress Member Type Manager">
                    <input type="hidden" name="button_subtype" value="services">
                    <input type="hidden" name="no_note" value="0">
                    <input type="hidden" name="currency_code" value="USD">
                    <input type="hidden" name="bn" value="PP-BuyNowBF:botton6-128.png:NonHostedGuest">
                    <input type="hidden" name="on0" value="Donate to support">

                    <select name="os0">
                                    <option value="Donate">Donate $5.00 USD</option>
                                    <option value="Donate">Donate $10.00 USD</option>
                                    <option value="Donate">Donate $15.00 USD</option>
                    </select>
                    <input type="hidden" name="currency_code" value="USD">
                    <input type="hidden" name="option_select0" value="Donate">
                    <input type="hidden" name="option_amount0" value="5.00">
                    <input type="hidden" name="option_select1" value="Donate">
                    <input type="hidden" name="option_amount1" value="10.00">
                    <input type="hidden" name="option_select2" value="Donate">
                    <input type="hidden" name="option_amount2" value="15.00">
                    <input type="hidden" name="option_index" value="0">
                    <br>
                    <input type="image" src="<?php echo BMTM_IMAGE_URL ?>donate-btn.png" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                    <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                </form>

            </div>
        </div>

    </div>


</div>