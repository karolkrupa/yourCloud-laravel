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
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 37);
/******/ })
/************************************************************************/
/******/ ({

/***/ 37:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(38);


/***/ }),

/***/ 38:
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(39);

/***/ }),

/***/ 39:
/***/ (function(module, exports) {

$('#update-full-name').click(function (event) {
    var fullName = $('#full-name');
    $.post('/api/v1/user/update/fullName', { full_name: fullName.val() }).done(function (response) {
        if (App.config.debug) {
            console.info('[dataUpdates] Recived: ' + JSON.stringify(response));
        }

        fullName.val(response.new_full_name);
        YourCloud.addAlert(response.message, 'success');
    }).fail(function (response) {
        if (App.config.debug) {
            console.error('[dataUpdates] Recived: ' + JSON.stringify(response));
        }

        YourCloud.addAlert(response.responseJSON.message, 'warning');
    });
});

$('#update-language').click(function (event) {
    var lang = $('#selected-language');
    $.post('/api/v1/user/update/language', { lang: lang.val() }).done(function (response) {
        if (App.config.debug) {
            console.info('[dataUpdates] Recived: ' + JSON.stringify(response));
        }

        // lang.val(response.new_full_name);
        YourCloud.addAlert(response.message, 'success');
        setTimeout(function () {
            location.reload();
        }, 3000);
    }).fail(function (response) {
        if (App.config.debug) {
            console.error('[dataUpdates] Recived: ' + JSON.stringify(response));
        }

        YourCloud.addAlert(response.responseJSON.message, 'warning');
    });
});

$('#update-password').click(function (event) {
    var password = $('#password');
    var password_repeat = $('#password-repeat');
    $.post('/api/v1/user/update/password', { password: password.val(), password_confirmation: password_repeat.val() }).done(function (response) {
        if (App.config.debug) {
            console.info('[dataUpdates] Recived: ' + JSON.stringify(response));
        }

        password.val('');
        password_repeat.val('');
        YourCloud.addAlert(response.message, 'success');
    }).fail(function (response) {
        if (App.config.debug) {
            console.error('[dataUpdates] Recived: ' + JSON.stringify(response));
        }

        YourCloud.addAlert(response.responseJSON.message, 'warning');
    });
});

/***/ })

/******/ });