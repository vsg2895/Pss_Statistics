@if(isset($moreInfo))
    <div class="start-append-more d-flex align-items-baseline">
        <button type="button" data-more="{{__('Show More Calls')}}" data-less="{{__('Show Less Calls')}}"
                class="btn btn-sm btn-info more-company-button cursor-pointer">{{__('Show Less Calls')}}
            <i class="loading-icon fa-lg fas fa-spinner d-none fa-spin hide"></i>
        </button>
    </div>
    {{-- More Info Table Component--}}
    @if(!empty($moreInfo))
        <h4 class="text-center in-center-more-detail">{{__('More Call Details')}}</h4>
        <x-billing.more-info-table :company="$company" :page="$page" :moreInfo="$moreInfo"/>
        <button type="button" data-more="{{__('Show More Calls')}}" data-less="{{__('Show Less Calls')}}"
                class="btn btn-sm btn-info more-company-button second-more-button cursor-pointer">{{__('Show Less Calls')}}
            <i class="loading-icon fa-lg fas fa-spinner d-none fa-spin hide"></i>
        </button>
    @else
        <h3 class="text-center in-center-more-detail">{{ __('No detailed information found') }}</h3>
    @endif
@else
    <button type="button"
            data-url="{{ route('admin.companies.more-info', ['company' => $company,'start' => request()->get('start'), 'end' => request()->get('end')]) }}"
            class="btn btn-sm btn-info more-company-button cursor-pointer">{{__('Show More Calls')}}
        <i class="loading-icon fa-lg fas fa-spinner d-none fa-spin hide"></i>
    </button>
@endif

@if(isset($companyChats))
    <div class="start-append-more-chat d-flex align-items-baseline">
        <button type="button" data-more="{{__('Show More Chat')}}" data-less="{{__('Show Less Chat')}}"
                class="btn btn-sm btn-info more-company-chat-button cursor-pointer">{{__('Show Less Chat')}}
            <i class="loading-icon-chat fa-lg fas fa-spinner d-none fa-spin hide"></i>
        </button>
    </div>
    {{-- More Info Chat Table Component--}}
    @if(!empty($companyChats))
        <h4 class="text-center in-center-more-detail-chat">{{__('More Chat Details')}}</h4>
        <x-billing.more-chat-table :company="$company" :companyChats="$companyChats"/>
        <button type="button" data-more="{{__('Show More Chat')}}" data-less="{{__('Show Less Chat')}}"
                class="btn btn-sm btn-info more-company-chat-button second-more-button cursor-pointer">{{__('Show Less Chat')}}
            <i class="loading-icon-chat fa-lg fas fa-spinner d-none fa-spin hide"></i>
        </button>
    @else
        <h3 class="text-center in-center-more-detail">{{ __('No detailed information found') }}</h3>
    @endif
@else
    <button type="button" data-more="{{__('Show More Chat')}}" data-less="{{__('Show Less Chat')}}"
            class="btn btn-sm btn-info more-company-chat-button cursor-pointer"
            data-url="{{ route('admin.companies.chat.more',['company' => $company,'start' => request()->start,'end' => request()->end]) }}">{{__('Show More Chat')}}
        <i class="loading-icon-chat fa-lg fas fa-spinner d-none fa-spin hide"></i>
    </button>
@endif


