/*=========================================================================================
  File Name: auth-login.js
  Description: Auth login js file.
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: PIXINVENT
  Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

$(function () {
  'use strict';

  var pageLoginForm = $('.auth-login-form');

  // jQuery Validation
  // --------------------------------------------------------------------
  if (pageLoginForm.length) {
    pageLoginForm.validate({
        errorPlacement: function(error, element) {
            // Append the error message below the input field
            console.log(element)
            if (element.attr("name") === "password") {
                error.appendTo(element.parent().parent());
                element.parent().addClass("is-invalid");
            }else
                error.appendTo(element.parent())
        },
      /*
      * ? To enable validation onkeyup
      onkeyup: function (element) {
        $(element).valid();
      },*/
      /*
      * ? To enable validation on focusout
      onfocusout: function (element) {
        $(element).valid();
      }, */
      rules: {
        'email': {
          required: true,
          email: true
        },
        'password': {
          required: true
        }
      }
    });
  }
});
