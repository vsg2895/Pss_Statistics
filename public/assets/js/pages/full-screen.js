const interval = 1000 * 60 * 5;//5 mins
initDataTable();
blinkBestAgent();

setInterval(function () {
    fetch('/full-screen', {
        headers: {
            "X-Requested-With": "XMLHttpRequest"
        },
    })
        .then(response => response.json())
        .then(data => {
            $('#dashboard_content').empty()
            $('#dashboard_content').append(data);
            initDataTable();
            blinkBestAgent();

        });
}, interval)

setInterval(function () {
    fetch('/get-live-data', {
        headers: {
            "X-Requested-With": "XMLHttpRequest"
        },
    })
        .then(response => response.json())
        .then(data => {
            setCallsQuData(data);
            setUserStatusData(data)
        });
}, 10000)

function initDataTable() {
    $(document).find('#user_stat_table').DataTable({
        "searching": false,
        "bInfo": false,
        "pageLength": 25,
        "lengthChange": false,
        "bPaginate": false,
        "order": [[3, "desc"]],
        language: {
            'paginate': {
                'previous': "<i class='ni ni-bold-left'></i>",
                'next': "<i class='ni ni-bold-right'></i>"
            }
        }
    });
}

function setCallsQuData(data) {
    if (data.calls_queue && data.user_status) {
        if ($('#calls-in-qu').text() !== data.calls_queue.calls) {
            $('#calls-in-qu').fadeOut(1000, function () {
                $('#calls-in-qu').text(data.calls_queue.calls);
                $('#calls-in-qu').fadeIn();
            });
        } else {
            $('#calls-in-qu').text(data.calls_queue.calls);
        }

        if ($('#agents').text() !== data.calls_queue.agents) {
            $('#agents').fadeOut(1000, function () {
                // $('#agents').text(data.calls_queue.agents);
                $('#agents').text(Object.keys(data.user_status).length);
                $('#agents').fadeIn();
            });
        } else {
            // $('#agents').text(data.calls_queue.agents);
            $('#agents').text(Object.keys(data.user_status).length);
        }

        if ($('#agents_ready').text() !== data.calls_queue.agentsReady) {
            $('#agents_ready').fadeOut(1000, function () {
                $('#agents_ready').text(data.calls_queue.agentsReady);
                $('#agents_ready').fadeIn();
            });
        } else {
            $('#agents_ready').text(data.calls_queue.agentsReady);
        }
    }
}

function setUserStatusData(data) {
    for (let userId in data.user_status) {
        let statusData = data.user_status[userId][0];
        let statusItem = $(`.status_${userId}`);

        let className = "bg-gray";
        let iconName = "ni ni-fat-delete";

        //bg-teal լիգհտ բլւե
        switch (statusData.status) {
            case "11"://in a call
                className = "bg-yellow";
                iconName = "ni ni-headphones";
                break;
            case "29"://ringing
                className = "bg-yellow";
                iconName = "ni ni-headphones";
                break;
            case "20"://ready
                className = "bg-green";
                iconName = "ni ni-headphones";
                break;
            case "10"://paused-chatting//17,18,19
                className = "bg-teal";
                iconName = "ni ni-chat-round";
                break;
            case "17"://paused//17,18,19
                className = "bg-gray";
                iconName = "ni ni-button-pause";
                break;
            case "18"://paused//17,18,19
                className = "bg-gray";
                iconName = "ni ni-button-pause";
                break;
            case "19"://paused//17,18,19
                className = "bg-gray";
                iconName = "ni ni-button-pause";
                break;
            case "12"://reply busy
                className = "bg-gradient-lighter";
                iconName = "ni ni-headphones";
                break;
            case "00"://logged off
                className = "bg-red";
                iconName = "ni ni-fat-remove";
                break;
            case "21"://no answer, busy
                className = "bg-blue";
                iconName = "ni ni-headphones";
                break;
        }

        let newStatusItem = getCurrentStatusTemplate(className, iconName);
        let statusChanged = !statusItem.find('.icon-sm').hasClass(className);

        if (statusChanged) {
            statusItem.fadeOut();
        }
        statusItem.empty();
        statusItem.append(newStatusItem);
        if (statusChanged) {
            statusItem.fadeIn();
        }
    }
}

function blinkBestAgent()
{
    let max = 0, thisProgress, bestItem;
    $('.user-progress').each(function () {
        thisProgress = parseInt($(this).text());
        let _this = $(this).parents('tr');
        if (thisProgress > max) {
            max = thisProgress;
            bestItem = _this;
        }
    });
    $('#user_stat_table').find('tr').removeClass('blinking-row');
    if (bestItem) bestItem.addClass('blinking-row');
}

function getCurrentStatusTemplate(className, icon) {
    return `
        <div class="icon-sm icon-shape ${className} text-white rounded-circle shadow ml-1">
            <i class="${icon}"></i>
        </div>
    `
}
