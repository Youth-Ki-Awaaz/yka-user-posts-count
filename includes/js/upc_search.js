jQuery(document).ready(function(){
	ykaUPC = {

		init: function() {
			jQuery('.upc-search-btn').on('click', this.searchHandlerr );
		},

		searchHandlerr : function(e) {

			var catSlug = jQuery('input[name=upc-post-count]').val();
			var postCount = Number( catSlug );

			if( postCount === '' || !postCount ) {

				jQuery('.author-list').empty();
				jQuery('.author-list').append('<li><div class="text-danger upc-invalid">Enter Valid Post Count!</div></li>');
				return false;
			}

			// HIDE ERROR
			jQuery('.upc-invalid').hide();

			// SHOW LOADER
			var loader = jQuery('.upc-search-btn');
			loader.text('Searching....');

			//use for paginating
			var config = {
				offset: 0,
				per_page: 12,
				url: '',
				resultContainer: ''
			}

			// URL FOR SEARCHING USERS BASED ON THE GIVEN POST_COUNT
			var ajaxUrl = ajaxurl + '?action=yka_upc_search&upc-post-count=' + postCount;

			config.url = ajaxUrl;

			// SET OFFSET AND LIMITS
			ajaxUrl = ajaxUrl + '&items_per_page=' + config.per_page + '&offset=' + config.offset;

			// SET ELEMENT ATTRIBUTE IN CONFIG
			config.resultContainer = '.author-list';

			// URL TO EXPORT DATA AS CSV
			var export_url = ajaxurl + '?action=yka_upc_search_csv&upc-post-count=' + postCount; // UNCOMMENT THIS
			//export_url = encodeURI(export_url);

			var self = this;

			jQuery.ajax({
				type:'get',
				url	: ajaxUrl,
				success: function(response) {
					console.log("Response Below: ");
					console.log(response);

					loader.text('Search');

					//remove load more button if present from previous search
					if( jQuery('.load-more') ){
						jQuery('.load-more').remove();
					}

					jQuery('.author-list').empty();

					response = JSON.parse(response);

					if( response.error ) {

						var output = '<li><div class="text-danger">' + response.error + '</div></li>';
						jQuery('.author-list').append(output);

					} else {

						if( response.length ) {
							//enable export as csv button
							jQuery('.csv-btn').show();
							jQuery('.csv-btn').attr('href', export_url);

							//add search result to the DOM
							var output = [];
							jQuery.each(response, function(key, data){
								var card = '<li><div><h3><a href="' + data.url + '">' + data.name + '</a></h3>'+
								    		'<p>'+ data.email + '</p></div></li>';

								output.push(card);

							});

							var list = output.join("");

							jQuery('.author-list').append(list);

							//enable pagination
							jQuery('.author-list-wrapper').append( ykaUPCLoadMore.init( config ) );

						} else {

							jQuery('.author-list').empty();
							jQuery('.author-list').append('<li><div>Nothing Found!</div></li>');
							//jQuery('.csv-btn').hide();

						}
					}
				},
				error: function(error) {
					console.log(error);
				}

			});

		},

	};



	// PAGINATION OBJECT
	var ykaUPCLoadMore = {

		config: {
			offset: 0,
			per_page: 3,
			resultContainer: '',
			url: ''
		},

		init: function(params = null){
			if( params != null ){
				this.config.offset = params.offset;
				this.config.per_page = params.per_page;
				this.config.url = params.url;
				this.config.resultContainer = params.resultContainer;
			}

			this.addButton();

		},

		addButton: function(){
			//add load more button
			if( this.config.resultContainer !== '' ){
				jQuery(this.config.resultContainer).parent().append("<button class='load-more'>Load More</button>");
				jQuery('.load-more').on('click', this.load.bind(this));
			}
		},

		load: function(e){

			e.preventDefault();

			var elem = jQuery(e.target);

			var self = this;

			// disable button
			elem.attr('disabled', 'disabled');

			// start loader
			// elem.children().addClass('fa-spin');
			elem.text('Loading....');

			// Update the offset
			self.config.offset = self.getOffset();

			var ajax_url = self.config.url + '&items_per_page=' + self.config.per_page + '&offset=' + self.config.offset;

			jQuery.ajax({
				type:'get',
				url	: ajax_url,
				success: function(response) {
					response = JSON.parse(response);

					if( response.length ){

						var output = [];
						jQuery.each(response, function(key, data){
							var card = '<li><div><h3><a href="' + data.url + '">' + data.name + '</a></h3>'+
							    		'<p>'+ data.email + '</p></div></li>';

							output.push(card);

						});

						var list = output.join("");

						jQuery('.author-list').append(list);

						elem.removeAttr('disabled');
						// elem.children().removeClass('fa-spin');
						elem.text('Load More');

					} else {
						elem.css('display','none');
					}

				}
			});
		},

		getOffset: function(){
			// THE NEW OFFSET TO BE UPDATED
			return parseInt(this.config.offset) + parseInt(this.config.per_page);

		},


	};


	ykaUPC.init();

});
