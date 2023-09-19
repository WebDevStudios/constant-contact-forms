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

/***/ "./assets/js/ctct-plugin-recaptcha-v2/index.js":
/*!*****************************************************!*\
  !*** ./assets/js/ctct-plugin-recaptcha-v2/index.js ***!
  \*****************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _recaptcha__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./recaptcha */ \"./assets/js/ctct-plugin-recaptcha-v2/recaptcha.js\");\n/* harmony import */ var _recaptcha__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_recaptcha__WEBPACK_IMPORTED_MODULE_0__);\n// This is the entry point for reCAPTCHA v2 JS. Add JavaScript imports here.\n//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9hc3NldHMvanMvY3RjdC1wbHVnaW4tcmVjYXB0Y2hhLXYyL2luZGV4LmpzIiwibWFwcGluZ3MiOiI7OztBQUFBIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vY29uc3RhbnQtY29udGFjdC1mb3Jtcy8uL2Fzc2V0cy9qcy9jdGN0LXBsdWdpbi1yZWNhcHRjaGEtdjIvaW5kZXguanM/OTViZSJdLCJzb3VyY2VzQ29udGVudCI6WyIvLyBUaGlzIGlzIHRoZSBlbnRyeSBwb2ludCBmb3IgcmVDQVBUQ0hBIHYyIEpTLiBBZGQgSmF2YVNjcmlwdCBpbXBvcnRzIGhlcmUuXG5pbXBvcnQgJy4vcmVjYXB0Y2hhJztcbiJdLCJuYW1lcyI6W10sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./assets/js/ctct-plugin-recaptcha-v2/index.js\n");

/***/ }),

/***/ "./assets/js/ctct-plugin-recaptcha-v2/recaptcha.js":
/*!*********************************************************!*\
  !*** ./assets/js/ctct-plugin-recaptcha-v2/recaptcha.js ***!
  \*********************************************************/
