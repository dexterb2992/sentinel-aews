// IIFE - Immediately Invoked Function Expression
(function(sentinel) {

    // The global jQuery object is passed as a parameter
    sentinel(window.jQuery, window, document);

}(function($, window, document) {

    // The $ is now locally scoped 

    // Listen for the jQuery ready event on the document
    $(function() {
        // The DOM is ready!

        var start_tests = $("#start_tests");
        var clear_logs = $("#clear_logs");
        var more_details = $("#more_details");
        var save_settings = $("#save_settings");
        var new_wp_scan = $("#new_wp_scan");
        // var prev_wp_scan = $("#prev_wp_scan");
        var wp_scan_modal = $("#wp_scan_modal");
        var wp_scans_modal = $("#wp_scans_modal");

        var wpScans = [];

        if( !$.fn.DataTable.isDataTable( '#tbl_wordpresses' ) && $("#tbl_wordpresses").is(":visible") ){
            $('#tbl_wordpresses').dataTable();
        }

        $('.data-table').dataTable();

        $('a[href="#plugins"]').on('shown.bs.tab', function (e) {
            if( !$.fn.DataTable.isDataTable( '#tbl_plugins' ) ){
                $('#tbl_plugins').dataTable();
            }
        });

        $('a[href="#themes"]').on('shown.bs.tab', function (e) {
            if( !$.fn.DataTable.isDataTable( '#tbl_themes' ) ){
                $('#tbl_themes').dataTable();
            }
        });

        start_tests.on("click", function (){
            $this = $(this);
            var __websites = $("#websites").val();
            __websites = __websites.split("http://").join("");
            __websites = __websites.split("https://").join("");

            if( __websites === "" ){
                return false;
            }

            var process_logs = $("#process_logs");
            var process_status = $("#process_status");
            var more_details = $("#more_details");

            //  refresh
            process_logs.html("");
            $(".scan-findings").fadeOut(function (){
                more_details.text("Show more details").removeClass('active');
            });

            var websites = explode_websites( __websites );
            var new_els = [];

            $.each(websites, function (index, row){
                el = $('<div id="res_'+row+'" title="Scan results for '+row+'"></div>');
                process_logs.append( el );
                new_els[row] = el;
            });

            var websites_length = websites.length;
            var HasError = false;

            $.each(websites, function (index, row){
                // we should use queue here
                // first, check if website is up & running

                process_status.text("Checking to see if "+row+" is up & running...");
                        $this.removeClass('btn-info').addClass('btn-warning')
                        .children('i').removeClass('fa-hourglass-start').addClass('fa-refresh fa-spin');

                if( validate_domain(row) ){
                    process_status.html("");
                    $this.removeClass('btn-warning').addClass('btn-info')
                        .children('i').removeClass('fa-refresh fa-spin').addClass('fa-hourglass-start');
                    
                    // check if site is up & running
                    checkDomain(row).done(function (data){
                        if( data.status == 'success' ){
                            // let's continue if the site is up & running
                            // SUCURI SCAN
                            sucuri_scan(row, $this, new_els[row]);
                        }else{
                            logErrors(row, $this);
                            HasError = true;
                        }
                    });    

                }else{
                    logErrors(row, $this);
                    HasError = true;
                }

                Executed = false;
                $(document).ajaxStop(function () {
                    // Executed when all ajax requests are done.
                    if (!Executed && !HasError) save_scan();
                    Executed = true;
                });

            });
        });

        clear_logs.on("click", function (){
            $("#process_logs").html("").fadeIn();
        });


        more_details.on("click", function (){
            $this = $(this);
            if( $this.hasClass("active") ){
                $(".scan-findings").fadeOut(function (){
                    $this.text("Show more details").removeClass('active');
                });
            }else{
                $(".scan-findings").fadeIn(function (){
                    $this.text("Show less details").addClass('active');
                });
            }
        });

        $(document).on("click", ".delete-scan", function (){
            var $this = $(this);
            var confirmation = confirm("Are you sure you want to delete this scan?");
            if( confirmation ){
                $.ajax({
                    url: ajaxurl,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        q: 'delete_scan',
                        id: $this.attr("data-id")
                    },
                    beforeSend: function (){
                        $this.text( 'Please wait...' ).addClass("disabled");
                    },
                    success: function (data){
                        if( data == 1 ){
                            $this.parent("td").parent("tr").fadeOut();
                        }
                    },
                    error: function (data){
                        $this.prev("i.fa-refresh").remove();
                        $this.html("Delete");
                    }
                });
            }
        });

        save_settings.on("click", function (){
            var email_address = $("#email_address");
            if( email_address.val() === "" ){
                email_address.focus();
                return false;
            }

            $this = $(this);
            $.ajax({
                url: ajaxurl,
                type: 'post',
                dataType: 'json',
                data: $("#sentinel_settings").serialize()+'&q=save_sentinel_settings',            
                beforeSend: function (){
                    $this.children('i').removeClass('fa-save').addClass('fa-refres fa-spin disabled');
                },
                success: function (res){
                    console.log(res);
                    $this.children('i').removeClass('fa-refres fa-spin disabled').add('fa-save');

                    if( res == 1 ){
                        statusInfo = $('<span class="label label-success"><i class="fa fa-check"></i> Your changes have been saved.</span>');
                    }else{
                        statusInfo = $('<span class="label label-danger"><i class="fa fa-warning"></i> Sorry, please reload the page and try again.</span>');

                    }

                    if( $this.prev('span.label').length !== 0 ){
                        $this.prev('span.label').replaceWith(statusInfo).fadeIn().delay(10000).queue(function(n) {
                          statusInfo.fadeOut().remove(); n();
                        });
                    }else{
                        $this.before(statusInfo).fadeIn().delay(10000).queue(function(n) {
                          statusInfo.fadeOut().remove(); n();
                        });
                    }
                    

                },
                error: function (data){
                    $this.children('i').removeClass('fa-refres fa-spin disabled').add('fa-save');
                }
            });
        });

        new_wp_scan.on("click", function (){
            $this = $(this);
            wp_scan_modal.modal('show');
        });

        // prev_wp_scan.on("click", function (){
        //     wp_scans_modal.modal('show');
        // });
    });

    // The rest of the code goes here!

    function ultratools_scan(url, button, output_loc){
        var process_status = $("#process_status");
        $.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                q: 'ultratools_scan', url: url
            },
            assync: false,
            beforeSend: function (){
                process_status.text("Cheking if "+url+" is listed on any Spam Blackist...");
                disableButton(button);
            },
            success: function (data){
                process_status.html("");
                output_loc.append( "<br/>"+data+"<hr>" );
                enableButton(button);
                console.log(data);
            },
            error: function (data){
                enableButton(button);
            }
        });
    }

    function google_diagnostic(url, button, output_loc){
        var process_status = $("#process_status");
        $.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                q: 'google_diagnostic', url: url
            },
            assync: false,
            beforeSend: function (){
                process_status.text("Cheking if google listed "+url+" as harmful...");
                disableButton(button);
            }
        }).done(function (data){
            process_status.html("");
            output_loc.append( "<br/>"+data+"<hr>" );
            enableButton(button);
            ultratools_scan(url, button, output_loc);

            console.log(data);
        }).error(function (data){
            enableButton(button);
            ultratools_scan(url, button, output_loc);
        });
    }


    function sucuri_scan(url, button, output_loc){
        var process_status = $("#process_status");
        $.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                q: 'sucuri_scan', url: url
            },
            assync: false,
            beforeSend: function (){
                process_status.text("Checking to see if "+url+" is blacklisted  and/or containing malware...");
                disableButton(button);
            }
        }).done(function (data){
            process_status.html("");
            enableButton(button);
            google_diagnostic(url, button, output_loc);

            output_loc.append( "<br/>"+data+"<hr>" );
            console.log(data);
        }).error(function (data){
            enableButton(button);
            google_diagnostic(url, button, output_loc);
        });
    }

    function explode_websites(str){
        var ks = str.split("\n");
        return ks;
    }

    function disableButton($button){
        $button.removeClass('btn-info').addClass('btn-warning')
            .children('i').removeClass('fa-hourglass-start').addClass('fa-refresh fa-spin');
    }

    function enableButton($button){
        $button.removeClass('btn-warning').addClass('btn-info')
            .children('i').removeClass('fa-refresh fa-spin').addClass('fa-hourglass-start');
    }


    function save_scan(){
        var websites = $("#websites").val().replace(/\n/g, ",");
        var process_status = $("#process_status");

        $.ajax({
            url: ajaxurl,
            type: 'post',
            dataType: 'json',
            data: {
                q: 'save_scan',
                url: websites,
                scan_result: $("#process_logs").html()
            },
            beforeSend: function (){
                process_status.text("");
            },
            success: function (data){
                process_status.text("Next scan will be on "+data.next_scan+".").fadeIn().delay(20000).queue(function(n) {
                  $(this).text("").fadeOut(); n();
                });
            },
            error: function (data){
                console.log(data);
                alert("Opps, something went wrong while we schedule for the next scan. Please try again later.");
            }
        });

    }

    function validate_domain(domain){
        if( /^[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9](?:\.[a-zA-Z]{2,})+$/.test(domain) )
           return true;
        return false;       
        
    }

    function checkDomain(domain){
        return $.ajax({
            url: ajaxurl,
            type: 'post',
            dataType: 'json',
            data: { url: domain, q: 'check_url' }
        });
    }

    function logErrors(domain, btn){
        var process_status = $("#process_status");
        var process_logs = $("#process_logs");

        process_status.text("");
        process_logs.html( process_logs.html()+"<span class='label label-danger'>"+domain+" cannot be reached.</span><br/>" );
        btn.removeClass('btn-warning').addClass('btn-info')
            .children('i').removeClass('fa-refresh fa-spin').addClass('fa-hourglass-start');
    }
}));