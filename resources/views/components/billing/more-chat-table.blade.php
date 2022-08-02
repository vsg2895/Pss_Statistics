<div class="table-responsive mt-4">
    <!-- Projects table -->
    <table class="table align-items-center table-flush table-striped more-detail-table-chat">
        <thead class="thead-light">
        <tr>
            <th scope="col">{{__('ChatId')}}</th>
            <th scope="col">{{__('Department')}}</th>
            <th scope="col">{{__('User')}}</th>
            <th scope="col">{{__('Date')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($companyChats as $chat)
            <tr>
                <th class="chat_detail" title="{{__('Show Conversations')}}">
                    <a href="#">{{ $chat->chat_id }}</a>
                    <i class="loading-icon-chat fa-lg fas fa-spinner d-none fa-spin hide"></i>
                </th>
                <td>{{ !is_null($chat->department) ? $chat->department->name : " - " }}</td>
                <td>{{ !is_null($chat->user) ? $chat->user->liveagent_username : " - " }}</td>
                <td>{{ $chat->date }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

