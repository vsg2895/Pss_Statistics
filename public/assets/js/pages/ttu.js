// console.log(route('admin.insert-tele-two-users-more'))
$('.show-info').click(function () {
    let self = $(this);
    let url = route('admin.insert-tele-two-users-more');
    let id = self.attr('data-id');
    if (id !== undefined && id !== "") {
        self.prop('disabled', true);
        getTtuMoreInfo(url, self, id)
    }

})

$('.update-ttu').click(function () {
    $(this).css('pointer-events', 'none')
})

function getTtuMoreInfo(url, self, id) {

    self.find(".loading-icon-info").removeClass("d-none");
    fetch(url, {
        method: "post",
        headers: {
            'Accept': 'application/json, text/plain',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        body: JSON.stringify({
            id: id,
        })
    })
        .then(function (response) {
            return response.json();
        })
        .then(function (result) {
            self.attr('data-target', '#show_ttu_info')
            let attr = self.attr('data-target');
            let name = self.attr('data-name');
            let id = self.attr('data-id');
            $('#ttuFullName').val("");
            $('#ttuId').val("");
            $('#ttuAvailable').val("");
            $('#ttuFullName').val(name);
            $('#ttuId').val(id);
            $('#ttuAvailable').val(result.res);
            $(attr).modal('show');
            self.prop('disabled', false);
            self.find(".loading-icon-info").addClass("d-none");
            console.log(result);
        })
        .catch(function (error) {
            console.log('Request failed', error);
        });

}

if (new URL(window.location.href).searchParams.get('start_date')) {
    $('#compare-tab').addClass('active show');
    $('#compare').addClass('active show');
    $('#range-tab').removeClass('active show');
    $('#range').removeClass('active show');
}
initDataTable();

function initDataTable() {
    $(document).find('#tele_two_users_table').DataTable({
        "bInfo": false,
        "pageLength": 25,
        "lengthChange": false,
        "order": [[3, "desc"]],
        language: {
            'paginate': {
                'previous': "<i class='ni ni-bold-left'></i>",
                'next': "<i class='ni ni-bold-right'></i>"
            }
        }
    });
}
