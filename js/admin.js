jQuery(document).ready(function() {

    showCurrentSection();

    const checkbox = document.getElementById('payment_setting_test_mode')

    checkbox.addEventListener('change', (event) => {
        if (event.currentTarget.checked) {
            setTestData();
        } else {
            clearTestData();
        }
    })

    document.getElementById('payment_setting_type')
        .addEventListener("change", showCurrentSection);

    function showCurrentSection() {
        let selected_value = jQuery('#payment_setting_type option:selected').val();

        let current_section = '';
        switch (selected_value) {
            case 'ЕРИП':
                jQuery('#erip_setting').show(400);
                current_section = 'erip_setting';
                break;
            case 'Интернет-эквайринг':
                jQuery('#successMessageContainer').hide(400);
                break;
            case 'E-POS':
                jQuery('#epos_setting').show(400);
                jQuery('#erip_setting').show(400);
                jQuery('#showQrCodeContainer').hide(400);
                jQuery('#successMessageContainer').hide(400);
                current_section = 'epos_setting';
                break;

        }

        jQuery('.other_setting').each(function() {


            if (current_section == 'epos_setting' && jQuery(this).attr('id') == 'erip_setting') {
                return;
            } else if (jQuery(this).attr('id') == current_section)
                return;
            jQuery(this).hide(400);
        });


    }

    function setTestData() {
        jQuery('#payment_setting_token').val("a75b74cbcfe446509e8ee874f421bd67");
        jQuery('#payment_setting_service_id').val("5");
    }

    function clearTestData() {
        jQuery('#payment_setting_token').val("");
        jQuery('#payment_setting_service_id').val("");
    }

});