<table>
    <thead>
    <tr>
        <th>{{__('Start')}}</th>
        <th>{{__('End')}}</th>
        <th>{{__('Id')}}</th>
        {{--        <th>{{__('Call Id')}}</th>--}}
        <th>{{__('Status')}}</th>
        {{--        <th>{{__('Company Id')}}</th>--}}
        {{--        <th>{{__('Agent')}}</th>--}}
        @if(count($checkMonths))
            <th>{{__('Free Call')}}</th>
        @endif
        <th>{{__('Calls Fee')}}</th>
        <th>{{__('Bookings Fee')}}</th>
        <th>{{__('Above 60 Fee')}}</th>
        <th>{{__('Cold Transferred Calls Fee')}}</th>
        <th>{{__('Warm Transferred Calls Fee')}}</th>
        <th>{{__('Time Above Seconds')}}</th>
        <th>{{__('Messages Fee')}}</th>
        <th>{{__('Sms Fee')}}</th>
        <th>{{__('Emails Fee')}}</th>
        @if(!is_null($company->service_provider_id))
            <th>{{__('P_Free Call')}}</th>
            <th>{{__('P_Calls Fee')}}</th>
            <th>{{__('P_Bookings Fee')}}</th>
            <th>{{__('P_Above 60 Fee')}}</th>
            <th>{{__('P_Cold Transferred Calls Fee')}}</th>
            <th>{{__('P_Warm Transferred Calls Fee')}}</th>
            <th>{{__('P_Time Above Seconds')}}</th>
            <th>{{__('P_Messages Fee')}}</th>
            <th>{{__('P_Sms Fee')}}</th>
            <th>{{__('P_Emails Fee')}}</th>
        @endif
        <th>{{__('Chats Fee')}}</th>
{{--        @if(!is_null($company->service_provider_id))--}}
            <th>{{__('P_Chats Fee')}}</th>
{{--        @endif--}}
    </tr>
    </thead>
    <tbody>

    @foreach($billingByCompany as $key => $invoice)
        <tr>
            <td>{{ $startExcel }}</td>
            <td>{{ $endExcel }}</td>
            <td>{{ $invoice->billing_id }}</td>
            {{--            <td>{{ $invoice->call_id }}</td>--}}
            <td>{{ $invoice->status }}</td>
            {{--            <td>{{ $invoice->company_id }}</td>--}}
            {{--            <td>{{ $invoice->agent_id }}</td>--}}
            @if(count($checkMonths))
                <td>{{ $invoice->free_call ? '1' : '0' }}</td>
            @endif
            <td>{{ $invoice->calls_fee }}</td>
            <td>{{ $invoice->bookings_fee }}</td>
            <td>{{ $invoice->above_60_fee }}</td>
            <td>{{ $invoice->cold_transferred_calls_fee }}</td>
            <td>{{ $invoice->warm_transferred_calls_fee }}</td>
            <td>{{ $invoice->time_above_seconds }}</td>
            <td>{{ $invoice->messages_fee }}</td>
            <td>{{ $invoice->sms_fee }}</td>
            <td>{{ $invoice->emails_fee }}</td>
            @if(!is_null($company->service_provider_id))
                @if(count($checkMonths))
                    <td>{{ $invoice->p_free_call }}</td>
                @endif
                <td>{{ $invoice->p_calls_fee }}</td>
                <td>{{ $invoice->p_bookings_fee }}</td>
                <td>{{ $invoice->p_above_60_fee }}</td>
                <td>{{ $invoice->p_cold_transferred_calls_fee }}</td>
                <td>{{ $invoice->p_warm_transferred_calls_fee }}</td>
                <td>{{ $invoice->p_time_above_seconds }}</td>
                <td>{{ $invoice->p_messages_fee }}</td>
                <td>{{ $invoice->p_sms_fee }}</td>
                <td>{{ $invoice->p_emails_fee }}</td>
            @endif
            @if($key === 0)
                <td>{{ $chatFee }}</td>
{{--                @if(!is_null($company->service_provider_id))--}}
                    <td>{{ $p_chatFee }}</td>
{{--                @endif--}}
            @endif
        </tr>
    @endforeach

    </tbody>
</table>
