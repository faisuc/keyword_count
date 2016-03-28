jQuery(document).ready(function($)
{

    $( '#alt_form' ).submit(function()
    {
        var $form       = $(this);
        var $ajaxUrl    = $( '#ajaxUrl' ).val();
        var $container  = $( '#result' );

        var $showposts  = $.trim( $( 'input[name="showposts"]' ).val() );
        var $keywords   = $.trim( $( 'input[name="keywords"]' ).val() );
        var $orderby    = $.trim( $( 'input[name="orderby"]:checked' ).val() );
        var $sort       = $.trim( $( 'input[name="sort"]:checked' ).val() );

        $.ajax({
            url : $ajaxUrl ,
            type : 'POST' ,
            dataType : 'json' ,
            data : {
                action      : 'form_submit' ,
                form_submit : true ,
                showposts   : $showposts ,
                keywords    : $keywords ,
                orderby     : $orderby ,
                sort        : $sort
            } ,
            success : function( data )
            {
                if ( data.status )
                {
                    var $table_result = data.result;
                    $container.empty();
                    $container.html($table_result);
                    $('#result table').dataTable( {
                      "pageLength": 1
                    } );
                }
            }
        });

        return false;
    });

    $( 'input[name="keywords"]' ).on( 'keyup' , function()
    {
        var $keywords = $.trim( $(this).val() );
        var $ajaxUrl    = $( '#ajaxUrl' ).val();
        var $html = "";

        $.ajax({
            url : $ajaxUrl ,
            type : 'POST' ,
            dataType : 'json' ,
            data : {
                action          : 'keywords_search' ,
                keywords_search  : true ,
                keywords        : $keywords
            } ,
            success : function( data )
            {
                if ( data.results.length > 0 )
                {
                    $html += "<ul>";
                    for ( var i = 0 ; i < data.results.length ; i++ )
                    {
                        $html += "<li><a href='#' class='search_results_link'>" + data.results[i].keywords + "</a></li>";
                    }
                    $html += "</ul>";
                    $( '#tag_update' ).html( $html );
                    document.getElementById('tag_update').style.display = "block";
                }
            }
        });

        $( document ).on( 'click' , '.search_results_link' , function(e)
        {
            e.preventDefault();

            var $value = $(this).text();
            $( 'input[name="keywords"] ').val( $value );
            document.getElementById('tag_update').style.display = "none";
        });

    });

});
