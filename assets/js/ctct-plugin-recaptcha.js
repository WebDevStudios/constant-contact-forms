/*
 * ATTENTION: An "eval-source-map" devtool has been used.
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file with attached SourceMaps in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/js/ctct-plugin-recaptcha/index.js":
/*!**************************************************!*\
  !*** ./assets/js/ctct-plugin-recaptcha/index.js ***!
  \**************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _recaptcha__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./recaptcha */ \"./assets/js/ctct-plugin-recaptcha/recaptcha.js\");\n/* harmony import */ var _recaptcha__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_recaptcha__WEBPACK_IMPORTED_MODULE_0__);\n// This is the entry point for reCAPTCHA JS. Add JavaScript imports here.\n//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9hc3NldHMvanMvY3RjdC1wbHVnaW4tcmVjYXB0Y2hhL2luZGV4LmpzIiwibWFwcGluZ3MiOiI7OztBQUFBIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vY29uc3RhbnQtY29udGFjdC1mb3Jtcy8uL2Fzc2V0cy9qcy9jdGN0LXBsdWdpbi1yZWNhcHRjaGEvaW5kZXguanM/M2EzNiJdLCJzb3VyY2VzQ29udGVudCI6WyIvLyBUaGlzIGlzIHRoZSBlbnRyeSBwb2ludCBmb3IgcmVDQVBUQ0hBIEpTLiBBZGQgSmF2YVNjcmlwdCBpbXBvcnRzIGhlcmUuXG5pbXBvcnQgJy4vcmVjYXB0Y2hhJztcbiJdLCJuYW1lcyI6W10sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./assets/js/ctct-plugin-recaptcha/index.js\n");

/***/ }),

/***/ "./assets/js/ctct-plugin-recaptcha/recaptcha.js":
/*!******************************************************!*\
  !*** ./assets/js/ctct-plugin-recaptcha/recaptcha.js ***!
  \******************************************************/
