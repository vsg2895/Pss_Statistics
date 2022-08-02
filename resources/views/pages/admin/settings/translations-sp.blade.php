@extends('layouts.app', [
    "activePage" => "translationsz",
    "headerName" => "Translations SPP",
])

@push('css')
    <style>
        .setting-description {
            text-align: left !important;
            max-width: 350px !important;
            white-space: pre-wrap !important;
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid main-container">
        <form action="{{route('admin.settings.translations.store.sp')}}" method="post">
            @csrf
            <div class="row mt-4">
                <div class="col-md-10">
                    <div class="card">
                        <!-- Light table -->
                        <div class="table-responsive">
                            <table class="table align-items-center">
                                <tbody>
                                @foreach($words as $english => $swedish)
                                    <tr>
                                        <td>
                                            {{$english}}
                                        </td>
                                        <td>
                                            <input type="text" name="keys[]" value="{{$english}}" hidden>
                                            <input class="w-100" type="text" name="values[]" value="{{$swedish}}">
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-success">
                        {{__('Save')}}
                    </button>
                </div>
            </div>
        </form>

        @include('layouts.footers.pages')
    </div>
@endsection

@push('js')
    <script>

    </script>
@endpush
