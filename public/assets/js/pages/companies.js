var url = new URL(window.location.href)
var start = url.searchParams.get('start') !== null ? url.searchParams.get('start') + " - " : "";
var end = url.searchParams.get('end') !== null ? url.searchParams.get('end') : "";

getGeneralPDF($("#current-company-export-block")[0])
getGeneralPDF($("#current-company-export-block-without")[0], null, $('#current-company-export-block-without'))
$('#export_current_companies_pdf').click(function () {
    generateGeneralPDF($('.header-name').text(), start, end)
})

$('#export_current_companies_pdf_by_company').click(function () {
    generateGeneralPDF($('.header-name').text(), start, end, 'By Company')
})


$('.companies-edit').click(function () {
    $.get(`/admin/companies/${$(this).attr('data-id')}/edit`, function (data, status) {
        let company = JSON.parse(data).company;
        let action = $('#companies_form').attr('action');
        action = action.replace(':id', company.id);
        $('#companies_form').attr('action', action);
    });
})

$('.delete-data').click(function () {
    let _this = $(this);
    if (_this.hasClass('provider-delete')) {
        _this.parents('td').find('input').val('');
    } else {
        _this.parents('td').find('input').remove();
    }
    console.log(_this.parents('form').attr('action'))
    _this.parents('form').submit();
})

$(document).on('click', '.more-company-button', function () {

    let self = $(this);
    let url = self.attr('data-url');
    let start = $('body').find('input[name="start"]').val();
    let end = $('body').find('input[name="end"]').val();
    console.log(url);
    self.prop('disabled', true);
    url !== undefined
        ? getCompanyMoreInfo(url, self)
        : hideMoreData(self)

})

function hideMoreData(elem) {
    let dataLess = elem.attr('data-less');
    let dataMore = elem.attr('data-more');
    elem.closest('#include-company-more').find('.more-detail-table').toggleClass('d-none');
    $('.more-detail-table').next().toggleClass('d-flex');
    $('.more-detail-table').next().toggleClass('d-none');
    elem.closest('#include-company-more').find('.start-append-more').toggleClass('d-flex');
    elem.closest('#include-company-more').find('.start-append-more').toggleClass('d-none');
    if (elem.closest('#include-company-more').find('.start-append-more').hasClass('d-none')) {
        elem.closest('#include-company-more').find('.more-company-button').text(dataMore)
        $('.in-center-more-detail').addClass('d-none');
        $('.more-company-chat-button').removeClass('d-none');
    } else {
        elem.closest('#include-company-more').find('.more-company-button').text(dataLess)
        $('.in-center-more-detail').removeClass('d-none');
        $('.more-company-chat-button').addClass('d-none');
    }

    elem.prop('disabled', false);
}

function hideMoreDataChat(elem) {
    let dataLess = elem.attr('data-less');
    let dataMore = elem.attr('data-more');
    elem.closest('#include-company-more').find('.more-detail-table-chat').toggleClass('d-none');
    elem.closest('#include-company-more').find('.more-detail-table-chat').parent().toggleClass('d-none')
    elem.closest('#include-company-more').find('.start-append-more-chat').toggleClass('d-flex');
    elem.closest('#include-company-more').find('.start-append-more-chat').toggleClass('d-none');
    if (elem.closest('#include-company-more').find('.start-append-more-chat').hasClass('d-none')) {
        elem.closest('#include-company-more').find('.more-company-chat-button').text(dataMore)
        $('.in-center-more-detail-chat').addClass('d-none');
        $('.more-company-button').removeClass('d-none');
    } else {
        elem.closest('#include-company-more').find('.more-company-chat-button').text(dataLess)
        $('.in-center-more-detail-chat').removeClass('d-none');
        $('.more-company-button').addClass('d-none');
    }

    elem.prop('disabled', false);
}

//Paginate in more calls
$(document).on('click', '.more-info-table .page-link', function (e) {
    e.preventDefault()
    let self = $(this);
    let url = route('admin.companies.more-info', $('.more-info-table').attr('data-company')) + self.attr('href');
    getCompanyMoreInfo(url, self)
    console.log(url)
})

// end paginate logic

function getCompanyMoreInfo(url, self) {
    $(".loading-icon").removeClass("d-none");
    fetch(url, {
        headers: {
            'Accept': 'application/json, text/plain',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
    })
        .then(function (response) {
            return response.json();
        })
        .then(function (result) {
            console.log(result);
            if (result.view) {
                $('#include-company-more').html(result.view)
                self.prop('disabled', false);
                $(".loading-icon").addClass("d-none");
                $('.more-company-chat-button').addClass('d-none');
            }
            if (result.errors) {
                window.location.reload();
            }
        })
        .catch(function (error) {
            console.log('Request failed', error);
        });

}

function getCompanyMoreChat(url, self) {
    $(".loading-icon-chat").removeClass("d-none");
    // $('.more-company-button').addClass('d-none');
    axios.post(url)
        .then(res => {
            $('#include-company-more').html(res.data.view)
            self.prop('disabled', false);
            $(".loading-icon-chat").addClass("d-none");
            $('.more-company-button').addClass('d-none');
        }).catch(error => { // Request error
        alert(error);

    });
}

$(document).on('click', '.more-company-chat-button', function () {
    let self = $(this);
    let url = self.attr('data-url');
    self.prop('disabled', true);
    url !== undefined
        ? getCompanyMoreChat(url, self)
        : hideMoreDataChat(self)
})

$(document).on('click', '.chat_detail', function (e) {
    e.preventDefault()
    let self = $(this);
    let url = route('admin.get.chat.conversations')
    let chat_id = $(this).text();
    let curRow = self.closest('tr');
    let messagesContent = curRow.next();
    if (!messagesContent.hasClass("current-conversation")) {
        self.find('.loading-icon-chat').toggleClass('d-none');
        axios.post(url, {
            chat_id: chat_id,
        }).then(res => {
            console.log(res, 'chatData')
            self.find('.loading-icon-chat').toggleClass('d-none');
            curRow.after(res.data.view)
        }).catch(error => { // Request error
            self.find('.loading-icon-chat').toggleClass('d-none');
            alert(error);
        });
    } else {
        messagesContent.toggleClass('d-none');
    }

})

$(document).on('click', '.toggle-current-message-group', function () {
    $(this).closest('.message-group').find('.messages-contents').toggleClass('d-none');
    $(this).toggleClass('fa-minus');
    $(this).toggleClass('fa-plus');
})
$(document).on('click', '.close-conversation', function () {
    $(this).closest('.current-conversation').addClass('d-none')
})
