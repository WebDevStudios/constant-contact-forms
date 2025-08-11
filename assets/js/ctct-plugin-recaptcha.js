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

eval("grecaptcha.ready(function () {\n  var forms = document.querySelectorAll('.ctct-form-wrapper form');\n  Array.from(forms).forEach(function (form) {\n    var submitBtn = form.querySelector('.ctct-submit');\n    form.addEventListener('submit', function (e) {\n      e.preventDefault();\n      try {\n        grecaptcha.execute(recaptchav3.site_key, {\n          action: 'constantcontactsubmit'\n        }).then(function (token) {\n          var recaptchaResponse = document.createElement('input');\n          recaptchaResponse.setAttribute('type', 'hidden');\n          recaptchaResponse.setAttribute('name', 'g-recaptcha-response');\n          recaptchaResponse.setAttribute('value', token);\n          form.append(recaptchaResponse.cloneNode(true));\n          var origBtnVal = document.createElement('input');\n          origBtnVal.setAttribute('type', 'hidden');\n          origBtnVal.setAttribute('name', 'ctct-submitted');\n          origBtnVal.setAttribute('value', 'true');\n          form.append(origBtnVal);\n          form.submit();\n        });\n      } catch (error) {\n        console.log(error);\n        return false;\n      }\n    });\n  });\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9hc3NldHMvanMvY3RjdC1wbHVnaW4tcmVjYXB0Y2hhL3JlY2FwdGNoYS5qcyIsIm5hbWVzIjpbImdyZWNhcHRjaGEiLCJyZWFkeSIsImZvcm1zIiwiZG9jdW1lbnQiLCJxdWVyeVNlbGVjdG9yQWxsIiwiQXJyYXkiLCJmcm9tIiwiZm9yRWFjaCIsImZvcm0iLCJzdWJtaXRCdG4iLCJxdWVyeVNlbGVjdG9yIiwiYWRkRXZlbnRMaXN0ZW5lciIsImUiLCJwcmV2ZW50RGVmYXVsdCIsImV4ZWN1dGUiLCJyZWNhcHRjaGF2MyIsInNpdGVfa2V5IiwiYWN0aW9uIiwidGhlbiIsInRva2VuIiwicmVjYXB0Y2hhUmVzcG9uc2UiLCJjcmVhdGVFbGVtZW50Iiwic2V0QXR0cmlidXRlIiwiYXBwZW5kIiwiY2xvbmVOb2RlIiwib3JpZ0J0blZhbCIsInN1Ym1pdCIsImVycm9yIiwiY29uc29sZSIsImxvZyJdLCJzb3VyY2VSb290IjoiIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vY29uc3RhbnQtY29udGFjdC1mb3Jtcy8uL2Fzc2V0cy9qcy9jdGN0LXBsdWdpbi1yZWNhcHRjaGEvcmVjYXB0Y2hhLmpzPzFlMWYiXSwic291cmNlc0NvbnRlbnQiOlsiZ3JlY2FwdGNoYS5yZWFkeShmdW5jdGlvbiAoKSB7XG5cdGxldCBmb3JtcyA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoJy5jdGN0LWZvcm0td3JhcHBlciBmb3JtJyk7XG5cdEFycmF5LmZyb20oZm9ybXMpLmZvckVhY2goZnVuY3Rpb24gKGZvcm0pIHtcblx0XHRsZXQgc3VibWl0QnRuID0gZm9ybS5xdWVyeVNlbGVjdG9yKCcuY3RjdC1zdWJtaXQnKTtcblx0XHRmb3JtLmFkZEV2ZW50TGlzdGVuZXIoJ3N1Ym1pdCcsIChlKSA9PiB7XG5cdFx0XHRlLnByZXZlbnREZWZhdWx0KCk7XG5cblx0XHRcdHRyeSB7XG5cdFx0XHRcdGdyZWNhcHRjaGEuZXhlY3V0ZShyZWNhcHRjaGF2My5zaXRlX2tleSwge2FjdGlvbjogJ2NvbnN0YW50Y29udGFjdHN1Ym1pdCd9KS50aGVuKGZ1bmN0aW9uICh0b2tlbikge1xuXHRcdFx0XHRcdGxldCByZWNhcHRjaGFSZXNwb25zZSA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2lucHV0Jyk7XG5cdFx0XHRcdFx0cmVjYXB0Y2hhUmVzcG9uc2Uuc2V0QXR0cmlidXRlKCd0eXBlJywgJ2hpZGRlbicpO1xuXHRcdFx0XHRcdHJlY2FwdGNoYVJlc3BvbnNlLnNldEF0dHJpYnV0ZSgnbmFtZScsICdnLXJlY2FwdGNoYS1yZXNwb25zZScpO1xuXHRcdFx0XHRcdHJlY2FwdGNoYVJlc3BvbnNlLnNldEF0dHJpYnV0ZSgndmFsdWUnLCB0b2tlbik7XG5cblx0XHRcdFx0XHRmb3JtLmFwcGVuZChyZWNhcHRjaGFSZXNwb25zZS5jbG9uZU5vZGUodHJ1ZSkpO1xuXG5cdFx0XHRcdFx0bGV0IG9yaWdCdG5WYWwgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdpbnB1dCcpO1xuXHRcdFx0XHRcdG9yaWdCdG5WYWwuc2V0QXR0cmlidXRlKCd0eXBlJywgJ2hpZGRlbicpO1xuXHRcdFx0XHRcdG9yaWdCdG5WYWwuc2V0QXR0cmlidXRlKCduYW1lJywgJ2N0Y3Qtc3VibWl0dGVkJyk7XG5cdFx0XHRcdFx0b3JpZ0J0blZhbC5zZXRBdHRyaWJ1dGUoJ3ZhbHVlJywgJ3RydWUnKTtcblx0XHRcdFx0XHRmb3JtLmFwcGVuZChvcmlnQnRuVmFsKTtcblxuXHRcdFx0XHRcdGZvcm0uc3VibWl0KCk7XG5cdFx0XHRcdH0pO1xuXHRcdFx0fSBjYXRjaCAoZXJyb3IpIHtcblx0XHRcdFx0Y29uc29sZS5sb2coZXJyb3IpO1xuXHRcdFx0XHRyZXR1cm4gZmFsc2U7XG5cdFx0XHR9XG5cdFx0fSk7XG5cdH0pO1xufSk7XG4iXSwibWFwcGluZ3MiOiJBQUFBQSxVQUFVLENBQUNDLEtBQUssQ0FBQyxZQUFZO0VBQzVCLElBQUlDLEtBQUssR0FBR0MsUUFBUSxDQUFDQyxnQkFBZ0IsQ0FBQyx5QkFBeUIsQ0FBQztFQUNoRUMsS0FBSyxDQUFDQyxJQUFJLENBQUNKLEtBQUssQ0FBQyxDQUFDSyxPQUFPLENBQUMsVUFBVUMsSUFBSSxFQUFFO0lBQ3pDLElBQUlDLFNBQVMsR0FBR0QsSUFBSSxDQUFDRSxhQUFhLENBQUMsY0FBYyxDQUFDO0lBQ2xERixJQUFJLENBQUNHLGdCQUFnQixDQUFDLFFBQVEsRUFBRSxVQUFDQyxDQUFDLEVBQUs7TUFDdENBLENBQUMsQ0FBQ0MsY0FBYyxDQUFDLENBQUM7TUFFbEIsSUFBSTtRQUNIYixVQUFVLENBQUNjLE9BQU8sQ0FBQ0MsV0FBVyxDQUFDQyxRQUFRLEVBQUU7VUFBQ0MsTUFBTSxFQUFFO1FBQXVCLENBQUMsQ0FBQyxDQUFDQyxJQUFJLENBQUMsVUFBVUMsS0FBSyxFQUFFO1VBQ2pHLElBQUlDLGlCQUFpQixHQUFHakIsUUFBUSxDQUFDa0IsYUFBYSxDQUFDLE9BQU8sQ0FBQztVQUN2REQsaUJBQWlCLENBQUNFLFlBQVksQ0FBQyxNQUFNLEVBQUUsUUFBUSxDQUFDO1VBQ2hERixpQkFBaUIsQ0FBQ0UsWUFBWSxDQUFDLE1BQU0sRUFBRSxzQkFBc0IsQ0FBQztVQUM5REYsaUJBQWlCLENBQUNFLFlBQVksQ0FBQyxPQUFPLEVBQUVILEtBQUssQ0FBQztVQUU5Q1gsSUFBSSxDQUFDZSxNQUFNLENBQUNILGlCQUFpQixDQUFDSSxTQUFTLENBQUMsSUFBSSxDQUFDLENBQUM7VUFFOUMsSUFBSUMsVUFBVSxHQUFHdEIsUUFBUSxDQUFDa0IsYUFBYSxDQUFDLE9BQU8sQ0FBQztVQUNoREksVUFBVSxDQUFDSCxZQUFZLENBQUMsTUFBTSxFQUFFLFFBQVEsQ0FBQztVQUN6Q0csVUFBVSxDQUFDSCxZQUFZLENBQUMsTUFBTSxFQUFFLGdCQUFnQixDQUFDO1VBQ2pERyxVQUFVLENBQUNILFlBQVksQ0FBQyxPQUFPLEVBQUUsTUFBTSxDQUFDO1VBQ3hDZCxJQUFJLENBQUNlLE1BQU0sQ0FBQ0UsVUFBVSxDQUFDO1VBRXZCakIsSUFBSSxDQUFDa0IsTUFBTSxDQUFDLENBQUM7UUFDZCxDQUFDLENBQUM7TUFDSCxDQUFDLENBQUMsT0FBT0MsS0FBSyxFQUFFO1FBQ2ZDLE9BQU8sQ0FBQ0MsR0FBRyxDQUFDRixLQUFLLENBQUM7UUFDbEIsT0FBTyxLQUFLO01BQ2I7SUFDRCxDQUFDLENBQUM7RUFDSCxDQUFDLENBQUM7QUFDSCxDQUFDLENBQUMiLCJpZ25vcmVMaXN0IjpbXX0=\n//# sourceURL=webpack-internal:///./assets/js/ctct-plugin-recaptcha/recaptcha.js\n");

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