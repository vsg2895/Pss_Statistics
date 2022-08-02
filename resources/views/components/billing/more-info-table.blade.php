<div class="table-responsive mt-4 more-info-table" data-company="{{ $company->id }}">
    <!-- Projects table -->
    <table class="table align-items-center table-flush table-striped more-detail-table">
        <thead class="thead-light">
        <tr>
            <th scope="col">{{__('CallId')}}</th>
            <th scope="col">{{__('Date')}}</th>
            <th scope="col">{{__('Agent Name')}}</th>
            <th scope="col">{{__('Status')}}</th>
            <th scope="col">{{__('Duration')}}</th>
            <th scope="col">{{__('Our Income')}}</th>
            @if(!isset($company) || !is_null($company->service_provider_id))
                <th scope="col">{{__('Provider Income')}}</th>
            @endif
            <th scope="col">{{__('A Number')}}</th>
            <th scope="col">{{__('B Number')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($moreInfo as $info)
            <tr>
                <th>
                    {{ $info->call_id }}
                </th>
                <td>{{ $info->date }}</td>
                <td scope="row">
                    @if($info->servit_id)
                        <a href="{{ route('admin.employee_statistics', [$info->servit_id]) }}">
                            {{ $info->servit_username }}
                        </a>
                    @else
                        -
                    @endif
                </td>
                <td>{{ $info->status }}</td>
                <td>{{ $info->duration }}</td>
                <td>{{ number_format($info->price, 2, '.', ' ')  . ' kr'}}</td>
                @if(!isset($company) || (isset($company) && !is_null($company->service_provider_id)))
                    <td>{{ number_format($info->provider_price, 2, '.', ' ')  . ' kr'}}</td>
                @endif
                <td>{{ $info->a_number }}</td>
                <td>{{ $info->b_number }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{--        {{ $moreInfo->links('vendor.pagination.bootstrap-4') }}--}}
    {!! str_replace('/?', '?', $moreInfo->appends(request()->query())->render('vendor.pagination.bootstrap-4')) !!}
</div>
