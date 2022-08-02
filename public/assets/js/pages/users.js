//-------------- user -----------------
$('.edit-user').click(function () {
    $.get(`/admin/users/${$(this).attr('data-id')}/edit`, function (data, status) {
        let user = data.user;
        let action = $('#user_update_form').attr('action');
        $('#user_name_update').val(user.name);
        $('#user_email_update').val(user.email);
        action = action.replace(':id', user.id);
        $('#user_update_form').attr('action', action);
    });
})

$('.delete-role').click(function () {
    let action = $('#role_delete_form').attr('action');
    action = action.replace(':role', $(this).attr('data-id'));
    $('#role_delete_form').attr('action', action);
})

$('.delete-permission').click(function () {
    console.log($('#permission_delete_form'))
    let action = $('#permission_delete_form').attr('action');
    console.log(action)
    action = action.replace(':permission', $(this).attr('data-id'));
    $('#permission_delete_form').attr('action', action);
})

$('.delete-user').click(function () {
    let action = $('#user_delete_form').attr('action');
    action = action.replace(':id', $(this).attr('data-id'));
    $('#user_delete_form').attr('action', action);
})
// Check modals anchor isset delete to url

$(document).on('click', '.close-role-permission-store', function () {
    history.replaceState('', document.title, window.location.origin + window.location.pathname + window.location.search);
})
$(document).on('click', '.close-role-permission', function () {
    history.replaceState('', document.title, window.location.origin + window.location.pathname + window.location.search);
})

$('.attach-role').click(function (e) {
    let self = $(this);
    let forms = ['#user_attach_role_form', '#user_delete_role_form', '#user_attach_permission_form', '#user_delete_permission_form'];
    getUserRolesPermissions(self, forms)
})

function showCheckboxes(key) {
    var checkboxes = document.getElementById(key);
    if (checkboxes.style.display == "block") {
        checkboxes.style.display = "none";
    } else {
        checkboxes.style.display = "block";
    }
}

$(document).on('click', '.close-role', function () {
    $('#role_attach').removeClass('show');
    $('#role_attach').css('display', 'none');
    $('#role_attach').modal('hide');
    $('body').removeClass('modal-open');
    $('.modal-backdrop').remove();
})

// Permission part

$('.permission-toggle').click(function () {
    $('#permission-part').toggleClass('d-none');
    $('.parent-tables-row').toggleClass('justify-content-between');
    if (!$('#permission-part').hasClass('d-none')) {
        $('.permission-part-icon').removeClass('.ni ni-fat-add');
        $('.permission-part-icon').addClass('.ni ni-fat-delete');
    } else {
        $('.permission-part-icon').addClass('.ni ni-fat-add');
        $('.permission-part-icon').removeClass('.ni ni-fat-delete');
    }
})

$(document).on('click', '.tabModal', function () {

    let id = $(this).attr('id');
    let currentFormId = $("div[aria-labelledby = " + id + "]").children('form').attr('id');
    $('.submit-modal-forms').attr('form', currentFormId)

});
$(document).on('click', '.tabModalRoleAction', function () {

    let id = $(this).attr('id');
    let currentFormId = $("div[aria-labelledby = " + id + "]").children('form').attr('id');
    $('.submit-modal-forms').attr('form', currentFormId)

});
$(document).on('click', '.tabModalCreate', function () {

    let id = $(this).attr('id');
    let currentFormId = $("div[aria-labelledby = " + id + "]").children('form').attr('id');
    $('.submit-create-modal-forms').attr('form', currentFormId)

});


