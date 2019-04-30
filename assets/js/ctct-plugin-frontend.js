/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "https://localhost:3000/wp-content/plugins/constant-contact-forms/assets/js/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 2);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./assets/js/ctct-plugin-frontend/index.js":
/*!*************************************************!*\
  !*** ./assets/js/ctct-plugin-frontend/index.js ***!
  \*************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _util__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./util */ \"./assets/js/ctct-plugin-frontend/util.js\");\n/* harmony import */ var _util__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_util__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _validation__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./validation */ \"./assets/js/ctct-plugin-frontend/validation.js\");\n/* harmony import */ var _validation__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_validation__WEBPACK_IMPORTED_MODULE_1__);\n\n//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9hc3NldHMvanMvY3RjdC1wbHVnaW4tZnJvbnRlbmQvaW5kZXguanMuanMiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9hc3NldHMvanMvY3RjdC1wbHVnaW4tZnJvbnRlbmQvaW5kZXguanM/NzY1OCJdLCJzb3VyY2VzQ29udGVudCI6WyJpbXBvcnQgJy4vdXRpbCc7XG5pbXBvcnQgJy4vdmFsaWRhdGlvbic7XG4iXSwibWFwcGluZ3MiOiJBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTsiLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./assets/js/ctct-plugin-frontend/index.js\n");

/***/ }),

/***/ "./assets/js/ctct-plugin-frontend/util.js":
/*!************************************************!*\
  !*** ./assets/js/ctct-plugin-frontend/util.js ***!
  \************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("/**\n * General purpose utility stuff for CC plugin.\n */\n(function (global, $) {\n  /**\n   * Temporarily prevent the submit button from being clicked.\n   */\n  $(document).ready(function () {\n    $('#ctct-submitted').on('click', function () {\n      setTimeout(function () {\n        disable_send_button();\n        setTimeout(enable_send_button, 3000);\n      }, 100);\n    });\n  });\n\n  function disable_send_button() {\n    return $('#ctct-submitted').attr('disabled', 'disabled');\n  }\n\n  function enable_send_button() {\n    return $('#ctct-submitted').attr('disabled', null);\n  }\n})(window, jQuery);//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9hc3NldHMvanMvY3RjdC1wbHVnaW4tZnJvbnRlbmQvdXRpbC5qcy5qcyIsInNvdXJjZXMiOlsid2VicGFjazovLy8uL2Fzc2V0cy9qcy9jdGN0LXBsdWdpbi1mcm9udGVuZC91dGlsLmpzPzQ1NWIiXSwic291cmNlc0NvbnRlbnQiOlsiLyoqXG4gKiBHZW5lcmFsIHB1cnBvc2UgdXRpbGl0eSBzdHVmZiBmb3IgQ0MgcGx1Z2luLlxuICovXG4oZnVuY3Rpb24oIGdsb2JhbCwgJCApe1xuXHQvKipcblx0ICogVGVtcG9yYXJpbHkgcHJldmVudCB0aGUgc3VibWl0IGJ1dHRvbiBmcm9tIGJlaW5nIGNsaWNrZWQuXG5cdCAqL1xuXHQkKCBkb2N1bWVudCApLnJlYWR5KCBmdW5jdGlvbigpIHtcblx0XHQkKCAnI2N0Y3Qtc3VibWl0dGVkJyApLm9uKCAnY2xpY2snLCBmdW5jdGlvbigpIHsgXG5cdFx0XHRzZXRUaW1lb3V0KCBmdW5jdGlvbigpIHtcblx0XHRcdFx0ZGlzYWJsZV9zZW5kX2J1dHRvbigpO1xuXHRcdFx0XHRzZXRUaW1lb3V0KCBlbmFibGVfc2VuZF9idXR0b24sIDMwMDAgKTtcblx0XHRcdH0sIDEwMCApO1xuXHRcdH0gKTtcblx0fSApO1xuXHRcblx0ZnVuY3Rpb24gZGlzYWJsZV9zZW5kX2J1dHRvbigpIHtcblx0XHRyZXR1cm4gJCggJyNjdGN0LXN1Ym1pdHRlZCcgKS5hdHRyKCAnZGlzYWJsZWQnLCAnZGlzYWJsZWQnICk7XG5cdH1cblxuXHRmdW5jdGlvbiBlbmFibGVfc2VuZF9idXR0b24oKSB7XG5cdFx0cmV0dXJuICQoICcjY3RjdC1zdWJtaXR0ZWQnICkuYXR0ciggJ2Rpc2FibGVkJywgbnVsbCApO1xuXHR9XG59KSggd2luZG93LCBqUXVlcnkgKTtcbiJdLCJtYXBwaW5ncyI6IkFBQUE7OztBQUdBO0FBQ0E7OztBQUdBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EiLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./assets/js/ctct-plugin-frontend/util.js\n");

