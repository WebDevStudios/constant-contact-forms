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
eval("{__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _recaptcha__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./recaptcha */ \"./assets/js/ctct-plugin-recaptcha/recaptcha.js\");\n/* harmony import */ var _recaptcha__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_recaptcha__WEBPACK_IMPORTED_MODULE_0__);\n// This is the entry point for reCAPTCHA JS. Add JavaScript imports here.\n//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9hc3NldHMvanMvY3RjdC1wbHVnaW4tcmVjYXB0Y2hhL2luZGV4LmpzIiwibWFwcGluZ3MiOiI7OztBQUFBIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vY29uc3RhbnQtY29udGFjdC1mb3Jtcy8uL2Fzc2V0cy9qcy9jdGN0LXBsdWdpbi1yZWNhcHRjaGEvaW5kZXguanM/M2EzNiJdLCJzb3VyY2VzQ29udGVudCI6WyIvLyBUaGlzIGlzIHRoZSBlbnRyeSBwb2ludCBmb3IgcmVDQVBUQ0hBIEpTLiBBZGQgSmF2YVNjcmlwdCBpbXBvcnRzIGhlcmUuXG5pbXBvcnQgJy4vcmVjYXB0Y2hhJztcbiJdLCJuYW1lcyI6W10sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./assets/js/ctct-plugin-recaptcha/index.js\n\n}");

/***/ }),

/***/ "./assets/js/ctct-plugin-recaptcha/recaptcha.js":
/*!******************************************************!*\
  !*** ./assets/js/ctct-plugin-recaptcha/recaptcha.js ***!
  \******************************************************/