function getUserRolesPermissions(self, forms) {
    fetch(`/admin/users/roles-permissions/${self.attr('data-id')}`, {
        method: "get",
        headers: {
            'Accept': 'application/json, text/plain',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
        .then(function (response) {
            return response.json();
        })
        .then(function (result) {
            $('#attachModal').html();
            $('#attachModal').html(result.view)
            $('#role_attach').addClass('show');
            $('#role_attach').css('display', 'block');

            for (let i = 0; i < forms.length; i++) {
                let action = $(forms[i]).attr('action');
                action = action.replace(':user', self.attr('data-id'));
                console.log($(forms[i]), ' - form', action, ' - action')
                $(forms[i]).attr('action', action);
            }

            $('.modal-select').select2();
            $('.select2-container').css('width', '100%');

        })
        .catch(function (error) {
            console.log('Request failed', error);
        });

}

//-------------- /user -----------------

// roles permissions

$('.action-role').on('click', function () {
    let self = $(this);
    let forms = ['#action_attach_permission_form', '#action_dettach_permission_form'];
    getRolePermissions(self, forms)

})

$('.checkRoleGuard').on('click', function () {
    let self = $(this);
    fetch(`/admin/get/guard-roles/${self.val()}`, {
        method: "get",
        headers: {
            'Accept': 'application/json, text/plain',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
        .then(function (response) {
            return response.json();
        })
        .then(function (result) {
            console.log(result, 'guard')
            $('#guardsRoles').html(result.view)
            $('.modal-select-create').select2();
            $('.select2-container').css('width', '100%');
        })
        .catch(function (error) {
            console.log('Request failed', error);
        });
})

function getRolePermissions(self, forms) {
    fetch(`/admin/get/permissions/${self.attr('data-id')}`, {
        method: "get",
        headers: {
            'Accept': 'application/json, text/plain',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
        .then(function (response) {
            return response.json();
        })
        .then(function (result) {
            console.log(result.dontAvailablePermissions)
            console.log(result.availablePermissions)
            for (let i = 0; i < result.dontAvailablePermissions.length; i++) {
                $('#attachPermissionSelect').append("<option value=" + result.dontAvailablePermissions[i].id + "> " + result.dontAvailablePermissions[i].name + " </option>")

            }
            for (let i = 0; i < result.availablePermissions.length; i++) {
                $('#detachPermissionSelect').append("<option value=" + result.availablePermissions[i].id + "> " + result.availablePermissions[i].name + " </option>")

            }
            for (let i = 0; i < forms.length; i++) {
                let action = $(forms[i]).attr('action');
                action = action.replace(':role', self.attr('data-id'));
                console.log($(forms[i]), ' - form', action, ' - action')
                $(forms[i]).attr('action', action);
            }
            $('#action_role').text($('#action_role').attr('data-text') + " - " + result.role.name)
            $('.modal-select-action').select2();
            $('.select2-container').css('width', '100%');

        })
        .catch(function (error) {
            console.log('Request failed', error);
        });

}

//-------------- provider user -----------------
$('.edit-provider-user').click(function () {
    $.get(`/admin/service-provider-users/${$(this).attr('data-id')}/edit`, function (data, status) {
        let user = data.user;
        let action = $('#provider_user_update_form').attr('action');
        $('#provider_user_name_update').val(user.name);
        $('#provider_user_email_update').val(user.email);
        $('#service_provider_id').val(user.service_provider_id);
        action = action.replace(':id', user.id);
        $('#provider_user_update_form').attr('action', action);
    });
})
$('.delete-provider-user').click(function () {
    let action = $('#provider_user_delete_form').attr('action');
    action = action.replace(':id', $(this).attr('data-id'));
    $('#provider_user_delete_form').attr('action', action);
})
//-------------- /provider user -----------------

//-------------- agent -----------------
$('.edit-agent').click(function () {
    $.get(`/admin/agents/${$(this).attr('data-id')}/edit`, function (data, status) {
        let agent = data.agent;
        let action = $('#agent_update_form').attr('action');
        $('#agent_name_update').val(agent.servit_username);
        $('#agent_email_update').val(agent.email);
        $('#agent_point').val(agent.main_point);
        action = action.replace(':id', agent.id);
        $('#agent_update_form').attr('action', action);
    });
})
//-------------- /agent -----------------


