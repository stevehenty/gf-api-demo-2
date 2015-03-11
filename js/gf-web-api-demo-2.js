(function (GFWebApiDemo2, $) {

    var apiVars, $sending, $results;

    $(document).ready(function () {

        // get globals
        apiVars = gf_web_api_demo_2_strings;

        $sending = $("#sending");
        $results = $("#response");

        $('#submit_button').click(function () {
            var url = apiVars['root_url'] + 'forms/' + apiVars['form_id'] +  '/submissions';
            submitForm( url );
        });



        $('#get_entries_button').click(function () {
            getEntries(apiVars['signed_urls']['get_entries']);
        });

        $('#filter_entries_button').click(function () {

            var url = apiVars['signed_urls']['get_entries'];

            var search = {
                field_filters : [
                    {
                        key: '3',
                        value: 'Complaint',
                        operator: 'is'
                    }
                ]
            };
            url += '&search=' + JSON.stringify(search);
            getEntries(url);
        });

        $('#get_results_button').click(function () {
            getResults(apiVars['signed_urls']['get_results']);
        });

    });

    function submitForm(url){

        var inputValues = {
            input_1: $('#input_1').val(),
            input_2: $('#input_2').val(),
            input_3: $('.input_3:checked').val(),
            input_4: $('#input_4').val()
        };

        var data = {
            input_values: inputValues
        };

        $.ajax({
            url: url,
            type: 'POST',
            data: JSON.stringify(data),
            beforeSend: function (xhr, opts) {
                $sending.show();
            }
        })
            .done(function (data, textStatus, xhr) {
                $sending.hide();
                var response = JSON.stringify(data.response, null, '\t');
                $results.val(response);
            })
    }

    function getEntries(url){
        $.ajax({
            url: url,
            type: 'GET',
            beforeSend: function (xhr, opts) {
                $sending.show();
            }
        })
            .done(function (data, textStatus, xhr) {
                $sending.hide();
                var response = JSON.stringify(data.response, null, '\t');
                $results.val(response);
            })
    }

    function getResults(url){
        $.ajax({
            url: url,
            type: 'GET',
            beforeSend: function (xhr, opts) {
                $sending.show();
            }
        })
            .done(function (data, textStatus, xhr) {
                $sending.hide();
                var response = JSON.stringify(data.response, null, '\t');
                $results.val(response);
            })
    }

}(window.GFWebApiDemo = window.GFWebApiDemo || {}, jQuery));