/***/ (function() {

eval("{grecaptcha.ready(function () {\n  var forms = document.querySelectorAll('.ctct-form-wrapper form');\n  Array.from(forms).forEach(function (form) {\n    // Do not attempt to process if form is submitting via ajax.\n    var doingajax = form.getAttribute('data-doajax');\n    if (doingajax && 'on' === doingajax) {\n      return;\n    }\n    form.addEventListener('submit', function (e) {\n      e.preventDefault();\n      try {\n        grecaptcha.execute(recaptchav3.site_key, {\n          action: 'constantcontactsubmit'\n        }).then(function (token) {\n          var recaptchaResponse = document.createElement('input');\n          recaptchaResponse.setAttribute('type', 'hidden');\n          recaptchaResponse.setAttribute('name', 'g-recaptcha-response');\n          recaptchaResponse.setAttribute('value', token);\n          form.append(recaptchaResponse.cloneNode(true));\n\n          // Because of how we're ending up submitting at this point. we are losing\n          // the original name attribute and \"value\" from the original submit button.\n          // Here we are instead just creating a hidden element with the \"ctct-submitted\"\n          // name attribute to met things proceed on the server.\n          var origBtnVal = document.createElement('input');\n          origBtnVal.setAttribute('type', 'hidden');\n          origBtnVal.setAttribute('name', 'ctct-submitted');\n          origBtnVal.setAttribute('value', 'true');\n          form.append(origBtnVal);\n          form.submit();\n        });\n      } catch (error) {\n        console.log(error);\n        return false;\n      }\n    });\n  });\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9hc3NldHMvanMvY3RjdC1wbHVnaW4tcmVjYXB0Y2hhL3JlY2FwdGNoYS5qcyIsIm5hbWVzIjpbImdyZWNhcHRjaGEiLCJyZWFkeSIsImZvcm1zIiwiZG9jdW1lbnQiLCJxdWVyeVNlbGVjdG9yQWxsIiwiQXJyYXkiLCJmcm9tIiwiZm9yRWFjaCIsImZvcm0iLCJkb2luZ2FqYXgiLCJnZXRBdHRyaWJ1dGUiLCJhZGRFdmVudExpc3RlbmVyIiwiZSIsInByZXZlbnREZWZhdWx0IiwiZXhlY3V0ZSIsInJlY2FwdGNoYXYzIiwic2l0ZV9rZXkiLCJhY3Rpb24iLCJ0aGVuIiwidG9rZW4iLCJyZWNhcHRjaGFSZXNwb25zZSIsImNyZWF0ZUVsZW1lbnQiLCJzZXRBdHRyaWJ1dGUiLCJhcHBlbmQiLCJjbG9uZU5vZGUiLCJvcmlnQnRuVmFsIiwic3VibWl0IiwiZXJyb3IiLCJjb25zb2xlIiwibG9nIl0sInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9jb25zdGFudC1jb250YWN0LWZvcm1zLy4vYXNzZXRzL2pzL2N0Y3QtcGx1Z2luLXJlY2FwdGNoYS9yZWNhcHRjaGEuanM/MWUxZiJdLCJzb3VyY2VzQ29udGVudCI6WyJncmVjYXB0Y2hhLnJlYWR5KGZ1bmN0aW9uICgpIHtcblx0bGV0IGZvcm1zID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbCgnLmN0Y3QtZm9ybS13cmFwcGVyIGZvcm0nKTtcblx0QXJyYXkuZnJvbShmb3JtcykuZm9yRWFjaChmdW5jdGlvbiAoZm9ybSkge1xuXHRcdC8vIERvIG5vdCBhdHRlbXB0IHRvIHByb2Nlc3MgaWYgZm9ybSBpcyBzdWJtaXR0aW5nIHZpYSBhamF4LlxuXHRcdGxldCBkb2luZ2FqYXggPSBmb3JtLmdldEF0dHJpYnV0ZSgnZGF0YS1kb2FqYXgnKTtcblx0XHRpZiAoZG9pbmdhamF4ICYmICdvbicgPT09IGRvaW5nYWpheCkge1xuXHRcdFx0cmV0dXJuO1xuXHRcdH1cblx0XHRmb3JtLmFkZEV2ZW50TGlzdGVuZXIoJ3N1Ym1pdCcsIChlKSA9PiB7XG5cdFx0XHRlLnByZXZlbnREZWZhdWx0KCk7XG5cblx0XHRcdHRyeSB7XG5cdFx0XHRcdGdyZWNhcHRjaGEuZXhlY3V0ZShyZWNhcHRjaGF2My5zaXRlX2tleSwge2FjdGlvbjogJ2NvbnN0YW50Y29udGFjdHN1Ym1pdCd9KS50aGVuKGZ1bmN0aW9uICh0b2tlbikge1xuXHRcdFx0XHRcdGxldCByZWNhcHRjaGFSZXNwb25zZSA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2lucHV0Jyk7XG5cdFx0XHRcdFx0cmVjYXB0Y2hhUmVzcG9uc2Uuc2V0QXR0cmlidXRlKCd0eXBlJywgJ2hpZGRlbicpO1xuXHRcdFx0XHRcdHJlY2FwdGNoYVJlc3BvbnNlLnNldEF0dHJpYnV0ZSgnbmFtZScsICdnLXJlY2FwdGNoYS1yZXNwb25zZScpO1xuXHRcdFx0XHRcdHJlY2FwdGNoYVJlc3BvbnNlLnNldEF0dHJpYnV0ZSgndmFsdWUnLCB0b2tlbik7XG5cblx0XHRcdFx0XHRmb3JtLmFwcGVuZChyZWNhcHRjaGFSZXNwb25zZS5jbG9uZU5vZGUodHJ1ZSkpO1xuXG5cdFx0XHRcdFx0Ly8gQmVjYXVzZSBvZiBob3cgd2UncmUgZW5kaW5nIHVwIHN1Ym1pdHRpbmcgYXQgdGhpcyBwb2ludC4gd2UgYXJlIGxvc2luZ1xuXHRcdFx0XHRcdC8vIHRoZSBvcmlnaW5hbCBuYW1lIGF0dHJpYnV0ZSBhbmQgXCJ2YWx1ZVwiIGZyb20gdGhlIG9yaWdpbmFsIHN1Ym1pdCBidXR0b24uXG5cdFx0XHRcdFx0Ly8gSGVyZSB3ZSBhcmUgaW5zdGVhZCBqdXN0IGNyZWF0aW5nIGEgaGlkZGVuIGVsZW1lbnQgd2l0aCB0aGUgXCJjdGN0LXN1Ym1pdHRlZFwiXG5cdFx0XHRcdFx0Ly8gbmFtZSBhdHRyaWJ1dGUgdG8gbWV0IHRoaW5ncyBwcm9jZWVkIG9uIHRoZSBzZXJ2ZXIuXG5cdFx0XHRcdFx0bGV0IG9yaWdCdG5WYWwgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdpbnB1dCcpO1xuXHRcdFx0XHRcdG9yaWdCdG5WYWwuc2V0QXR0cmlidXRlKCd0eXBlJywgJ2hpZGRlbicpO1xuXHRcdFx0XHRcdG9yaWdCdG5WYWwuc2V0QXR0cmlidXRlKCduYW1lJywgJ2N0Y3Qtc3VibWl0dGVkJyk7XG5cdFx0XHRcdFx0b3JpZ0J0blZhbC5zZXRBdHRyaWJ1dGUoJ3ZhbHVlJywgJ3RydWUnKTtcblx0XHRcdFx0XHRmb3JtLmFwcGVuZChvcmlnQnRuVmFsKTtcblxuXHRcdFx0XHRcdGZvcm0uc3VibWl0KCk7XG5cdFx0XHRcdH0pO1xuXHRcdFx0fSBjYXRjaCAoZXJyb3IpIHtcblx0XHRcdFx0Y29uc29sZS5sb2coZXJyb3IpO1xuXHRcdFx0XHRyZXR1cm4gZmFsc2U7XG5cdFx0XHR9XG5cdFx0fSk7XG5cdH0pO1xufSk7XG4iXSwibWFwcGluZ3MiOiJBQUFBQSxVQUFVLENBQUNDLEtBQUssQ0FBQyxZQUFZO0VBQzVCLElBQUlDLEtBQUssR0FBR0MsUUFBUSxDQUFDQyxnQkFBZ0IsQ0FBQyx5QkFBeUIsQ0FBQztFQUNoRUMsS0FBSyxDQUFDQyxJQUFJLENBQUNKLEtBQUssQ0FBQyxDQUFDSyxPQUFPLENBQUMsVUFBVUMsSUFBSSxFQUFFO0lBQ3pDO0lBQ0EsSUFBSUMsU0FBUyxHQUFHRCxJQUFJLENBQUNFLFlBQVksQ0FBQyxhQUFhLENBQUM7SUFDaEQsSUFBSUQsU0FBUyxJQUFJLElBQUksS0FBS0EsU0FBUyxFQUFFO01BQ3BDO0lBQ0Q7SUFDQUQsSUFBSSxDQUFDRyxnQkFBZ0IsQ0FBQyxRQUFRLEVBQUUsVUFBQ0MsQ0FBQyxFQUFLO01BQ3RDQSxDQUFDLENBQUNDLGNBQWMsQ0FBQyxDQUFDO01BRWxCLElBQUk7UUFDSGIsVUFBVSxDQUFDYyxPQUFPLENBQUNDLFdBQVcsQ0FBQ0MsUUFBUSxFQUFFO1VBQUNDLE1BQU0sRUFBRTtRQUF1QixDQUFDLENBQUMsQ0FBQ0MsSUFBSSxDQUFDLFVBQVVDLEtBQUssRUFBRTtVQUNqRyxJQUFJQyxpQkFBaUIsR0FBR2pCLFFBQVEsQ0FBQ2tCLGFBQWEsQ0FBQyxPQUFPLENBQUM7VUFDdkRELGlCQUFpQixDQUFDRSxZQUFZLENBQUMsTUFBTSxFQUFFLFFBQVEsQ0FBQztVQUNoREYsaUJBQWlCLENBQUNFLFlBQVksQ0FBQyxNQUFNLEVBQUUsc0JBQXNCLENBQUM7VUFDOURGLGlCQUFpQixDQUFDRSxZQUFZLENBQUMsT0FBTyxFQUFFSCxLQUFLLENBQUM7VUFFOUNYLElBQUksQ0FBQ2UsTUFBTSxDQUFDSCxpQkFBaUIsQ0FBQ0ksU0FBUyxDQUFDLElBQUksQ0FBQyxDQUFDOztVQUU5QztVQUNBO1VBQ0E7VUFDQTtVQUNBLElBQUlDLFVBQVUsR0FBR3RCLFFBQVEsQ0FBQ2tCLGFBQWEsQ0FBQyxPQUFPLENBQUM7VUFDaERJLFVBQVUsQ0FBQ0gsWUFBWSxDQUFDLE1BQU0sRUFBRSxRQUFRLENBQUM7VUFDekNHLFVBQVUsQ0FBQ0gsWUFBWSxDQUFDLE1BQU0sRUFBRSxnQkFBZ0IsQ0FBQztVQUNqREcsVUFBVSxDQUFDSCxZQUFZLENBQUMsT0FBTyxFQUFFLE1BQU0sQ0FBQztVQUN4Q2QsSUFBSSxDQUFDZSxNQUFNLENBQUNFLFVBQVUsQ0FBQztVQUV2QmpCLElBQUksQ0FBQ2tCLE1BQU0sQ0FBQyxDQUFDO1FBQ2QsQ0FBQyxDQUFDO01BQ0gsQ0FBQyxDQUFDLE9BQU9DLEtBQUssRUFBRTtRQUNmQyxPQUFPLENBQUNDLEdBQUcsQ0FBQ0YsS0FBSyxDQUFDO1FBQ2xCLE9BQU8sS0FBSztNQUNiO0lBQ0QsQ0FBQyxDQUFDO0VBQ0gsQ0FBQyxDQUFDO0FBQ0gsQ0FBQyxDQUFDIiwiaWdub3JlTGlzdCI6W119\n//# sourceURL=webpack-internal:///./assets/js/ctct-plugin-recaptcha/recaptcha.js\n\n}");

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