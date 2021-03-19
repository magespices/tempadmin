/**
 * Created by Q-Solutions Studio
 *
 * @category    Magespices
 * @package     Magespices_TempAdmin
 * @author      Sebastian Strojwas <sebastian@qsolutionsstudio.com>
 */

define([
    'jquery',
    ], function($) {
        'use strict';

        return function() {
            jQuery(document).ready(function() {
                let tempAdminForm = jQuery('#temp_admin');

                tempAdminForm.on('submit', function (event) {
                    event.preventDefault();
                    if (tempAdminForm.valid()) {
                        submitTempAdminForm();
                    }
                });

                function submitTempAdminForm() {
                    let role_id = jQuery('#temp_admin select[name="role_id"]').val();
                    let duration = jQuery('#temp_admin select[name="duration"]').val();
                    let forKey = jQuery('#temp_admin input[name="form_key"]').val();
                    let submitButton = jQuery('#temp_admin button');
                    let jsMessage = jQuery('#temp_admin_notification .js-message');

                    submitButton.css('opacity', 0.5).attr('disabled', 'disabled');

                    jQuery.ajax({
                        url: '/temp_admin/index/create',
                        data: {
                            'role_id': role_id,
                            'duration': duration,
                            'form_key':forKey,
                        },
                        success: function (response) {
                            response = JSON.parse(response);
                            if (response.status && response.message) {
                                jsMessage.html(response.message);
                                tempAdminForm.remove();
                            } else if (response.message) {
                                jsMessage.html(response.message);
                                submitButton.css('opacity', 1).removeAttr('disabled');
                            } else {
                                submitButton.css('opacity', 1).removeAttr('disabled');
                            }
                        }
                    });
                }

            });
        }
    }
);
