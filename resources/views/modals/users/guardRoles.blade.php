@if(isset($currRoles))
    <span
        class="input-group-text p-1 d-contents">{{__('Permission Relate To Role')}}</span>
    {{--    @dd($currRoles)--}}
    @if(count($currRoles))
        <select class="w-100 modal-select-create modal-create-permission" name="role[]"
                multiple="multiple">
            @foreach($currRoles as $role)
                <option value="{{ $role->id }}" class="roleType">
                    {{ $role->name }}
                </option>
            @endforeach
        </select>
    @else
        <span
            class="input-group-text p-1 d-contents">{{__('Permission Relate To Role')}}</span>
        <select class="w-100 modal-select-create modal-create-permission" name="role[]"
                multiple="multiple">
            <optgroup class="roleType"> {{ __('Dont Available Roles In This Guard') }}</optgroup>
        </select>
    @endif
@else
    <span
        class="input-group-text p-1 d-contents">{{ __('Select Guard Type') }}</span>
@endif
