(function ( $, x5engine ) {
	var x = x5engine,
		s = x.settings,
		p = s.currentPath,
		b = x.boot;

	s.siteId = '5E8FA260368C3F6858325D74818F1C02';
	s.version = '16-3-1-1';
	b.push(function () {
		x.setupDateTime();
		x.imAccess.showLogout();
		x.utils.autoHeight();
		x.cart.ui.updateWidget();
		x.imGrid.init();
	});

	// ShowBox
	$.extend(s.imShowBox, {
		"effect": "none", "customEffect": "generic animated none",
		'transitionEffect' : 'fade',
		'fullScreenEnabled' : true,
		'zoomEnabled' : true,
		'showProgress' : true,
		'shadow' : '',
		'background' : 'rgba(55, 71, 79, 1)',
		'borderWidth' : {
			'top': 0,
			'right': 0,
			'bottom': 0,
			'left': 0
		},
		'buttonLeft': '<svg class=\"im-common-left-button\"  xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 50 50\"><defs><style>.a{fill:#3e3d40;opacity:0.5;}.b{fill:#f6f6f6;}<\/style><\/defs><rect class=\"a\" x=\"5.02\" y=\"5\" width=\"39.94\" height=\"39.94\" rx=\"3.54\" ry=\"3.54\"/><path class=\"b\" d=\"M23.75,9.2a1.17,1.17,0,0,1,1.63,0,1.13,1.13,0,0,1,0,1.61l-13,13.3,27.64-.22h0a1.14,1.14,0,1,1,0,2.28l-27.63.22L25.94,39.16a1.13,1.13,0,0,1,0,1.61,1.17,1.17,0,0,1-.83.34,1.14,1.14,0,0,1-.8-.32L9.46,26.65a2,2,0,0,1-.05-2.79Z\"/><\/svg>',
		'buttonRight': '<svg class=\"im-common-right-button\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 50 50\"><defs><style>.a{fill:#3e3d40;opacity:0.5;}.b{fill:#f6f6f6;}<\/style><\/defs><rect class=\"a\" x=\"5.02\" y=\"5\" width=\"39.94\" height=\"39.94\" rx=\"3.54\" ry=\"3.54\"/><path class=\"b\" d=\"M26.32,9.2a1.17,1.17,0,0,0-1.63,0,1.13,1.13,0,0,0,0,1.61l13,13.3L10,23.84h0a1.14,1.14,0,1,0,0,2.28l27.63.22L24.14,39.16a1.13,1.13,0,0,0,0,1.61,1.17,1.17,0,0,0,.83.34,1.14,1.14,0,0,0,.8-.32L40.61,26.65a2,2,0,0,0,.05-2.79Z\"/><\/svg>',
		'buttonClose': '<svg class=\"im-common-close-button\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 50 50\"><defs><style>.a{fill:#3e3d40;opacity:0.5;}.b{fill:#f6f6f6;}<\/style><\/defs><rect class=\"a\" x=\"5.02\" y=\"5.03\" width=\"39.94\" height=\"39.94\" rx=\"3.54\" ry=\"3.54\"/><path class=\"b\" d=\"M36.55,34.69l-9.76-9.76,9.7-9.82a1.21,1.21,0,0,0,0-1.7,1.2,1.2,0,0,0-1.69,0L25.1,23.24l-9.77-9.76a1.18,1.18,0,0,0-.84-.35h0a1.2,1.2,0,0,0-.84,2.05l9.76,9.77-9.7,9.82a1.21,1.21,0,0,0,0,1.7,1.18,1.18,0,0,0,.84.35h0a1.18,1.18,0,0,0,.82-.35l9.69-9.82,9.76,9.76a1.2,1.2,0,0,0,.87.35,1.17,1.17,0,0,0,.82-.36A1.21,1.21,0,0,0,36.55,34.69Z\"/><\/svg>',
		'buttonEnterFS': '<svg class=\"im-common-enter-fs-button\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 50 50\"><defs><style>.a{fill:#3e3d40;opacity:0.5;}.b{fill:#f6f6f6;}<\/style><\/defs><rect class=\"a\" x=\"5.02\" y=\"5.03\" width=\"39.94\" height=\"39.94\" rx=\"3.54\" ry=\"3.54\"/><path class=\"b\" d=\"M39.1,21.41a1,1,0,0,0,1-1V10.9a1,1,0,0,0-1-1H29.6a1,1,0,1,0,0,2h7.06L25,23.58,13.31,11.91h7.06a1,1,0,0,0,0-2h-9.5a1,1,0,0,0-1,1v9.5a1,1,0,0,0,2,0V13.34L23.56,25,11.88,36.69V29.63a1,1,0,1,0-2,0v9.5a1,1,0,0,0,1,1h9.5a1,1,0,0,0,0-2H13.31L25,26.44,36.66,38.11H29.6a1,1,0,1,0,0,2h9.5a1,1,0,0,0,1-1v-9.5a1,1,0,1,0-2,0v7.06L26.41,25,38.09,13.34V20.4A1,1,0,0,0,39.1,21.41Z\"/><\/svg>',
		'buttonExitFS': '<svg class=\"im-common-exit-fs-button\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 50 50\"><defs><style>.a{fill:#fff;opacity:0.5;}.b{fill:#3e3d40;}<\/style><\/defs><rect class=\"a\" x=\"5.03\" y=\"5.03\" width=\"39.94\" height=\"39.94\" rx=\"3.54\" ry=\"3.54\"/><path class=\"b\" d=\"M35.37,13.75l-12.9,13V19.42a1.15,1.15,0,0,0-2.3,0V29.53a1.15,1.15,0,0,0,1.15,1.15H31.69a1.15,1.15,0,0,0,0-2.3H24.1l12.9-13a1.15,1.15,0,0,0-1.63-1.63Z\"/><path class=\"b\" d=\"M37.33,18.7a1.18,1.18,0,0,0-1.18,1.18v15H13.85V15.08H30.43a1.18,1.18,0,1,0,0-2.36H12.67a1.18,1.18,0,0,0-1.18,1.18V36.1a1.18,1.18,0,0,0,1.18,1.18H37.33a1.18,1.18,0,0,0,1.18-1.18V19.88A1.18,1.18,0,0,0,37.33,18.7Z\"/><\/svg>',
		'buttonZoomIn': '<svg class=\"im-common-zoom-in-button\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 50 50\"><defs><style>.a{fill:#3e3d40;opacity:0.5;}.b{fill:#f6f6f6;}<\/style><\/defs><rect class=\"a\" x=\"5.02\" y=\"5\" width=\"39.94\" height=\"39.94\" rx=\"3.54\" ry=\"3.54\"/><path class=\"b\" d=\"M40.87,39.34l-9.68-9.68a12.84,12.84,0,1,0-1.64,1.64L39.23,41a1.16,1.16,0,1,0,1.64-1.64Zm-9-17.82a10.45,10.45,0,1,1-3.06-7.38A10.39,10.39,0,0,1,31.86,21.51Z\"/><path class=\"b\" d=\"M27.39,20.2H22.6V15.9a1.16,1.16,0,0,0-1.16-1.16h-.28A1.16,1.16,0,0,0,20,15.9v4.3H15.21a1.12,1.12,0,0,0-1.12,1.11v.27a1.12,1.12,0,0,0,1.12,1.12H20V27a1.16,1.16,0,0,0,1.16,1.16h.28A1.16,1.16,0,0,0,22.6,27v-4.3h4.79a1.12,1.12,0,0,0,1.12-1.12v-.27A1.12,1.12,0,0,0,27.39,20.2Z\"/><\/svg>',
		'buttonZoomOut': '<svg class=\"im-common-zoom-out-button\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 50 50\"><defs><style>.a{fill:#3e3d40;opacity:0.5;}.b{fill:#f6f6f6;}<\/style><\/defs><rect class=\"a\" x=\"5.02\" y=\"4.57\" width=\"39.94\" height=\"39.94\" rx=\"3.54\" ry=\"3.54\"/><path class=\"b\" d=\"M40.87,38.91l-9.68-9.68a12.84,12.84,0,1,0-1.64,1.64l9.68,9.67a1.16,1.16,0,1,0,1.64-1.64Zm-9-17.82A10.45,10.45,0,1,1,28.8,13.7,10.39,10.39,0,0,1,31.86,21.08Z\"/><path class=\"b\" d=\"M15.21,19.77H27.39a1.12,1.12,0,0,1,1.12,1.12v.27a1.12,1.12,0,0,1-1.12,1.12H15.21a1.12,1.12,0,0,1-1.12-1.12v-.27A1.11,1.11,0,0,1,15.21,19.77Z\"/><\/svg>',
		'buttonZoomRestore': '<svg class=\"im-common-zoom-restore-button\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 50 50\"><defs><style>.a{fill:#3e3d40;opacity:0.5;}.b{fill:#f6f6f6;}<\/style><\/defs><rect class=\"a\" x=\"5.02\" y=\"4.57\" width=\"39.94\" height=\"39.94\" rx=\"3.54\" ry=\"3.54\"/><path class=\"b\" d=\"M41,38.89,29.68,27.8l-.44.48-.15.16A10.57,10.57,0,1,1,32.19,21,10.36,10.36,0,0,1,32,22.88l-4.79-3.46a.9.9,0,0,0-1.06,1.46l6.09,4.4a1.3,1.3,0,0,0,.76.23h0a1.32,1.32,0,0,0,.78-.25l6-4.52a.9.9,0,1,0-1.09-1.44L34.1,22.78A12.18,12.18,0,0,0,34.26,21a12.74,12.74,0,1,0-4.5,9.62l9.76,9.76A1,1,0,1,0,41,38.89ZM40.66,40h0Z\"/><\/svg>',
		'borderRadius' : '3px 3px 3px 3px',
		'borderColor' : 'rgba(55, 71, 79, 1) rgba(55, 71, 79, 1) rgba(55, 71, 79, 1) rgba(55, 71, 79, 1)',
		'textColor' : 'rgba(0, 0, 0, 1)',
		'fontFamily' : 'Tahoma',
		'fontStyle' : 'normal',
		'fontWeight' : 'normal',
		'fontSize' : '9pt',
		'textAlignment' : 'left',
		'boxColor' : 'rgba(255, 255, 255, 1)',
		'opacity' : 0.9,
		'radialBg' : false // Works only in Mozilla Firefox and Google Chrome
	});

	// PopUp
	$.extend(s.imPopUp, {
		'effect' : 'websitex5.bl.templates.showboxanimation',
		'width' : 500,
		'shadow' : '',
		'background' : 'rgba(55, 71, 79, 1)',
		'borderRadius' : 10,
		'textColor' : 'rgba(0, 0, 0, 1)',
		'boxColor' : 'rgba(255, 255, 255, 1)',
		'opacity' : 0.9
	});

	// Tip
	$.extend(s.imTip, {
		'borderRadius' : 1,
		'arrow' : true,
		'shape' : 'classic',
		'position' : 'top',
		'effect' : 'none',
		'showTail' : true
	});

	// PageToTop
	$.extend(s.imPageToTop, {
		'imageFile' : 'style/page-to-top.png'
	});

	b.push(function() { x.passwordpolicy.init({ "requiredPolicy": "false", "minimumCharacters": "6", "includeUppercase": "false", "includeNumeric": "false", "includeSpecial": "false" });
	});

	// BreakPoints
	s.breakPoints.push({ "hash": "ea2f0ee4d5cbb25e1ee6c7c4378fee7b", "name": "Desktop", "start": "max", "end": 0, "fluid": false });

	b.push(function () { x.cookielaw.activateScripts() });

	s.loaded = true;
})( _jq, x5engine );
