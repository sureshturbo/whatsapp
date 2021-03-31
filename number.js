/*
International Phone Number Handling
By: Alex Sperellis

Requires:
intl-tel-input: https://github.com/jackocnr/intl-tel-input
formatter.js:  https://github.com/firstopinion/formatter.js

Description:
Dropdown and Telephone input to handle international phone number entry. Default option is US but can be changed. Uses formatter js to add spacing, dashes and parentheses if needed while the user types. On selection of country in the dropdown the gray dial code box next to the telephone input is updated with the selected countries dial code. The formatter pattern is also updated on change. Validation is through intl-tel-inputs built in validator from googles libphonenumber. This is fully responsive and mobile optimized.

Test Numbers:
UK: +44 07400 123456 
JP: +81 070-1234-5678
DE: +49 01512 3456789
FR: +33 06 12 34 56 78
*/

const intlPhoneNumber = function(countryCode) {
  // get the country data from the plugin
  const countryData = $.fn.intlTelInput.getCountryData();
  const telInput = $("#phone-number");
  const telInputLabel = telInput.parents(".form-group").find("label");
  const countryDropdown = $("#phone-number-country");
  const phonePrefix = $('.phone-number-prefix');
  const fullPhoneNumber = $('#phone-number-full');
  const errorMsg = $("#error-msg");
  const initCountry = countryCode || 'us';
  let pattern = '';
  
  //set initial pattern for formatting
  if (initCountry === 'us') {
    pattern = '({{999}}) {{999}}-{{9999}}';
  } else {
    // using as temp until formatting on init figured out
    pattern = '{{9999999999999999999999}}'; 
  }
  
  // reset function to reset error state on validation
  const reset = function() {
    telInput.attr("placeholder", "PHONE NUMBER");
    telInput.removeClass("has-error");
    telInputLabel.removeClass("has-error");
    errorMsg.addClass("hidden-xs-up");
  };

  // populate the country dropdown with intl-tel-input countries data
  $.each(countryData, function(i, country) {
    countryDropdown.append($("<option></option>").attr("value", country.iso2).text(country.name));
  });

  // init plugin for formatting placeholders
  telInput.intlTelInput({
    allowDropdown: false,
    initialCountry: initCountry,
    utilsScript: "https://1cf5229636340d3e1dd5-0eccc4d82b7628eccb93a74a572fd3ee.ssl.cf1.rackcdn.com/testing/utils.js"
  });
  
    // set dropdowns initial value
  const initialCountry = telInput.intlTelInput("getSelectedCountryData").iso2;
  let dialCode = telInput.intlTelInput("getSelectedCountryData").dialCode;
  countryDropdown.val(initialCountry);
  phonePrefix.text("+" + dialCode);

  // init format
  telInput.formatter({
    'pattern': pattern
  });  
  
  // delete intl-tel-input items that aren't needed
  $('.flag-container').remove();
  $('.intl-tel-input').replaceWith(function() {
    return $('#phone-number', this);
  });

  // set placeholder
  telInput.attr("placeholder", "PHONE NUMBER");

  // on blur: validate
  telInput.blur(function() {
    // reset states
    reset();

    if ($.trim(telInput.val())) {
      // if number is not valid
      if (telInput.intlTelInput("isValidNumber")) {
        // set hidden input to dial code + inputted number
        fullPhoneNumber.val(telInput.intlTelInput("getSelectedCountryData").dialCode + telInput.val());
      } else {
        // set error states
        telInput.addClass("has-error");
        telInputLabel.addClass("has-error");
        errorMsg.removeClass("hidden-xs-up");
        //clear hidden input val
        fullPhoneNumber.val("");
      }
    }
  });

  // on keyup / change flag: reset
  telInput.on("keyup change", reset);

  // listen to the country dropdown for changes.
  // updates placeholder and prefix when changed
  countryDropdown.change(function() {
    // Update Placeholder via plugin - so we can get the example number + format
    telInput.intlTelInput("setCountry", $(this).val());
    // Update Dial Code Prefix
    dialCode = telInput.intlTelInput("getSelectedCountryData").dialCode;
    phonePrefix.text("+" + dialCode);
    // Use updated placeholder to define formatting pattern
    pattern = telInput.attr("placeholder").replace(new RegExp("[0-9]", "g"), "9").replace(/([9]\d{0,10})/g, '{{$1}}');
    // update formatter
    telInput.formatter().resetPattern(pattern);
    // clear telephone input to prevent validation errors
    telInput.val("");
    // update placeholder to specific
    telInput.attr("placeholder", "PHONE NUMBER");
  });
};

// Testing for prepopulation. If country code not supplied: default = 'us'
// const initCountryCode = 'ca'; // uncomment to pass in as param
intlPhoneNumber();