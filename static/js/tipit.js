$(function() {
	var oldBillTotal = "";

	$("#rangeSlider").noUiSlider({
		start: 15,
		behaviour: 'drag',
		step:1,
		range: {
			min: 1, 
			max: 94,
		}
	});	
	$('#rangeSlider').on({
		slide: function() {
			var min = parseInt($(this).val());
			var max = min + 5;

			$("#min").text(min + "%");
			$("#max").text(max + "%");
			update();	
		}
	});
	$("#billTotal").keyup(function(){
		if ($("#tipOn").val() == oldBillTotal) {
			$("#tipOn").val($(this).val());
		}
		oldBillTotal = $("#billTotal").val();

		update();
	});
	$("#tipOn").keyup(function(){
		update();
	});

	function update() {
		$('#results').empty();

		var min = parseInt($("#min").text());
		var max = parseInt($("#max").text());

		var totalPennies = Math.floor(parseFloat($("#billTotal").val()) * 100);
		var tipOnPennies = Math.floor(parseFloat($("#tipOn").val()) * 100);

		var minTipPennies = Math.ceil(tipOnPennies * min / 100.0);
		var maxTipPennies = Math.floor(tipOnPennies * max / 100.0);
		
		var totalPenniesSpan = $('<span/>').text(format(totalPennies));

		var totalPenniesStr = totalPennies.toString();

		if (isPi(totalPenniesStr)) {
			totalPenniesSpan.addClass('pi');
		}
		if (isPalindrome(totalPenniesStr)) {
			totalPenniesSpan.addClass('palindrome');
		}
		if (isCounting(totalPenniesStr)) {
			totalPenniesSpan.addClass('counting');
		}

		for (var i = minTipPennies; i <= maxTipPennies; i++) {
			var totalAmtPennies = i + totalPennies;

			var tipSpan = $('<span/>').text(format(i));
			var totalAmtPenniesSpan = $('<span>').text(format(totalAmtPennies));

			var interesting = false;

			var totalAmtPenniesStr = totalAmtPennies.toString();
			var iStr = i.toString();

			if (isPi(totalAmtPenniesStr)) {
				totalAmtPenniesSpan.addClass('pi');
				interesting = true;
			}
			if (isPi(iStr)) {
				tipSpan.addClass('pi');
				interesting = true;
			}
			if (isPalindrome(totalAmtPenniesStr)) {
				totalAmtPenniesSpan.addClass('palindrome');
				interesting = true;
			}
			if (isPalindrome(iStr)) {
				tipSpan.addClass('palindrome');
				interesting = true;
			}
			if (isCounting(totalAmtPenniesStr)) {
				totalAmtPenniesSpan.addClass('counting');
				interesting = true;
			}
			if (isCounting(iStr)) {
				tipSpan.addClass('counting');
				interesting = true;
			}

			if (!interesting) {
				continue;
			}

			var result = $('<div/>');
			
			var pct = (i / 1.0 / tipOnPennies * 100).toFixed(2);

			result.append([
				totalPenniesSpan.clone(),
				" + ",
				tipSpan,
				" = ",
				totalAmtPenniesSpan,
				" (" + pct + "%)"
			]);

			$('#results').append(result);
		}

		if ($('#results').children().length == 0) {
			$('#noResults').show();
		} else {
			$('#noResults').hide();
		}
	}

	function isPi(s) {
		var pi = "314159265358979";

		return s ==  pi.substring(0, s.length);
	}

	function isPalindrome(s) {
		var l = s.length;
		var h = s.length / 2;

		for(i = 0; i < h; i++) {
			if (s.charCodeAt(i) != s.charCodeAt(l - 1 - i)) {
				return false;
			}
		}

		return true;
	}

	function isCounting(s) {
		if (s.length < 2) {
			return false;
		}

		var first = s.charCodeAt(0);
		var current = s.charCodeAt(1);

		var d = first - current;

		if (d != 1 && d != -1) {
			return false;
		}

		for (i = 2; i < s.length; i++) {
			if (current - s.charCodeAt(i) != d) {
				return false;
			}

			current = s.charCodeAt(i);
		}
		
		return true;
	}

	function format(pennies) {
		pennies = pennies / 100.0;

		return pennies.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString();
	}
});
