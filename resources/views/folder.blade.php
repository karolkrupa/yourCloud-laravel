@extends('layouts.app')

@section('custom-head')
    @parent

    {{--<link rel="stylesheet" href="{{ asset('css/folder_page.css') }}">--}}

@endsection

@section('alerts-container')

@endsection

@section('left-menu')
    <a data-overlap="main" href="/files" class="btn"><i class="far fa-folder"></i>main</a>
    <a data-overlap="favorites" href="/files/favorites" class="btn"><i class="fas fa-star"></i>Favorites</a>
    <div data-overlap="tags" class="btn">
        <i class="fas fa-circle"></i>Tags
        <ul>
            <li data-tag-id="1"><a href="/files/tag/1" class="text-primary"><i class="fas fa-circle"></i>Blue</a></li>
            <li data-tag-id="2"><a href="/files/tag/2" class="text-success"><i class="fas fa-circle"></i>Green</a></li>
            <li data-tag-id="3"><a href="/files/tag/3" class="text-danger"><i class="fas fa-circle"></i>Red</a></li>
            <li data-tag-id="4"><a href="/files/tag/4" class="text-warning"><i class="fas fa-circle"></i>Yellow</a></li>
            <li data-tag-id="5"><a href="/files/tag/5" class="text-info"><i class="fas fa-circle"></i>Azure</a></li>
        </ul>
    </div>
    <a data-overlap="shared_for_me" href="/files/sharedforme" class="btn"><i class="fas fa-share-alt-square"></i>Shared for me</a>
    <a data-overlap="shared_by_me" href="/files/sharedbyme" class="btn"><i class="fas fa-share-alt-square"></i>Shared by me</a>
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
                    <th scope="col" class="cell-one-row"></th>
                    <th scope="col" class="cell-one-row"></th>
                    <th scope="col" class="cell-one-row">Size</th>
                    <th scope="col" class="cell-one-row">Last modify</th>
                </tr>
                <tr id="no-files-indicator" class="d-none">
                    <td colspan="6" class="text-center">You don't have files</td>
                </tr>
            </thead>
            <tbody>
                <!-- File list -->
            </tbody>
        </table>
    </div>


    <div id="file-context-menu">
        <div class="list-group">
            <button data-action="newFolder" type="button" class="list-group-item list-group-item-action"><i class="fas fa-folder"></i>New Folder</button>
            <button data-action="newFile" type="button" class="list-group-item list-group-item-action"><i class="fas fa-file-alt"></i>New File</button>
            <button data-action="share" type="button" class="list-group-item list-group-item-action"><i class="fas fa-share-alt"></i>Share</button>
            <div id="tag-context-menu">
                <button data-action="tag" type="button" class="list-group-item list-group-item-action"><i class="fas fa-star"></i>Tag</button>
                <div class="list-group">
                    <button data-action="tag" data-tag-id="1" type="button" class="list-group-item list-group-item-action text-primary"><i class="fas fa-circle"></i>Blue</button>
                    <button data-action="tag" data-tag-id="2" type="button" class="list-group-item list-group-item-action text-success"><i class="fas fa-circle"></i>Green</button>
                    <button data-action="tag" data-tag-id="3" type="button" class="list-group-item list-group-item-action text-danger"><i class="fas fa-circle"></i>Red</button>
                    <button data-action="tag" data-tag-id="4" type="button" class="list-group-item list-group-item-action text-warning"><i class="fas fa-circle"></i>Yellow</button>
                    <button data-action="tag" data-tag-id="5" type="button" class="list-group-item list-group-item-action text-info"><i class="fas fa-circle"></i>Azure</button>
                </div>
            </div>

            <button data-action="rename" type="button" class="list-group-item list-group-item-action"><i class="fas fa-pencil-alt"></i>Rename</button>
            <button data-action="deleteFile" type="button" class="list-group-item list-group-item-action"><i class="fas fa-trash-alt"></i>Delete</button>
            <button data-action="downloadFile" type="button" class="list-group-item list-group-item-action"><i class="fas fa-download"></i>Download</button>
        </div>
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
    </script>
@endsection
