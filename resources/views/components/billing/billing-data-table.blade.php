<div class="table-responsive mt-4">
    <!-- Projects table -->
    <table class="table align-items-center table-flush table-striped">
        <thead class="thead-light">
        <tr>
            <th scope="col">{{__('Title')}}</th>
            <th scope="col">{{__('Value')}}</th>
            <th scope="col">{{__('Our Income')}}</th>
            @if(!isset($company) || !is_null($company->service_provider_id))
                <th scope="col">{{__('Provider Income')}}</th>
            @endif
        </tr>
        </thead>
        <tbody>
        @foreach($cdrStatistics as $key => $cdrStatistic)
            @if($key == 'total_income' && isset($fixed))
                <tr>
                    <th>
                        {{__('Monthly Fee')}}
                    </th>
                    <td>-</td>
                    <td>{{ number_format($fixed['our_fee'], 2, '.', ' ') . ' kr' }}</td>
                    @if(!isset($company) || !is_null($company->service_provider_id))
                        <td>{{ number_format($fixed['provider_fee'], 2, '.', ' ') . ' kr' }}</td>
                    @endif
                </tr>
            @endif
            <tr class="{{($key == 'total_income') ? 'total_tr' : ''}}">
                <th>
                    {{__($cdrStatistic['name'])}}
                </th>
                @if(isset($cdrStatistic['p_count']) && (!isset($company) || !is_null($company->service_provider_id)))
                    <td>{{$cdrStatistic['count'] . ' / ' . $cdrStatistic['p_count']}}</td>
                @else
                    <td>{{$cdrStatistic['count']}}</td>
                @endif
                <td>{{$cdrStatistic['fee'] != '-' && $cdrStatistic['fee']['price'] != '-' ? $cdrStatistic['fee']['price'] . ' kr' : '-'}}</td>
                @if(!isset($company) || (isset($company) && !is_null($company->service_provider_id)))
                    <td>{{$cdrStatistic['fee'] != '-' && $cdrStatistic['fee']['p_price'] != '-' ? $cdrStatistic['fee']['p_price'] . ' kr' : '-'}}</td>
                @endif
            </tr>
        @endforeach


        </tbody>
    </table>
</div>
