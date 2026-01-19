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

/***/ "./assets/js/ctct-plugin-turnstile/index.js":
/*!**************************************************!*\
  !*** ./assets/js/ctct-plugin-turnstile/index.js ***!
  \**************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("{__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _turnstile__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./turnstile */ \"./assets/js/ctct-plugin-turnstile/turnstile.js\");\n/* harmony import */ var _turnstile__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_turnstile__WEBPACK_IMPORTED_MODULE_0__);\n// This is the entry point for Turnstile JS. Add JavaScript imports here.\n//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9hc3NldHMvanMvY3RjdC1wbHVnaW4tdHVybnN0aWxlL2luZGV4LmpzIiwibWFwcGluZ3MiOiI7OztBQUFBIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vY29uc3RhbnQtY29udGFjdC1mb3Jtcy8uL2Fzc2V0cy9qcy9jdGN0LXBsdWdpbi10dXJuc3RpbGUvaW5kZXguanM/ZWE3ZSJdLCJzb3VyY2VzQ29udGVudCI6WyIvLyBUaGlzIGlzIHRoZSBlbnRyeSBwb2ludCBmb3IgVHVybnN0aWxlIEpTLiBBZGQgSmF2YVNjcmlwdCBpbXBvcnRzIGhlcmUuXG5pbXBvcnQgJy4vdHVybnN0aWxlJztcbiJdLCJuYW1lcyI6W10sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./assets/js/ctct-plugin-turnstile/index.js\n\n}");

/***/ }),

/***/ "./assets/js/ctct-plugin-turnstile/turnstile.js":
/*!******************************************************!*\
  !*** ./assets/js/ctct-plugin-turnstile/turnstile.js ***!
  \******************************************************/
