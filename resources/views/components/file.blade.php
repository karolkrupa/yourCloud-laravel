<tr
        data-file-id="{{ $data['id'] }}"
        data-file-parent-id="{{ $data['parent_id'] }}"
        data-file-type="{{ $data['type'] }}"
        data-file-name="{{ $data['name'] }}"
        data-file-size="{{ $data['size'] }}"
        data-file-updated-at="{{ $data['updated_at'] }}"
>
    <td><i class="fas fa-{{ ($data['type'] == 1)? 'file' : 'folder' }}" style="font-size: 25px"></i></td>
    <td class="file-name">{{ $data['name'] }}</td>
    @if($data['pivot']['favorite'])
        <td class="favorite-btn active" role="button"></td>
    @else
        <td class="favorite-btn" role="button"></td>
    @endif
    <td class="file-size">{{ ($data['type'] == 0)? '-' : UnitConverter::bytesToHuman($data['size']) }}</td>
    <td class="file-updated-at">{{ $data['updated_at'] }}</td>
</tr>