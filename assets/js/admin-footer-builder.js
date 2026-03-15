/**
 * Zendotech Footer Builder — Admin JS
 * Tab switching, repeater fields, icon preview
 */
(function ($) {
    'use strict';

    $(document).ready(function () {

        /* ==========================================
           1. TAB SWITCHING
           ========================================== */
        $('.zfb-tab').on('click', function () {
            var tabId = $(this).data('tab');
            $('.zfb-tab').removeClass('active');
            $(this).addClass('active');
            $('.zfb-panel').removeClass('active');
            $('#tab-' + tabId).addClass('active');

            // Sync with hidden input for persistence after save
            $('#zfb_active_tab').val(tabId);
        });

        /* ==========================================
           2. COLUMN SOURCE TOGGLE
           ========================================== */
        $('.zfb-source-select').on('change', function () {
            var col = $(this).data('col');
            var val = $(this).val();

            // Hide all source fields for this column
            $('.zfb-source-menu-' + col + ', .zfb-source-custom-' + col).hide();

            // Show the matching one
            if (val === 'menu') {
                $('.zfb-source-menu-' + col).show();
            } else if (val === 'custom') {
                $('.zfb-source-custom-' + col).show();
            }
        });

        /* ==========================================
           3. ADD LINK (Custom columns)
           ========================================== */
        $('.zfb-add-link').on('click', function () {
            var col = $(this).data('col');
            var row = $(
                '<div class="zfb-repeater-row">' +
                '<input type="text" name="col' + col + '_links[text][]" value="" placeholder="Link Text" />' +
                '<input type="url" name="col' + col + '_links[url][]" value="" placeholder="URL" />' +
                '<button type="button" class="zfb-remove-row" title="Remove"><i class="dashicons dashicons-no-alt"></i></button>' +
                '</div>'
            );
            $(this).siblings('.zfb-repeater').append(row);
        });

        /* ==========================================
           4. ADD SOCIAL LINK
           ========================================== */
        $('.zfb-add-social').on('click', function () {
            var row = $(
                '<div class="zfb-repeater-row">' +
                '<input type="text" name="social_icon[]" value="" placeholder="fa-brands fa-instagram" />' +
                '<input type="url" name="social_url[]" value="" placeholder="https://..." />' +
                '<span class="zfb-icon-preview"><i class=""></i></span>' +
                '<button type="button" class="zfb-remove-row" title="Remove"><i class="dashicons dashicons-no-alt"></i></button>' +
                '</div>'
            );
            $('#social-repeater').append(row);
        });

        /* ==========================================
           5. ADD PAYMENT ICON
           ========================================== */
        $('.zfb-add-payment').on('click', function () {
            var row = $(
                '<div class="zfb-repeater-row">' +
                '<input type="text" name="payment_icons[]" value="" placeholder="fa-brands fa-cc-visa" />' +
                '<span class="zfb-icon-preview"><i class=""></i></span>' +
                '<button type="button" class="zfb-remove-row" title="Remove"><i class="dashicons dashicons-no-alt"></i></button>' +
                '</div>'
            );
            $('#payment-repeater').append(row);
        });

        /* ==========================================
           6. ADD BOTTOM LINK
           ========================================== */
        $('.zfb-add-bottom-link').on('click', function () {
            var row = $(
                '<div class="zfb-repeater-row">' +
                '<input type="text" name="bottom_links[text][]" value="" placeholder="Link Text" />' +
                '<input type="url" name="bottom_links[url][]" value="" placeholder="URL" />' +
                '<button type="button" class="zfb-remove-row" title="Remove"><i class="dashicons dashicons-no-alt"></i></button>' +
                '</div>'
            );
            $('#bottom-links-repeater').append(row);
        });

        /* ==========================================
           6.5 ADD HEADER LINK (Manual)
           ========================================== */
        $('.zfb-add-row-manual').on('click', function () {
            var row = $(
                '<div class="zfb-repeater-row">' +
                '<input type="text" name="topbar_link_icon[]" value="" placeholder="Icon (fa-solid fa-truck)" />' +
                '<input type="text" name="topbar_link_text[]" value="" placeholder="Text" />' +
                '<input type="text" name="topbar_link_url[]" value="" placeholder="URL" />' +
                '<button type="button" class="zfb-remove-row"><i class="dashicons dashicons-no-alt"></i></button>' +
                '</div>'
            );
            $(this).siblings('.zfb-repeater').append(row);
        });

        /* ==========================================
           7. REMOVE ROW
           ========================================== */
        $(document).on('click', '.zfb-remove-row', function () {
            $(this).closest('.zfb-repeater-row').slideUp(200, function () {
                $(this).remove();
            });
        });

        /* ==========================================
           8. LIVE ICON PREVIEW
           ========================================== */
        $(document).on('input', '.zfb-repeater-row input[name*="social_icon"], .zfb-repeater-row input[name*="payment_icon"]', function () {
            var iconClass = $(this).val().trim();
            var preview = $(this).closest('.zfb-repeater-row').find('.zfb-icon-preview i');
            if (preview.length) {
                preview.attr('class', iconClass);
            }
        });

        /* ==========================================
           9. RESET TO DEFAULTS
           ========================================== */
        // Handled inline via onclick in the PHP

    });

})(jQuery);
