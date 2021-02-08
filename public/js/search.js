$( document ).ready(function() {
    $("#search").click(function() {
        var authorityCode = $('#authority').val();
        var postcode = $('#postcode').val();
        $('.sortby option[value="rating"]').prop("selected", true);
        if (authorityCode != '-1') {
            search(authorityCode, postcode);
        } else {
            alert("Please select an authority");
        }
    });

    $("#clear").click(function() {
        clearPage();
    });
});

function search(authorityCode, postcode, sortBy = 'rating') {
    url = '/index.php/establishments?authorityCode=' + authorityCode + '&sortBy=' + sortBy;
    if (postcode != '') {
        url += '&postCode=' + postcode;
    }
    $.ajax({
        type: 'GET',
        url: url,
        crossDomain: true,
        dataType: "json"
    }).done(function(result, textStatus, jqXHR) {
        var data = result.data;
        if (!data.hasOwnProperty('message')) {
            //Response has a valid establishment list
            var html = ['<ul>'];
            data.forEach(function (establishment) {
                html.push('<li><code>' + establishment.name + ' -> (Rating: ' + establishment.rating.name + ', Postcode: ' + establishment.postCode + ')</code></li>');
            });
            html.push('</ul>');
            $('#search_result').html(html.join(''));
            $('.sortby').show();
        } else {
            $('#search_result').html('<div class="message">No result found</div>');
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        alert("Unable to perform the search");
    });
}

function clearPage() {
    $('#authority option[value="-1"]').prop("selected", true);
    $('#postcode').val('');
    $('#search_result').empty();
    $('.sortby').hide();
}

function sortBy() {
    var authorityCode = $('#authority').val();
    var postcode = $('#postcode').val();
    var sortBy = $('#sort').val();
    search(authorityCode, postcode, sortBy);
}
