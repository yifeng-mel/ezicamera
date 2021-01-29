<script>
var currentPage = {{$current_page}};
var previousPage = {{$previous_page ?? 'null'}};
var nextPage = {{$next_page ?? 'null'}};
var totalPages = {{$total_pages}}
var startEntry = {{$start_entry}}
var endEntry = {{$end_entry}}
var noOfFiles = {{$no_of_files}}

$( document ).ready(function() {
    $("#page-select").val(currentPage);

    $('body').on('click', '#previous-btn', function(){
        $.get( "/api/videos/filter", { page:previousPage } )
            .done(function( data ) {
                processData(data);
            });
    })

    $('body').on('click', '#next-btn', function(){
        $.get( "/api/videos/filter", { page:nextPage } )
            .done(function( data ) {
                processData(data);
            });
    })

    $('body').on('change', '#page-select', function(e){
        let page = $(this).val()
        $.get( "/api/videos/filter", { page:page } )
            .done(function( data ) {
                processData(data);
            });        
    })    

    $('body').on('click', '[data-filename-value]', function(){
        $('.overlay').removeClass('d-none');
        $('.downloading').removeClass('d-none');

        let fileName = $(this).attr('data-filename-value');
        let byteSize = $(this).attr('data-byte-size-value');
        
        websocketServerConnect(fileName, byteSize)
    })

    $('body').on('click', '#save-file-btn', function(){
        $('.overlay').addClass('d-none');
        $('.downloading').addClass('d-none');
        $('.downloaded').addClass('d-none');
    })

    function processData(data)
    {
        data = JSON.parse(data)
        currentPage = data.current_page
        previousPage = data.previous_page
        nextPage = data.next_page
        totalPages = data.total_pages
        startEntry = data.start_entry
        endEntry = data.end_entry
        noOfFiles = data.no_of_files

        let newTrs = '<tbody>' + data.files_arr.reduce(function(all, e){
            return all + '<tr><td>' + e.date + '</td><td>' +e.time + '</td><td>' +e.duration+ '</td><td>' +e.size+ '</td><td><button class="btn btn-link p-0" role="link"  data-filename-value=' +e.filename+ ' data-byte-size-value=' +e.byte_size+ '>download</button></td></tr>'
        }, '') + '</tbody>'

        $('tbody').replaceWith(newTrs)
        $('#start_entry').html(startEntry)
        $('#end_entry').html(endEntry)
        $('#total_entry').html(noOfFiles)
        $("#page-select").val(currentPage)
        $('#total_pages').html(totalPages)

        if (previousPage) {
            $('#previous-btn').prop('disabled', false);
        } else {
            $('#previous-btn').prop('disabled', true);
        }

        if (nextPage) {
            $('#next-btn').prop('disabled', false);
        } else {
            $('#next-btn').prop('disabled', true);
        }
    }
});
</script>
@include('video.webrtc')