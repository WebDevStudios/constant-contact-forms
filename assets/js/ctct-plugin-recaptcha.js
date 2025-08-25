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

eval("grecaptcha.ready(function () {\n  var forms = document.querySelectorAll('.ctct-form-wrapper form');\n  Array.from(forms).forEach(function (form) {\n    form.addEventListener('submit', function (e) {\n      e.preventDefault();\n      try {\n        grecaptcha.execute(recaptchav3.site_key, {\n          action: 'constantcontactsubmit'\n        }).then(function (token) {\n          var recaptchaResponse = document.createElement('input');\n          recaptchaResponse.setAttribute('type', 'hidden');\n          recaptchaResponse.setAttribute('name', 'g-recaptcha-response');\n          recaptchaResponse.setAttribute('value', token);\n          form.append(recaptchaResponse.cloneNode(true));\n\n          // Because of how we're ending up submitting at this point. we are losing\n          // the original name attribute and \"value\" from the original submit button.\n          // Here we are instead just creating a hidden element with the \"ctct-submitted\"\n          // name attribute to met things proceed on the server.\n          var origBtnVal = document.createElement('input');\n          origBtnVal.setAttribute('type', 'hidden');\n          origBtnVal.setAttribute('name', 'ctct-submitted');\n          origBtnVal.setAttribute('value', 'true');\n          form.append(origBtnVal);\n          form.submit();\n        });\n      } catch (error) {\n        console.log(error);\n        return false;\n      }\n    });\n  });\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9hc3NldHMvanMvY3RjdC1wbHVnaW4tcmVjYXB0Y2hhL3JlY2FwdGNoYS5qcyIsIm5hbWVzIjpbImdyZWNhcHRjaGEiLCJyZWFkeSIsImZvcm1zIiwiZG9jdW1lbnQiLCJxdWVyeVNlbGVjdG9yQWxsIiwiQXJyYXkiLCJmcm9tIiwiZm9yRWFjaCIsImZvcm0iLCJhZGRFdmVudExpc3RlbmVyIiwiZSIsInByZXZlbnREZWZhdWx0IiwiZXhlY3V0ZSIsInJlY2FwdGNoYXYzIiwic2l0ZV9rZXkiLCJhY3Rpb24iLCJ0aGVuIiwidG9rZW4iLCJyZWNhcHRjaGFSZXNwb25zZSIsImNyZWF0ZUVsZW1lbnQiLCJzZXRBdHRyaWJ1dGUiLCJhcHBlbmQiLCJjbG9uZU5vZGUiLCJvcmlnQnRuVmFsIiwic3VibWl0IiwiZXJyb3IiLCJjb25zb2xlIiwibG9nIl0sInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9jb25zdGFudC1jb250YWN0LWZvcm1zLy4vYXNzZXRzL2pzL2N0Y3QtcGx1Z2luLXJlY2FwdGNoYS9yZWNhcHRjaGEuanM/MWUxZiJdLCJzb3VyY2VzQ29udGVudCI6WyJncmVjYXB0Y2hhLnJlYWR5KGZ1bmN0aW9uICgpIHtcblx0bGV0IGZvcm1zID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbCgnLmN0Y3QtZm9ybS13cmFwcGVyIGZvcm0nKTtcblx0QXJyYXkuZnJvbShmb3JtcykuZm9yRWFjaChmdW5jdGlvbiAoZm9ybSkge1xuXHRcdGZvcm0uYWRkRXZlbnRMaXN0ZW5lcignc3VibWl0JywgKGUpID0+IHtcblx0XHRcdGUucHJldmVudERlZmF1bHQoKTtcblxuXHRcdFx0dHJ5IHtcblx0XHRcdFx0Z3JlY2FwdGNoYS5leGVjdXRlKHJlY2FwdGNoYXYzLnNpdGVfa2V5LCB7YWN0aW9uOiAnY29uc3RhbnRjb250YWN0c3VibWl0J30pLnRoZW4oZnVuY3Rpb24gKHRva2VuKSB7XG5cdFx0XHRcdFx0bGV0IHJlY2FwdGNoYVJlc3BvbnNlID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnaW5wdXQnKTtcblx0XHRcdFx0XHRyZWNhcHRjaGFSZXNwb25zZS5zZXRBdHRyaWJ1dGUoJ3R5cGUnLCAnaGlkZGVuJyk7XG5cdFx0XHRcdFx0cmVjYXB0Y2hhUmVzcG9uc2Uuc2V0QXR0cmlidXRlKCduYW1lJywgJ2ctcmVjYXB0Y2hhLXJlc3BvbnNlJyk7XG5cdFx0XHRcdFx0cmVjYXB0Y2hhUmVzcG9uc2Uuc2V0QXR0cmlidXRlKCd2YWx1ZScsIHRva2VuKTtcblxuXHRcdFx0XHRcdGZvcm0uYXBwZW5kKHJlY2FwdGNoYVJlc3BvbnNlLmNsb25lTm9kZSh0cnVlKSk7XG5cblx0XHRcdFx0XHQvLyBCZWNhdXNlIG9mIGhvdyB3ZSdyZSBlbmRpbmcgdXAgc3VibWl0dGluZyBhdCB0aGlzIHBvaW50LiB3ZSBhcmUgbG9zaW5nXG5cdFx0XHRcdFx0Ly8gdGhlIG9yaWdpbmFsIG5hbWUgYXR0cmlidXRlIGFuZCBcInZhbHVlXCIgZnJvbSB0aGUgb3JpZ2luYWwgc3VibWl0IGJ1dHRvbi5cblx0XHRcdFx0XHQvLyBIZXJlIHdlIGFyZSBpbnN0ZWFkIGp1c3QgY3JlYXRpbmcgYSBoaWRkZW4gZWxlbWVudCB3aXRoIHRoZSBcImN0Y3Qtc3VibWl0dGVkXCJcblx0XHRcdFx0XHQvLyBuYW1lIGF0dHJpYnV0ZSB0byBtZXQgdGhpbmdzIHByb2NlZWQgb24gdGhlIHNlcnZlci5cblx0XHRcdFx0XHRsZXQgb3JpZ0J0blZhbCA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2lucHV0Jyk7XG5cdFx0XHRcdFx0b3JpZ0J0blZhbC5zZXRBdHRyaWJ1dGUoJ3R5cGUnLCAnaGlkZGVuJyk7XG5cdFx0XHRcdFx0b3JpZ0J0blZhbC5zZXRBdHRyaWJ1dGUoJ25hbWUnLCAnY3RjdC1zdWJtaXR0ZWQnKTtcblx0XHRcdFx0XHRvcmlnQnRuVmFsLnNldEF0dHJpYnV0ZSgndmFsdWUnLCAndHJ1ZScpO1xuXHRcdFx0XHRcdGZvcm0uYXBwZW5kKG9yaWdCdG5WYWwpO1xuXG5cdFx0XHRcdFx0Zm9ybS5zdWJtaXQoKTtcblx0XHRcdFx0fSk7XG5cdFx0XHR9IGNhdGNoIChlcnJvcikge1xuXHRcdFx0XHRjb25zb2xlLmxvZyhlcnJvcik7XG5cdFx0XHRcdHJldHVybiBmYWxzZTtcblx0XHRcdH1cblx0XHR9KTtcblx0fSk7XG59KTtcbiJdLCJtYXBwaW5ncyI6IkFBQUFBLFVBQVUsQ0FBQ0MsS0FBSyxDQUFDLFlBQVk7RUFDNUIsSUFBSUMsS0FBSyxHQUFHQyxRQUFRLENBQUNDLGdCQUFnQixDQUFDLHlCQUF5QixDQUFDO0VBQ2hFQyxLQUFLLENBQUNDLElBQUksQ0FBQ0osS0FBSyxDQUFDLENBQUNLLE9BQU8sQ0FBQyxVQUFVQyxJQUFJLEVBQUU7SUFDekNBLElBQUksQ0FBQ0MsZ0JBQWdCLENBQUMsUUFBUSxFQUFFLFVBQUNDLENBQUMsRUFBSztNQUN0Q0EsQ0FBQyxDQUFDQyxjQUFjLENBQUMsQ0FBQztNQUVsQixJQUFJO1FBQ0hYLFVBQVUsQ0FBQ1ksT0FBTyxDQUFDQyxXQUFXLENBQUNDLFFBQVEsRUFBRTtVQUFDQyxNQUFNLEVBQUU7UUFBdUIsQ0FBQyxDQUFDLENBQUNDLElBQUksQ0FBQyxVQUFVQyxLQUFLLEVBQUU7VUFDakcsSUFBSUMsaUJBQWlCLEdBQUdmLFFBQVEsQ0FBQ2dCLGFBQWEsQ0FBQyxPQUFPLENBQUM7VUFDdkRELGlCQUFpQixDQUFDRSxZQUFZLENBQUMsTUFBTSxFQUFFLFFBQVEsQ0FBQztVQUNoREYsaUJBQWlCLENBQUNFLFlBQVksQ0FBQyxNQUFNLEVBQUUsc0JBQXNCLENBQUM7VUFDOURGLGlCQUFpQixDQUFDRSxZQUFZLENBQUMsT0FBTyxFQUFFSCxLQUFLLENBQUM7VUFFOUNULElBQUksQ0FBQ2EsTUFBTSxDQUFDSCxpQkFBaUIsQ0FBQ0ksU0FBUyxDQUFDLElBQUksQ0FBQyxDQUFDOztVQUU5QztVQUNBO1VBQ0E7VUFDQTtVQUNBLElBQUlDLFVBQVUsR0FBR3BCLFFBQVEsQ0FBQ2dCLGFBQWEsQ0FBQyxPQUFPLENBQUM7VUFDaERJLFVBQVUsQ0FBQ0gsWUFBWSxDQUFDLE1BQU0sRUFBRSxRQUFRLENBQUM7VUFDekNHLFVBQVUsQ0FBQ0gsWUFBWSxDQUFDLE1BQU0sRUFBRSxnQkFBZ0IsQ0FBQztVQUNqREcsVUFBVSxDQUFDSCxZQUFZLENBQUMsT0FBTyxFQUFFLE1BQU0sQ0FBQztVQUN4Q1osSUFBSSxDQUFDYSxNQUFNLENBQUNFLFVBQVUsQ0FBQztVQUV2QmYsSUFBSSxDQUFDZ0IsTUFBTSxDQUFDLENBQUM7UUFDZCxDQUFDLENBQUM7TUFDSCxDQUFDLENBQUMsT0FBT0MsS0FBSyxFQUFFO1FBQ2ZDLE9BQU8sQ0FBQ0MsR0FBRyxDQUFDRixLQUFLLENBQUM7UUFDbEIsT0FBTyxLQUFLO01BQ2I7SUFDRCxDQUFDLENBQUM7RUFDSCxDQUFDLENBQUM7QUFDSCxDQUFDLENBQUMiLCJpZ25vcmVMaXN0IjpbXX0=\n//# sourceURL=webpack-internal:///./assets/js/ctct-plugin-recaptcha/recaptcha.js\n");

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