/**
 * Created by jyl on 06.07.2017.
 */
if (typeof(tsar) == 'undefined') {
	var tsar = new Object();
}

$(document).ready(function(){
	if ((typeof($mobile) == 'undefined' || !$mobile)) {
		tsar.Init.initTopAndBottomMenu();
	}else {
		tsar.Init.mobileMenu();
		tsar.Init.mobileSlider();
	}
	tsar.Init.heartSlider();
	tsar.Init.showOrHidePaneContent();
	tsar.Init.changeCheckbox();
	tsar.Init.addStars();
	tsar.Init.showOrHideItemContentBottom();
	tsar.Init.showPopupImg();
	// tsar.Init.showPopupImgSlider();
	tsar.Init.ImgsInPopup();
	tsar.Init.showMap();
	tsar.Init.showOrHidePanelDefaultBody();
	tsar.Init.showDayTab();
	tsar.Init.showOrHidebookingForm();

});

tsar.Init = {
	initTopAndBottomMenu : function(){
		$('.tsarMenu').wrapAll('<div class="wrapTsarMenu"></div>');

		var $t = $('.tsar .wrapTsarMenu'),
			$b = $('.tsar .tabbed');

		$('.logoTsar').clone().prepend('.menuMain .wrapPage');

		if(!$('.tsarMenu').closest('.mainPage').length && !$('.tsarMenu').closest('.event').length){
			$t.addClass('doTop');
			//$('.tsar .menuBlockTitle').css('margin-top','-54px');
		}

		$(window).scroll(function(){
			var $h = $(this).scrollTop();

			if($('.tsarMenu').closest('.mainPage').length || $('.tsarMenu').closest('.event').length){
				var $top = $t.offset().top;
				if ($h > $top) {
					$t.addClass('doTop');
				} else {
					$t.removeClass('doTop');
				}
			}

			var $footer = $b.offset().top;
			if ($footer >= $h + $(window).height()) {
				$b.addClass('doBottom');
			} else {
				$b.removeClass('doBottom');
			}
		});
	},
	mobileMenu: function(){
		$('.menuTop').wrap('<div class="headerFixed"></div>');

		$('.temp').wrapAll('<div class="temp1"></div>');
		$('.headerFixed').after($('.search')).after($('.temp1'));


		var $html = $('<div class="mobile_menu">' +
			'<div class="mobile_menu_icon"></div>' +
			'<div class="mobile_menu_block">' +
			'<div class="mobile_menu_back_close">x</div>' +
			'<div class="mobile_menu_back"></div>' +
			'<div class="mobile_menu_items"><div class="menu_closeNew"></div></div>' +
			'</div>' +
			'</div>');



		$('.menuTop .wrapPage').prepend($html);
		$('.mobile_menu').append($('.logoTsar'));

		$('.mobile_menu_items').append($('.menuMain')).append($('.logoInternational')).append($('.menuTop ul'));

		//показываем менюху
		$('.mobile_menu_icon').click(function () {
			$('body').css('position','fixed');
			$('.mobile_menu_block').fadeIn();
		});

		//закрываем менюху
		$('.mobile_menu_back_close, .mobile_menu_back, .menu_closeNew').click(function () {
			$('body').css('position','relative');
			$('.mobile_menu_block').fadeOut();
		});
	},
	mobileSlider: function(){
			$('.sliderMobileItems').carouFredSel({
				direction : "left",
				auto : 7000,
				scroll : {
					duration        : 700,
					pauseOnHover    : true,
					fx : 'scroll',
					// easing : 'elastic'

				},
				height : 390,
				width : '100%',
				prev : ".sliderMobileItemLeft",
				next : ".sliderMobileItemRight",
				pagination : '.sliderMobilePagination'
			});

			$('.sliderMobileItems').swipeleft(function(){
				$('.sliderMobileItemRight').click();
			}).swiperight(function(){
				$('.sliderMobileItemLeft').click();
			});
	},
	heartSlider: function(){
		$('.heartSliderItems').carouFredSel({
			direction : "left",
			// auto: false,
			auto : 7000,
			scroll : {
				duration        : 700,
				pauseOnHover    : true,
				fx : 'scroll',
				// easing : 'elastic'
			},
			height : 372,
			width : '100%',
			prev : ".heartSliderItemLeft",
			next : ".heartSliderItemRight",
			pagination : '.heartSliderPagination'
		});

		$('.heartSliderItems').swipeleft(function(){
			$('.heartSliderItemRight').click();
		}).swiperight(function(){
			$('.heartSliderItemLeft').click();
		});
	},
	destinationProdSlider: function(){
		$('.destinationProdSliderItems').carouFredSel({
			direction : "left",
			auto : false,
			scroll : {
				duration        : 700,
				pauseOnHover    : true,
				fx : 'scroll',
				items: 1
			},
			height : 513,
			width : '100%',
			prev : ".destinationProdSliderLeft",
			next : ".destinationProdSliderRight",
			pagination : '.destinationPodSliderPagination'
		});

		$('.destinationProdSliderItems').swipeleft(function(){
			$('.destinationProdSliderRight').click();
		}).swiperight(function(){
			$('.destinationProdSliderLeft').click();
		});
	},
	eventsProdSlider: function(){
		$('.eventsProdSliderItems').carouFredSel({
			direction : "left",
			auto : false,
			scroll : {
				duration        : 700,
				pauseOnHover    : true,
				fx : 'scroll',
				items: 1
			},
			width : '100%',
			height : 151,
			prev : ".eventsProdSliderLeft",
			next : ".eventsProdSliderRight",
			pagination : '.eventsPodSliderPagination'
		});

		$('.eventsProdSliderItems').swipeleft(function(){
			$('.eventsProdSliderRight').click();
		}).swiperight(function(){
			$('.eventsProdSliderLeft').click();
		});
	},
	showOrHidePaneContent: function () {
		$('.panelPane .paneTitle:not(.noClose)').click(function() {
			var $paneContent = $(this).next(),
			$expandReduce = $(this).children('.expandReduce'),
			$fa = $($expandReduce).children('.fa');

			if($paneContent.is(':hidden')){
				// $($expandReduce).addClass('expanded');
				$($fa).removeClass('fa-plus').addClass('fa-minus');
				$paneContent.slideDown();
			}else{
				// $($expandReduce).removeClass('expanded');
				$($fa).removeClass('fa-minus').addClass('fa-plus');
				$paneContent.slideUp();
			}
		});
	},
	showOrHidePanelDefaultBody: function () {
		$('.tsar .panelDefaultTitle').click(function() {
			var $panelDefaultBody = $(this).next(),
				$panel = $(this).closest('.panelDefault');

			if($panelDefaultBody.is(':hidden')){
				if($panel.siblings('.panelDefault').hasClass('open')){
					$panel.siblings('.panelDefault').removeClass('open');
					$panel.siblings('.panelDefault').children('.panelDefaultBody').slideUp();
				}
				$panel.addClass('open');
				$panelDefaultBody.slideDown();
			}else{
				$panel.removeClass('open');
				$panelDefaultBody.slideUp();
			}
		});
	},
	showOrHidebookingForm: function() {

		$('.bookingForm .block_dates .formItem.isTitle, .bookingForm .block_contacts .formItem.isTitle').each(function(i, item) {
			$(item).siblings('.formItem').wrapAll("<div class='bookingFormHideBlock'><div class='wrapHideBlock'></div></div>");

		});

		$('.bookingForm .formItem.isTitle ').click(function () {
			if($(this).hasClass('close')){
				$(this).removeClass('close');
			}else{
				$(this).addClass('close');
			}
			$(this).siblings('.bookingFormHideBlock').slideToggle();
		});
		
	},
	showOrHideItemContentBottom: function () {
		$('.tsar .itemPart3Info .viewmore').click(function() {
			var $itemContentBottom = $(this).closest('.itemContent').next();

			if($itemContentBottom.is(':hidden')){
				$itemContentBottom.slideDown();
			}else{
				$itemContentBottom.slideUp();
			}
		});
		$('.tsar .itemContentBottom .closeBottom').click(function() {
			$(this).closest('.itemContentBottom').slideUp();
		});
	},
	changeCheckbox: function () {
		$("input[name='checkboxForPaneItem']").change(function() {
			if($(this).is(':checked')){
				$(this).siblings('.checkPic').addClass('checked');
				$(this).parents('.paneItem').addClass('active');
			}else{
				$(this).siblings('.checkPic').removeClass('checked');
				$(this).parents('.paneItem').removeClass('active');
			}
		});
	},
	addStars: function () {
		$('.tsar .itemStars[data-star="5 Etoiles"],.tsar .itemStars[data-star="4 Etoiles"],.tsar .itemStars[data-star="3 Etoiles"]').html('');
		$('.tsar .itemStars[data-star="2 Etoiles / Hostels"]').html('/ Hostels');
	},
	showPopupImg : function(){
		$('.tsar .lightboxIcon').click(function(){
			var $path = $(this).attr('data-img');
			var $title = $(this).attr('data-title');
			tsar.Popup.Show({
				addClass: 'voir',
				image: $path,
				callback: function () {
					$('.fancyboxTitleFloatWrap .child').html($title);
				},
				// //imageZoom: false
			})

		});
	},
	showPopupImgSlider : function(){
		$('.tsar .clImg').click(function(){
			var $path = $(this).children('img').attr('data-img'),
			$title = $(this).children('.tipForImg').text(),
			$title1 = $(this).attr('data-title');
			tsar.Popup.Show({
				addClass: 'popupImgsSlider',
				image: $path,
				callback: function () {
					if($title1){
						$('.fancyboxTitleFloatWrap .child').html($title1);
					}else{
						$('.fancyboxTitleFloatWrap .child').html($title);
					}

				},
				// //imageZoom: false
			})

		});
	},
	ImgsInPopup : function(){
		$('.tsar .clImg').click(function(){
			var arr=[],
				thisElem={
					img: $(this).children('img').attr('data-img')
				};
				var $title1 = $(this).attr('data-title');
				if($title1){
					thisElem.title = $title1;
				}else{
					thisElem.title = $(this).children('.tipForImg').text();
				}
				arr.push(thisElem);

				$(this).siblings('.clImg').each(function(i,item) {
					var obj={};
					obj.img = $(item).children('img').attr('data-img');
					var $title1 = $(item).attr('data-title');
					if($title1){
						obj.title = $title1;
					}else{
						obj.title = $(item).children('.tipForImg').text();
					}
					arr.push(obj);
				});
				// console.log(arr);

				var slider = '<div class="sliderInPopup">'+
				'<div class="slLeft sliderInPopupItemLeft" style="display: block;"><div class="elem"></div></div>'+
				'<div class="slRight sliderInPopupItemRight" style="display: block;"><div class="elem"></div></div>'+
				'<div class="sliderInPopupItems noSelect">'+
				'</div>'+
				'</div>';

				tsar.Popup.Show({
					addClass: 'popupImgsSlider',
					html: slider,
					callback: function () {
						for(var i = 0; i < arr.length; i++){
							$('.sliderInPopupItems').append('<div class="sliderInPopupItem" data-title="'+arr[i].title+'"><img src="'+arr[i].img+'" alt=""></div>');
						}
						tsar.Init.sliderInPopup();
						$('.tsar .sliderInPopupItemLeft, .tsar .sliderInPopupItemRight').css('opacity','1');

						$('.fancyboxTitleFloatWrap .child').html(arr[0].title);
					},
				})

		});
	},
	sliderInPopup: function(){
		function highlight(item) {
			$(item).addClass('active');
			$('.fancyboxTitleFloatWrap .child').html($(item).attr('data-title'));
		};
		function unhighlight(item) {
			$(item).removeClass('active');
		};

		$('.sliderInPopupItems').carouFredSel({
			direction : "left",
			auto : false,
			scroll : {
				duration        : 700,
				pauseOnHover    : true,
				fx : 'scroll',
				items: 1,
				onAfter:  function(data) {
					highlight(data.items.visible[0]);
				},
				onBefore: function(data) {
					unhighlight( data.items.old );
				},
			},
			width : '100%',
			height : 450,
			prev : ".sliderInPopupItemLeft",
			next : ".sliderInPopupItemRight",
			// pagination : '.eventsPodSliderPagination'
		});

		$('.sliderInPopupItems').swipeleft(function(){
			$('.sliderInPopupItemRight').click();
		}).swiperight(function(){
			$('.sliderInPopupItemLeft').click();
		});
	},
	showMap : function(){
		$('.tsar .showMap').click(function() {
			var $params = [], $city = $(this).attr('data-city'), $street = $(this).attr('data-street'), $title = $(this).attr('data-title');
			if ($city) { $params.push($city); }
			if ($street) { $params.push($street); }
			if ($title) { $params.push($title); }
			tsar.Popup.Show({
				addClass: 'map',
				iframe: {url:'https://www.google.com/maps/embed/v1/place?key='+tsar.mapiFrameKey+'&q='+$params.join(',')}
			})
		})
	},
	showDayTab: function () {
		$('.dayTabbed .labelTab').click(function() {
			var $this = $(this),
				$attr = $(this).attr('data-tab'),
				$tab = $this.closest('.labelsTab').siblings('.contentTabs').children('.contentTab[data-tab="'+$attr+'"]');

			if(!$this.hasClass('active')){
				$this.addClass('active').siblings('.labelTab').removeClass('active');
				$tab.addClass('active').siblings('.contentTab').removeClass('active');
			}
		});
	},


};

