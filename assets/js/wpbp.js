(function($){

	Bulk_Process = function() {
		var self = this;
		
		self.ajax_data = {
			action: 'run_bulk_process',
			nonce: WPBP.nonce,
			process_id: '',
			step:1,
			status: 'started',
		};

		self.init = function() {

			self.initElements();

			$(document).on('click', '.start-bulk-process', self.prepare );
			$(document).on('click', '.stop-bulk-process', self.stopProcess );
			$(document).on('click', '#window', self.toggleConsole );
		};

		self.initElements = function(){
			self.container = $('#bulk_process_container');
			self.cmd = self.container.find('#cmd_text');
			self.start_button = self.container.find('.start-bulk-process');
			self.stop_button = self.container.find('.stop-bulk-process');
			self.progressBar = self.container.find('.wpbp_progress');
		};

		self.toggleConsole = function(e){
			e.preventDefault();
			self.container.find('#cmd').toggleClass('hide');
		};

		self.stopProcess = function(e){
			e.preventDefault();

			self.ajax_data.status = 'stopped';
			$(this).text(WPBP.stop_process_text);
		};
		
		self.prepare = function(e) {
			e.preventDefault();
			
			var r = confirm("Are you sure to process ?");

			if (r == true) {
				var process = $('input[name="process"]:checked').val();
				self.ajax_data.process_id = process;
				self.ajax_data.step = 1;
				self.progressBar.asPieProgress('reset');
			    self.bulkProcess();
			}
			
		};

		self.bulkProcess = function(e){
			// alert(self.ajax_data.step);
			if(self.ajax_data.status == 'started'){
				self.start_button.addClass('disabled');
				self.stop_button.removeClass('hide');
				self.cmd.append('<span class="process">'+WPBP.start_process_text+'</span>');
			}

			$.ajax( {
				dataType: 'json',
				data: self.ajax_data,
				type: 'post',
				url: WPBP.ajax_url,
				success: function(response) {
					console.log(response);

					self.updateConsole(response.messages);

					if( ['stopped', 'noresult', 'finished'].indexOf(response.status) > 0 ){
						self.start_button.removeClass('disabled');
						self.stop_button.addClass('hide');
						return;
					}

					if(typeof(response.status) != 'undefined'){
						self.ajax_data.status = response.status.toLowerCase();
					}

					if( ['running', 'finished'].indexOf(response.status) > 0 ){
						self.progressBar.asPieProgress('go', response.progress + '%');
					}
					
					if( response.success ){
						if( 'running' === self.ajax_data.status ){
							self.ajax_data.step = parseInt(self.ajax_data.step)+1;
							self.bulkProcess();
						}
					} else {
						
						alert(response.errors.join(','));
					}
              	}
			} );
		};

		self.updateConsole = function(messages){
			/**
			 * Success
			 * @param  {String} (typeof(messages.success) !             [description]
			 * @return {[type]}                           [description]
			 */
			if ( (typeof(messages.success) != 'undefined') && messages.success.length ) {
				for (i = 0; i < messages.success.length; i++) { 
				    self.cmd.append('<span class="result success">'+messages.success[i]+'</span>');
				}
			}

			/**
			 * Notices
			 * @param  {String} (typeof(messages.notices) !             [description]
			 * @return {[type]}                           [description]
			 */
			if ( (typeof(messages.notices) != 'undefined') && messages.notices.length ) {
				for (i = 0; i < messages.notices.length; i++) { 
				    self.cmd.append('<span class="result">'+messages.notices[i]+'</span>');
				}
			}

			/**
			 * Error
			 * @param  {String} (typeof(messages.errors) !             [description]
			 * @return {[type]}                           [description]
			 */
			if ( (typeof(messages.errors) != 'undefined') && messages.errors.length ) {
				for (i = 0; i < messages.errors.length; i++) { 
				    self.cmd.append('<span class="result failed">'+messages.errors[i]+'</span>');
				}
			}
		};

		self.updateStatus = function(id, success, response){

			// console.log('items_count : ' + self.items_count);
			// console.log('items_total : ' + self.items_total);

			$('.wpbp_progress').asPieProgress('go', ( self.items_count / self.items_total ) * 100 + '%');

			self.items_count = self.items_count + 1;

			if ( success ) {
				self.items_successes = self.items_successes + 1;
				
				// write to log
				// console.log(response.notices);
				$('#cmd_text').append('<span class="result success">'+response.message+'</span>');

				var notices = response.notices;
				if ( (typeof(notices) != 'undefined') && notices.length ) {
					var details = '<span class="result detail">';
					for (i = 0; i < notices.length; i++) { 
					    details += '<span class="content-detail">'+notices[i]+'</span>';
					}
					details += '</span>';

					$('#cmd_text').append(details);
				}
			}
			else {
				self.items_errors = self.items_errors + 1;
				self.items_failedlist = self.items_failedlist + ',' + id;

				// write to log
				// console.log(response.notices);
				$('#cmd_text').append('<span class="result failed">'+response.message+'</span>');

				var notices = response.notices;
				if ( (typeof(notices) != 'undefined') && notices.length ) {
					var details = '<span class="result detail">';
					for (i = 0; i < notices.length; i++) { 
					    details += '<span class="content-detail">'+notices[i]+'</span>';
					}
					details += '</span>';

					$('#cmd_text').append(details);
				}
			}
		};
		
		return self;
	}

	var Bulk_Process_Js;

	$(document).ready(function() {

		$('.wpbp_progress').asPieProgress({
	        namespace: 'wpbp_progress'
	  	});

		if(Bulk_Process_Js == null) {
			Bulk_Process_Js = new Bulk_Process();
			Bulk_Process_Js.init();
		}
	});

})(jQuery);