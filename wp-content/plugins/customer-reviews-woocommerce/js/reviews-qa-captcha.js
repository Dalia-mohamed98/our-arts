if (typeof grecaptcha !== 'undefined' && grecaptcha && jQuery('.ivole-recaptcha').length) {
	grecaptcha.ready(() => {
		grecaptcha.render(jQuery('.ivole-recaptcha')[0], {
			sitekey: crReviewsQaCaptchaConfig.v2Sitekey
		});
	});
}