/***/ (function() {

eval("grecaptcha.ready(function () {\n  grecaptcha.execute(recaptchav3.site_key, {\n    action: 'constantcontactsubmit'\n  }).then(function (token) {\n    var forms = document.querySelectorAll('.ctct-form-wrapper form');\n    var recaptchaResponse = document.createElement('input');\n    recaptchaResponse.setAttribute('type', 'hidden');\n    recaptchaResponse.setAttribute('name', 'g-recaptcha-response');\n    recaptchaResponse.setAttribute('value', token);\n    Array.from(forms).forEach(function (form) {\n      form.append(recaptchaResponse.cloneNode(true));\n    });\n\n    /*jQuery( '.ctct-form-wrapper form' ).append( '<input type=\"hidden\" name=\"g-recaptcha-response\" value=\"' + token + '\">' );*/\n  });\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9hc3NldHMvanMvY3RjdC1wbHVnaW4tcmVjYXB0Y2hhL3JlY2FwdGNoYS5qcyIsIm5hbWVzIjpbImdyZWNhcHRjaGEiLCJyZWFkeSIsImV4ZWN1dGUiLCJyZWNhcHRjaGF2MyIsInNpdGVfa2V5IiwiYWN0aW9uIiwidGhlbiIsInRva2VuIiwiZm9ybXMiLCJkb2N1bWVudCIsInF1ZXJ5U2VsZWN0b3JBbGwiLCJyZWNhcHRjaGFSZXNwb25zZSIsImNyZWF0ZUVsZW1lbnQiLCJzZXRBdHRyaWJ1dGUiLCJBcnJheSIsImZyb20iLCJmb3JFYWNoIiwiZm9ybSIsImFwcGVuZCIsImNsb25lTm9kZSJdLCJzb3VyY2VSb290IjoiIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vY29uc3RhbnQtY29udGFjdC1mb3Jtcy8uL2Fzc2V0cy9qcy9jdGN0LXBsdWdpbi1yZWNhcHRjaGEvcmVjYXB0Y2hhLmpzPzFlMWYiXSwic291cmNlc0NvbnRlbnQiOlsiZ3JlY2FwdGNoYS5yZWFkeShmdW5jdGlvbiAoKSB7XG5cdGdyZWNhcHRjaGEuZXhlY3V0ZSggcmVjYXB0Y2hhdjMuc2l0ZV9rZXksIHthY3Rpb246ICdjb25zdGFudGNvbnRhY3RzdWJtaXQnfSApLnRoZW4oIGZ1bmN0aW9uICggdG9rZW4gKSB7XG5cdFx0bGV0IGZvcm1zID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbCggJy5jdGN0LWZvcm0td3JhcHBlciBmb3JtJyApO1xuXHRcdGxldCByZWNhcHRjaGFSZXNwb25zZSA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2lucHV0Jyk7XG5cdFx0cmVjYXB0Y2hhUmVzcG9uc2Uuc2V0QXR0cmlidXRlKCd0eXBlJywgJ2hpZGRlbicpO1xuXHRcdHJlY2FwdGNoYVJlc3BvbnNlLnNldEF0dHJpYnV0ZSgnbmFtZScsICdnLXJlY2FwdGNoYS1yZXNwb25zZScpO1xuXHRcdHJlY2FwdGNoYVJlc3BvbnNlLnNldEF0dHJpYnV0ZSgndmFsdWUnLCB0b2tlbik7XG5cblx0XHRBcnJheS5mcm9tKCBmb3JtcyApLmZvckVhY2goIGZ1bmN0aW9uKCBmb3JtICkge1xuXHRcdFx0Zm9ybS5hcHBlbmQocmVjYXB0Y2hhUmVzcG9uc2UuY2xvbmVOb2RlKHRydWUpKTtcblx0XHR9ICk7XG5cblx0XHQvKmpRdWVyeSggJy5jdGN0LWZvcm0td3JhcHBlciBmb3JtJyApLmFwcGVuZCggJzxpbnB1dCB0eXBlPVwiaGlkZGVuXCIgbmFtZT1cImctcmVjYXB0Y2hhLXJlc3BvbnNlXCIgdmFsdWU9XCInICsgdG9rZW4gKyAnXCI+JyApOyovXG5cdH0pO1xufSk7XG4iXSwibWFwcGluZ3MiOiJBQUFBQSxVQUFVLENBQUNDLEtBQUssQ0FBQyxZQUFZO0VBQzVCRCxVQUFVLENBQUNFLE9BQU8sQ0FBRUMsV0FBVyxDQUFDQyxRQUFRLEVBQUU7SUFBQ0MsTUFBTSxFQUFFO0VBQXVCLENBQUUsQ0FBQyxDQUFDQyxJQUFJLENBQUUsVUFBV0MsS0FBSyxFQUFHO0lBQ3RHLElBQUlDLEtBQUssR0FBR0MsUUFBUSxDQUFDQyxnQkFBZ0IsQ0FBRSx5QkFBMEIsQ0FBQztJQUNsRSxJQUFJQyxpQkFBaUIsR0FBR0YsUUFBUSxDQUFDRyxhQUFhLENBQUMsT0FBTyxDQUFDO0lBQ3ZERCxpQkFBaUIsQ0FBQ0UsWUFBWSxDQUFDLE1BQU0sRUFBRSxRQUFRLENBQUM7SUFDaERGLGlCQUFpQixDQUFDRSxZQUFZLENBQUMsTUFBTSxFQUFFLHNCQUFzQixDQUFDO0lBQzlERixpQkFBaUIsQ0FBQ0UsWUFBWSxDQUFDLE9BQU8sRUFBRU4sS0FBSyxDQUFDO0lBRTlDTyxLQUFLLENBQUNDLElBQUksQ0FBRVAsS0FBTSxDQUFDLENBQUNRLE9BQU8sQ0FBRSxVQUFVQyxJQUFJLEVBQUc7TUFDN0NBLElBQUksQ0FBQ0MsTUFBTSxDQUFDUCxpQkFBaUIsQ0FBQ1EsU0FBUyxDQUFDLElBQUksQ0FBQyxDQUFDO0lBQy9DLENBQUUsQ0FBQzs7SUFFSDtFQUNELENBQUMsQ0FBQztBQUNILENBQUMsQ0FBQyJ9\n//# sourceURL=webpack-internal:///./assets/js/ctct-plugin-recaptcha/recaptcha.js\n");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval-source-map devtool is used.
/******/ 	var __webpack_exports__ = __webpack_require__("./assets/js/ctct-plugin-recaptcha/index.js");
/******/ 	
/******/ })()
;