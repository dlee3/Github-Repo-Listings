
$(document).ready(function() {

    // Add fancy stuff to table
    $('#repoTable').DataTable({
        "order": []  // Don't order the table on initialisation
    });


    // Capture row clicks and update the Repo Details
    $('#repoList').on('click', '.clickable-row', function() {
        var id = $(this).data("id");
        var divId = '#repo' + id;

        $('.repo-container').addClass('hidden').fadeOut();

        $(divId).fadeIn().removeClass('hidden');
    });
});



// If the Repo list and details are side-by-side then let's
// have it scroll with the list.
var elementPosition = $('#repoDetails').offset();

$(window).scroll(function(){
    var windowWidth = $(window).width();

    // We only want to do this if the list
    // and details are side-by-side
    if ( windowWidth >= 991) {
        if($(window).scrollTop() > elementPosition.top){
            $('#repoDetails').css('position','fixed')
                .css('top','0').css('right', '0');
        } else {
            $('#repoDetails').css('position','static');
        }
    } else
    {
        $('#repoDetails').css('position','static');
    }


});