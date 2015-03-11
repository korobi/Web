$(function() {
    $('#permission-add').click(function(event) {
        event.preventDefault();

        var permissionList = $('#permissions');
        var permission = permissionList.attr('data-prototype');
        permission = permission.replace(/__name__/g, permissionCount);
        var permissionLi = $('<tr data-content="form_permissions_' + permissionCount + '"></tr>').html(
            '<td>' + permission + '</td><td><a href="#" id="permission-del" data-related="form_permissions_' + permissionCount + '">Remove</a></td>'
        );
        permissionLi.appendTo(permissionList);

        permissionCount++;
    });

    $('body').on('click', 'a', function(event) {
        if(event.currentTarget.text == 'Remove') {
            console.log(event);
            event.preventDefault();
            var id = $(this).attr('data-related');
            $('*[data-content="' + id + '"]').remove();
            permissionCount--;
        }
    });
});
