
var File_list = {
    sort_asc: function () {
        $('#file-list tbody tr[data-file-type="0"]').sort(function(a,b){
            return $(a).attr('data-file-name').localeCompare($(b).attr('data-file-name'), {}, {numeric: true});
        }).appendTo('#file-list tbody');

        $('#file-list tbody tr[data-file-type="1"]').sort(function(a,b){
            return $(a).attr('data-file-name').localeCompare($(b).attr('data-file-name'), {}, {numeric: true});
        }).appendTo('#file-list tbody');

        this.sort = this.sort_asc;
    },

    sort_desc: function () {
        $('#file-list tbody tr[data-file-type="1"]').sort(function(a,b){
            return $(a).attr('data-file-name').localeCompare($(b).attr('data-file-name'), {}, {numeric: true})*-1;
        }).appendTo('#file-list tbody');

        $('#file-list tbody tr[data-file-type="0"]').sort(function(a,b){
            return $(a).attr('data-file-name').localeCompare($(b).attr('data-file-name'), {}, {numeric: true})*-1;
        }).appendTo('#file-list tbody');

        this.sort = this.sort_desc;
    },

    sort: null,

    sort_toggle: function() {
        if(this.sort == this.sort_asc) {
            this.sort_desc();
        }else{
            this.sort_asc();
        }
    }
}

window.File_list = File_list;

File_list.sort_asc();






// function partition(el, p, r) {
//     var val = el.eq(p).attr('data-file-name');
//     var i = p;
//     var j = r;
//     var b;
//     var element;
//     // alert();
//     while(true) {
//         while(el.eq(j).attr('data-file-name').localeCompare(val, {}, {numeric: true}) == 1) {
//             // alert(el.eq(j).attr('data-file-name')+ '>' + val);
//             // alert(j);
//             j--;
//         }
//
//         while(el.eq(i).attr('data-file-name').localeCompare(val, {}, {numeric: true}) == -1) {
//             i++;
//         }
//
//         if(i < j) {
//             b = el.eq(i).attr('data-file-name');
//             el.eq(i).attr('data-file-name', el.eq(j).attr('data-file-name'));
//             el.eq(i).find('.file-name').html(el.eq(j).attr('data-file-name'));
//             el.eq(j).attr('data-file-name', b);
//             el.eq(j).find('.file-name').html(b);
//
//             i++;
//             j--;
//         }else {
//             return j;
//         }
//     }
// }
//
// function quicsort(el, p, r) {
//     var q;
//     if(p < r) {
//         q = partition(el, p, r);
//         quicsort(el, p, q);
//         quicsort(el, q+1, r);
//     }
// }
//
// var el = $('#file-list tbody tr');
// var p = el.first();
// var r = el.last();

// quicsort(el, 0, el.length-1);
