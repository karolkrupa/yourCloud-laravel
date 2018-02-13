@extends('layouts.app')

@section('alerts-container')

@endsection

@section('left-menu')
    <a data-overlap="main" href="/files" class="btn"><i class="far fa-folder"></i>@lang('folderView.menu_main')</a>
    <a data-overlap="favorites" href="/files/favorites" class="btn"><i class="fas fa-star"></i>@lang('folderView.menu_favorites')</a>
    <div data-overlap="tags" class="btn">
        <i class="fas fa-circle"></i>@lang('folderView.menu_tags')
        <ul>
            <li data-tag-id="1"><a href="/files/tag/1" class="text-primary"><i class="fas fa-circle"></i>@lang('folderView.tag_blue')</a></li>
            <li data-tag-id="2"><a href="/files/tag/2" class="text-success"><i class="fas fa-circle"></i>@lang('folderView.tag_green')</a></li>
            <li data-tag-id="3"><a href="/files/tag/3" class="text-danger"><i class="fas fa-circle"></i>@lang('folderView.tag_red')</a></li>
            <li data-tag-id="4"><a href="/files/tag/4" class="text-warning"><i class="fas fa-circle"></i>@lang('folderView.tag_yellow')</a></li>
            <li data-tag-id="5"><a href="/files/tag/5" class="text-info"><i class="fas fa-circle"></i>@lang('folderView.tag_azure')</a></li>
        </ul>
    </div>
    <a data-overlap="shared_for_me" href="/files/sharedforme" class="btn"><i class="fas fa-share-alt-square"></i>@lang('folderView.menu_shared_for_me')</a>
    <a data-overlap="shared_by_me" href="/files/sharedbyme" class="btn"><i class="fas fa-share-alt-square"></i>@lang('folderView.menu_shared_by_me')</a>
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

@section('content')
    <div class="row">
        <div class="col-12 p-0">
            <nav aria-label="breadcrumb" role="navigation" id="breadcrumb">
                <ol class="breadcrumb mb-0">
                    {{-- Bradcrumb items inserts by Backbone --}}
                </ol>
            </nav>
        </div>

        <table class="table table-hover table-dark files-list col-11" id="file-table" data-files-count="0">
            <thead>
                <tr>
                    <th scope="col" class="text-center" id="checkbox-select-all" role="checkbox">
                        <i class="far fa-square"></i>
                    </th>
                    <th scope="col" data-sort-by="file-name">@lang('folderView.file_name')<i class="ml-2 fas fa-caret-up"></i></th>
                    <th scope="col" class="cell-one-row"></th>
                    {{--<th scope="col" class="cell-one-row"></th>--}}
                    <th scope="col" data-sort-by="file-size" class="cell-one-row file-size">@lang('folderView.file_size')<i class="ml-2 fas fa-caret-up invisible"></i></th>
                    <th scope="col" data-sort-by="file-last-modify" class="cell-one-row file-updated-at">@lang('folderView.file_last_modify')<i class="ml-2 fas fa-caret-up invisible"></i></th>
                </tr>
                <tr id="no-files-indicator" class="d-none">
                    {{-- In this place, Backbone inserts files creted by template from assets/js/folderView/templates/FileView/FileView.html --}}
                    <td colspan="6" class="text-center">@lang('folderView.no_files_msg')</td>
                </tr>
            </thead>
            <tbody>
                <!-- File list -->
            </tbody>
        </table>
    </div>


    <div id="file-context-menu">
        <div class="list-group">
            <button data-action="newFolder" type="button" class="list-group-item list-group-item-action"><i class="fas fa-folder"></i>@lang('folderView.context_new_folder')</button>
            <button data-action="newFile" type="button" class="list-group-item list-group-item-action"><i class="fas fa-file-alt"></i>@lang('folderView.context_new_file')</button>
            <button data-action="share" type="button" class="list-group-item list-group-item-action"><i class="fas fa-share-alt"></i>@lang('folderView.context_share')</button>
            <div id="tag-context-menu">
                <button data-action="tag" type="button" class="list-group-item list-group-item-action"><i class="fas fa-star"></i>@lang('folderView.context_tag')</button>
                <div class="list-group">
                    <button data-action="tag" data-tag-id="1" type="button" class="list-group-item list-group-item-action text-primary"><i class="fas fa-circle"></i>@lang('folderView.tag_blue')</button>
                    <button data-action="tag" data-tag-id="2" type="button" class="list-group-item list-group-item-action text-success"><i class="fas fa-circle"></i>@lang('folderView.tag_green')</button>
                    <button data-action="tag" data-tag-id="3" type="button" class="list-group-item list-group-item-action text-danger"><i class="fas fa-circle"></i>@lang('folderView.tag_red')</button>
                    <button data-action="tag" data-tag-id="4" type="button" class="list-group-item list-group-item-action text-warning"><i class="fas fa-circle"></i>@lang('folderView.tag_yellow')</button>
                    <button data-action="tag" data-tag-id="5" type="button" class="list-group-item list-group-item-action text-info"><i class="fas fa-circle"></i>@lang('folderView.tag_azure')</button>
                </div>
            </div>

            <button data-action="rename" type="button" class="list-group-item list-group-item-action"><i class="fas fa-pencil-alt"></i>@lang('folderView.context_rename')</button>
            <button data-action="deleteFile" type="button" class="list-group-item list-group-item-action"><i class="fas fa-trash-alt"></i>@lang('folderView.context_delete')</button>
            <button data-action="downloadFile" type="button" class="list-group-item list-group-item-action"><i class="fas fa-download"></i>@lang('folderView.context_download')</button>
        </div>
    </div>


@endsection

@section('custom-js')
    @parent

    <script src="{{ asset('js/folderView.js') }}">
        App.enable_dropzonejs();
    </script>
@endsection