/***/ }),

/***/ "./assets/js/ctct-plugin-frontend/validation.js":
/*!******************************************************!*\
  !*** ./assets/js/ctct-plugin-frontend/validation.js ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("/**\n * Front-end form validation.\n *\n * @since 1.0.0\n */\nwindow.CTCTSupport = {};\n\n(function (window, $, app) {\n  /**\n   * @constructor\n   *\n   * @author Constant Contact\n   * @since 1.0.0\n   */\n  app.init = function () {\n    app.cache();\n    app.bindEvents();\n    app.removePlaceholder();\n  };\n  /**\n   * Remove placeholder text values.\n   *\n   * @author Constant Contact\n   * @since 1.0.0\n   */\n\n\n  app.removePlaceholder = function () {\n    $('.ctct-form-field input, textarea').focus(function () {\n      $(this).data('placeholder', $(this).attr('placeholder')).attr('placeholder', '');\n    }).blur(function () {\n      $(this).attr('placeholder', $(this).data('placeholder'));\n    });\n  };\n  /**\n   * Cache DOM elements.\n   *\n   * @author Constant Contact\n   * @since 1.0.0\n   */\n\n\n  app.cache = function () {\n    app.$c = {\n      $window: $(window),\n      $body: $('body'),\n      $forms: []\n    };\n    app.$c.$body.find('.ctct-form-wrapper').each(function (i, formWrapper) {\n      app.$c.$forms.push($(formWrapper).find('form'));\n    });\n    app.$c.$forms.each(function (i, el) {\n      console.log('honeypot for ' + i, app.$c.$forms[i].find('#ctct_usage_field'));\n      console.log('submitButton for ' + i, app.$c.$forms[i].find('input[type=submit]'));\n      console.log('recaptcha for ' + i, app.$c.$forms[i].find('.g-recaptcha'));\n      app.$c.$forms[i].$honeypot = app.$c.$forms[i].find('#ctct_usage_field');\n      app.$c.$forms[i].$submitButton = app.$c.$forms[i].find('input[type=submit]');\n      app.$c.$forms[i].$recaptcha = app.$c.$forms[i].find('.g-recaptcha');\n    });\n    app.timeout = null;\n  };\n  /**\n   * Remove the ctct-invalid class from elements that have it.\n   *\n   * @author Constant Contact\n   * @since 1.0.0\n   */\n\n\n  app.setAllInputsValid = function () {\n    $(app.$c.$form + ' .ctct-invalid').removeClass('ctct-invalid');\n  };\n  /**\n   * Clears form inputs of current values.\n   *\n   * @author Constant Contact\n   * @since 1.0.0\n   *\n   * @param {string} formIdSelector The selector for the form that has just been submitted.\n   */\n\n\n  app.clearFormInputs = function (formIdSelector) {\n    // jQuery doesn't have a native reset function so the [0] will convert to a JavaScript object.\n    var submittedForm = $(formIdSelector + ' form');\n    submittedForm[0].reset();\n  };\n  /**\n   * Adds .ctct-invalid HTML class to inputs whose values are invalid.\n   *\n   * @author Constant Contact\n   * @since 1.0.0\n   *\n   * @param {object} error AJAX response error object.\n   */\n\n\n  app.processError = function (error) {\n    // If we have an id property set.\n    if ('undefined' !== typeof error.id) {\n      $('#' + error.id).addClass('ctct-invalid');\n    }\n  };\n  /**\n   * Check the value of the hidden honeypot field; disable form submission button if anything in it.\n   *\n   * @author Constant Contact\n   * @since 1.0.0\n   */\n\n\n  app.checkHoneypot = function () {\n    var honeypotLength = app.$c.$honeypot.val().length; // If there is text in the honeypot, disable the submit button\n\n    if (0 < honeypotLength) {\n      app.$c.$submitButton.attr('disabled', 'disabled');\n    } else {\n      app.$c.$submitButton.attr('disabled', false);\n    }\n  };\n  /**\n   * Handle the form submission.\n   *\n   * @author Constant Contact\n   * @since 1.0.0\n   *\n   * @param {object} e The submit event.\n   */\n\n\n  app.handleSubmit = function (e) {\n    if ('on' !== app.$c.$form.find('.ctct-form').attr('data-doajax')) {\n      return;\n    }\n\n    var formId = $(this).closest('.ctct-form-wrapper').attr('id');\n    var formIdSelector = '';\n\n    if ('' !== formId) {\n      formIdSelector = '#' + formId + ' ';\n    }\n\n    var doProcess = true;\n    $.each($(formIdSelector + '.ctct-form [required]'), function (i, field) {\n      if (false === field.checkValidity()) {\n        doProcess = false;\n      }\n    });\n\n    if (false === doProcess) {\n      return;\n    }\n\n    e.preventDefault();\n    clearTimeout(app.timeout);\n    app.timeout = setTimeout(function () {\n      $('#ctct-submitted').prop('disabled', true);\n      $.post(window.ajaxurl, {\n        'action': 'ctct_process_form',\n        'data': $(formIdSelector + 'form').serialize()\n      }, function (response) {\n        $('#ctct-submitted').prop('disabled', false); // Make sure we got the 'status' attribute in our response.\n\n        if ('undefined' !== typeof response.status) {\n          if ('success' === response.status) {\n            // Add a timestamp to the message so that we only remove this message and not all at once.\n            var timeClass = 'message-time-' + $.now();\n            var messageClass = 'ctct-message ' + response.status + ' ' + timeClass;\n            $(formIdSelector + '.ctct-form').before('<p class=\"' + messageClass + '\">' + response.message + '</p>');\n\n            if ('' !== formIdSelector) {\n              app.clearFormInputs(formIdSelector);\n            } // Set a 5 second timeout to remove the added success message.\n\n\n            setTimeout(function () {\n              $('.' + timeClass).fadeOut('slow');\n            }, 5000);\n          } else {\n            // Here we'll want to disable the submit button and add some error classes.\n            if ('undefined' !== typeof response.errors) {\n              app.setAllInputsValid();\n              response.errors.forEach(app.processError);\n            } else {\n              $(formIdSelector + '.ctct-form').before('<p class=\"ctct-message ' + response.status + '\">' + response.message + '</p>');\n            }\n          }\n        }\n      });\n    }, 500);\n  };\n  /**\n   * Set up event bindings and callbacks.\n   *\n   * @author Constant Contact\n   * @since 1.0.0\n   */\n\n\n  app.bindEvents = function () {\n    $(app.$c.$form).on('click', 'input[type=submit]', app.handleSubmit);\n    $(app.$c.$honeypot).on('change keyup', app.checkHoneypot);\n    /**\n     * Disable the submit button by default until the captcha is passed (if captcha exists).\n     *\n     * @author Constant Contact\n     * @since 1.0.0\n     */\n\n    if (0 < app.$c.$recaptcha.length) {\n      app.$c.$submitButton.attr('disabled', 'disabled');\n    }\n  };\n\n  $(app.init);\n})(window, jQuery, window.CTCTSupport);//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9hc3NldHMvanMvY3RjdC1wbHVnaW4tZnJvbnRlbmQvdmFsaWRhdGlvbi5qcy5qcyIsInNvdXJjZXMiOlsid2VicGFjazovLy8uL2Fzc2V0cy9qcy9jdGN0LXBsdWdpbi1mcm9udGVuZC92YWxpZGF0aW9uLmpzPzMzOTkiXSwic291cmNlc0NvbnRlbnQiOlsiLyoqXG4gKiBGcm9udC1lbmQgZm9ybSB2YWxpZGF0aW9uLlxuICpcbiAqIEBzaW5jZSAxLjAuMFxuICovXG5cbiB3aW5kb3cuQ1RDVFN1cHBvcnQgPSB7fTtcblxuKCBmdW5jdGlvbiggd2luZG93LCAkLCBhcHAgKSB7XG5cblx0LyoqXG5cdCAqIEBjb25zdHJ1Y3RvclxuXHQgKlxuXHQgKiBAYXV0aG9yIENvbnN0YW50IENvbnRhY3Rcblx0ICogQHNpbmNlIDEuMC4wXG5cdCAqL1xuXHRhcHAuaW5pdCA9IGZ1bmN0aW9uKCkge1xuXHRcdGFwcC5jYWNoZSgpO1xuXHRcdGFwcC5iaW5kRXZlbnRzKCk7XG5cdFx0YXBwLnJlbW92ZVBsYWNlaG9sZGVyKCk7XG5cdH07XG5cblx0LyoqXG5cdCAqIFJlbW92ZSBwbGFjZWhvbGRlciB0ZXh0IHZhbHVlcy5cblx0ICpcblx0ICogQGF1dGhvciBDb25zdGFudCBDb250YWN0XG5cdCAqIEBzaW5jZSAxLjAuMFxuXHQgKi9cblx0YXBwLnJlbW92ZVBsYWNlaG9sZGVyID0gZnVuY3Rpb24oKSB7XG5cdFx0JCggJy5jdGN0LWZvcm0tZmllbGQgaW5wdXQsIHRleHRhcmVhJyApLmZvY3VzKCBmdW5jdGlvbigpIHtcblx0XHRcdCQoIHRoaXMgKS5kYXRhKCAncGxhY2Vob2xkZXInLCAkKCB0aGlzICkuYXR0ciggJ3BsYWNlaG9sZGVyJyApICkuYXR0ciggJ3BsYWNlaG9sZGVyJywgJycgKTtcblx0XHR9ICkuYmx1ciggZnVuY3Rpb24oKSB7XG5cdFx0XHQkKCB0aGlzICkuYXR0ciggJ3BsYWNlaG9sZGVyJywgJCggdGhpcyApLmRhdGEoICdwbGFjZWhvbGRlcicgKSApO1xuXHRcdH0gKTtcblx0fTtcblxuXHQvKipcblx0ICogQ2FjaGUgRE9NIGVsZW1lbnRzLlxuXHQgKlxuXHQgKiBAYXV0aG9yIENvbnN0YW50IENvbnRhY3Rcblx0ICogQHNpbmNlIDEuMC4wXG5cdCAqL1xuXHRhcHAuY2FjaGUgPSBmdW5jdGlvbigpIHtcblxuXHRcdGFwcC4kYyA9IHtcblx0XHRcdCR3aW5kb3c6ICQoIHdpbmRvdyApLFxuXHRcdFx0JGJvZHk6ICQoICdib2R5JyApLFxuXHRcdFx0JGZvcm1zOiBbXVxuXHRcdH07XG5cblx0XHRhcHAuJGMuJGJvZHkuZmluZCggJy5jdGN0LWZvcm0td3JhcHBlcicgKS5lYWNoKCBmdW5jdGlvbiggaSwgZm9ybVdyYXBwZXIgKSB7XG5cdFx0XHRhcHAuJGMuJGZvcm1zLnB1c2goICQoIGZvcm1XcmFwcGVyICkuZmluZCggJ2Zvcm0nICkgKTtcblx0XHR9ICk7XG5cblx0XHRhcHAuJGMuJGZvcm1zLmVhY2goIGZ1bmN0aW9uKCBpLCBlbCApIHtcblx0XHRcdGNvbnNvbGUubG9nKCAnaG9uZXlwb3QgZm9yICcrIGksIGFwcC4kYy4kZm9ybXNbIGkgXS5maW5kKCAnI2N0Y3RfdXNhZ2VfZmllbGQnICkgKTtcblx0XHRcdGNvbnNvbGUubG9nKCAnc3VibWl0QnV0dG9uIGZvciAnKyBpLCBhcHAuJGMuJGZvcm1zWyBpIF0uZmluZCggJ2lucHV0W3R5cGU9c3VibWl0XScgKSApO1xuXHRcdFx0Y29uc29sZS5sb2coICdyZWNhcHRjaGEgZm9yICcrIGksIGFwcC4kYy4kZm9ybXNbIGkgXS5maW5kKCAnLmctcmVjYXB0Y2hhJyApICk7XG5cblxuXHRcdFx0YXBwLiRjLiRmb3Jtc1sgaSBdLiRob25leXBvdCAgICAgPSBhcHAuJGMuJGZvcm1zWyBpIF0uZmluZCggJyNjdGN0X3VzYWdlX2ZpZWxkJyApO1xuXHRcdFx0YXBwLiRjLiRmb3Jtc1sgaSBdLiRzdWJtaXRCdXR0b24gPSBhcHAuJGMuJGZvcm1zWyBpIF0uZmluZCggJ2lucHV0W3R5cGU9c3VibWl0XScgKTtcblx0XHRcdGFwcC4kYy4kZm9ybXNbIGkgXS4kcmVjYXB0Y2hhICAgID0gYXBwLiRjLiRmb3Jtc1sgaSBdLmZpbmQoICcuZy1yZWNhcHRjaGEnICk7XG5cdFx0fSApO1xuXG5cdFx0YXBwLnRpbWVvdXQgPSBudWxsO1xuXHR9O1xuXG5cdC8qKlxuXHQgKiBSZW1vdmUgdGhlIGN0Y3QtaW52YWxpZCBjbGFzcyBmcm9tIGVsZW1lbnRzIHRoYXQgaGF2ZSBpdC5cblx0ICpcblx0ICogQGF1dGhvciBDb25zdGFudCBDb250YWN0XG5cdCAqIEBzaW5jZSAxLjAuMFxuXHQgKi9cblx0YXBwLnNldEFsbElucHV0c1ZhbGlkID0gZnVuY3Rpb24oKSB7XG5cdFx0JCggYXBwLiRjLiRmb3JtICsgJyAuY3RjdC1pbnZhbGlkJyApLnJlbW92ZUNsYXNzKCAnY3RjdC1pbnZhbGlkJyApO1xuXHR9O1xuXG5cdC8qKlxuXHQgKiBDbGVhcnMgZm9ybSBpbnB1dHMgb2YgY3VycmVudCB2YWx1ZXMuXG5cdCAqXG5cdCAqIEBhdXRob3IgQ29uc3RhbnQgQ29udGFjdFxuXHQgKiBAc2luY2UgMS4wLjBcblx0ICpcblx0ICogQHBhcmFtIHtzdHJpbmd9IGZvcm1JZFNlbGVjdG9yIFRoZSBzZWxlY3RvciBmb3IgdGhlIGZvcm0gdGhhdCBoYXMganVzdCBiZWVuIHN1Ym1pdHRlZC5cblx0ICovXG5cdGFwcC5jbGVhckZvcm1JbnB1dHMgPSBmdW5jdGlvbiggZm9ybUlkU2VsZWN0b3IgKSB7XG5cblx0XHQvLyBqUXVlcnkgZG9lc24ndCBoYXZlIGEgbmF0aXZlIHJlc2V0IGZ1bmN0aW9uIHNvIHRoZSBbMF0gd2lsbCBjb252ZXJ0IHRvIGEgSmF2YVNjcmlwdCBvYmplY3QuXG5cdFx0dmFyIHN1Ym1pdHRlZEZvcm0gPSAkKCBmb3JtSWRTZWxlY3RvciArICcgZm9ybScgKTtcblx0XHRzdWJtaXR0ZWRGb3JtWzBdLnJlc2V0KCk7XG5cdH07XG5cblx0LyoqXG5cdCAqIEFkZHMgLmN0Y3QtaW52YWxpZCBIVE1MIGNsYXNzIHRvIGlucHV0cyB3aG9zZSB2YWx1ZXMgYXJlIGludmFsaWQuXG5cdCAqXG5cdCAqIEBhdXRob3IgQ29uc3RhbnQgQ29udGFjdFxuXHQgKiBAc2luY2UgMS4wLjBcblx0ICpcblx0ICogQHBhcmFtIHtvYmplY3R9IGVycm9yIEFKQVggcmVzcG9uc2UgZXJyb3Igb2JqZWN0LlxuXHQgKi9cblx0YXBwLnByb2Nlc3NFcnJvciA9IGZ1bmN0aW9uKCBlcnJvciApIHtcblxuXHRcdC8vIElmIHdlIGhhdmUgYW4gaWQgcHJvcGVydHkgc2V0LlxuXHRcdGlmICggJ3VuZGVmaW5lZCcgIT09IHR5cGVvZiggZXJyb3IuaWQgKSApIHtcblx0XHRcdCQoICcjJyArIGVycm9yLmlkICkuYWRkQ2xhc3MoICdjdGN0LWludmFsaWQnICk7XG5cdFx0fVxuXHR9O1xuXG5cdC8qKlxuXHQgKiBDaGVjayB0aGUgdmFsdWUgb2YgdGhlIGhpZGRlbiBob25leXBvdCBmaWVsZDsgZGlzYWJsZSBmb3JtIHN1Ym1pc3Npb24gYnV0dG9uIGlmIGFueXRoaW5nIGluIGl0LlxuXHQgKlxuXHQgKiBAYXV0aG9yIENvbnN0YW50IENvbnRhY3Rcblx0ICogQHNpbmNlIDEuMC4wXG5cdCAqL1xuXHRhcHAuY2hlY2tIb25leXBvdCA9IGZ1bmN0aW9uKCkge1xuXHRcdHZhciBob25leXBvdExlbmd0aCA9IGFwcC4kYy4kaG9uZXlwb3QudmFsKCkubGVuZ3RoO1xuXG5cdFx0Ly8gSWYgdGhlcmUgaXMgdGV4dCBpbiB0aGUgaG9uZXlwb3QsIGRpc2FibGUgdGhlIHN1Ym1pdCBidXR0b25cblx0XHRpZiAoIDAgPCBob25leXBvdExlbmd0aCApIHtcblx0XHRcdGFwcC4kYy4kc3VibWl0QnV0dG9uLmF0dHIoICdkaXNhYmxlZCcsICdkaXNhYmxlZCcgKTtcblx0XHR9IGVsc2Uge1xuXHRcdFx0YXBwLiRjLiRzdWJtaXRCdXR0b24uYXR0ciggJ2Rpc2FibGVkJywgZmFsc2UgKTtcblx0XHR9XG5cdH07XG5cblx0LyoqXG5cdCAqIEhhbmRsZSB0aGUgZm9ybSBzdWJtaXNzaW9uLlxuXHQgKlxuXHQgKiBAYXV0aG9yIENvbnN0YW50IENvbnRhY3Rcblx0ICogQHNpbmNlIDEuMC4wXG5cdCAqXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSBlIFRoZSBzdWJtaXQgZXZlbnQuXG5cdCAqL1xuXHRhcHAuaGFuZGxlU3VibWl0ID0gZnVuY3Rpb24oIGUgKSB7XG5cblx0XHRpZiAoICdvbicgIT09IGFwcC4kYy4kZm9ybS5maW5kKCAnLmN0Y3QtZm9ybScgKS5hdHRyKCAnZGF0YS1kb2FqYXgnICkgKSB7XG5cdFx0XHRyZXR1cm47XG5cdFx0fVxuXG5cdFx0dmFyIGZvcm1JZCAgICAgICAgID0gJCggdGhpcyApLmNsb3Nlc3QoICcuY3RjdC1mb3JtLXdyYXBwZXInICkuYXR0ciggJ2lkJyApO1xuXHRcdHZhciBmb3JtSWRTZWxlY3RvciA9ICcnO1xuXG5cdFx0aWYgKCAnJyAhPT0gZm9ybUlkICkge1xuXHRcdFx0Zm9ybUlkU2VsZWN0b3IgPSAnIycgKyBmb3JtSWQgKyAnICc7XG5cdFx0fVxuXG5cdFx0dmFyIGRvUHJvY2VzcyA9IHRydWU7XG5cblx0XHQkLmVhY2goICQoIGZvcm1JZFNlbGVjdG9yICsgJy5jdGN0LWZvcm0gW3JlcXVpcmVkXScgKSwgZnVuY3Rpb24oIGksIGZpZWxkICkge1xuXHRcdFx0aWYgKCBmYWxzZSA9PT0gZmllbGQuY2hlY2tWYWxpZGl0eSgpICkge1xuXHRcdFx0XHRkb1Byb2Nlc3MgPSBmYWxzZTtcblx0XHRcdH1cblx0XHR9ICk7XG5cblx0XHRpZiAoIGZhbHNlID09PSBkb1Byb2Nlc3MgKSB7XG5cdFx0XHRyZXR1cm47XG5cdFx0fVxuXG5cdFx0ZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG5cdFx0Y2xlYXJUaW1lb3V0KCBhcHAudGltZW91dCApO1xuXG5cdFx0YXBwLnRpbWVvdXQgPSBzZXRUaW1lb3V0KCBmdW5jdGlvbigpIHtcblxuXHRcdFx0JCggJyNjdGN0LXN1Ym1pdHRlZCcgKS5wcm9wKCAnZGlzYWJsZWQnLCB0cnVlICk7XG5cblx0XHRcdCQucG9zdChcblx0XHRcdFx0d2luZG93LmFqYXh1cmwsXG5cdFx0XHRcdHtcblx0XHRcdFx0XHQnYWN0aW9uJzogJ2N0Y3RfcHJvY2Vzc19mb3JtJyxcblx0XHRcdFx0XHQnZGF0YSc6ICQoIGZvcm1JZFNlbGVjdG9yICsgJ2Zvcm0nICkuc2VyaWFsaXplKClcblx0XHRcdFx0fSxcblx0XHRcdFx0ZnVuY3Rpb24oIHJlc3BvbnNlICkge1xuXHRcdFx0XHRcdCQoICcjY3RjdC1zdWJtaXR0ZWQnICkucHJvcCggJ2Rpc2FibGVkJywgZmFsc2UgKTtcblxuXHRcdFx0XHRcdC8vIE1ha2Ugc3VyZSB3ZSBnb3QgdGhlICdzdGF0dXMnIGF0dHJpYnV0ZSBpbiBvdXIgcmVzcG9uc2UuXG5cdFx0XHRcdFx0aWYgKCAndW5kZWZpbmVkJyAhPT0gdHlwZW9mKCByZXNwb25zZS5zdGF0dXMgKSApIHtcblxuXHRcdFx0XHRcdFx0aWYgKCAnc3VjY2VzcycgPT09IHJlc3BvbnNlLnN0YXR1cyApIHtcblxuXHRcdFx0XHRcdFx0XHQvLyBBZGQgYSB0aW1lc3RhbXAgdG8gdGhlIG1lc3NhZ2Ugc28gdGhhdCB3ZSBvbmx5IHJlbW92ZSB0aGlzIG1lc3NhZ2UgYW5kIG5vdCBhbGwgYXQgb25jZS5cblx0XHRcdFx0XHRcdFx0dmFyIHRpbWVDbGFzcyA9ICdtZXNzYWdlLXRpbWUtJyArICQubm93KCk7XG5cblx0XHRcdFx0XHRcdFx0dmFyIG1lc3NhZ2VDbGFzcyA9ICdjdGN0LW1lc3NhZ2UgJyArIHJlc3BvbnNlLnN0YXR1cyArICcgJyArIHRpbWVDbGFzcztcblx0XHRcdFx0XHRcdFx0JCggZm9ybUlkU2VsZWN0b3IgKyAnLmN0Y3QtZm9ybScgKS5iZWZvcmUoICc8cCBjbGFzcz1cIicgKyBtZXNzYWdlQ2xhc3MgKyAnXCI+JyArIHJlc3BvbnNlLm1lc3NhZ2UgKyAnPC9wPicgKTtcblxuXHRcdFx0XHRcdFx0XHRpZiAoICcnICE9PSBmb3JtSWRTZWxlY3RvciApIHtcblx0XHRcdFx0XHRcdFx0XHRhcHAuY2xlYXJGb3JtSW5wdXRzKCBmb3JtSWRTZWxlY3RvciApO1xuXHRcdFx0XHRcdFx0XHR9XG5cblx0XHRcdFx0XHRcdFx0Ly8gU2V0IGEgNSBzZWNvbmQgdGltZW91dCB0byByZW1vdmUgdGhlIGFkZGVkIHN1Y2Nlc3MgbWVzc2FnZS5cblx0XHRcdFx0XHRcdFx0c2V0VGltZW91dCggZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRcdFx0JCggJy4nICsgdGltZUNsYXNzICkuZmFkZU91dCggJ3Nsb3cnICk7XG5cdFx0XHRcdFx0XHRcdH0sIDUwMDAgKTtcblx0XHRcdFx0XHRcdH0gZWxzZSB7XG5cblx0XHRcdFx0XHRcdFx0Ly8gSGVyZSB3ZSdsbCB3YW50IHRvIGRpc2FibGUgdGhlIHN1Ym1pdCBidXR0b24gYW5kIGFkZCBzb21lIGVycm9yIGNsYXNzZXMuXG5cdFx0XHRcdFx0XHRcdGlmICggJ3VuZGVmaW5lZCcgIT09IHR5cGVvZiggcmVzcG9uc2UuZXJyb3JzICkgKSB7XG5cdFx0XHRcdFx0XHRcdFx0YXBwLnNldEFsbElucHV0c1ZhbGlkKCk7XG5cdFx0XHRcdFx0XHRcdFx0cmVzcG9uc2UuZXJyb3JzLmZvckVhY2goIGFwcC5wcm9jZXNzRXJyb3IgKTtcblx0XHRcdFx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHRcdFx0XHQkKCBmb3JtSWRTZWxlY3RvciArICcuY3RjdC1mb3JtJyApLmJlZm9yZSggJzxwIGNsYXNzPVwiY3RjdC1tZXNzYWdlICcgKyByZXNwb25zZS5zdGF0dXMgKyAnXCI+JyArIHJlc3BvbnNlLm1lc3NhZ2UgKyAnPC9wPicgKTtcblx0XHRcdFx0XHRcdFx0fVxuXG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9XG5cdFx0XHQpO1xuXHRcdH0sIDUwMCApO1xuXHR9O1xuXG5cdC8qKlxuXHQgKiBTZXQgdXAgZXZlbnQgYmluZGluZ3MgYW5kIGNhbGxiYWNrcy5cblx0ICpcblx0ICogQGF1dGhvciBDb25zdGFudCBDb250YWN0XG5cdCAqIEBzaW5jZSAxLjAuMFxuXHQgKi9cblx0YXBwLmJpbmRFdmVudHMgPSBmdW5jdGlvbigpIHtcblxuXHRcdCQoIGFwcC4kYy4kZm9ybSApLm9uKCAnY2xpY2snLCAnaW5wdXRbdHlwZT1zdWJtaXRdJywgYXBwLmhhbmRsZVN1Ym1pdCApO1xuXG5cdFx0JCggYXBwLiRjLiRob25leXBvdCApLm9uKCAnY2hhbmdlIGtleXVwJywgYXBwLmNoZWNrSG9uZXlwb3QgKTtcblxuXHRcdC8qKlxuXHRcdCAqIERpc2FibGUgdGhlIHN1Ym1pdCBidXR0b24gYnkgZGVmYXVsdCB1bnRpbCB0aGUgY2FwdGNoYSBpcyBwYXNzZWQgKGlmIGNhcHRjaGEgZXhpc3RzKS5cblx0XHQgKlxuXHRcdCAqIEBhdXRob3IgQ29uc3RhbnQgQ29udGFjdFxuXHRcdCAqIEBzaW5jZSAxLjAuMFxuXHRcdCAqL1xuXHRcdGlmICggMCA8IGFwcC4kYy4kcmVjYXB0Y2hhLmxlbmd0aCApIHtcblx0XHRcdGFwcC4kYy4kc3VibWl0QnV0dG9uLmF0dHIoICdkaXNhYmxlZCcsICdkaXNhYmxlZCcgKTtcblx0XHR9XG5cdH07XG5cblx0JCggYXBwLmluaXQgKTtcblxufSAoIHdpbmRvdywgalF1ZXJ5LCB3aW5kb3cuQ1RDVFN1cHBvcnQgKSApO1xuIl0sIm1hcHBpbmdzIjoiQUFBQTs7Ozs7QUFNQTtBQUNBO0FBQ0E7QUFFQTs7Ozs7O0FBTUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUVBOzs7Ozs7OztBQU1BO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBRUE7Ozs7Ozs7O0FBTUE7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUhBO0FBTUE7QUFDQTtBQUNBO0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFHQTtBQUNBO0FBQ0E7QUFDQTtBQUVBO0FBQ0E7QUFFQTs7Ozs7Ozs7QUFNQTtBQUNBO0FBQ0E7QUFFQTs7Ozs7Ozs7OztBQVFBO0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFFQTs7Ozs7Ozs7OztBQVFBO0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUVBOzs7Ozs7OztBQU1BO0FBQ0E7QUFDQTtBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUVBOzs7Ozs7Ozs7O0FBUUE7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBRUE7QUFFQTtBQUVBO0FBRUE7QUFHQTtBQUNBO0FBRkE7QUFLQTtBQUNBO0FBRUE7QUFFQTtBQUVBO0FBQ0E7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUVBO0FBQ0E7QUFDQTtBQUVBO0FBQ0E7QUFFQTs7Ozs7Ozs7QUFNQTtBQUVBO0FBRUE7QUFFQTs7Ozs7OztBQU1BO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUVBIiwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///./assets/js/ctct-plugin-frontend/validation.js\n");

/***/ }),

/***/ 2:
/*!*******************************************************!*\
  !*** multi ./assets/js/ctct-plugin-frontend/index.js ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! ./assets/js/ctct-plugin-frontend/index.js */"./assets/js/ctct-plugin-frontend/index.js");


/***/ })

/******/ });