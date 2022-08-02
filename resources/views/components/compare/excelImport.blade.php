<div class="col-6">
    <table class="table compare-table">
        @if(count($data['data']))
            <h3>{{ $currentKey }}</h3>
            <p>{{__('Total companies')}}: {{count($data['data'])}}</p>
            <p>
                {{isset($data['netto']) ? __('New companies') : __('Lost companies')}}
                : {{count($data['diffIds'])}}
                @if(isset($data['netto']))
                    | {{__('Netto')}}: {{$data['netto']['companies']}}
                    <span
                        class="{{$data['netto']['companies'] >= 0 ? 'text-success' : 'text-danger'}}">
                                            <i class="fa {{$data['netto']['companies'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'}}"></i>
                                        </span>
                @endif
            </p>
            <p>
                {{isset($data['netto']) ? __('New') : __('Churn')}}: {{$data['diff_percent']}}%
                @if(isset($data['netto']))
                    <span class="text-success">
                                            <i class="fa fa-arrow-up ql-color-green"></i>
                                        </span>
                    <span>
                                            | {{__('Netto')}}: {{$data['netto']['percent']}}%
                                            <span
                                                class="{{$data['netto']['percent'] >= 0 ? 'text-success' : 'text-danger'}}">
                                                <i class="fa {{$data['netto']['percent'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'}}"></i>
                                            </span>
                                        </span>
                @else
                    <span class="text-danger">
                                            <i class="fa fa-arrow-down"></i>
                                        </span>
                @endif
            </p>
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">{{ __('Company Name') }}</th>
                <th scope="col">{{ __('Calls Count') }}</th>
                <th>{{isset($data['netto']) ? __('New') : __('Lost')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data['data'] as $keyData => $elem)
                <tr>
                    <td>{{ $elem[0]->company_id }}</td>
                    <td>
                        {{ $elem[0]->name }}
                    </td>
                    <td>{{$elem[0]->calls_count}}</td>
                    <td>{{$currentKey == $compare_start . ' - ' . $compare_end
                                          && array_key_exists($keyData, $compareData[$compare_s_start . ' - ' . $compare_s_end]['data']->toArray())
                                          || $currentKey == $compare_s_start . ' - ' . $compare_s_end
                                          && array_key_exists($keyData, $compareData[$compare_start . ' - ' . $compare_end]['data']->toArray()) ? 0 : 1}}
                    </td>
                </tr>
            @endforeach
            </tbody>
        @else
            <thead>
            <tr>
                <th scope="col">
                    {{ __('In') }} {{ $currentKey }} {{ __('There are no data') }}
                </th>
            </tr>
            </thead>
        @endif

    </table>
</div>


