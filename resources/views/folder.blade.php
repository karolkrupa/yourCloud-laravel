@extends('layouts.app')

@section('custom-head')
    @parent

    <link rel="stylesheet" href="{{ asset('css/folder_page.css') }}">

@endsection

@section('left-menu')
    <a href="" class="btn"><i class="fas fa-star"></i>Oznaczone</a>
    <a href="" class="btn"><i class="fas fa-align-justify"></i>Kategorie</a>
    <a href="" class="btn"><i class="fas fa-share-alt-square"></i>Udostępnione dla mnie</a>
    <a href="" class="btn"><i class="fas fa-share-alt-square"></i>Udostępnione przeze mnie</a>
@endsection

@section('navbar-right')
    <div id="dropzonejs-container">

        <div class="dropzonejs-template dropzonejs-file-container" id="dropzonejs-template">
            <div class="dropzonejs-layout dropzonejs-template">
                <div class="text-center">
                    <span data-dz-name>Nazwa pliku</span> (<span data-dz-size><b>33</b>kb </span>)
                </div>

                <div class="progress">
                    <div data-dz-uploadprogress class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">25%</div>
                </div>
            </div>

            <div data-dz-remove class="dropzonejs-layout-hover d-flex align-items-center justify-content-center">
                Anuluj
            </div>
        </div>
    </div>



@endsection

{{--<div class="row upload-item">--}}
    {{--<div class="col-12 action text-right">--}}
        {{--<button data-dz-remove class="btn btn-cancel">Anuluj</button>--}}
    {{--</div>--}}
    {{--<div class="col-2 icon">--}}
        {{--<img src="" alt="">--}}
    {{--</div>--}}
    {{--<div class="col-10 text-right file-details">--}}
        {{--<div class="row">--}}
            {{--<div data-dz-name class="col-12 text-right">Nazwa pliku</div>--}}
            {{--<div data-dz-size class="col-12 text-right">6.5MB</div>--}}
        {{--</div>--}}
    {{--</div>--}}
    {{--<div class="w-100"><div class="progress">--}}
            {{--<div data-dz-uploadprogress class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</div>--}}

@section('content')
    <div class="row">
        <div class="col-12 p-0">
            <nav aria-label="breadcrumb" role="navigation" id="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Library</li>
                </ol>
            </nav>
        </div>

        <table class="table table-hover table-dark files-list col-11" style="z-index: 1" id="file-list">
            <thead>
                <tr>
                    <th scope="col" class="text-center" id="checkbox-select-all">
                        <input type="checkbox">
                    </th>
                    <th scope="col">File name</th>
                    <th scope="col" style="width: 150px">Size</th>
                    <th scope="col" style="width: 150px">Last modify</th>
                </tr>
            </thead>
            <tbody>
            @foreach($files as $file)
                @component('components.file', ['data' => $file]) @endcomponent
            @endforeach
            <tr class="yc-template"
                    data-file-id="id"
                    data-file-parent-id="id"
                    data-file-type="1/2"
                    data-file-name="name"
                    data-file-size="size"
                    data-file-updated-at="date"
            >
                <!-- fa-folder/fa-file -->
                <td><i class="fas" style="font-size: 25px"></i></td>
                <td class="file-name">name</td>
                <td class="file-size">size</td>
                <td class="file-updated-at">date</td>
            </tr>
            </tbody>
        </table>
    </div>

    {{--<div id="dropzone-template" >--}}
        {{--<div class="" style="visibility: hidden">--}}
            {{--<div class="text-center">--}}
                {{--<span data-dz-name></span >(<span data-dz-size></span>)--}}
            {{--</div>--}}

            {{--<div class="progress">--}}
                {{--<div data-dz-uploadprogress class="progress-bar progress-bar-striped" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>--}}
            {{--</div>--}}
        {{--</div>--}}

    {{--</div>--}}
@endsection

@section('custom-js')
    @parent

    <script>
        enable_dropzonejs();

        // $('#file-list').dataTable({
        //     paging: false,
        //     searching: false,
        //     scrollY: 400,
        //     autoWidth: true,
        // });
    </script>
@endsection