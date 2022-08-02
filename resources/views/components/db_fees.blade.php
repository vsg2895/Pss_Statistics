@foreach($settings as $key => $setting)
    <tr>
        <td>{{$setting->name}}</td>
        <td class="d-flex align-items-baseline">
            <input type="hidden" name="fee_type_ids[]"
                   value="{{$setting->feeType->id}}">
            <input type="text" id="fee_field_{{ $key }}"
                   class="fee_input fee_input_value"
                   name="values[]"
                   value="{{$fees[$setting->feeType->slug] ?? $setting->value}}">
        </td>
        <td>
            <div class="row">
                @if((Route::currentRouteName() === "admin.companies.dashboard" && $setting->slug !== 'monthly_fee')
                     || Route::currentRouteName() !== "admin.companies.dashboard")
                    {{--                @if($setting->slug !== 'monthly_fee')--}}
                    <div
                        class="w-25 fee-checkbox-div d-flex justify-content-center">
                        <input type="checkbox"
                               name="checks[{{ $setting->slug }}]"
                               class="ml-2 custom-control-input-fee current_change"
                               id="checked_fee_{{ $key }}"
                               data-slug="{{$setting->slug}}"
                               value="{{ $fees[$setting->feeType->slug] ?? $setting->value }}">
                    </div>
                    <div
                        class="w-75 fee-checkbox-div d-flex justify-content-center">
                        @if(array_key_exists($setting->name,$feesWithTableNames))
                            {{ $feesWithTableNames[$setting->name] }}
                        @endif
                    </div>
                @endif
            </div>
        </td>
    </tr>
@endforeach
