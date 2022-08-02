<tr class="current-conversation">
    <td style="background-color: white" colspan="4">
        <div class="messages-container">
            <div class="close close-conversation-block">
                <i class="fa fa-times close-conversation"></i>
            </div>
            @foreach($chatData as $keyGroup => $group)
                <div class="message-group">
                    <div class="row align-items-center no-margin-right-conversation">
                        <div class="col-xl-3 col-lg-3 col-md-3 col-6">
                            <h3>{{__('Message Group')}}
                                #{{$group['messagegroupid']}}</h3>
                        </div>
                        <div
                            class="col-xl-1 col-lg-1 col-md-1 col-3 {{ $keyGroup == 0 ? 'ml-xl-1 ml-lg-1 ml-md-1 ml-2' : '' }} mobile-icon-block">
                            <i class="fa-solid fa-minus cursor-pointer toggle-current-message-group"></i>
                        </div>
                    </div>
                    <div class="messages-contents">
                        @foreach($group['messages'] as $key => $message)
                            <div class="message-wrapper {{ $key % 2 == 0 ? 'start' : 'end' }}">
                            <span class="message">
                                  {!!$message['message']!!}
                                <div class="message-date">
                                    {{$message['datecreated']}}
                                </div>
                            </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </td>
</tr>