/***/ (function() {

eval("{function _toConsumableArray(r) { return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread(); }\nfunction _nonIterableSpread() { throw new TypeError(\"Invalid attempt to spread non-iterable instance.\\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.\"); }\nfunction _unsupportedIterableToArray(r, a) { if (r) { if (\"string\" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return \"Object\" === t && r.constructor && (t = r.constructor.name), \"Map\" === t || \"Set\" === t ? Array.from(r) : \"Arguments\" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }\nfunction _iterableToArray(r) { if (\"undefined\" != typeof Symbol && null != r[Symbol.iterator] || null != r[\"@@iterator\"]) return Array.from(r); }\nfunction _arrayWithoutHoles(r) { if (Array.isArray(r)) return _arrayLikeToArray(r); }\nfunction _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }\n/**\n * Enable submit button.\n *\n * @since 2.15.1\n *\n * @param {Object} submitBtn Submit DOM element.\n */\nwindow.ctctTurnstileEnableBtn = function (submitBtn) {\n  submitBtn.removeAttribute('disabled');\n};\n\n/**\n * Disable submit button.\n *\n * @since 2.15.1\n *\n * @param {Object} submitBtn Submit DOM element.\n */\nwindow.ctctTurnstileDisableBtn = function (submitBtn) {\n  submitBtn.setAttribute('disabled', 'disabled');\n};\n\n/**\n * Render turnstiles.\n *\n * @since 2.15.1\n *\n */\nwindow.onload = function () {\n  var turnstiles = document.querySelectorAll('.turnstile');\n  Array.from(turnstiles).forEach(function (turnstileobj) {\n    var submitBtn = '';\n    var siblings = _toConsumableArray(turnstileobj.parentElement.children);\n    siblings.forEach(function (item) {\n      if (item.classList.contains('ctct-form-field-submit')) {\n        submitBtn = document.querySelector(\"#\" + item.children[0].id);\n      }\n    });\n    turnstile.render(turnstileobj, {\n      'sitekey': turnstileobj.getAttribute('data-sitekey', ''),\n      'size': turnstileobj.getAttribute('data-size', ''),\n      'tabindex': turnstileobj.getAttribute('data-tabindex', ''),\n      'callback': function callback() {\n        if (submitBtn) {\n          window.ctctTurnstileEnableBtn(submitBtn);\n        }\n      },\n      'expired-callback': function expiredCallback() {\n        if (submitBtn) {\n          window.ctctTurnstileDisableBtn(submitBtn);\n        }\n      },\n      'isolated': true\n    });\n  });\n};//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9hc3NldHMvanMvY3RjdC1wbHVnaW4tdHVybnN0aWxlL3R1cm5zdGlsZS5qcyIsIm5hbWVzIjpbIndpbmRvdyIsImN0Y3RUdXJuc3RpbGVFbmFibGVCdG4iLCJzdWJtaXRCdG4iLCJyZW1vdmVBdHRyaWJ1dGUiLCJjdGN0VHVybnN0aWxlRGlzYWJsZUJ0biIsInNldEF0dHJpYnV0ZSIsIm9ubG9hZCIsInR1cm5zdGlsZXMiLCJkb2N1bWVudCIsInF1ZXJ5U2VsZWN0b3JBbGwiLCJBcnJheSIsImZyb20iLCJmb3JFYWNoIiwidHVybnN0aWxlb2JqIiwic2libGluZ3MiLCJfdG9Db25zdW1hYmxlQXJyYXkiLCJwYXJlbnRFbGVtZW50IiwiY2hpbGRyZW4iLCJpdGVtIiwiY2xhc3NMaXN0IiwiY29udGFpbnMiLCJxdWVyeVNlbGVjdG9yIiwiaWQiLCJ0dXJuc3RpbGUiLCJyZW5kZXIiLCJnZXRBdHRyaWJ1dGUiLCJjYWxsYmFjayIsImV4cGlyZWRDYWxsYmFjayJdLCJzb3VyY2VSb290IjoiIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vY29uc3RhbnQtY29udGFjdC1mb3Jtcy8uL2Fzc2V0cy9qcy9jdGN0LXBsdWdpbi10dXJuc3RpbGUvdHVybnN0aWxlLmpzPzYyZTQiXSwic291cmNlc0NvbnRlbnQiOlsiLyoqXG4gKiBFbmFibGUgc3VibWl0IGJ1dHRvbi5cbiAqXG4gKiBAc2luY2UgMi4xNS4xXG4gKlxuICogQHBhcmFtIHtPYmplY3R9IHN1Ym1pdEJ0biBTdWJtaXQgRE9NIGVsZW1lbnQuXG4gKi9cbndpbmRvdy5jdGN0VHVybnN0aWxlRW5hYmxlQnRuID0gZnVuY3Rpb24gKHN1Ym1pdEJ0bikge1xuXHRzdWJtaXRCdG4ucmVtb3ZlQXR0cmlidXRlKCdkaXNhYmxlZCcpO1xufTtcblxuLyoqXG4gKiBEaXNhYmxlIHN1Ym1pdCBidXR0b24uXG4gKlxuICogQHNpbmNlIDIuMTUuMVxuICpcbiAqIEBwYXJhbSB7T2JqZWN0fSBzdWJtaXRCdG4gU3VibWl0IERPTSBlbGVtZW50LlxuICovXG53aW5kb3cuY3RjdFR1cm5zdGlsZURpc2FibGVCdG4gPSBmdW5jdGlvbiAoc3VibWl0QnRuKSB7XG5cdHN1Ym1pdEJ0bi5zZXRBdHRyaWJ1dGUoJ2Rpc2FibGVkJywgJ2Rpc2FibGVkJyk7XG59XG5cbi8qKlxuICogUmVuZGVyIHR1cm5zdGlsZXMuXG4gKlxuICogQHNpbmNlIDIuMTUuMVxuICpcbiAqL1xud2luZG93Lm9ubG9hZCA9IGZ1bmN0aW9uICgpIHtcblx0bGV0IHR1cm5zdGlsZXMgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yQWxsKCAnLnR1cm5zdGlsZScgKTtcblxuXHRBcnJheS5mcm9tKHR1cm5zdGlsZXMpLmZvckVhY2goZnVuY3Rpb24gKHR1cm5zdGlsZW9iaikge1xuXHRcdFx0bGV0IHN1Ym1pdEJ0biA9ICcnO1xuXHRcdFx0Y29uc3Qgc2libGluZ3MgPSBbLi4udHVybnN0aWxlb2JqLnBhcmVudEVsZW1lbnQuY2hpbGRyZW5dO1xuXHRcdFx0c2libGluZ3MuZm9yRWFjaChmdW5jdGlvbihpdGVtKXtcblx0XHRcdFx0XHRpZiAoIGl0ZW0uY2xhc3NMaXN0LmNvbnRhaW5zKCdjdGN0LWZvcm0tZmllbGQtc3VibWl0JykgKSB7XG5cdFx0XHRcdFx0XHRcdHN1Ym1pdEJ0biA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoXCIjXCIgKyBpdGVtLmNoaWxkcmVuWzBdLmlkKTtcblx0XHRcdFx0XHR9XG5cdFx0XHR9KTtcblx0XHRcdHR1cm5zdGlsZS5yZW5kZXIodHVybnN0aWxlb2JqLCB7XG5cdFx0XHRcdFx0J3NpdGVrZXknICA6IHR1cm5zdGlsZW9iai5nZXRBdHRyaWJ1dGUoJ2RhdGEtc2l0ZWtleScsICcnKSxcblx0XHRcdFx0XHQnc2l6ZScgICAgIDogdHVybnN0aWxlb2JqLmdldEF0dHJpYnV0ZSgnZGF0YS1zaXplJywgJycpLFxuXHRcdFx0XHRcdCd0YWJpbmRleCcgOiB0dXJuc3RpbGVvYmouZ2V0QXR0cmlidXRlKCdkYXRhLXRhYmluZGV4JywgJycpLFxuXHRcdFx0XHRcdCdjYWxsYmFjaycgOiBmdW5jdGlvbiAoKSB7XG5cdFx0XHRcdFx0XHRcdGlmICggc3VibWl0QnRuICkge1xuXHRcdFx0XHRcdFx0XHRcdHdpbmRvdy5jdGN0VHVybnN0aWxlRW5hYmxlQnRuKHN1Ym1pdEJ0bik7XG5cdFx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9LFxuXHRcdFx0XHRcdCdleHBpcmVkLWNhbGxiYWNrJzogZnVuY3Rpb24gKCkge1xuXHRcdFx0XHRcdFx0XHRpZiAoIHN1Ym1pdEJ0biApIHtcblx0XHRcdFx0XHRcdFx0XHR3aW5kb3cuY3RjdFR1cm5zdGlsZURpc2FibGVCdG4oc3VibWl0QnRuKTtcblx0XHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdH0sXG5cdFx0XHRcdFx0J2lzb2xhdGVkJyA6IHRydWUsXG5cdFx0XHR9KTtcblx0fSk7XG59O1xuIl0sIm1hcHBpbmdzIjoiOzs7Ozs7QUFBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBQSxNQUFNLENBQUNDLHNCQUFzQixHQUFHLFVBQVVDLFNBQVMsRUFBRTtFQUNwREEsU0FBUyxDQUFDQyxlQUFlLENBQUMsVUFBVSxDQUFDO0FBQ3RDLENBQUM7O0FBRUQ7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQUgsTUFBTSxDQUFDSSx1QkFBdUIsR0FBRyxVQUFVRixTQUFTLEVBQUU7RUFDckRBLFNBQVMsQ0FBQ0csWUFBWSxDQUFDLFVBQVUsRUFBRSxVQUFVLENBQUM7QUFDL0MsQ0FBQzs7QUFFRDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQUwsTUFBTSxDQUFDTSxNQUFNLEdBQUcsWUFBWTtFQUMzQixJQUFJQyxVQUFVLEdBQUdDLFFBQVEsQ0FBQ0MsZ0JBQWdCLENBQUUsWUFBYSxDQUFDO0VBRTFEQyxLQUFLLENBQUNDLElBQUksQ0FBQ0osVUFBVSxDQUFDLENBQUNLLE9BQU8sQ0FBQyxVQUFVQyxZQUFZLEVBQUU7SUFDckQsSUFBSVgsU0FBUyxHQUFHLEVBQUU7SUFDbEIsSUFBTVksUUFBUSxHQUFBQyxrQkFBQSxDQUFPRixZQUFZLENBQUNHLGFBQWEsQ0FBQ0MsUUFBUSxDQUFDO0lBQ3pESCxRQUFRLENBQUNGLE9BQU8sQ0FBQyxVQUFTTSxJQUFJLEVBQUM7TUFDN0IsSUFBS0EsSUFBSSxDQUFDQyxTQUFTLENBQUNDLFFBQVEsQ0FBQyx3QkFBd0IsQ0FBQyxFQUFHO1FBQ3ZEbEIsU0FBUyxHQUFHTSxRQUFRLENBQUNhLGFBQWEsQ0FBQyxHQUFHLEdBQUdILElBQUksQ0FBQ0QsUUFBUSxDQUFDLENBQUMsQ0FBQyxDQUFDSyxFQUFFLENBQUM7TUFDL0Q7SUFDRixDQUFDLENBQUM7SUFDRkMsU0FBUyxDQUFDQyxNQUFNLENBQUNYLFlBQVksRUFBRTtNQUM3QixTQUFTLEVBQUlBLFlBQVksQ0FBQ1ksWUFBWSxDQUFDLGNBQWMsRUFBRSxFQUFFLENBQUM7TUFDMUQsTUFBTSxFQUFPWixZQUFZLENBQUNZLFlBQVksQ0FBQyxXQUFXLEVBQUUsRUFBRSxDQUFDO01BQ3ZELFVBQVUsRUFBR1osWUFBWSxDQUFDWSxZQUFZLENBQUMsZUFBZSxFQUFFLEVBQUUsQ0FBQztNQUMzRCxVQUFVLEVBQUcsU0FBYkMsUUFBVUEsQ0FBQSxFQUFlO1FBQ3ZCLElBQUt4QixTQUFTLEVBQUc7VUFDaEJGLE1BQU0sQ0FBQ0Msc0JBQXNCLENBQUNDLFNBQVMsQ0FBQztRQUN6QztNQUNGLENBQUM7TUFDRCxrQkFBa0IsRUFBRSxTQUFwQnlCLGVBQWtCQSxDQUFBLEVBQWM7UUFDOUIsSUFBS3pCLFNBQVMsRUFBRztVQUNoQkYsTUFBTSxDQUFDSSx1QkFBdUIsQ0FBQ0YsU0FBUyxDQUFDO1FBQzFDO01BQ0YsQ0FBQztNQUNELFVBQVUsRUFBRztJQUNmLENBQUMsQ0FBQztFQUNKLENBQUMsQ0FBQztBQUNILENBQUMiLCJpZ25vcmVMaXN0IjpbXX0=\n//# sourceURL=webpack-internal:///./assets/js/ctct-plugin-turnstile/turnstile.js\n\n}");

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
/******/ 	var __webpack_exports__ = __webpack_require__("./assets/js/ctct-plugin-turnstile/index.js");
/******/ 	
/******/ })()
;