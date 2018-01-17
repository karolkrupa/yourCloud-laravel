<tr
        data-file-id="{{ $data['id'] }}"
        data-file-parent-id="{{ $data['parent_id'] }}"
        data-file-type="{{ $data['type'] }}"
        data-file-name="{{ $data['name'] }}"
        data-file-size="{{ $data['size'] }}"
        data-file-updated-at="{{ $data['updated_at'] }}"
        data-tag-id="{{ $data['tag_id'] }}"
>
    {{--<td><i class="fas fa-{{ ($data['type'] == 1)? 'file' : 'folder' }}" style="font-size: 25px"></i></td>--}}
    <td class="file-icon">
        <span class="fa-layers" style="font-size: 25px">
            <i class="fas fa-{{ ($data['type'] == 1)? 'file' : 'folder' }}"></i>
            @if($data['tag_id'])
                <i class="fas fa-circle" data-tag-id="{{ $data['tag_id'] }}" data-fa-transform="shrink-10 up-5 left-7"></i>
            @else
                <i class="fas fa-circle" data-fa-transform="shrink-10 up-5 left-7" data-tag-id="null"></i>
            @endif
        </span>
    </td>
    <td class="file-name">{{ $data['name'] }}</td>
    <td class="file-controls">
        <div>
            <i class="fas fa-user share-user-icon" style="{{ ($data['share_type'] != 0)? '' : 'display: none' }}"></i>
            <i class="fas fa-link share-link-icon" style="{{ $data['share_link']? '' : 'display: none' }}"></i>
        </div>
    </td>

    @if($data['favorite'])
        <td class="favorite-btn active" role="button"></td>
    @else
        <td class="favorite-btn" role="button"></td>
    @endif
    <td class="file-size">{{ ($data['type'] == 0)? '-' : UnitConverter::bytesToHuman($data['size']) }}</td>
    <td class="file-updated-at">{{ $data['updated_at'] }}</td>
</tr>