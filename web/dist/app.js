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
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
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
/******/ 	__webpack_require__.p = "/dist/";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./assets/js/app.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./assets/js/app.js":
/*!**************************!*\
  !*** ./assets/js/app.js ***!
  \**************************/
/*! dynamic exports provided */
/*! all exports used */
/***/ (function(module, exports, __webpack_require__) {


/* scripts */
__webpack_require__(/*! ./menu */ "./assets/js/menu.js")();

/* style */
__webpack_require__(/*! ./../scss/main.scss */ "./assets/scss/main.scss");

/***/ }),

/***/ "./assets/js/menu.js":
/*!***************************!*\
  !*** ./assets/js/menu.js ***!
  \***************************/
/*! dynamic exports provided */
/*! all exports used */
/***/ (function(module, exports) {

function scanLi(li, clickCallable) {

    var submenu = li.querySelector(':scope > ul');

    if (submenu !== null) {

        submenu.originalHeight = submenu.offsetHeight + "px";

        Array.from(submenu.querySelectorAll(':scope > li')).forEach(function (childLi) {

            scanLi(childLi, function (event) {

                event.stopPropagation();
                toggle(childLi);
            });
        });

        submenu.style.height = "0px";
    }

    li.onclick = clickCallable;
}

function toggle(li) {

    if (li.classList.contains("active")) closeLi(li);else openLi(li);
}

function openLi(li) {

    li.classList.add('active');

    var submenu = li.querySelector(':scope > ul');

    if (submenu) submenu.style.height = submenu.originalHeight;
}

function closeLi(li) {

    li.classList.remove('active');

    var submenu = li.querySelector(':scope > ul');

    if (submenu) submenu.style.height = "0px";

    Array.from(li.querySelectorAll('li')).forEach(function (childLi) {
        closeLi(childLi);
    });
}

module.exports = function () {

    var linodelist = document.querySelectorAll('.menu-content > ul > li:not(.menu-category)');
    var lis = Array.from(linodelist);

    lis.forEach(function (topli) {

        scanLi(topli, function (event) {

            event.stopPropagation();

            //Close other top level lis
            lis.forEach(function (toclose) {
                if (toclose !== topli) closeLi(toclose);
            });

            toggle(topli);
        });
    });

    //Init active link if any
    var cntnt = document.querySelector('.menu-content');
    var item = cntnt.querySelector('li.active');

    while (item) {

        if (item.tagName === 'LI') openLi(item);

        if (item === cntnt) break;

        item = item.parentElement;
    }
};

/***/ }),

/***/ "./assets/scss/main.scss":
/*!*******************************!*\
  !*** ./assets/scss/main.scss ***!
  \*******************************/
/*! dynamic exports provided */
/*! all exports used */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ })

/******/ });