tsar.Article = {
	initTabs : function(){
		var $list = $('.articleContent .tabs-container ul.nav li');
		var $panels = $('.articleContent .tabs-container .tab-pane');
		$list.width( (Math.floor(100 / $list.length)) + '%' );
		$list.find('a').click(function(e){
			e.preventDefault();
			var $parent = $(this).closest('li');
			if ($parent.is('.active')) {
				return false;
			}
			$list.removeClass('active');
			$parent.addClass('active');
			$panels.removeClass('active');
			$panels.eq( $($list).filter('.active').index() ).addClass('active');
			tsar.Article.initMap();
		});
		tsar.Article.initMap();
	},
	initCarousels : function(){
		$('.owl-carousel').each(function(){
			$(this).carouFredSel({
				auto : false,
				height: 250,
				width: '100%',
				prev : $(this).closest('.item-list').find('.owl-prev'),
				next : $(this).closest('.item-list').find('.owl-next'),
				scroll: {
					items: 1
				}
			});
		});
	},
	initMap : function(){
		var $mapDiv = $('#map');
		if ($mapDiv.length == 0) {
			return false
		}
		$mapDiv.empty();
		var $actPlace = $('ul.nav li.active');
		var $lat = parseFloat($actPlace.find('a').attr('data-lat'));
		var $lng = parseFloat($actPlace.find('a').attr('data-lng'));
		var myLatLng = {lat: $lat, lng: $lng};

		var map = new google.maps.Map(document.getElementById('map'), {
			center: myLatLng,
			zoom: 14,
			disableDefaultUI: true,
			draggable: false,
			scrollwheel: false,
		});
		var marker = new google.maps.Marker({
			position: myLatLng,
			map: map,
			title: 'Uluru (Ayers Rock)',
			icon: '/sites/all/themes/tsar_frontend/images/map-marker.png'
		});
	}
};