/***/ (function() {

eval("function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }\nfunction _nonIterableSpread() { throw new TypeError(\"Invalid attempt to spread non-iterable instance.\\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.\"); }\nfunction _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === \"string\") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === \"Object\" && o.constructor) n = o.constructor.name; if (n === \"Map\" || n === \"Set\") return Array.from(o); if (n === \"Arguments\" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }\nfunction _iterableToArray(iter) { if (typeof Symbol !== \"undefined\" && iter[Symbol.iterator] != null || iter[\"@@iterator\"] != null) return Array.from(iter); }\nfunction _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }\nfunction _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }\n/**\n * Enable submit button.\n *\n * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>\n * @since  1.8.3\n *\n * @param  {Object} submitBtn Submit DOM element.\n */\nwindow.ctctEnableBtn = function (submitBtn) {\n  submitBtn.setAttribute('disabled', false);\n};\n\n/**\n * Disable submit button.\n *\n * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>\n * @since  1.8.3\n *\n * @param  {Object} submitBtn Submit DOM element.\n */\nwindow.ctctDisableBtn = function (submitBtn) {\n  submitBtn.setAttribute('disabled', true);\n};\nwindow.renderReCaptcha = function () {\n  var grecaptchas = document.querySelectorAll('.g-recaptcha');\n  Array.from(grecaptchas).forEach(function (grecaptchaobj, index) {\n    var submitBtn = ''; /*jQuery(grecaptchaobj).siblings('.ctct-form-field-submit').find('.ctct-submit');*/\n    var siblings = _toConsumableArray(grecaptchaobj.parentElement.children);\n    siblings.forEach(function (item) {\n      if (item.classList.contains('ctct-form-field-submit')) {\n        submitBtn = document.querySelector(\"#\" + item.children[0].id);\n      }\n    });\n    grecaptcha.render(grecaptchaobj, {\n      'sitekey': grecaptchaobj.getAttribute('data-sitekey', ''),\n      'size': grecaptchaobj.getAttribute('data-size', ''),\n      'tabindex': grecaptchaobj.getAttribute('data-tabindex', ''),\n      'callback': function callback() {\n        window.ctctEnableBtn(submitBtn);\n      },\n      'expired-callback': function expiredCallback() {\n        window.ctctDisableBtn(submitBtn);\n      },\n      'isolated': true\n    });\n  });\n};//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9hc3NldHMvanMvY3RjdC1wbHVnaW4tcmVjYXB0Y2hhLXYyL3JlY2FwdGNoYS5qcyIsIm5hbWVzIjpbIndpbmRvdyIsImN0Y3RFbmFibGVCdG4iLCJzdWJtaXRCdG4iLCJzZXRBdHRyaWJ1dGUiLCJjdGN0RGlzYWJsZUJ0biIsInJlbmRlclJlQ2FwdGNoYSIsImdyZWNhcHRjaGFzIiwiZG9jdW1lbnQiLCJxdWVyeVNlbGVjdG9yQWxsIiwiQXJyYXkiLCJmcm9tIiwiZm9yRWFjaCIsImdyZWNhcHRjaGFvYmoiLCJpbmRleCIsInNpYmxpbmdzIiwiX3RvQ29uc3VtYWJsZUFycmF5IiwicGFyZW50RWxlbWVudCIsImNoaWxkcmVuIiwiaXRlbSIsImNsYXNzTGlzdCIsImNvbnRhaW5zIiwicXVlcnlTZWxlY3RvciIsImlkIiwiZ3JlY2FwdGNoYSIsInJlbmRlciIsImdldEF0dHJpYnV0ZSIsImNhbGxiYWNrIiwiZXhwaXJlZENhbGxiYWNrIl0sInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9jb25zdGFudC1jb250YWN0LWZvcm1zLy4vYXNzZXRzL2pzL2N0Y3QtcGx1Z2luLXJlY2FwdGNoYS12Mi9yZWNhcHRjaGEuanM/OWE0NCJdLCJzb3VyY2VzQ29udGVudCI6WyIvKipcbiAqIEVuYWJsZSBzdWJtaXQgYnV0dG9uLlxuICpcbiAqIEBhdXRob3IgUmViZWthaCBWYW4gRXBwcyA8cmViZWthaC52YW5lcHBzQHdlYmRldnN0dWRpb3MuY29tPlxuICogQHNpbmNlICAxLjguM1xuICpcbiAqIEBwYXJhbSAge09iamVjdH0gc3VibWl0QnRuIFN1Ym1pdCBET00gZWxlbWVudC5cbiAqL1xud2luZG93LmN0Y3RFbmFibGVCdG4gPSBmdW5jdGlvbiAoc3VibWl0QnRuKSB7XG4gICAgc3VibWl0QnRuLnNldEF0dHJpYnV0ZSgnZGlzYWJsZWQnLCBmYWxzZSk7XG59O1xuXG4vKipcbiAqIERpc2FibGUgc3VibWl0IGJ1dHRvbi5cbiAqXG4gKiBAYXV0aG9yIFJlYmVrYWggVmFuIEVwcHMgPHJlYmVrYWgudmFuZXBwc0B3ZWJkZXZzdHVkaW9zLmNvbT5cbiAqIEBzaW5jZSAgMS44LjNcbiAqXG4gKiBAcGFyYW0gIHtPYmplY3R9IHN1Ym1pdEJ0biBTdWJtaXQgRE9NIGVsZW1lbnQuXG4gKi9cbndpbmRvdy5jdGN0RGlzYWJsZUJ0biA9IGZ1bmN0aW9uIChzdWJtaXRCdG4pIHtcbiAgICBzdWJtaXRCdG4uc2V0QXR0cmlidXRlKCdkaXNhYmxlZCcsIHRydWUpO1xufVxuXG5cbndpbmRvdy5yZW5kZXJSZUNhcHRjaGEgPSBmdW5jdGlvbiAoKSB7XG4gICAgbGV0IGdyZWNhcHRjaGFzID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbCggJy5nLXJlY2FwdGNoYScgKTtcblxuICAgIEFycmF5LmZyb20oZ3JlY2FwdGNoYXMpLmZvckVhY2goZnVuY3Rpb24gKGdyZWNhcHRjaGFvYmosIGluZGV4KSB7XG4gICAgICAgIGxldCBzdWJtaXRCdG4gPSAnJzsgLypqUXVlcnkoZ3JlY2FwdGNoYW9iaikuc2libGluZ3MoJy5jdGN0LWZvcm0tZmllbGQtc3VibWl0JykuZmluZCgnLmN0Y3Qtc3VibWl0Jyk7Ki9cbiAgICAgICAgY29uc3Qgc2libGluZ3MgPSBbLi4uZ3JlY2FwdGNoYW9iai5wYXJlbnRFbGVtZW50LmNoaWxkcmVuXTtcbiAgICAgICAgc2libGluZ3MuZm9yRWFjaChmdW5jdGlvbihpdGVtKXtcbiAgICAgICAgICAgIGlmICggaXRlbS5jbGFzc0xpc3QuY29udGFpbnMoJ2N0Y3QtZm9ybS1maWVsZC1zdWJtaXQnKSApIHtcbiAgICAgICAgICAgICAgICBzdWJtaXRCdG4gPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKFwiI1wiICsgaXRlbS5jaGlsZHJlblswXS5pZCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuICAgICAgICBncmVjYXB0Y2hhLnJlbmRlcihncmVjYXB0Y2hhb2JqLCB7XG4gICAgICAgICAgICAnc2l0ZWtleScgICAgICAgICA6IGdyZWNhcHRjaGFvYmouZ2V0QXR0cmlidXRlKCdkYXRhLXNpdGVrZXknLCAnJyksXG4gICAgICAgICAgICAnc2l6ZScgICAgICAgICAgICA6IGdyZWNhcHRjaGFvYmouZ2V0QXR0cmlidXRlKCdkYXRhLXNpemUnLCAnJyksXG4gICAgICAgICAgICAndGFiaW5kZXgnICAgICAgICA6IGdyZWNhcHRjaGFvYmouZ2V0QXR0cmlidXRlKCdkYXRhLXRhYmluZGV4JywgJycpLFxuICAgICAgICAgICAgJ2NhbGxiYWNrJyAgICAgICAgOiBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgd2luZG93LmN0Y3RFbmFibGVCdG4oc3VibWl0QnRuKTtcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAnZXhwaXJlZC1jYWxsYmFjayc6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICB3aW5kb3cuY3RjdERpc2FibGVCdG4oc3VibWl0QnRuKTtcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAnaXNvbGF0ZWQnICAgICAgICA6IHRydWUsXG4gICAgICAgIH0pO1xuICAgIH0pO1xufTtcbiJdLCJtYXBwaW5ncyI6Ijs7Ozs7O0FBQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBQSxNQUFNLENBQUNDLGFBQWEsR0FBRyxVQUFVQyxTQUFTLEVBQUU7RUFDeENBLFNBQVMsQ0FBQ0MsWUFBWSxDQUFDLFVBQVUsRUFBRSxLQUFLLENBQUM7QUFDN0MsQ0FBQzs7QUFFRDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0FILE1BQU0sQ0FBQ0ksY0FBYyxHQUFHLFVBQVVGLFNBQVMsRUFBRTtFQUN6Q0EsU0FBUyxDQUFDQyxZQUFZLENBQUMsVUFBVSxFQUFFLElBQUksQ0FBQztBQUM1QyxDQUFDO0FBR0RILE1BQU0sQ0FBQ0ssZUFBZSxHQUFHLFlBQVk7RUFDakMsSUFBSUMsV0FBVyxHQUFHQyxRQUFRLENBQUNDLGdCQUFnQixDQUFFLGNBQWUsQ0FBQztFQUU3REMsS0FBSyxDQUFDQyxJQUFJLENBQUNKLFdBQVcsQ0FBQyxDQUFDSyxPQUFPLENBQUMsVUFBVUMsYUFBYSxFQUFFQyxLQUFLLEVBQUU7SUFDNUQsSUFBSVgsU0FBUyxHQUFHLEVBQUUsQ0FBQyxDQUFDO0lBQ3BCLElBQU1ZLFFBQVEsR0FBQUMsa0JBQUEsQ0FBT0gsYUFBYSxDQUFDSSxhQUFhLENBQUNDLFFBQVEsQ0FBQztJQUMxREgsUUFBUSxDQUFDSCxPQUFPLENBQUMsVUFBU08sSUFBSSxFQUFDO01BQzNCLElBQUtBLElBQUksQ0FBQ0MsU0FBUyxDQUFDQyxRQUFRLENBQUMsd0JBQXdCLENBQUMsRUFBRztRQUNyRGxCLFNBQVMsR0FBR0ssUUFBUSxDQUFDYyxhQUFhLENBQUMsR0FBRyxHQUFHSCxJQUFJLENBQUNELFFBQVEsQ0FBQyxDQUFDLENBQUMsQ0FBQ0ssRUFBRSxDQUFDO01BQ2pFO0lBQ0osQ0FBQyxDQUFDO0lBQ0ZDLFVBQVUsQ0FBQ0MsTUFBTSxDQUFDWixhQUFhLEVBQUU7TUFDN0IsU0FBUyxFQUFXQSxhQUFhLENBQUNhLFlBQVksQ0FBQyxjQUFjLEVBQUUsRUFBRSxDQUFDO01BQ2xFLE1BQU0sRUFBY2IsYUFBYSxDQUFDYSxZQUFZLENBQUMsV0FBVyxFQUFFLEVBQUUsQ0FBQztNQUMvRCxVQUFVLEVBQVViLGFBQWEsQ0FBQ2EsWUFBWSxDQUFDLGVBQWUsRUFBRSxFQUFFLENBQUM7TUFDbkUsVUFBVSxFQUFVLFNBQUFDLFNBQUEsRUFBWTtRQUM1QjFCLE1BQU0sQ0FBQ0MsYUFBYSxDQUFDQyxTQUFTLENBQUM7TUFDbkMsQ0FBQztNQUNELGtCQUFrQixFQUFFLFNBQUF5QixnQkFBQSxFQUFZO1FBQzVCM0IsTUFBTSxDQUFDSSxjQUFjLENBQUNGLFNBQVMsQ0FBQztNQUNwQyxDQUFDO01BQ0QsVUFBVSxFQUFVO0lBQ3hCLENBQUMsQ0FBQztFQUNOLENBQUMsQ0FBQztBQUNOLENBQUMifQ==\n//# sourceURL=webpack-internal:///./assets/js/ctct-plugin-recaptcha-v2/recaptcha.js\n");

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
/******/ 	var __webpack_exports__ = __webpack_require__("./assets/js/ctct-plugin-recaptcha-v2/index.js");
/******/ 	
/******/ })()
;