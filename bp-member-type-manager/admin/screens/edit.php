<?php 
global $bmtm;
$bmtm_options = bmtm_get_options();  
$member_type = $_REQUEST['id'];
?>
<div class="wrap bmtm-wrap">
        <h2><?php _e( 'Edit Member Types', 'bmtm' ); ?> </h2>
        

    <div id="ajax-response"></div>

    <br class="clear">

    <div id="col-container">

            <div class="col-wrap">

                <div class="form-wrap">
                    <form method="post">
                       <input type="hidden" name="bmtm_action" value="bmtm_edit_member_type" />
                       <input type="hidden" name="screen" value="<?php echo $_REQUEST['page'] ?>">
                       <input type="hidden" name="member-type-slug" value="<?php echo $_REQUEST['id'] ?>">
                       
                       <?php wp_nonce_field( 'bmtm_save_member_type' ) ?>


                        <div class="form-field form-required member-type-name-wrap">
                            <label for="member-type-name">Member Type Name</label>
                            <input name="member-type-name" id="member-type-name" type="text" value="<?php echo  esc_attr( $bmtm_options['types'][$member_type]["name"] ) ?>" size="40" aria-required="true">
                            <p>The name is how it appears on your site. e.g. "Student"</p>
                        </div>


                        <div class="form-field form-required member-type-name-plural-wrap">
                            <label for="member-type-name-plural">Member Type Plural Name</label>
                            <input name="member-type-name-plural" id="member-type-name-plural" type="text" value="<?php echo esc_attr( $bmtm_options['types'][$member_type]["plural_name"] ) ?>" size="40" aria-required="true">
                            <p>Plural name for member type e.g. "Students" for member type "Student"</p>
                        </div>


                        <div class="form-field form-required member-type-directory-wrap">
                            <label for="member-type-directory">Enable Directory?</label>
                            <select name="member-type-enable-directory" id="member-type-enable-directory" aria-required="true">
                                <option value="1" <?php echo !empty($bmtm_options['types'][$member_type]["has_directory"]) ? ' selected ' : '' ?> >Enabled</option>
                                <option value="0" <?php echo empty($bmtm_options['types'][$member_type]["has_directory"]) ? ' selected ' : '' ?>>Disabled</option>
                            </select>
                            <p><br/></p>
                            <p>Enable directory listing for this member type?
                                If Enabled, a tab will be added in member directory page to filter this member type.
                            </p>
                        </div>


                        <div class="form-field form-required member-type-user-role">
                            <label for="member-type-directory">Assign User Role?</label>
                            <select name="member-type-user-role" id="member-type-user-role" aria-required="false">
                                <?php wp_dropdown_roles( $bmtm_options['types'][$member_type]["user_role"] ) ?>
                            </select>
                            <p><br/></p>
                            <p>Assign WP User Role for this member type?
                                If assigned, All users of this member type will be assigned to selected user role.
                            </p>
                        </div>


                        <div class="form-field form-required member-type-make-default-wrap">
                            <label for="member-type-make-default">Mark as default type?</label>
                            <input name="member-type-make-default" id="member-type-make-default" type="checkbox" value="1" <?php echo !empty($bmtm_options['types'][$member_type]["is_default"]) ? ' checked ' : '' ?> ><span> Make it default</span>
                            <p>Default type will be used for unassigned users on registration.
                            </p>
                        </div>

                        <p class="submit">
                            <input type="submit" name="submit" id="submit" class="button button-primary" value="Update Member Type">
                        </p>

                    </form>
                </div>

            </div>


    </div>


</div>