tsar.destinationSearch = function(){
	$('#destinationsSearch').keyup(function(){
		var $val = $(this).val();
		var $items = $('.destinationsItem');
		if ($val == '') {
			$items.find('a').data('show',1).show()
		} else {
			$items.each(function(i,item){
				var $found = 0;
				$(item).find('a').each(function(i,item){
					var regex = new RegExp($val,'i');
					if ( regex.test( $.trim($(item).text()) ) ) {
						$(this).data('show',1).show();
						$found++;
					} else {
						$(this).data('show',0);
						if ( $(this).closest('.isChildren').length > 0 ) {
							$(this).hide();
						}
					}
				});
				if ($found > 0) {
					$(item).show();
				} else {
					$(item).hide();
				}
			});
		}
	});
};

tsar.Subscribe = {
	checkForm : function( $form ){
		var $field = '#subscribe input[name="mail"]';
		var $result = false, $allErrors = [];
		if ($.trim($($field).val()) == '') {
			$allErrors.push({mess: $($field).attr('data-error'), field: $field});
		} else if ( !tsar.TestEmail($.trim($($field).val())) ) {
			$allErrors.push({mess: $($field).attr('data-error'), field: $field});
		}

		if ($allErrors.length > 0) {
			tsar.CheckForm.Result($allErrors, false);
		} else {
			tsar.Loading.Start();
			$result = true;
		}
		console.log($result);
		return $result;
	}
};

tsar.Search = {
	checkForm: function($form){
		var $field = '#searchInput';
		var $result = false, $allErrors = [];
		if ($.trim($($field).val()) == '' || $.trim($($field).val()).length < 3) {
			$allErrors.push({mess: $($field).attr('data-error'), field: ''});
		}
		if ($allErrors.length > 0) {
			tsar.CheckForm.Result($allErrors, false);
		} else {
			tsar.Loading.Start();
			$result = true;
		}
		return $result;
	}
};